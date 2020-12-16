<?php

namespace DigitalEquation\TeamworkDesk\Http\Controllers;

use DB;
use DigitalEquation\TeamworkDesk\Events\SupportTicketWebhookReceived;
use DigitalEquation\TeamworkDesk\Models\SupportTicket;
use Illuminate\Http\Request;
use JsonException;

class TeamworkDeskWebhookController
{
    protected string $secretToken;

    protected string $teamworkDeskUrl;

    /**
     * TeamworkDeskWebhookController constructor.
     */
    public function __construct()
    {
        $this->secretToken     = config('teamwork-desk.webhook_token');
        $this->teamworkDeskUrl = sprintf(
            'https://%s.teamwork.com/desk/#/tickets/',
            config('teamwork-desk.domain')
        );
    }

    /**
     * Send notification to user when ticket priority changes
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postPriority(Request $request): \Illuminate\Http\JsonResponse
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'content' => sprintf(
                'The priority for your support ticket with the id
                <strong class="red-500">%s</strong> was set to <strong class="ticket-priority %s">%s</strong>.',
                $data['id'],
                $data['priority']['name'],
                ucfirst($data['priority']['name']) ?: 'None'
            ),

            'action_text' => 'View',
            'action_url'  => sprintf('/%s/%s/%s',
                config('teamwork-desk.tickets_paths.list'),
                config('teamwork-desk.tickets_paths.details'),
                $data['id']
            ),
        ];

        return response()->json([
            'success' => $this->dispatchNotification($request, $data['id'], $content),
        ]);
    }

    /**
     * Send notification to user when ticket status changes
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'content' => sprintf(
                'The status for your support ticket with the id <strong class="red-500">%s</strong> is now set to <strong class="ticket %s">%s</strong>.',
                $data['id'],
                $data['status']['code'],
                $data['status']['name']
            ),

            'action_text' => 'View',
            'action_url'  => sprintf('/%s/%s/%s',
                config('teamwork-desk.tickets_paths.list'),
                config('teamwork-desk.tickets_paths.details'),
                $data['id']
            ),
        ];

        return response()->json([
            'success' => $this->dispatchNotification($request, $data['id'], $content),
        ]);
    }

    /**
     * Send notification to user when ticket receives a reply
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReply(Request $request): \Illuminate\Http\JsonResponse
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'content' => sprintf(
                'A new reply was added to your support ticket with the id <strong class="red-500">%s</strong>.',
                $data['ticket']['id']
            ),

            'action_text' => 'View',
            'action_url'  => sprintf('/%s/%s/%s',
                config('teamwork-desk.tickets_paths.list'),
                config('teamwork-desk.tickets_paths.details'),
                $data['ticket']['id']
            ),
        ];

        return response()->json([
            'success' => $this->dispatchNotification($request, $data['ticket']['id'], $content),
        ]);
    }

    /**
     * Send notification to user when ticket receives a new note
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postNote(Request $request): \Illuminate\Http\JsonResponse
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'content' => sprintf(
                'A new note was added to your support ticket with the id <strong class="red-500">%s</strong>.',
                $data['ticket']['id']
            ),

            'action_text' => 'View',
            'action_url'  => sprintf(
                '%s/%s/%s',
                $this->teamworkDeskUrl,
                $data['ticket']['id'],
                $data['thread']['id']
            ),
        ];

        return response()->json([
            'success' => $this->dispatchNotification($request, $data['ticket']['id'], $content),
        ]);
    }

    /**
     * Send notification to user when ticket is deleted
     *
     * @param Request $request
     *
     * @return mixed
     * @throws JsonException
     */
    public function postDelete(Request $request)
    {
        return DB::try(function () use ($request) {
            // Teamwork Desk Webhook data
            $data = $request->all();

            // Notification content
            $content = [
                'content' => sprintf(
                    'The ticket with the id <strong class="red-500">%s</strong> was deleted.',
                    $data['id']
                ),

                'action_text' => '',
                'action_url'  => '',
            ];

            $dispatch = $this->dispatchNotification($request, $data['id'], $content);

            if (!$dispatch && !SupportTicket::where('ticket_id', $data['id'])->delete()) {
                return response()->json(['success' => false]);
            }

            return response()->json(['success' => true]);
        });
    }

    /**
     * Build the user notification
     *
     * @param Request $request
     * @param int     $ticketID
     * @param array   $content
     *
     * @return bool
     */
    protected function dispatchNotification(Request $request, int $ticketID, array $content): bool
    {
        // Get request headers
        $apiSignature     = $request->header('x-desk-signature');
        $signatureMatches = $this->checkSignature($apiSignature);

        // If HMAC signatures does not match, abort
        if (!$signatureMatches) {
            l('Teamwork Desk Support Tickets:  Bad signature on webhook token!');
            abort(400);
        }

        // Get user ticket and the associated user
        $userTicket = SupportTicket::where('ticket_id', $ticketID)->with('user')->first();

        if (empty($userTicket)) {
            return false;
        }

        $notification = [
            'user'   => $userTicket->user,
            'ticket' => $content,
        ];

        event(new SupportTicketWebhookReceived($notification));

        return true;
    }

    /**
     * Check HMAC signatures
     *
     * @param string $apiSignature
     *
     * @return boolean
     */
    protected function checkSignature(string $apiSignature): bool
    {
        $body      = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $body, $this->secretToken, false);

        return $signature === $apiSignature;
    }
}
