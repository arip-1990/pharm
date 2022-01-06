<?php

namespace App\Entities;

class Customer
{
    public function __construct(public string $name, public string $phone, public string $email) {}
}
