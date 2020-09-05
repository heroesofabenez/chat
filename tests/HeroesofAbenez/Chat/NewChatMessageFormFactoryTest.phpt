<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
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