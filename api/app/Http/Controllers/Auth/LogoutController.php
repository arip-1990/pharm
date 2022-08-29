<?php

namespace App\Http\Controllers\Auth;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController
{
    public function handle(Request $request): JsonResponse
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/Logout';
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);

        $user = $request->user();
        $data = ['parameter' => ['id' => $user->id, 'sessionid' => $user->session]];
        $response = $client->post($url, ['body' => json_encode($data)]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse([
                'code' => $data['odata.error']['code'],
                'message' => $data['odata.error']['message']['value']
            ], 500);
        }

        $user->update(['session' => null]);
        Auth::logout();
        $request->session()->invalidate();

        return new JsonResponse();
    }
}
