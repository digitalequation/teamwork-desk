<?php

namespace Digitalequation\TeamworkDesk\Repositories;

use Digitalequation\TeamworkDesk\Contracts\TicketRepository as TicketRepositoryContract;
use Digitalequation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Models\SupportTicket;
use DigitalEquation\TeamworkDesk\Services\Teamwork;
use Illuminate\Http\Request;
use Illumintate\Contracts\Auth\Authenticatable;
use RuntimeException;

class TicketRepository implements TicketRepositoryContract
{
    /**
     * @inheritDoc
     */
    public function create(Authenticatable $user, TicketRequest $data): array
    {
        return DB::try(function () use ($user, $data) {
            $payload = [
                'assignedTo'          => Teamwork::desk()->me()['user']['id'],
                'inboxId'             => Teamwork::desk()->inbox('Decker (TEST)')['id'],
                'tags'                => 'NewsWire ticket',
                'priority'            => $data->priority ?? 'low',
                'status'              => 'active',
                'source'              => 'Email (Manual)',
                'customerFirstName'   => $user->first_name,
                'customerLastName'    => $user->last_name,
                'customerEmail'       => $user->email,
                'customerPhoneNumber' => $user->phone,
                'subject'             => $data->subject,
                'previewTest'         => $data->subject,
                'message'             => $data->message,
            ];

            $response = Teamwork::tickets()->post($payload);

            if (isset($response['errors'])) {
                throw new RuntimeException('Something went wrong, please try again later!');
            }

            // Save user associated ticket ID
            $ticket = new SupportTicket([
                'user_id'          => $user->id,
                'ticket_id'        => $response['id'],
                'event_creator_id' => $response['ticket']['CreatedBy']['Int64'],
            ]);

            $ticket->save();

            // Save customer ID to user
            $user->customer_support_id = $response['ticket']['CustomerID'];

            $user->save();

            return [
                'body' => sprintf(
                    'Your message for the support team with the subject <strong class="blue-500">%s</strong>
                            was registered with the number <span class="red-500">%d</span>.',
                    $response['ticket']['Subject'],
                    $response['ticket']['ID']
                ),

                'action_text' => 'View',
                'action_url'  => $response['id'],
            ];
        });
    }

    /**
     * @inheritDoc
     */
    public function update(Request $request): array
    {
        $customerId = Auth::user()->customerSupportId();

        $files       = [];
        $attachments = [];
        foreach ($request->file('files') as $file) {
            $response      = Teamwork::desk()->upload($customerId, $file);
            $attachments[] = $response['id'];
            $files[]       = $response;
        }

        return [
            'ids'   => $attachments,
            'files' => $files,
        ];
    }
}