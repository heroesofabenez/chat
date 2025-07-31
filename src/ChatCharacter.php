<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatCharacter
 *
 * @author Jakub Konečný
 */
class ChatCharacter {
  use \Nette\SmartObject;
  
  /** @var int|string */
  public $id;
  
  /**
   * @param int|string $id
   */
  public function __construct($id, public string $name) {
    $this->id = $id;
  }
  
  /**
   * @deprecated Access the property directly
   * @return int|string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @deprecated Access the property directly
   * @param int|string $id
   */
  protected function setId($id): void {
    $this->id = $id;
  }

  /**
   * @deprecated Access the property directly
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @deprecated Access the property directly
   */
  protected function setName(string $name): void {
    $this->name = $name;
  }
}
?>