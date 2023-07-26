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

class VerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->user = User::findOrFail($userId);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Brick Hill - Verify Email')
            ->view('mail.verification')->with([
                'user' => $this->user,
                'email' => Email::where([['user_id', $this->user->id], ['verified', 0]])->orderBy('updated_at', 'DESC')->firstOrFail()
            ]);
    }
}
