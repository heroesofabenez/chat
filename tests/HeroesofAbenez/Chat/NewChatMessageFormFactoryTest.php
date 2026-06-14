<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;

#[TestSuite("NewChatMessageFormFactory")]
final class NewChatMessageFormFactoryTest extends \MyTester\TestCase
{
    //use \Testbench\TPresenter;

    #[Skip]
    public function testCreate(): void
    {
        //$this->checkAction("Chat:default");
    }
}
