<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $type
 * @property float $price
 * @property int $min
 * @property int $max
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_DELIVERY = 'delivery';
    const TYPE_PICKUP = 'pickup';

    protected $fillable = [
        'title',//название
        'description',//описание
        'type',//тип
        'price',//минимальная цена
        'min',//минимальный срок доставки
        'max',//максимальный срок доставки
    ];
}
