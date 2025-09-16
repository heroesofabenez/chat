<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatCharacter
 *
 * @author Jakub Konečný
 */
class ChatCharacter
{
    use \Nette\SmartObject;

    /** @var int|string */
    public $id;

    /**
     * @param int|string $id
     */
    public function __construct($id, public string $name)
    {
        $this->id = $id;
    }

    /**
     * @return int|string
     * @deprecated Access the property directly
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @deprecated Access the property directly
     */
    protected function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @deprecated Access the property directly
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated Access the property directly
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }
}
