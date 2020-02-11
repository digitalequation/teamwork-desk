<?php

namespace DigitalEquation\TeamworkDesk;

class TeamworkDesk
{
    /**
     * Indicated if Teamwork Desk migrations will be run.
     */
    public static bool $runsMigrations = true;

    /**
     * Configure Teamwork Desk to not register its migrations.
     */
    public static function ignoreMigrations(): TeamworkDesk
    {
        static::$runsMigrations = false;

        return new static;
    }
}
