<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $type
 * @property string $product_id
 * @property int $sort
 */
class Photo extends Model
{
    use SoftDeletes;

    const DEFAULT_FILE = '/images/default.png';

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    protected $fillable = ['type', 'product_id', 'sort'];

    public function getUrl(): ?string
    {
        $file = $this->getFilePath();
        return $file ? url("storage/$file", secure: false) : null;
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
