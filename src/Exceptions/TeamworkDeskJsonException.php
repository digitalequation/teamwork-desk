<?php

namespace DigitalEquation\TeamworkDesk\Exceptions;

class TeamworkDeskJsonException extends \RuntimeException
{
    public function __construct(string $message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
