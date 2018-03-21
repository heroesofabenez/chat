<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Tester\Assert,
    Nexendrie\Translation\Translator,
    Nexendrie\Translation\Loaders\NeonLoader;

require __DIR__ . "/../../bootstrap.php";

final class ChatControlTest extends \Tester\TestCase {
  use \Testbench\TComponent;
  use \Testbench\TCompiledContainer;
  
  /** @var ExampleChatControl */
  protected $control;
  
  public function setUp() {
    static $control = NULL;
    if(is_null($control)) {
      $control = $this->getService(IExampleChatControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testLang() {
    $this->control->lang = "cs";
    Assert::same("cs", $this->control->lang);
  }
  
  public function testTranslator() {
    $loader = new NeonLoader();
    $loader->folders = [__DIR__ . "/../../../src/lang"];
    $this->control->translator = new Translator($loader);
    Assert::same("en", $this->control->translator->lang);
    $result = $this->control->translator->translate("chat.peopleInRoom");
    Assert::type("string", $result);
    Assert::same("People in this room:", $result);
    $this->control->translator->lang = "cs";
    $result = $this->control->translator->translate("chat.peopleInRoom");
    Assert::type("string", $result);
    Assert::same("Lidé v této místnosti:", $result);
  }
  
  public function testRender() {
    $this->control->lang = "en";
    $this->checkRenderOutput($this->control, __DIR__ . "/chatExpected.latte");
  }
}

$test = new ChatControlTest();
$test->run();
?>