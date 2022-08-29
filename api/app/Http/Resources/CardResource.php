<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->Id,
            'number' => $this->Number,
            'bonusType' => $this->BonusType,
            'cardType' => $this->CardType,
            'statusDate' => $this->StatusDate,
            'expiryDate' => $this->ExpiryDate,
            'statusCode' => $this->StatusCode,
            'techType' => $this->TechType,
            'collaborationType' => $this->CollaborationType,
            'balance' => $this->Balance,
            'activeBalance' => $this->ActiveBalance,
            'debet' => $this->Debet,
            'credit' => $this->Credit,
            'summ' => $this->Summ,
            'summDiscounted' => $this->SummDiscounted,
            'discount' => $this->Discount,
            'discountSumm' => $this->DiscountSumm,
            'quantity' => $this->Quantity,
            'partnerId' => $this->PartnerId,
            'partnerName' => $this->PartnerName,
            'orgUnitId' => $this->OrgUnitId,
            'orgUnitName' => $this->OrgUnitName,
        ];
    }
}
