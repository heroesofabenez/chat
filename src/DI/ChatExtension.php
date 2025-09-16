<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

use HeroesofAbenez\Chat\ChatCommandsProcessor;
use HeroesofAbenez\Chat\IChatCommand;
use HeroesofAbenez\Chat\ChatControl;
use HeroesofAbenez\Chat\NewChatMessageFormFactory;
use HeroesofAbenez\Chat\ChatMessageProcessor;
use HeroesofAbenez\Chat\DatabaseAdapter;
use Nette\DI\MissingServiceException;
use HeroesofAbenez\Chat\InvalidChatControlFactoryException;
use HeroesofAbenez\Chat\InvalidMessageProcessorException;
use HeroesofAbenez\Chat\InvalidDatabaseAdapterException;
use Nette\Schema\Expect;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;

/**
 * ChatExtension
 *
 * @author Jakub Konečný
 * @property Config $config
 */
final class ChatExtension extends \Nette\DI\CompilerExtension
{
    /** @internal */
    public const SERVICE_CHAT_COMMANDS_PROCESSOR = "commandsProcessor";
    /** @internal */
    public const SERVICE_DATABASE_ADAPTER = "databaseAdapter";
    /** @internal */
    public const SERVICE_NEW_MESSAGE_FORM = "newMessageForm";
    /** @internal */
    public const TAG_CHAT = "chat.chat";

    public function getConfigSchema(): \Nette\Schema\Schema
    {
        return Expect::from(new Config(), [
            "chats" => Expect::arrayOf("interface")->default([])->required(),
            "messageProcessors" => Expect::arrayOf("class")->default([
                self::SERVICE_CHAT_COMMANDS_PROCESSOR => ChatCommandsProcessor::class,
            ]),
            "databaseAdapter" => Expect::type("class")->required(),
        ]);
    }

    /**
     * @throws InvalidChatControlFactoryException
     */
    private function validateFactory(string $interface): void
    {
        try {
            $rm = new \ReflectionMethod($interface, "create");
        } catch (\ReflectionException $e) {
            throw new InvalidChatControlFactoryException("Interface $interface does not contain method create.", 0, $e);
        }
        $returnType = $rm->getReturnType();
        if ($returnType === null || !is_subclass_of($returnType->getName(), ChatControl::class)) {
            throw new InvalidChatControlFactoryException("Return type of $interface::create() is not a subtype of " . ChatControl::class . ".");
        }
    }

    /**
     * @throws InvalidChatControlFactoryException
     */
    private function getChats(): array
    {
        $chats = [];
        foreach ($this->config->chats as $name => $interface) {
            $this->validateFactory($interface);
            $chats[$name] = $interface;
        }
        return $chats;
    }

    /**
     * @throws InvalidMessageProcessorException
     */
    private function getMessageProcessors(): array
    {
        $messageProcessors = [];
        foreach ($this->config->messageProcessors as $name => $processor) {
            if (!is_subclass_of($processor, ChatMessageProcessor::class)) {
                throw new InvalidMessageProcessorException("Invalid message processor $processor.");
            }
            $messageProcessors[$name] = $processor;
        }
        return $messageProcessors;
    }

    /**
     * @throws InvalidDatabaseAdapterException
     */
    private function getDatabaseAdapter(): string
    {
        $adapter = $this->config->databaseAdapter;
        if (!is_subclass_of($adapter, DatabaseAdapter::class)) {
            throw new InvalidDatabaseAdapterException("Invalid database adapter $adapter.");
        }
        return $adapter;
    }

    /**
     * @throws InvalidChatControlFactoryException
     * @throws InvalidMessageProcessorException
     * @throws InvalidDatabaseAdapterException
     */
    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $chats = $this->getChats();
        $characterProfileLink = $this->config->characterProfileLink;
        foreach ($chats as $name => $interface) {
            $chat = $builder->addFactoryDefinition($this->prefix($name))
                ->setImplement($interface)
                ->addTag(self::TAG_CHAT);
            if ($characterProfileLink !== "") {
                $chat->getResultDefinition()->addSetup("setCharacterProfileLink", [$characterProfileLink]);
            }
        }
        $messageProcessors = $this->getMessageProcessors();
        foreach ($messageProcessors as $name => $processor) {
            $builder->addDefinition($this->prefix($name))
                ->setType($processor);
        }
        $databaseAdapter = $this->getDatabaseAdapter();
        $builder->addDefinition($this->prefix(self::SERVICE_DATABASE_ADAPTER))
            ->setType($databaseAdapter);
        $builder->addDefinition($this->prefix(self::SERVICE_NEW_MESSAGE_FORM))
            ->setType(NewChatMessageFormFactory::class);
    }

    private function registerMessageProcessors(): void
    {
        $builder = $this->getContainerBuilder();
        $chats = $this->getChats();
        $messageProcessors = $this->getMessageProcessors();
        foreach ($chats as $chat => $chatClass) {
            /** @var FactoryDefinition $chatService */
            $chatService = $builder->getDefinition($this->prefix($chat));
            foreach ($messageProcessors as $processor => $processorClass) {
                $processorService = $builder->getDefinition($this->prefix($processor));
                $chatService->getResultDefinition()->addSetup("addMessageProcessor", [$processorService]);
            }
        }
    }

    private function registerChatCommands(): void
    {
        $builder = $this->getContainerBuilder();
        try {
            /** @var ServiceDefinition $processor */
            $processor = $builder->getDefinition($this->prefix(self::SERVICE_CHAT_COMMANDS_PROCESSOR));
        } catch (MissingServiceException) {
            return;
        }
        $chatCommands = $builder->findByType(IChatCommand::class);
        foreach ($chatCommands as $command) {
            $processor->addSetup("addCommand", [$command]);
        }
    }

    public function beforeCompile(): void
    {
        $this->registerMessageProcessors();
        $this->registerChatCommands();
    }
}
