<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

use HeroesofAbenez\Chat\ChatCommandsProcessor;
use HeroesofAbenez\Chat\Test2Command;
use HeroesofAbenez\Chat\TestCommand;
use HeroesofAbenez\Chat\InvalidChatControlFactoryException;
use HeroesofAbenez\Chat\InvalidMessageProcessorException;
use HeroesofAbenez\Chat\InvalidDatabaseAdapterException;
use HeroesofAbenez\Chat\ExampleChatControlFactory;
use MyTester\Attributes\AfterTest;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;

#[TestSuite("ChatExtension")]
#[RequiresPhpVersion("8.4.0")]
final class ChatExtensionTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    #[AfterTest]
    public function restoreContainer(): void
    {
        $this->refreshContainer();
    }

    public function testChats(): void
    {
        /** @var ExampleChatControlFactory $factory */
        $factory = $this->getService(ExampleChatControlFactory::class);
        $this->assertType(ExampleChatControlFactory::class, $factory);
        $this->assertSame("", $factory->create()->characterProfileLink);
        $config = [
            "chat" => [
                "characterProfileLink" => "Abc:",
            ]
        ];
        $this->refreshContainer($config);
        /** @var ExampleChatControlFactory $factory */
        $factory = $this->getService(ExampleChatControlFactory::class);
        $this->assertType(ExampleChatControlFactory::class, $factory);
        $this->assertSame("Abc:", $factory->create()->characterProfileLink);
        $config = [
            "chat" => [
                "chats" => [
                    "abc" => \Countable::class,
                ],
            ]
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidChatControlFactoryException::class);
        $config["chat"]["chats"]["abc"] = FakeFactory::class;
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidChatControlFactoryException::class);
    }

    public function testMessageProcessors(): void
    {
        $config = [
            "chat" => [
                "messageProcessors" => [
                    "abc" => \stdClass::class,
                ],
            ],
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidMessageProcessorException::class);
    }

    public function testDatabaseAdapter(): void
    {
        $config = [
            "chat" => [
                "databaseAdapter" => \stdClass::class,
            ],
        ];
        $this->assertThrowsException(function () use ($config) {
            $this->refreshContainer($config);
        }, InvalidDatabaseAdapterException::class);
    }

    public function testChatCommands(): void
    {
        $config = [
            "services" => [
                TestCommand::class, Test2Command::class,
            ]
        ];
        $this->refreshContainer($config);
        /** @var ChatCommandsProcessor $processor */
        $processor = $this->getService(ChatCommandsProcessor::class);
        /** @var TestCommand $command1 */
        $command1 = $this->getService(TestCommand::class);
        /** @var Test2Command $command2 */
        $command2 = $this->getService(Test2Command::class);
        $this->assertTrue($processor->hasCommand($command1->getName()));
        $this->assertTrue($processor->hasCommand($command2->getName()));
    }
}
