<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ExampleChatControlFactory
 *
 * @author Jakub Konečný
 */
interface ExampleChatControlFactory
{
    public function create(): ExampleChatControl;
}
