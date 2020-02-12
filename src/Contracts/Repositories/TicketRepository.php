<?php

namespace DigitalEquation\TeamworkDesk\Contracts\Repositories;

use App\User;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use Illuminate\Http\Request;

interface TicketRepository
{
    /**
     * TicketRepository constructor.
     * @param TicketService $service
     */
    public function __construct(TicketService $service);

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