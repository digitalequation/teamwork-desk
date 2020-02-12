<?php

namespace Digitalequation\TeamworkDesk\Contracts;

use Digitalequation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Services\Tickets;
use Illuminate\Http\Request;
use Illumintate\Contracts\Auth\Authenticatable;

interface TicketRepository
{
    /**
     * TicketRepository constructor.
     * @param Tickets $tickets
     */
    public function __construct(Tickets $tickets);

    /**
     * Create a new ticket.
     *
     * @param Authenticatable $user
     * @param TicketRequest $data
     *
     * @return array
     */
    public function create(Authenticatable $user, TicketRequest $data): array;

    /**
     * Upload a file to Teamwork Desk.
     *
     * @param Request $request
     *
     * @return array
     */
    public function update(Request $request): array;
}