<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatMessage
 *
 * @author Jakub Konečný
 */
class ChatMessage {
  use \Nette\SmartObject;
  
  public function __construct(public int $id, public string $message, public string $when, public ChatCharacter $character) {
  }

  /**
   * @deprecated Access the property directly
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @deprecated Access the property directly
   */
  protected function setId(int $id): void {
    $this->id = $id;
  }

  /**
   * @deprecated Access the property directly
   */
  public function getMessage(): string {
    return $this->message;
  }

  /**
   * @deprecated Access the property directly
   */
  protected function setMessage(string $message): void {
    $this->message = $message;
  }

  /**
   * @deprecated Access the property directly
   */
  public function getWhen(): string {
    return $this->when;
  }

  /**
   * @deprecated Access the property directly
   */
  protected function setWhen(string $when): void {
    $this->when = $when;
  }

  /**
   * @deprecated Access the property directly
   */
  public function getCharacter(): ChatCharacter {
    return $this->character;
  }

  /**
   * @deprecated Access the property directly
   */
  protected function setCharacter(ChatCharacter $character): void {
    $this->character = $character;
  }
}
?>