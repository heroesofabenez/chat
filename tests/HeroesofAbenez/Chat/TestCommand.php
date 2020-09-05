<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

final class TestCommand extends ChatCommand {
  /** @var string */
  protected string $name = "test1";
  
  public function execute(): string {
    return "passed";
  }
}
?>