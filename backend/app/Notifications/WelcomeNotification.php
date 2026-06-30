<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the notification may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the notification.
     */
    public int $backoff = 60;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
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
        $loginUrl = config('app.frontend_url', config('app.url')) . '/login';

        return (new MailMessage())
            ->subject('Welcome to Vertex-Admin!')
            ->greeting('Welcome to Vertex-Admin!')
            ->line('Hello, ' . $notifiable->name . '!')
            ->line('Your account has been successfully created. You now have access to the Vertex-Admin panel.')
            ->line('You can log in at any time using the button below.')
            ->action('Login to Vertex-Admin', $loginUrl)
            ->line('If you did not create this account or believe this was a mistake, please contact the system administrator immediately.')
            ->salutation('Best regards, The Vertex-Admin Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'welcome',
            'user_id' => $notifiable->id,
            'name'    => $notifiable->name,
            'email'   => $notifiable->email,
        ];
    }
}
