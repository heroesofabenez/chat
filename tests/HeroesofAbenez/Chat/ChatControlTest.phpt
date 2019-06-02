<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Tester\Assert;
use Nexendrie\Translation\Translator;

require __DIR__ . "/../../bootstrap.php";

final class ChatControlTest extends \Tester\TestCase {
  use \Testbench\TComponent;
  use \Testbench\TCompiledContainer;
  
  /** @var ExampleChatControl */
  protected $control;
  
  public function setUp() {
    static $control = null;
    if(is_null($control)) {
      $control = $this->getService(IExampleChatControlFactory::class)->create();
    }
    $this->control = $control;
    $this->attachToPresenter($this->control);
  }
  
  public function testMessagesPerPage() {
    $originalValue = $this->control->messagesPerPage;
    Assert::type("int", $originalValue);
    $this->control->messagesPerPage = 1;
    Assert::same(1, $this->control->messagesPerPage);
    $this->control->messagesPerPage = -1;
    Assert::same(0, $this->control->messagesPerPage);
    $this->control->messagesPerPage = $originalValue;
  }
  
  public function testCharacterProfileLink() {
    $originalValue = $this->control->characterProfileLink;
    Assert::type("string", $originalValue);
    $this->control->characterProfileLink = "abc";
    Assert::same("abc", $this->control->characterProfileLink);
    $this->control->characterProfileLink = $originalValue;
  }
  
  public function testTranslator() {
    /** @var Translator $translator */
    $translator = $this->getService(Translator::class);
    Assert::same("en", $translator->lang);
    $result = $translator->translate("chat.peopleInRoom");
    Assert::type("string", $result);
    Assert::same("People in this room:", $result);
    $translator->lang = "cs";
    $result = $translator->translate("chat.peopleInRoom");
    Assert::type("string", $result);
    Assert::same("Lidé v této místnosti:", $result);
    $translator->lang = "en";
  }
  
  public function testRender() {
    $this->control->characterProfileLink = "Profile:default";
    $this->checkRenderOutput($this->control, __DIR__ . "/chatExpected.latte");
  }
}

$test = new ChatControlTest();
$test->run();
?>