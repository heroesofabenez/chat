<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

/**
 * FakeFactory
 *
 * @author Jakub Konečný
 */
interface FakeFactory
{
    public function create(): \stdClass;
}
