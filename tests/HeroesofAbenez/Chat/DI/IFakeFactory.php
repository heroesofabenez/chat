<?php
declare(strict_types=1);

namespace HeroesofAbenez\Chat\DI;

/**
 * IFakeFactory
 *
 * @author Jakub Konečný
 */
interface IFakeFactory
{
    public function create(): \stdClass;
}
