<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\UseCases\User\UserService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    private Client $client;

    public function __construct(private readonly UserService $userService) {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function phoneAuth(string $phone, string $password): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/AdvancedPhoneEmailLogin';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'PhoneOrEmail' => $phone,
            'Password' => $password,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $session = $data['SessionId'];
        if (!$user = User::query()->find($data['Id'])) {
            $data = $this->userService->getInfo($data['Id'], $data['SessionId']);
            $user = User::query()->create([
                'id' => $data['Id'],
                'first_name' => $data['FirstName'],
                'last_name' => $data['LastName'],
                'middle_name' => $data['MiddleName'],
                'email' => $data['EmailAddress'],
                'phone' => $data['MobilePhone'],
                'gender' => $data['GenderCode'],
                'birth_date' => Carbon::parse($data['BirthDate']),
                'session' => $session,
                'password' => ''
            ]);
        }
        else $user->update(['session' => $session]);

        Auth::login($user);
    }

    public function logout(User $user): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/Logout';
        $data = ['id' => $user->id, 'sessionid' => $user->session];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $user->update(['session' => null]);
        Auth::guard('web')->logout();
    }
}
