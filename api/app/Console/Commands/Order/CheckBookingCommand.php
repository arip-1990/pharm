<?php

namespace App\Console\Commands\Order;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;

class CheckBookingCommand extends Command
{
    protected $signature = 'order:check-booking';
    protected $description = 'Check booking orders';

    public function handle(): int
    {
        Order::where('delivery_id', 3)->each(function (Order $order) {
            if ($order->isSent() or $order->isCancelled() or $order->payment->isType(Payment::TYPE_CARD) and !$order->isPay())
                return;

            if ($order->created_at->diffInDays() > 3) {
                $order->cancel('Истек время ожидания брони');
                return;
            }

            $offers = Offer::whereIn('product_id', $order->items->pluck('product_id'))
                ->where('store_id', $order->store_id)->get();

            $send = true;
            /** @var Offer $offer */
            foreach ($offers as $offer) {
                if (!$item = $order->items->firstWhere('product_id', $offer->product_id) or ($item->quantity > $offer->quantity)) {
                    $send = false;
                    break;
                }
            }

            if ($send) {
                $order->sent();
                $order->save();
            }
        });

        $this->info('Процесс завершен!');
        return 0;
    }
}
