<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatCharactersCollection
 *
 * @author Jakub Konečný
 */
final class ChatCharactersCollection extends \Nexendrie\Utils\Collection {
  protected string $class = ChatCharacter::class;
}
?>