<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class AfterRegister extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $register;
    public function __construct(User $user)
    {
        $this->register = $user;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   return $this->subject('Validation de compte')->view('users.register');

    }
}
