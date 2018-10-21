<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * FakeDatabaseAdapter
 *
 * @author Jakub Konečný
 */
final class FakeDatabaseAdapter implements IDatabaseAdapter {
  protected function getFakeCharacter(): ChatCharacter {
    return new ChatCharacter(1, "fake");
  }

  /**
   * @param mixed $value
   */
  public function getTexts(string $column, $value, int $limit): ChatMessagesCollection {
    $texts = new ChatMessagesCollection();
    for($i = 1; $i <= $limit; $i++) {
      $texts[] = new ChatMessage($i, "text", "now", $this->getFakeCharacter());
    }
    return $texts;
  }

  /**
   * @param mixed $value
   */
  public function getCharacters(string $column, $value): ChatCharactersCollection {
    $characters = new ChatCharactersCollection();
    $characters[] = $this->getFakeCharacter();
    return $characters;
  }
  
  public function addMessage(string $message, string $filterColumn, int $filterValue): void {
  }
}
?>