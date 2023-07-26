<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\User\{
    User,
    Email\Email
};

class VerifyEmailChange extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $user;
    private $currentEmail;
    private $newEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userId, $current_email, $new_email)
    {
        $this->user = User::find($userId);
        $this->currentEmail = $current_email;
        $this->newEmail = $new_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Brick Hill - Email Change')
            ->view('mail.verifychange')->with([
                'user' => $this->user,
                'current_email' => $this->currentEmail,
                'new_email' => $this->newEmail
            ]);
    }
}
