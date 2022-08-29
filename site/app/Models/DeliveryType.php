<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryType extends Model
{
    use HasFactory;

    const DELIVERY = 'delivery';
    const PICKUP = 'pickup';

    protected $fillable = [
        'slug_id',//идентификатор
        'title',//название
        'description',//описание
        'type',//тип
        'price',//минимальная цена
        'min',//минимальный срок доставки
        'max',//максимальный срок доставки
    ];
}
