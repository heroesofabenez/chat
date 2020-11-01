<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatCharacter
 *
 * @author Jakub Konečný
 * @property int|string $id
 * @property string $name
 */
class ChatCharacter {
  use \Nette\SmartObject;
  
  /** @var int|string */
  protected $id;
  protected string $name;
  
  /**
   * @param int|string $id
   */
  public function __construct($id, string $name) {
    $this->id = $id;
    $this->name = $name;
  }
  
  /**
   * @return int|string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int|string $id
   */
  protected function setId($id): void {
    $this->id = $id;
  }

  public function getName(): string {
    return $this->name;
  }

  protected function setName(string $name): void {
    $this->name = $name;
  }
}
?>