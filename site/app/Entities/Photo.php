<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        return $this->getFilePath() ? Storage::url($this->getFilePath()) : null;
    }

    public function getFilePath(): ?string
    {
        $files = Storage::files("images/original/{$this->product_id}");
        foreach ($files as $file) {
            $exp = explode('/', $file);
            if ($this->id == explode('.', array_pop($exp))[0])
                return $file;
        }
        return $files ? $files[0] : null;
    }

    public function getThumbFilePath(string $type = 'thumb'): ?string
    {
        $type = match ($type) {
            'cart' => 'cart',
            default => 'thumb'
        };
        $files = glob("storage/images/original/{$this->product_id}/{$type}_{$this->id}");

        return $files ? $files[0] : null;
    }
}
