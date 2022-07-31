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
        $url = config('data.loyalty.test.url.lk') . 'Identity/Logout';
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);

        $user = Auth::user();
        $data = ['parameter' => ['id' => $user->id, 'sessionid' => $user->session]];
        $response = $client->post($url, ['body' => json_encode($data)]);
        if ($response->getStatusCode() !== 200)
            return new JsonResponse(json_decode($response->getBody()), $response->getStatusCode());

        $user->update(['session' => null]);
        Auth::logout();
        $request->session()->invalidate();

        return new JsonResponse();
    }
}
