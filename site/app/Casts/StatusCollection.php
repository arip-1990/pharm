<?php

namespace App\Casts;

use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class StatusCollection implements CastsAttributes
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
        return $tmp->map(function ($item) {
            $date = is_array($item['created_at']) ? Carbon::parse($item['created_at']['date']) : Carbon::parse($item['created_at']);
            return new Status($item['value'], $date, $item['state']);
        });
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
