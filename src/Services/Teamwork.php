<?php

namespace DigitalEquation\TeamworkDesk\Services;

class Teamwork
{
    public static function desk(): Desk
    {
        return new Desk();
    }

    public static function helpDocs(): HelpDocs
    {
        return new HelpDocs();
    }

    public static function tickets(): Tickets
    {
        return new Tickets();
    }
}