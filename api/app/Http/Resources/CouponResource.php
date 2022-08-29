<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->Id,
            'name' => $this->Name,
            'description' => $this->Description,
            'number' => $this->Number,
            'partnerId' => $this->PartnerId,
            'partnerName' => $this->PartnerName,
            'statusType' => $this->StatusType,
            'cardId' => $this->CardId,
            'isActive' => $this->IsActive,
            'logoUrl' => $this->LogoUrl,
            'actualStart' => $this->ActualStart,
            'actualEnd' => $this->ActualEnd,
        ];
    }
}
