<?php

namespace DigitalEquation\TeamworkDesk\Contracts\Repositories;

use DigitalEquation\TeamworkDesk\Services\TicketService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

interface TicketRepository
{
    public function __construct(TicketService $service);

    public function create(Authenticatable $user, array $data): array;

    public function update(Request $request): array;
}