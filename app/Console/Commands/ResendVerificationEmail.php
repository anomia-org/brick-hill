<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use App\Models\User\Email\Email;

class ResendVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:resendverification {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resends the latest verification email to an account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = Email::userId($this->argument('user'))->unverified()->firstOrFail();

        if (strlen($email->verify_code) == 0) {
            $this->error('Email has no verify code');
            return Command::FAILURE;
        }

        Mail::to($email->send_email)->send(new \App\Mail\VerificationMail($this->argument('user')));

        $email->last_resend = Carbon::now();
        $email->save();

        $this->info('Email sent');
        return Command::SUCCESS;
    }
}
