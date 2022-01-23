<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $type
 * @property string $product_id
 * @property int $sort
 */
class Photo extends Model
{
    const DEFAULT_FILE = '/images/default.png';

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    public $timestamps = false;
    protected $fillable = ['type'];

    public static function getNextId(): int
    {
        $statement = DB::select("show table status like 'photos'");
        return $statement[0]->Auto_increment;
    }

    public function getOriginalFile(): string
    {
        return "storage/images/original/{$this->product_id}/" . $this->id;
    }

    public function getThumbnailFile(string $type = 'thumb'): string
    {
        $type = match ($type) {
            'cart' => 'cart',
            default => 'thumb'
        };

        return "storage/images/thumb/{$this->product_id}/$type" . '_' . $this->id;
    }
}
