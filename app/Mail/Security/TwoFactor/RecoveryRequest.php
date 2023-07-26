<?php

namespace App\Mail\Security\TwoFactor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User\User;

class RecoveryRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private User $user, private string $recoveryCode)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Brick Hill - Two Factor Authentication Recovery')
            ->view('mail.security.twofactor.recoveryrequest')->with([
                'user' => $this->user,
                'recoveryCode' => $this->recoveryCode
            ]);
    }
}
