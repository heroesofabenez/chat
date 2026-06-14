<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\TestSuite;
use Nexendrie\Translation\Translator;

#[TestSuite("ChatControl")]
final class ChatControlTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteApplication\TComponent;
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    protected ExampleChatControl $control;

    #[BeforeTest]
    public function prepareControl(): void
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
        $this->assertType("int", $originalValue);
        $this->control->messagesPerPage = 1;
        $this->assertSame(1, $this->control->messagesPerPage);
        $this->control->messagesPerPage = -1;
        $this->assertSame(0, $this->control->messagesPerPage);
        $this->control->messagesPerPage = $originalValue;
    }

    public function testCharacterProfileLink(): void
    {
        $originalValue = $this->control->characterProfileLink;
        $this->assertType("string", $originalValue);
        $this->control->characterProfileLink = "abc";
        $this->assertSame("abc", $this->control->characterProfileLink);
        $this->control->characterProfileLink = $originalValue;
    }

    public function testTranslator(): void
    {
        /** @var Translator $translator */
        $translator = $this->getService(Translator::class);
        $this->assertSame("en", $translator->lang);
        $result = $translator->translate("chat.peopleInRoom");
        $this->assertType("string", $result);
        $this->assertSame("People in this room:", $result);
        $translator->lang = "cs";
        $result = $translator->translate("chat.peopleInRoom");
        $this->assertType("string", $result);
        $this->assertSame("Lidé v této místnosti:", $result);
        $translator->lang = "en";
    }

    public function testRender(): void
    {
        $this->control->characterProfileLink = "Profile:default";
        $this->assertRenderOutputFile($this->control, __DIR__ . "/chatExpected.latte");
    }
}
