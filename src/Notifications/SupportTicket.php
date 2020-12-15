<?php

namespace DigitalEquation\TeamworkDesk\Notifications;

use DigitalEquation\TeamworkDesk\Contracts\Repositories\TicketRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicket extends Notification
{
    use Queueable;

    protected TicketRepository $ticket;


    public function __construct(TicketRepository $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->ticket['body'])
            ->action($this->ticket['action_text'], $this->ticket['action_url'])
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'icon'        => 'fa-info',
            'body'        => $this->ticket['body'],
            'from'        => $notifiable,
            'action_text' => $this->ticket['action_text'],
            'action_url'  => $this->ticket['action_url'],
        ];
    }
}
