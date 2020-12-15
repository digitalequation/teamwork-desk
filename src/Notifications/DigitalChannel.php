<?php

namespace DigitalEquation\TeamworkDesk\Notifications;

use DigitalEquation\TeamworkDesk\Repositories\TicketRepository;
use Illuminate\Notifications\Notification;

class DigitalChannel
{
    private TicketRepository $notification;

    public function __construct(TicketRepository $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        if (method_exists($notifiable, 'routeNotificationForDigitalApp')) {
            $notifiable = $notifiable->routeNotificationForDigitalApp() ?: $notifiable;
        }

        $data = $this->getData($notifiable, $notification);

        $this->notification->create($notifiable, $data);
    }

    /**
     * Get the data for the notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @return array
     */
    protected function getData($notifiable, Notification $notification): array
    {
        $message = $notification->toDigitalApp($notifiable);

        return [
            'icon'        => $message->icon,
            'body'        => $message->body,
            'from'        => $message->from,
            'action_text' => $message->actionText,
            'action_url'  => $message->actionUrl,
        ];
    }
}
