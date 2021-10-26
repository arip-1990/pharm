<?php

namespace App\Services\Sms;

class ArraySender implements SmsSender
{
    private array $messages = [];

    public function send(string $number, string $text): void
    {
        $this->messages[] = [
            'to' => '+' . trim($number, '+'),
            'text' => $text
        ];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
