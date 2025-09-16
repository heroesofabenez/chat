<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * IExampleChatControlFactory
 *
 * @author Jakub Konečný
 */
interface IExampleChatControlFactory
{
    public function create(): ExampleChatControl;
}
