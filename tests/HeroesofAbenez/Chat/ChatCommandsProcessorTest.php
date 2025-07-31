<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ChatCommandsProcessorTest extends \Tester\TestCase {
  const COMMAND_NAME = "test1";
  const TEXT = "/" . self::COMMAND_NAME;

  protected ChatCommandsProcessor $model;
  
  use \Testbench\TCompiledContainer;
  
  public function __construct() {
    $this->model = $this->getService(ChatCommandsProcessor::class); // @phpstan-ignore assign.propertyType
    $this->model->addCommand(new TestCommand());
  }
  
  public function testAddCommand(): void {
    $model = clone $this->model;
    $model->addCommand(new Test2Command());
    Assert::same("test", $model->parse("/" . Test2Command::NAME));
    Assert::exception(function() use($model) {
      $model->addCommand(new Test2Command());
    }, CommandNameAlreadyUsedException::class);
  }
  
  public function testAddAlias(): void {
    $model = clone $this->model;
    $model->addAlias(self::COMMAND_NAME, "test");
    Assert::same("passed", $model->parse("/test"));
    Assert::exception(function() use($model) {
      $model->addAlias("abc", "test");
    }, CommandNotFoundException::class);
    Assert::exception(function() use($model) {
      $model->addAlias(self::COMMAND_NAME, "test");
    }, CommandNameAlreadyUsedException::class);
  }
  
  public function testExtractCommand(): void {
    Assert::same("", $this->model->extractCommand("anagfdffd"));
    Assert::same("", $this->model->extractCommand("/anagfdffd"));
    Assert::same(self::COMMAND_NAME, $this->model->extractCommand(self::TEXT));
  }
  
  /**
   * @return array<int, string[]>
   */
  public function getTexts(): array {
    return [
      ["anagfdffd", "/anagfdffd", ]
    ];
  }
  
  /**
   * @dataProvider getTexts
   */
  public function testExtractParametersNothing(string $text): void {
    $result = $this->model->extractParameters($text);
    Assert::type("array", $result);
    Assert::count(0, $result);
  }
  
  public function testExtractParameters(): void {
    $result = $this->model->extractParameters("/test abc 123");
    Assert::type("array", $result);
    Assert::count(2, $result);
  }
  
  public function testHasCommand(): void {
    Assert::false($this->model->hasCommand("anagfdffd"));
    Assert::true($this->model->hasCommand(self::COMMAND_NAME));
  }
  
  public function testGetCommand(): void {
    Assert::type(IChatCommand::class, $this->model->getCommand(self::COMMAND_NAME));
    Assert::exception(function() {
      $this->model->getCommand("abc");
    }, CommandNotFoundException::class);
  }
  
  public function testParse(): void {
    $model = clone $this->model;
    $model->addCommand(new Test2Command());
    Assert::same("passed", $this->model->parse(self::TEXT));
    Assert::null($model->parse("anagfdffd"));
    Assert::null($model->parse("/anagfdffd"));
    Assert::same("test12", $model->parse("/test2 1 2"));
  }
}

$test = new ChatCommandsProcessorTest();
$test->run();
?>