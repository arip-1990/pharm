<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChequeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['Id'],
            'number' => $this['Number'],
            'date' => $this['Date'],
            'partnerId' => $this['PartnerId'],
            'partnerName' => $this['PartnerName'],
            'orgUnitId' => $this['OrgUnitId'],
            'orgUnitName' => $this['OrgUnitName'],
            'orgUnitFullName' => $this['OrgUnitFullName'],
            'orgUnitAddress' => $this['OrgUnitAddress'],
            'operationTypeName' => $this['OperationTypeName'],
            'chequeItemCount' => $this['ChequeItemCount'],
            'operationTypeCode' => $this['OperationTypeCode'],
            'summ' => $this['Summ'],
            'bonus' => $this['Bonus'],
            'paidByBonus' => $this['PaidByBonus'],
            'paidByMoney' => $this['PaidByMoney'] ?? null,
            'cardId' => $this['CardId'],
            'cardNumber' => $this['CardNumber'],
            'discount' => $this['Discount'],
            'summDiscounted' => $this['SummDiscounted'],
            'score' => $this['Score'],
            'lowerBound' => $this['LowerBound'],
            'upperBound' => $this['UpperBound'],
        ];
    }
}
