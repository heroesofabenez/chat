<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

if (false) {
    /** @deprecated use ChatMessageProcessor */
    interface IChatMessageProcessor extends ChatMessageProcessor
    {
    }

} else {
    class_alias(ChatMessageProcessor::class, IChatMessageProcessor::class);
}
