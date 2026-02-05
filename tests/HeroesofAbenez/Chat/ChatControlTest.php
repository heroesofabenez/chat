<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use Tester\Assert;
use Nexendrie\Translation\Translator;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ChatControlTest extends \Tester\TestCase
{
    use \Testbench\TComponent;
    use \Testbench\TCompiledContainer;

    protected ExampleChatControl $control;

    public function setUp(): void
    {
        static $control = null;
        if ($control === null) {
            /** @var ExampleChatControlFactory $factory */
            $factory = $this->getService(ExampleChatControlFactory::class);
            $control = $factory->create();
        }
        $this->control = $control;
        $this->attachToPresenter($this->control);
    }

    public function testMessagesPerPage(): void
    {
        $originalValue = $this->control->messagesPerPage;
        Assert::type("int", $originalValue);
        $this->control->messagesPerPage = 1;
        Assert::same(1, $this->control->messagesPerPage);
        $this->control->messagesPerPage = -1;
        Assert::same(0, $this->control->messagesPerPage);
        $this->control->messagesPerPage = $originalValue;
    }

    public function testCharacterProfileLink(): void
    {
        $originalValue = $this->control->characterProfileLink;
        Assert::type("string", $originalValue);
        $this->control->characterProfileLink = "abc";
        Assert::same("abc", $this->control->characterProfileLink);
        $this->control->characterProfileLink = $originalValue;
    }

    public function testTranslator(): void
    {
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

    public function testRender(): void
    {
        $this->control->characterProfileLink = "Profile:default";
        $this->checkRenderOutput($this->control, __DIR__ . "/chatExpected.latte");
    }
}

$test = new ChatControlTest();
$test->run();
