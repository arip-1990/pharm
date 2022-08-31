<?php

namespace App\Http\Controllers\Cheque;

use App\Http\Resources\ChequeResource;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function handle(Request $request): JsonResponse
    {
        $url = config('data.loyalty.test.url.lk') . '/Cheque/GetAllByContact';
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);

        $user = $request->user();
        $response = $client->get($url, ['query' => "contactid='{$user->id}'&sessionid='{$user->session}'"]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse([
                'code' => $data['odata.error']['code'],
                'message' => $data['odata.error']['message']['value']
            ], 500);
        }
        return new JsonResponse(ChequeResource::collection($data['value']));
    }
}
