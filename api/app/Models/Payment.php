<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property ?string $description
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Payment extends Model
{
    use HasFactory, softDeletes;

    const TYPE_CASH             = 'cash';
    const TYPE_CARD_ON_DELIVERY = 'card_on_delivery';
    const TYPE_CARD             = 'card';
    const TYPE_IOS              = 'iOS';
    const TYPE_ANDROID          = 'android';

    protected $fillable = [
        'title',
        'description',
        'type',
    ];
}
