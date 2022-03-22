<?php

namespace App\Models;

use App\Exceptions\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Order $order
 */
class Exception extends Model
{
    public static function create(int $orderId, string $type, string $message): self
    {
        $exception = new static();
        $exception->initiator = Order::class;
        $exception->initiator_id = $orderId;
        $exception->type = $type;
        $exception->message = $message;
        return $exception;
    }

    public function getOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'initiator_id');
    }
}
