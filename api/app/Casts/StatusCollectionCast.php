<?php

namespace App\Casts;

use App\Models\Status\OrderStatus;
use App\Models\Status\OrderState;
use App\Models\Status\Status;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class StatusCollectionCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $tmp = new Collection(json_decode($value, true));
        return $tmp->map(fn($item) => new Status(OrderStatus::from($item['value']), new Carbon($item['created_at']), OrderState::from($item['state'])));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (!$value instanceof Collection) {
            throw new \InvalidArgumentException('Данное значение не является экземпляром Collection.');
        }

        return $value->toJson();
    }
}
