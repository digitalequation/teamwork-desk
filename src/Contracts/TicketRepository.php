<?php

namespace Digitalequation\TeamworkDesk\Contracts;

use Digitalequation\TeamworkDesk\Http\Requests\TicketRequest;
use Illuminate\Http\Request;
use Illumintate\Contracts\Auth\Authenticatable;

interface TicketRepository
{
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