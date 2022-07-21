<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function handle(): JsonResponse
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/Login';
//        $data = $request->validated();
        $data = ['parameter' => ['Login' => 'crm\Integr', 'Password' => 'E9JxGqe2Z']];

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);

        $response = $client->post($url, ['body' => json_encode($data)]);
        if ($response->getStatusCode() !== 200)
            return new JsonResponse(json_decode($response->getBody()), $response->getStatusCode());

        return new JsonResponse(json_decode($response->getBody()));
    }
}
