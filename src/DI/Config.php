<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

use HeroesofAbenez\Chat\IChatMessageProcessor;
use HeroesofAbenez\Chat\IDatabaseAdapter;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Config {
  /**
   * @var class-string[]
   */
  public array $chats = [];

  /**
   * @var class-string<IChatMessageProcessor>[]
   */
  public array $messageProcessors = [];

  /**
   * @var class-string<IDatabaseAdapter>
   */
  public string $databaseAdapter;

  public string $characterProfileLink = "";
}
?>