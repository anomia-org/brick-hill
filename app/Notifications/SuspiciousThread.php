<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Awssat\Notifications\Messages\DiscordMessage;

use App\Models\Forum\ForumThread;

class SuspiciousThread extends Notification
{
    use Queueable;

    /**
     * ForumThread being notified
     * @var \App\Models\Forum\ForumThread
     */
    protected ForumThread $thread;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ForumThread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable)
    {
        return (new DiscordMessage)
            ->from('Laravel')
            ->embed(function ($embed) {
                $embed->title('Suspicious Thread')->description("[{$this->thread->title}](https://brick-hill.com/forum/thread/{$this->thread->id})")
                    ->field('Author', "[{$this->thread->author->username}](https://brick-hill.com/user/{$this->thread->author_id})", true)
                    ->field('Body', substr($this->thread->body, 0, 100), true);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
