<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BonusResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['Id'],
            'operationType' => $this['OperationType'],
            'debet' => $this['Debet'],
            'credit' => $this['Credit'],
            'remainder' => $this['Remainder'],
            'cardId' => $this['CardId'],
            'cardNumber' => $this['CardNumber'],
            'chequeId' => $this['ChequeId'],
            'chequeNumber' => $this['ChequeNumber'],
            'partnerId' => $this['PartnerId'],
            'partnerName' => $this['PartnerName'],
            'campaignId' => $this['CampaignId'],
            'campaignName' => $this['CampaignName'],
            'ruleId' => $this['RuleId'],
            'ruleName' => $this['RuleName'],
            'chequeItemId' => $this['ChequeItemId'],
            'chequeItemProductName' => $this['ChequeItemProductName'],
            'chequeItemParentId' => $this['ChequeItemParentId'],
            'chequeItemParentNumber' => $this['ChequeItemParentNumber'],
            'parentType' => $this['ParentType'],
            'parentName' => $this['ParentName'],
            'createdDate' => $this['CreatedDate'],
            'actualStart' => $this['ActualStart'],
            'actualEnd' => $this['ActualEnd'],
        ];
    }
}
