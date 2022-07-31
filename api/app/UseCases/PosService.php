<?php

namespace App\UseCases;

use Carbon\Carbon;
use GuzzleHttp\Client;

class PosService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://loyalty.manzanagroup.ru/loyalty.xsd/ProcessRequest'
            ],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function getBalance(string $phone): \SimpleXMLElement
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
            <Number>+' . $phone . '</Number>
            <SendCode>1</SendCode>
            </MobilePhone>
            </BalanceRequest>';

        $response = $this->client->post($url, ['body' => $this->buildXml($data)]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Ошибка получения баланса');

        if (!$xml = simplexml_load_string($response->getBody()->getContents()))
            throw new \DomainException('Ошибка парсинга xml');

        return $xml;
    }

    private function buildXml(string $data): string
    {
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
            <orgName>Loyalty</orgName>
            </ProcessRequest>
            </soap:Body>
            </soap:Envelope>';

        return $tmp;
    }
}
