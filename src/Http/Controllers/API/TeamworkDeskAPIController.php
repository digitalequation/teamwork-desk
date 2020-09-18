<?php

namespace DigitalEquation\TeamworkDesk\Http\Controllers\API;

use App\Http\Controllers\Controller;
use DigitalEquation\TeamworkDesk\Contracts\Repositories\TicketRepository;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketReplyRequest;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Notifications\SupportTicket as SupportTicketNotification;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeamworkDeskAPIController extends Controller
{
    protected TicketRepository $ticket;

    protected TicketService $service;

    public function __construct(TicketRepository $ticket, TicketService $service)
    {
        $this->middleware(config('teamwork-desk.authorization'))->only([
            'getIndex',
            'postIndex',
            'putIndex',
            'deleteIndex',
            'postUpload',
        ]);

        $this->ticket  = $ticket;
        $this->service = $service;
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

        $priorities = $this->service->priorities();
        $tickets    = !empty($customerId) ? $this->service->customer($customerId) : [];

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
        return success(['ticket' => $this->service->ticket($id)['ticket']]);
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
            $user = Auth::user();

            $ticket = $this->ticket->create($user, $request);

            $user->notify(new SupportTicketNotification($ticket));

            return success(['teamwork' => $ticket]);
        } catch (Exception $e) {
            return error($e->getMessage());
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
            'ticket' => $this->service->reply($request->all()),
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
            return error('No files selected for upload...');
        }

        $files       = [];
        $attachments = [];
        foreach ($request->file('files') as $file) {
            $response      = $this->service->upload(Auth::user()->customer_support_id, $file);
            $attachments[] = $response['id'];
            $files[]       = $response;
        }

        return success([
            'attachments' => [
                'ids'   => $attachments,
                'files' => $files,
            ],
        ]);
    }
}