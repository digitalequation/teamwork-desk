<?php

namespace DigitalEquation\TeamworkDesk\Http\Controllers\API;

use DigitalEquation\TeamworkDesk\Contracts\Repositories\TicketRepository;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketReplyRequest;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Notifications\SupportTicket;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamworkDeskAPIController
{
    protected TicketRepository $ticket;

    protected TicketService $service;

    public function __construct(TicketRepository $ticket, TicketService $service)
    {
        $this->ticket  = $ticket;
        $this->service = $service;
    }

    /**
     * Return a list of tickets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex(): \Illuminate\Http\JsonResponse
    {
        $customerId = Auth::user()->customer_support_id;

        $priorities = $this->service->priorities();
        $tickets    = !empty($customerId) ? $this->service->customer($customerId) : [];

        return response()->json([
            'success'    => true,
            'priorities' => $priorities,
            'tickets'    => !empty($tickets['tickets']) && is_array($tickets['tickets']) ?
                array_reverse($tickets['tickets']) : [],
        ]);
    }

    /**
     * Return single ticket.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicket(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'ticket'  => $this->service->ticket($id)['ticket']]);
    }

    /**
     * Create a new ticket.
     *
     * @param TicketRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(TicketRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = Auth::user();

            $ticket = $this->ticket->create($user, $request->all());

            $user->notify(new SupportTicket($ticket));

            return response()->json([
                'success'  => true,
                'teamwork' => $ticket,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Post a reply to a ticket.
     *
     * @param TicketReplyRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function postReply(TicketReplyRequest $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'ticket'  => $this->service->reply($request->all()),
        ]);
    }

    /**
     * Upload a file to Teamwork.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpload(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->hasFile('files')) {
            return response()->json([
                'success' => false,
                'message' => 'No files selected for upload...',
            ]);
        }

        $files       = [];
        $attachments = [];
        foreach ($request->file('files') as $file) {
            $response      = $this->service->upload(Auth::user()->customer_support_id, $file);
            $attachments[] = $response['id'];
            $files[]       = $response;
        }

        return response()->json([
            'success'     => true,
            'attachments' => [
                'ids'   => $attachments,
                'files' => $files,
            ],
        ]);
    }
}