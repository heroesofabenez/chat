<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ExampleChat
 *
 * @author Jakub Konečný
 */
final class ExampleChatControl extends ChatControl
{
    public function __construct(DatabaseAdapter $databaseAdapter)
    {
        parent::__construct($databaseAdapter, "example", 1);
    }
}
