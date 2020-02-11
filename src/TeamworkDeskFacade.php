<?php

namespace DigitalEquation\TeamworkDesk;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalEquation\TeamworkDesk\TeamworkDesk
 */
class TeamworkDeskFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'teamwork-desk';
    }
}
