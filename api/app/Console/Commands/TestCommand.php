<?php

namespace App\Console\Commands;

use App\Order\Entity\Order;
use App\Order\UseCase\GenerateDataService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {orderId}';
    protected $description = 'test';

    public function handle(): int
    {
        $queueClient = Redis::connection()->client();

        $data = $queueClient->publish('bot', 'test');
        print_r($data);
//        $order = Order::find((int)$this->argument('orderId'));
//        $order->addStatus(OrderStatus::STATUS_SEND);
//        try {
//            $orderNumber = config('data.orderStartNumber') + $order->id;
//            $response = simplexml_load_string($this->orderSend($order));
//
//            if(isset($response->errors->error->code))
//                throw new \DomainException("Номер заказа: {$orderNumber}. {$response->errors->error->message}");
//
//            if(isset($response->success->order_id))
//                $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
//        }
//        catch (\Exception $e) {
//            $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);
//            Log::info($e->getMessage());
//
//            $queueClient->publish('bot:error', json_encode([
//                'file' => self::class . ' (' . $e->getLine() . ')',
//                'message' => $e->getMessage()
//            ], JSON_UNESCAPED_UNICODE));
//        } finally {
//            $order->save();
//        }

        $this->info("Процесс завершена!");
        return self::SUCCESS;
    }

    private function orderSend(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');

        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $service->generateSenData(Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
