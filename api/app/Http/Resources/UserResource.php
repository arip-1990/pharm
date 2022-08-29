<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->Id,
            'firstName' => $this->FirstName,
            'lastName' => $this->LastName,
            'middleName' => $this->MiddleName,
            'email' => $this->EmailAddress,
            'phone' => $this->MobilePhone,
            'gender' => $this->GenderCode,
            'birthDate' => Carbon::parse($this->BirthDate),
            'emailVerified' => $this->EmailVerified,
            'phoneVerified' => $this->MobilePhoneVerified,
            'allowEmail' => $this->AllowEmail,
            'allowSms' => $this->AllowSms,
            'balance' => $this->Balance,
            'activeBalance' => $this->ActiveBalance,
            'debet' => $this->Debet,
            'credit' => $this->Credit,
            'summ' => $this->Summ,
            'summDiscounted' => $this->SummDiscounted,
            'discountSumm' => $this->DiscountSumm,
            'quantity' => $this->Quantity,
            'orgUnitName' => $this->OrgUnitName,
            'preferredOrgUnitName' => $this->PreferredOrgUnitName,
            'registrationDate' => $this->RegistrationDate,
        ];
    }
}
