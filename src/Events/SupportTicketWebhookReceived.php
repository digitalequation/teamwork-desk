<?php

namespace DigitalEquation\TeamworkDesk\Events;

class SupportTicketWebhookReceived
{
    public array $ticket;

    public function __construct(array $ticket)
    {
        $this->ticket = $ticket;
    }
}