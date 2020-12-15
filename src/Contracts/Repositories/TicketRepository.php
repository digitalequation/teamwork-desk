<?php

namespace DigitalEquation\TeamworkDesk\Contracts\Repositories;

use App\User;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

interface TicketRepository
{
    /**
     * TicketRepository constructor.
     *
     * @param TicketService $service
     */
    public function __construct(TicketService $service);

    /**
     * Create a new ticket.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     *
     * @return array
     */
    public function create(Authenticatable $user, array $data): array;

    /**
     * Upload a file to Teamwork Desk.
     *
     * @param Request $request
     *
     * @return array
     */
    public function update(Request $request): array;
}