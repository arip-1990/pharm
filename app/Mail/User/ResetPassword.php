<?php

namespace App\Mail\User;

use App\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject('Сброс пароля на сайте ' . env('APP_NAME'))
            ->view('user.reset.confirm_html')
            ->text('user.reset.confirm_text');
    }
}
