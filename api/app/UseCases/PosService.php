<?php

namespace App\UseCases;

use App\Models\User;
use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;

class PosService extends LoyaltyService
{
    public function __construct() {
        parent::__construct(true);
    }

    public function getBalance(string $phone, bool $sendCode = false, string $validationCode = null): array
    {
        $date = Carbon::now();
        $data = '<BalanceRequest>
            <RequestID>' . $date->timestamp . '</RequestID>
            <DateTime>' . $date->format('Y-m-d\TH:i:sP') . '</DateTime>
            <Organization>' . $this->config['organization'] . '</Organization>
            <BusinessUnit>' . $this->config['business_unit'] . '</BusinessUnit>
            <POS>' . $this->config['pos'] . '</POS>
            <MobilePhone>
            <Number>' . $phone . '</Number>';

        if ($validationCode) {
            $data .= '<ValidationCode>' . $validationCode . '</ValidationCode>';
        }
        else $data .= '<SendCode>' . ($sendCode ? 1 : 0) . '</SendCode>';

        $data .= '</MobilePhone></BalanceRequest>';

        $response = $this->client->post($this->urls['pos'], ['body' => $this->buildXml($data)]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        $data = $xml->children('soap', true)->Body->children()->ProcessRequestResponse->ProcessRequestResult->BalanceResponse;
        if (0 !== (int)$data->ReturnCode) {
            if ($validationCode)
                throw new \DomainException('Проверочный код не корректный', (int)$data->ReturnCode);

//            throw new \DomainException((string)$data->Message, (int)$data->ReturnCode);
        }

        if (!isset($data->ContactID)) return (array)$data;

        return [
            "contactID" => (string)$data->ContactID,
            'cardNumber' => (string)$data->Card->CardNumber,
            "cardBalance" => (float)$data->CardBalance,
            "cardNormalBalance" => (float)$data->CardNormalBalance,
            "cardStatusBalance" => (float)$data->CardStatusBalance,
            "cardActiveBalance" => (float)$data->CardActiveBalance,
            "cardNormalActiveBalance" => (float)$data->CardNormalActiveBalance,
            "cardStatusActiveBalance" => (float)$data->CardStatusActiveBalance,
            "cardSumm" => (float)$data->CardSumm,
            "cardSummDiscounted" => (float)$data->CardSummDiscounted,
            "cardDiscount" => (float)$data->CardDiscount,
            "cardQuantity" => (int)$data->CardQuantity,
            "contactPresence" => (int)$data->ContactPresence,
            "cardType" => $data->CardType,
            "cardStatus" => (int)$data->CardStatus,
            "cardCollaborationType" => (int)$data->CardCollaborationType,
            "cardChargeType" => (int)$data->CardChargeType,
            "cardChargedBonus" => (float)$data->CardChargedBonus,
            "cardWriteoffBonus" => (float)$data->CardWriteoffBonus,
            "cardChargedMoney" => (float)$data->CardChargedMoney,
            "cardWriteoffMoney" => (float)$data->CardWriteoffMoney,
            "cardMoneyBalance" => (float)$data->CardMoneyBalance
        ];
    }

    #[ArrayShape(['contactID' => "string", 'contactPresence' => "int", 'cardNumber' => "string", 'cardType' => "string", 'cardStatus' => "int"])]
    public function createCard(User $user): array
    {
        $date = Carbon::now();
        $data = '<ContactInfoUpdateRequest>
            <Timeout>3000</Timeout>
            <RequestID>' . $date->timestamp . '</RequestID>
            <DateTime>' . $date->format('Y-m-d\TH:i:s\Z') . '</DateTime>
            <Organization>' . $this->config['organization'] . '</Organization>
            <BusinessUnit>' . $this->config['business_unit'] . '</BusinessUnit>
            <POS>' . $this->config['pos'] . '</POS>
            <AwardType>ContactUpdate</AwardType>
            <ContactID>
              <MobilePhone>' . $user->phone . '</MobilePhone>
              <Email>' . $user->email . '</Email>
            </ContactID>
            <CreateCard>
              <CreateCard>1</CreateCard>
              <IDTaskCard>' . $this->config['id_task_card'] . '</IDTaskCard>
            </CreateCard>
            <Attribute>
              <Key>firstname</Key>
              <Value>' . $user->first_name . '</Value>
            </Attribute>';
        if ($user->last_name) {
            $data .= '<Attribute><Key>lastname</Key><Value>' . $user->last_name . '</Value></Attribute>';
        }
        if ($user->middle_name) {
            $data .= '<Attribute><Key>middlename</Key><Value>' . $user->middle_name . '</Value></Attribute>';
        }
        if ($user->birth_date) {
            $data .= '<Attribute><Key>birthdate</Key><Value>' . $user->birth_date->format('Y-m-d\TH:i:s') . '</Value></Attribute>';
        }

        $data .= '</ContactInfoUpdateRequest>';

        $response = $this->client->post($this->urls['pos'], ['body' => $this->buildXml($data)]);
        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        $data = $xml->children('soap', true)->Body->children()->ProcessRequestResponse->ProcessRequestResult->ContactInfoUpdateResponse;
        if (0 !== (int)$data->ReturnCode)
            throw new \DomainException((string)$data->Message, (int)$data->ReturnCode);

        return [
            'contactID' => (string)$data->ContactID,
            'contactPresence' => (int)$data->ContactPresence,
            'cardNumber' => (string)$data->CardNumber,
            'cardType' => (string)$data->CardType,
            'cardStatus' => (int)$data->CardStatus
        ];
    }

    private function buildXml(string $data): string
    {
        $tmp = '<?xml version="1.0"?>
            <soap:Envelope
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
            >
            <soap:Body>
            <ProcessRequest xmlns="http://loyalty.manzanagroup.ru/loyalty.xsd">
            <request>';

        $tmp .= $data;

        $tmp .= '</request>
            <orgName>' . $this->config['org_name'] . '</orgName>
            </ProcessRequest>
            </soap:Body>
            </soap:Envelope>';

        return $tmp;
    }
}
