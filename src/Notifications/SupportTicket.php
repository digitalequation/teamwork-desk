<?php

namespace DigitalEquation\TeamworkDesk\Notifications;

use App\Notifications\{DigitalNotification, DigitalChannel};
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicket extends Notification
{
    use Queueable;

    protected $content;

    /**
     * Create a new notification instance.
     *
     * @param $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return [DigitalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->content['body'])
            ->action($this->content['action_text'], $this->content['action_url'])
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Get the Digital representation of the notification
     *
     * @param mixed $notifiable
     *
     * @return DigitalNotification
     */
    public function toDigitalApp($notifiable): DigitalNotification
    {
        return (new DigitalNotification)
            ->icon('fa-info')
            ->action($this->content['action_text'], $this->content['action_url'])
            ->body($this->content['body']);
    }
}
