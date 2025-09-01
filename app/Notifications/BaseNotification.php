<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting($this->getGreeting())
            ->line($this->getContent())
            ->action($this->getActionText(), $this->getActionUrl())
            ->line($this->getFooter());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => get_class($this),
            'data' => $this->getData(),
            'created_at' => now(),
        ];
    }

    /**
     * Get the notification subject.
     */
    abstract protected function getSubject(): string;

    /**
     * Get the notification greeting.
     */
    protected function getGreeting(): string
    {
        return 'Hola!';
    }

    /**
     * Get the notification content.
     */
    abstract protected function getContent(): string;

    /**
     * Get the action text.
     */
    protected function getActionText(): string
    {
        return 'Ver más';
    }

    /**
     * Get the action URL.
     */
    protected function getActionUrl(): string
    {
        return url('/');
    }

    /**
     * Get the notification footer.
     */
    protected function getFooter(): string
    {
        return 'Gracias por usar nuestra plataforma.';
    }

    /**
     * Get additional data for the notification.
     */
    protected function getData(): array
    {
        return [];
    }
}
