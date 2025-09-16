<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

final class Test2Command extends BaseChatCommand
{
    public const NAME = "test2";

    public function execute(): string
    {
        $args = func_get_args();
        return "test" . implode("", $args);
    }
}
