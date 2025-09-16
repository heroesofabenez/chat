<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

if (false) {
    /** @deprecated use BaseChatCommand */
    abstract class ChatCommand extends BaseChatCommand
    {
    }

} else {
    class_alias(BaseChatCommand::class, ChatCommand::class);
}
