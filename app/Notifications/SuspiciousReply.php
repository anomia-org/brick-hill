<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Awssat\Notifications\Messages\DiscordMessage;

use App\Models\Forum\ForumPost;

class SuspiciousReply extends Notification
{
    use Queueable;

    /**
     * ForumPost being notified
     * 
     * @var \App\Models\Forum\ForumPost
     */
    protected ForumPost $reply;

    /**
     * Page that the ForumPost is on
     * @var int
     */
    protected int $pageCount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ForumPost $reply, $pageCount)
    {
        $this->reply = $reply;
        $this->pageCount = $pageCount;
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
                $embed->title('Suspicious Reply')->description("[{$this->reply->thread->title}](https://brick-hill.com/forum/thread/{$this->reply->thread->id}/{$this->pageCount})")
                    ->field('Author', "[{$this->reply->author->username}](https://brick-hill.com/user/{$this->reply->author_id})", true)
                    ->field('Body', substr($this->reply->body, 0, 100), true);
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
