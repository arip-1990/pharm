<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    protected $fillable = ['type', 'product_id', 'sort'];

    public function getUrl(): ?string
    {
        $file = $this->getFilePath();
        return $file ? Storage::url($file) : null;
    }

    public function getFilePath(): ?string
    {
        foreach (Storage::files("images/original/{$this->product_id}") as $file) {
            $exp = explode('/', $file);
            if ((string)$this->id === explode('.', array_pop($exp))[0])
                return $file;
        }
        return null;
    }

    public function getThumbFilePath(string $type = 'thumb'): ?string
    {
        $type = match ($type) {
            'cart' => 'cart',
            default => 'thumb'
        };

        foreach (Storage::files("images/original/{$this->product_id}") as $file) {
            $exp = explode('/', $file);
            if ("{$type}_{$this->id}" === explode('.', array_pop($exp))[0])
                return $file;
        }
        return null;
    }
}
