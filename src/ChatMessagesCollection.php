<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatMessagesCollection
 *
 * @author Jakub Konečný
 */
final class ChatMessagesCollection extends \Nexendrie\Utils\Collection {
  protected $class = ChatMessage::class;
}
?>