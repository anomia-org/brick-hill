<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CheckVerifiedEmail
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if(Auth::check()) {
            $email = Auth::user()->email()->whereNull('revert_code')->first();
            
            if($email && $email->verified == 0) {
                if(is_null($email->last_resend))
                    $view->with('email_sent', true);
                else
                    $view->with('email_verified', $email->email);
            } else if(!$email) {
                $view->with('needs_email', true);
            }
        }
    }
}