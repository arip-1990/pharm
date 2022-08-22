<?php

namespace App\UseCases;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;

class PosService
{
    private Client $client;

    public function __construct() {
        $config = config('data.loyalty.test');
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://loyalty.manzanagroup.ru/loyalty.xsd/ProcessRequest'
            ],
            'auth' => [$config['login'], $config['password']],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function getBalance(string $phone, bool $sendCode = false, string $validationCode = null): array
    {
        $url = config('data.loyalty.test.url.pos');
        $organization = config('data.loyalty.test.organization');
        $businessUnit = config('data.loyalty.test.business_unit');
        $pos = config('data.loyalty.test.pos');
        $date = Carbon::now()->format('Y-m-d\TH:i:sP');

        $data = '<BalanceRequest>
            <RequestID>1001</RequestID>
            <DateTime>' . $date . '</DateTime>
            <Organization>' . $organization . '</Organization>
            <BusinessUnit>' . $businessUnit . '</BusinessUnit>
            <POS>' . $pos . '</POS>
            <MobilePhone>
            <Number>+' . $phone . '</Number>';

        if ($validationCode) $data .= '<ValidationCode>' . $validationCode . '</ValidationCode>';
        elseif ($sendCode) $data .= '<SendCode>1</SendCode>';

        $data .= '</MobilePhone></BalanceRequest>';

        $response = $this->client->post($url, ['body' => $this->buildXml($data)]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        return (array)$xml->children('soap', true)->Body->children()->ProcessRequestResponse->ProcessRequestResult->BalanceResponse;
    }

    public function createCard(string $phone, string $email, string $firstName, string $lastName = null, string $middleName = null, Carbon $birthDate = null): array
    {
        $url = config('data.loyalty.test.url.pos');
        $organization = config('data.loyalty.test.organization');
        $businessUnit = config('data.loyalty.test.business_unit');
        $pos = config('data.loyalty.test.pos');
        $date = Carbon::now()->format('Y-m-d\TH:i:sP');

        $data = '<ContactInfoUpdateRequest>
            <Timeout>3000</Timeout>
            <RequestID>02</RequestID>
            <DateTime>' . $date . '</DateTime>
            <Organization>' . $organization . '</Organization>
            <BusinessUnit>' . $businessUnit . '</BusinessUnit>
            <POS>' . $pos . '</POS>
            <AwardType>ContactUpdate</AwardType>
            <ContactID>
              <MobilePhone>+' . $phone . '</MobilePhone>
              <Email>' . $email . '</Email>
            </ContactID>
            <CreateCard>
              <CreateCard>1</CreateCard>
              <IDTaskCard>integr2</IDTaskCard>
            </CreateCard>
            <Attribute>
              <Key>firstname</Key>
              <Value>' . $firstName . '</Value>
            </Attribute>';
        if ($lastName) {
            $data .= '<Attribute><Key>lastname</Key><Value>' . $lastName . '</Value></Attribute>';
        }
        if ($middleName) {
            $data .= '<Attribute><Key>middlename</Key><Value>' . $middleName . '</Value></Attribute>';
        }
        if ($birthDate) {
            $data .= '<Attribute><Key>birthdate</Key><Value>' . $birthDate->format('Y-m-d\TH:i:sP') . '</Value></Attribute>';
        }

        $data .= '</ContactInfoUpdateRequest>';

        $response = $this->client->post($url, ['body' => $this->buildXml($data)]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        $xml = simplexml_load_string($response->getBody()->getContents());
        if ($xml === false)
            throw new \DomainException('Ошибка парсинга xml');

        return (array)$xml->children('soap', true)->Body->children()->ProcessRequestResponse->ProcessRequestResult->BonusResponse;
    }

    private function buildXml(string $data): string
    {
        $orgName = config('data.loyalty.test.org_name');
        $tmp = '<?xml version="1.0" encoding="utf-8"?>
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
            <orgName>' . $orgName . '</orgName>
            </ProcessRequest>
            </soap:Body>
            </soap:Envelope>';

        return $tmp;
    }
}
