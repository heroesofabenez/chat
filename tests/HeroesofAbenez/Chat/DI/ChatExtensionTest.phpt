<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

require __DIR__ . "/../../../bootstrap.php";

use HeroesofAbenez\Chat\ChatCommandsProcessor;
use HeroesofAbenez\Chat\Test2Command;
use HeroesofAbenez\Chat\TestCommand;
use Tester\Assert;
use HeroesofAbenez\Chat\InvalidChatControlFactoryException;
use HeroesofAbenez\Chat\InvalidMessageProcessorException;
use HeroesofAbenez\Chat\InvalidDatabaseAdapterException;
use HeroesofAbenez\Chat\IExampleChatControlFactory;

final class ChatExtensionTest extends \Tester\TestCase {
  use \Testbench\TCompiledContainer;
  
  public function testChats() {
    /** @var IExampleChatControlFactory $factory */
    $factory = $this->getService(IExampleChatControlFactory::class);
    Assert::type(IExampleChatControlFactory::class, $factory);
    Assert::same("", $factory->create()->characterProfileLink);
    $config = [
      "chat" => [
        "characterProfileLink" => "Abc:",
      ]
    ];
    $this->refreshContainer($config);
    /** @var IExampleChatControlFactory $factory */
    $factory = $this->getService(IExampleChatControlFactory::class);
    Assert::type(IExampleChatControlFactory::class, $factory);
    Assert::same("Abc:", $factory->create()->characterProfileLink);
    $config = [
      "chat" => [
        "chats" => [
          "abc" => \Countable::class,
        ],
      ]
    ];
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
    $config["chat"]["chats"]["abc"] = IFakeFactory::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidChatControlFactoryException::class);
  }
  
  public function testMessageProcessors() {
    $config["chat"]["messageProcessors"]["abc"] = \stdClass::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidMessageProcessorException::class);
  }
  
  public function testDatabaseAdapter() {
    $config["chat"]["databaseAdapter"] = \stdClass::class;
    Assert::exception(function() use($config) {
      $this->refreshContainer($config);
    }, InvalidDatabaseAdapterException::class);
  }
  
  public function testChatCommands() {
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
    Assert::true($processor->hasCommand($command1->getName()));
    Assert::true($processor->hasCommand($command2->getName()));
  }
}

$test = new ChatExtensionTest();
$test->run();
?>