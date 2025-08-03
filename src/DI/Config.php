<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

use HeroesofAbenez\Chat\ChatMessageProcessor;
use HeroesofAbenez\Chat\DatabaseAdapter;

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
   * @var class-string<ChatMessageProcessor>[]
   */
  public array $messageProcessors = [];

  /**
   * @var class-string<DatabaseAdapter>
   */
  public string $databaseAdapter;

  public string $characterProfileLink = "";
}
?>