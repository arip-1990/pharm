<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $type
 * @property string $file
 * @property string $product_id
 */
class Photo extends Model
{
    const DEFAULT_FILE = '/images/default.png';

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    public $timestamps = false;
    protected $fillable = ['type', 'file'];

    public function getOriginalFile(): string
    {
        return "/images/product/original/$this->file";
    }

    public function getThumbnailFile(string $type = 'thumb'): string
    {
        $type = match ($type) {
            'cart' => 'cart',
            default => 'thumb'
        };

        return "/images/product/thumbnail/$type" . '_' . $this->file;
    }
}
