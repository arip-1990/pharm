<?php

namespace App\Services\Sms;

interface SmsSender
{
    public function send(string $number, string $text): void;
}
