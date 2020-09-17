<?php

namespace DigitalEquation\TeamworkDesk\Http\Controllers;

use DigitalEquation\TeamworkDesk\Models\SupportTicket;
use DigitalEquation\TeamworkDesk\Notifications\SupportTicket as SupportTicketNotification;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use JsonException;

class TeamworkDeskWebhookController
{
    /**
     * @var Repository|mixed
     */
    protected string $secretToken;

    /**
     * @var string
     */
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
     * @return Response
     */
    public function postPriority(Request $request): Response
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'body' => sprintf(
                'The priority for your support ticket with the id
                <strong class="red-500">%s</strong> was set to <strong class="ticket-priority %s">%s</strong>.',
                $data['id'],
                $data['priority']['name'],
                ucfirst($data['priority']['name']) ?? 'None'
            ),

            'action_text' => 'View',
            'action_url'  => sprintf('/%s/%s/%s',
                config('teamwork-desk.tickets_paths.list'),
                config('teamwork-desk.tickets_paths.details'),
                $data['id']
            ),
        ];

        return $this->buildNotification($request, $data['id'], $content);
    }

    /**
     * Send notification to user when ticket status changes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postStatus(Request $request): Response
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'body' => sprintf(
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

        return $this->buildNotification($request, $data['id'], $content);
    }

    /**
     * Send notification to user when ticket receives a reply
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postReply(Request $request): Response
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'body' => sprintf(
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

        return $this->buildNotification($request, $data['ticket']['id'], $content);
    }

    /**
     * Send notification to user when ticket receives a new note
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postNote(Request $request): Response
    {
        // Teamwork Desk Webhook data
        $data = $request->all();

        // Notification content
        $content = [
            'body' => sprintf(
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

        return $this->buildNotification($request, $data['ticket']['id'], $content);
    }

    /**
     * Send notification to user when ticket is deleted
     *
     * @param Request $request
     *
     * @return Response
     * @throws JsonException
     */
    public function postDelete(Request $request): Response
    {
        return DB::try(function () use ($request) {
            // Teamwork Desk Webhook data
            $data = $request->all();

            // Notification content
            $content = [
                'body' => sprintf(
                    'The ticket with the id <strong class="red-500">%s</strong> was deleted.',
                    $data['id']
                ),

                'action_text' => '',
                'action_url'  => '',
            ];

            $notification = $this->buildNotification($request, $data['id'], $content);

            if (json_decode($notification->getContent(), false, 512, JSON_THROW_ON_ERROR)->success) {
                SupportTicket::where('ticket_id', $data['id'])->delete();

                return success();
            }

            return error();
        });
    }

    /**
     * Build the user notification
     *
     * @param Request $request
     * @param integer $ticketID
     * @param array $content
     *
     * @return Response
     */
    protected function buildNotification($request, int $ticketID, $content): Response
    {
        // Get request headers
        $apiSignature     = $request->header('x-desk-signature');
        $signatureMatches = $this->checkSignature($apiSignature);

        // If HMAC signatures does not match, abort
        if (!$signatureMatches) {
            l('Bad signature!');
            abort(400);
        }

        // Get user ticket and the associated user
        $userTicket = SupportTicket::where('ticket_id', $ticketID)->with('user')->first();

        if (empty($userTicket)) {
            return error('Resend status...');
        }

        Notification::send($userTicket->user, new SupportTicketNotification($content));

        return success();
    }

    /**
     * Check HMAC signatures
     *
     * @param string $apiSignature
     *
     * @return boolean
     */
    protected function checkSignature($apiSignature): bool
    {
        $body      = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $body, $this->secretToken, false);

        return $signature === $apiSignature;
    }
}
