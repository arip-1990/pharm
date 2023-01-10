<?php

namespace App\Http\Controllers\V1\Mobile\User;

use App\Models\User;
use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __construct(private readonly CardService $cardService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            if (!$user = User::find($request->get('userIdentifier')))
                throw new \DomainException('Пользователь не найден');

            $items = $this->cardService->getAllByUser($user->id);

            $card = [];
            $cards = [];
            foreach ($items as $item) {
                if ($item['StatusCode'] === 2)
                    $card = $item;
                $cards[] = [
                    'cardNumber' => $item['Number'],
                    'bonuses' => $item['Balance'],
                    'status' => $item['CardType'],
                    'cardColor' => '#000',
                    'cardTextColor' => '#111'
                ];
            }

            return new JsonResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'emailConfirmed' => true,
                    'gender' => $user->getGenderLabel(),
                    'age' => $user->birth_date?->age,
                    'cardNumber' => $card['Number'],
                    'bonuses' => $card['Balance'],
                    'units' => 'Бонусов', // $this->bonusTypeLabel($card['BonusType']),
                    'cardPercent' => $card['Discount'],
                    'status' => $card['CardType'],
                    'pendingBonuses' => 0,
                    'pendingBonusesTitle' => 'Будет накоплено',
                    'expressBonuses' => 0,
                    'exressBonusesTitle' => 'Экспресс-бонусы',
                    'cardColor' => '#000',
                    'cardTextColor' => '#111',
                    'loyaltyProgram' => $cards,
                ]
            ]);
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function bonusTypeLabel(int $bonusType): string
    {
        return match ($bonusType) {
            1 => 'Бонус',
            2 => 'Скидка',
            3 => 'Бонус + скидка',
            4 => 'Подарочная',
            default => '',
        };
    }
}
