<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderChangeStatus;
use App\Models\Status\Status;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusListener implements ShouldQueue
{
    private array $statuses = [
        'A' => 'placed', // создан
        'O' => 'processing', // в обработке -> ожидается подтверждение аптеки
        'H' => 'ready_for_pickup', // готов к выдаче
        'F' => 'done', // выполнен. выкуплен
        'R' => 'canceled', // отменен
        // 'H' => 'ready_to_dispatch', -> готов к отправке
        // 'D' => 'dispatched', -> отправлен в доставку
        // 'D' => 'delivered', -> доставлен
        // 'C' => 'closed', -> завершен без выкупа
    ];
    private array $statusMessages = [
        'A' => 'создан',
        'O' => 'в обработке',
        'H' => 'готов к выдаче',
        'F' => 'выполнен',
        'R' => 'отменен',
        // 'H' => 'ready_to_dispatch', -> готов к отправке
        // 'D' => 'dispatched', -> отправлен в доставку
        // 'D' => 'delivered', -> доставлен
        // 'C' => 'closed', -> завершен без выкупа
    ];

    public function handle(OrderChangeStatus $event): void
    {
        /** @var Status $status */
        $order = $event->order;
        $status = $order->statuses->last();
        if (!isset($this->statuses[$status->value->value])) return;

        $data = [
            'id' => (string)$order->id,
            'code' => $this->statuses[$status->value->value],
            'message' => $this->statusMessages[$status->value->value]
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
