<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $this */
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'middleName' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthDate' => $this->birth_date,
            'emailVerified' => !!$this->email_verified_at,
            'phoneVerified' => !!$this->phone_verified_at,
        ];
    }
}
