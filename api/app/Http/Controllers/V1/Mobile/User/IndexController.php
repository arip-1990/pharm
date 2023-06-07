<?php

namespace App\Http\Controllers\V1\Mobile\User;

use App\Models\User;
use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

readonly class IndexController
{
    public function __construct(private CardService $cardService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            if (!$user = User::find($request->get('userIdentifier')))
                throw new \DomainException('Пользователь не найден');

            $items = $this->cardService->getAllByUser($user->id);

            $cards = array_filter($items, fn($item) => $item['StatusCode'] === 2);
            $card = array_pop($cards);

            return new JsonResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'emailConfirmed' => true,
                    'gender' => $user->getGenderLabel(),
                    'age' => $user->birth_date?->age,
                    'birthday' => $user->birth_date?->format('Y-m-d'),
                    'cardNumber' => $card['Number'] ?? null,
                    'bonuses' => $card['Balance'] ?? 0,
                    'units' => 'Бонусов', // $this->bonusTypeLabel($card['BonusType']),
                    'status' => 'Карта постоянного покупателя', // $card['CardType'],
                    'cardColor' => '#3ab0a3',
                    'cardTextColor' => '#fdfdfd',
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
