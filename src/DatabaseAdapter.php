<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * DatabaseAdapter
 *
 * @author Jakub Konečný
 */
interface DatabaseAdapter {
  /**
   * @param mixed $value
   */
  public function getTexts(string $column, $value, int $limit): ChatMessagesCollection;
  /**
   * @param mixed $value
   */
  public function getCharacters(string $column, $value): ChatCharactersCollection;
  public function addMessage(string $message, string $filterColumn, int $filterValue): void;
}
?>