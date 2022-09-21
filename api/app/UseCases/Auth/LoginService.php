<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\UseCases\LoyaltyService;
use App\UseCases\User\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JetBrains\PhpStorm\ArrayShape;

class LoginService extends LoyaltyService
{
    public function __construct(private readonly UserService $userService) {
        parent::__construct();
    }

    #[ArrayShape(['token' => "string", 'session' => "string"])]
    public function phoneAuth(string $phone, string $password): array
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

        $orders = [];
        $visits = [];
        $session = $data['SessionId'];
        if (!$user = User::query()->find($data['Id'])) {
            $data = $this->userService->getInfo($data['Id'], $session);
            $user = User::query()->firstOrNew(['phone' => $data['MobilePhone']]);

            $orders = $user->orders;
            $visits = $user->visits;
            if (count($orders) or count($visits)) {
                $tmp = User::query()->find('5ac09db5-8f02-4158-ac3a-283c548de800');
                if (count($orders)) $tmp->orders()->saveMany($orders);
                if (count($visits)) $tmp->visits()->saveMany($visits);
            }

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

        if (count($orders)) $user->orders()->saveMany($orders);
        if (count($visits)) $user->visits()->saveMany($visits);

        return [
            'token' => Auth::login($user),
            'session' => $session
        ];
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
