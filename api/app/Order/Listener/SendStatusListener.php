<?php

namespace App\Order\Listener;

use App\Order\Event\OrderChangeStatus;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusListener implements ShouldQueue
{
    private array $statuses = [
        'A' => 'Создан',                            // placed
        'O' => 'Ожидается подтверждение аптеки',    // processing
        'H' => 'Готов к выдаче',                    // ready_for_pickup
        'F' => 'Выполнен',                          // done
        'R' => 'Отменен',                           // canceled
        // 'H' => 'Готов к отправке',               // ready_to_dispatch
        // 'D' => 'Отправлен в доставку',           // dispatched
        // 'D' => 'Доставлен',                      // delivered
        // 'C' => 'Завершен без выкупа',            // closed
    ];

    public function handle(OrderChangeStatus $event): void
    {
        $order = $event->order;
        $status = $order->statuses->last();

        if ($order->platform == 'web' or !isset($this->statuses[$status->value->value])) return;

        $data = [
            'id' => (string)$order->id,
            'code' => $status->value->value,
            'message' => $this->statuses[$status->value->value]
        ];

        if ($order->user) $data['userId'] = $order->user->id;
        elseif ($order->phone) $data['userId'] = $order->phone;

        $data = $this->sendStatus($data);
        if (!$data['success']) throw new \DomainException('Не удалось синхронизировать статус!');
    }

    private function sendStatus(array $data): array
    {
        $url = 'https://api1.imshop.io/v1/clients/apteka120/statuses/sync/5f9c5363-917f-4588-83ab-ea4058ccdff6';
        $client = new Client();
        $response = $client->post($url, ['json' => $data]);

        return json_decode($response->getBody(), true);
    }
}
