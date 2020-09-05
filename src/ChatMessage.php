<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatMessage
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $message
 * @property string $when
 * @property ChatCharacter $character
 */
class ChatMessage {
  use \Nette\SmartObject;

  protected int $id;
  protected string $message;
  protected string $when;
  protected ChatCharacter $character;
  
  public function __construct(int $id, string $message, string $when, ChatCharacter $character) {
    $this->id = $id;
    $this->message = $message;
    $this->when = $when;
    $this->character = $character;
  }

  public function getId(): int {
    return $this->id;
  }

  protected function setId(int $id): void {
    $this->id = $id;
  }

  public function getMessage(): string {
    return $this->message;
  }

  protected function setMessage(string $message): void {
    $this->message = $message;
  }

  public function getWhen(): string {
    return $this->when;
  }

  protected function setWhen(string $when): void {
    $this->when = $when;
  }

  public function getCharacter(): ChatCharacter {
    return $this->character;
  }

  protected function setCharacter(ChatCharacter $character): void {
    $this->character = $character;
  }
}
?>