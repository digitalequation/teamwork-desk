<?php

namespace DigitalEquation\TeamworkDesk\Events;

class SupportTicketCreated
{
    public array $ticket;

    public function __construct(array $ticket)
    {
        $this->ticket = $ticket;
    }
}
