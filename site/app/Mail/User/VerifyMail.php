<?php

namespace App\Mail\User;

use App\Entities\User;
use Illuminate\Mail\Mailable;

class VerifyMail extends Mailable
{
    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject('Сброс пароля на сайте ' . env('APP_NAME'))
            ->view('user.register.verify_html')
            ->text('user.register.verify_text');
    }
}
