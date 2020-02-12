<?php

namespace DigitalEquation\TeamworkDesk\Contracts\Repositories;

use App\User;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Services\Tickets;
use Illuminate\Http\Request;

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
     * @param User $user
     * @param TicketRequest $data
     *
     * @return array
     */
    public function create(User $user, TicketRequest $data): array;

    /**
     * Upload a file to Teamwork Desk.
     *
     * @param Request $request
     *
     * @return array
     */
    public function update(Request $request): array;
}