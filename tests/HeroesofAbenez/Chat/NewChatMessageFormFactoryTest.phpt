<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

require __DIR__ . "/../../bootstrap.php";

/**
 * @skip testbench does not fully support nette/http 3.0
 */
final class NewChatMessageFormFactoryTest extends \Tester\TestCase {
  use \Testbench\TPresenter;

  public function testCreate() {
    $this->checkAction("Chat:default");
  }
}

$test = new NewChatMessageFormFactoryTest();
$test->run();
?>