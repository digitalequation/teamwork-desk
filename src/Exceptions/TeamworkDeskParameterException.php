<?php

namespace DigitalEquation\TeamworkDesk\Exceptions;

class TeamworkDeskParameterException extends \RuntimeException
{
    public function __construct(string $message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
