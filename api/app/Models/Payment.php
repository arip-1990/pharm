<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    const TYPE_CASH             = 'cash';
    const TYPE_CARD_ON_DELIVERY = 'card_on_delivery';
    const TYPE_CARD             = 'card';
    const TYPE_IOS              = 'iOS';
    const TYPE_ANDROID          = 'android';

    protected $fillable = [
        'slug_id',
        'title',
        'description',
        'type',
    ];
}
