<?php

namespace Digitalequation\TeamworkDesk\Http\API;

use Digitalequation\TeamworkDesk\Contracts\TicketRepository;
use Digitalequation\TeamworkDesk\Http\Requests\TicketReplyRequest;
use Digitalequation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Notifications\SupportTicket as SupportTicketNotification;
use DigitalEquation\TeamworkDesk\Services\Tickets;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeamworkDeskAPIController
{
    protected TicketRepository $ticket;

    public function __construct(TicketRepository $ticket)
    {
        $this->middleware('role:user')->only([
            'getIndex',
            'postIndex',
            'putIndex',
            'deleteIndex',
            'postUpload',
        ]);

        $this->ticket = $ticket;
    }

    /**
     * Return all tickets for the current user.
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function getIndex()
    {
        $customerId = Auth::user()->customer_support_id;

        $priorities = Tickets::tickets()->priorities();
        $tickets    = !empty($customerId) ? Tickets::tickets()->customer($customerId) : [];

        return success([
            'priorities' => $priorities,
            'tickets'    => !empty($tickets['tickets']) && is_array($tickets['tickets']) ? array_reverse($tickets['tickets']) : [],
        ]);
    }

    /**
     * Get a single ticket by id.
     *
     * @param int $id
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function getTicket($id)
    {
        return success(['ticket' => Tickets::tickets()->ticket($id)['ticket']]);
    }

    /**
     * Create a new ticket.
     *
     * @param TicketRequest $request
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function postIndex(TicketRequest $request)
    {
        try {
            $user = auth()->user();

            $ticket = $this->ticket->create($user, $request);

            $user->notify(new SupportTicketNotification($ticket));

            return success(['teamwork' => $ticket]);
        } catch (Exception $e) {
            return error($e->getMessage(), 422);
        }
    }

    /**
     * Post a reply to a ticket.
     *
     * @param TicketReplyRequest $request
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function postReply(TicketReplyRequest $request)
    {
        return success([
            'ticket' => Tickets::tickets()->reply($request->all()),
        ]);
    }

    /**
     * Upload a file to Teamwork.
     *
     * @param Request $request
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function postUpload(Request $request)
    {
        if (!$request->hasFile('files')) {
            return error('No files selected for upload...', 422);
        }

        return success([
            'attachments' => $this->ticket->upload($request),
        ]);
    }
}