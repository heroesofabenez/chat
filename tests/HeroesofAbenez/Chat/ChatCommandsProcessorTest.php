<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use MyTester\Attributes\BeforeTestSuite;
use MyTester\Attributes\DataProvider;
use MyTester\Attributes\TestSuite;

#[TestSuite("ChatCommandsProcessor")]
final class ChatCommandsProcessorTest extends \MyTester\TestCase
{
    use \MyTester\Bridges\NetteDI\TCompiledContainer;

    private const string COMMAND_NAME = "test1";
    private const string TEXT = "/" . self::COMMAND_NAME;

    protected ChatCommandsProcessor $model;

    #[BeforeTestSuite]
    public function prepareModel(): void
    {
        $this->model = $this->getService(ChatCommandsProcessor::class);
        $this->model->addCommand(new TestCommand());
    }

    public function testAddCommand(): void
    {
        $model = clone $this->model;
        $model->addCommand(new Test2Command());
        $this->assertSame("test", $model->parse("/" . Test2Command::NAME));
        $this->assertThrowsException(function () use ($model) {
            $model->addCommand(new Test2Command());
        }, CommandNameAlreadyUsedException::class);
    }

    public function testAddAlias(): void
    {
        $model = clone $this->model;
        $model->addAlias(self::COMMAND_NAME, "test");
        $this->assertSame("passed", $model->parse("/test"));
        $this->assertThrowsException(function () use ($model) {
            $model->addAlias("abc", "test");
        }, CommandNotFoundException::class);
        $this->assertThrowsException(function () use ($model) {
            $model->addAlias(self::COMMAND_NAME, "test");
        }, CommandNameAlreadyUsedException::class);
    }

    public function testExtractCommand(): void
    {
        $this->assertSame("", $this->model->extractCommand("anagfdffd"));
        $this->assertSame("", $this->model->extractCommand("/anagfdffd"));
        $this->assertSame(self::COMMAND_NAME, $this->model->extractCommand(self::TEXT));
    }

    /**
     * @return array<int, string[]>
     */
    public function getTexts(): array
    {
        return [
            ["anagfdffd",],
            ["/anagfdffd",],
        ];
    }

    #[DataProvider("getTexts")]
    public function testExtractParametersNothing(string $text): void
    {
        $result = $this->model->extractParameters($text);
        $this->assertType("array", $result);
        $this->assertCount(0, $result);
    }

    public function testExtractParameters(): void
    {
        $result = $this->model->extractParameters("/test abc 123");
        $this->assertType("array", $result);
        $this->assertCount(2, $result);
    }

    public function testHasCommand(): void
    {
        $this->assertFalse($this->model->hasCommand("anagfdffd"));
        $this->assertTrue($this->model->hasCommand(self::COMMAND_NAME));
    }

    public function testGetCommand(): void
    {
        $this->assertType(IChatCommand::class, $this->model->getCommand(self::COMMAND_NAME));
        $this->assertThrowsException(function () {
            $this->model->getCommand("abc");
        }, CommandNotFoundException::class);
    }

    public function testParse(): void
    {
        $model = clone $this->model;
        $model->addCommand(new Test2Command());
        $this->assertSame("passed", $this->model->parse(self::TEXT));
        $this->assertNull($model->parse("anagfdffd"));
        $this->assertNull($model->parse("/anagfdffd"));
        $this->assertSame("test12", $model->parse("/test2 1 2"));
    }
}
