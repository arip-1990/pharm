<?php

namespace App\Jobs;

use App\Entities\Exception;
use App\Entities\Order;
use App\Entities\Status;
use App\Mail\Order\CreateOrder;
use App\UseCases\Order\GenerateDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Order $order) {}

    public function handle(): void
    {
        $order_number = config('data.orderStartNumber') + $this->order->id;

        try {
            $response = simplexml_load_string($this->getSendInfo());

            if(isset($response->errors->error->code)) {
                $message = 'Номер заказа: ' . $order_number . '. Код ошибки: ' . $response->errors->error->code . '.';

                throw new \DomainException($message . $response->errors->error->message);
            }

            if(isset($response->success->order_id)) {
                $this->order->changeStatusState(Status::STATE_SUCCESS);

                Mail::to(Auth::user())->send(new CreateOrder($this->order));

//            try {
//                $this->mailer->send($order);
//            }
//            catch (\Exception $e) {
//                OrderException::create($order->id, 'email', $e->getMessage())->save();
//            }
            }
        }
        catch (\Exception $e) {
            $this->order->changeStatusState(Status::STATE_ERROR);
            Exception::create($this->order->id, '1c', $e->getMessage())->save();
        }
        finally {
            $this->order->save();
        }
    }

    private function getSendInfo(): string
    {
        $service = new GenerateDataService($this->order);
        $config = config('data.1c');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://' . $config['user'] . ':' . $config['password'] . '@' . $config['urls'][5],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POSTFIELDS => $service->generateSenData(new \DateTimeImmutable())
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
