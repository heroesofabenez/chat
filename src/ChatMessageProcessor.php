<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat;

/**
 * ChatMessageProcessor
 *
 * @author Jakub Konečný
 */
interface ChatMessageProcessor
{
    /**
     * @return null|string The result/null if the processor is not applicable
     */
    public function parse(string $message): ?string;
}
