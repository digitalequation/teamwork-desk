<?php

namespace DigitalEquation\TeamworkDesk\Repositories;

use App\User;
use DB;
use DigitalEquation\TeamworkDesk\Contracts\Repositories\TicketRepository as TicketRepositoryContract;
use DigitalEquation\TeamworkDesk\Http\Requests\TicketRequest;
use DigitalEquation\TeamworkDesk\Models\SupportTicket;
use DigitalEquation\TeamworkDesk\Services\Tickets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class TicketRepository implements TicketRepositoryContract
{
    protected Tickets $tickets;

    /**
     * @inheritDoc
     */
    public function __construct(Tickets $tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @inheritDoc
     */
    public function create(User $user, TicketRequest $data): array
    {
        return DB::try(function () use ($user, $data) {
            $payload = [
                'assignedTo'          => $this->tickets->me()['user']['id'],
                'inboxId'             => $this->tickets->inbox(config('teamwork-desk.inbox'))['id'],
                'tags'                => 'Ticket',
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

            $response = $this->tickets->post($payload);

            if (isset($response['errors'])) {
                throw new RuntimeException('Something went wrong, please try again later!');
            }

            // Save user associated ticket ID
            $ticket = SupportTicket::create([
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
        $customerId = Auth::user()->customer_support_id;

        $files       = [];
        $attachments = [];
        foreach ($request->file('files') as $file) {
            $response      = $this->tickets->upload($customerId, $file);
            $attachments[] = $response['id'];
            $files[]       = $response;
        }

        return [
            'ids'   => $attachments,
            'files' => $files,
        ];
    }
}