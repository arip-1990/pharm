<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\UseCases\LoyaltyService;
use App\UseCases\User\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService extends LoyaltyService
{
    public function __construct(private readonly UserService $userService) {
        parent::__construct();
    }

    public function login(string $phone, string $password): string
    {
        $url = $this->urls['lk'] . '/Identity/AdvancedPhoneEmailLogin';
        $data = [
            'PhoneOrEmail' => $phone,
            'Password' => $password,
            'PartnerId' => $this->config['partner_id']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $session = $data['SessionId'];
        if (!$user = User::find($data['Id'])) {
            $data = $this->userService->getInfo($data['Id'], $session);
            $user = User::firstOrNew(['phone' => $data['MobilePhone']]);

            $user->fill([
                'id' => $data['Id'],
                'email' => $data['EmailAddress'],
                'first_name' => $data['FirstName'],
                'last_name' => $data['LastName'],
                'middle_name' => $data['MiddleName'],
                'gender' => $data['GenderCode'],
                'birth_date' => Carbon::parse($data['BirthDate']),
                'phone_verified_at' => Carbon::parse($data['RegistrationDate']),
                'password' => Hash::make($password),
            ]);
        }

        $user->phone_verified_at = $user->phone_verified_at ?? Carbon::now();
        $user->save();

        Auth::login($user);

        return $session;
    }

    public function logout(User $user, string $session): void
    {
        $url = $this->urls['lk'] . '/Identity/Logout';
        $data = ['id' => $user->id, 'sessionid' => $session];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        Auth::logout();
    }
}
