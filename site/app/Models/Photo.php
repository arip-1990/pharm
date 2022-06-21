<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $type
 * @property string $file
 * @property string $product_id
 * @property int $sort
 * @property int $status
 */
class Photo extends Model
{
    const DEFAULT_FILE = '/images/default.png';

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    const STATUS_NOT_CHECKED = 0;
    const STATUS_CHECKED = 1;

    protected $fillable = ['type', 'file', 'status', 'product_id', 'sort'];

    public function getUrl(): ?string
    {
        if (Storage::exists('images/original/' . $this->file))
            return Storage::url('images/original/' . $this->file);
        return null;
    }

//    public function getThumbFilePath(string $type = 'thumb'): ?string
//    {
//        $type = match ($type) {
//            'cart' => 'cart',
//            default => 'thumb'
//        };
//
//        foreach (Storage::files("images/original/{$this->product_id}") as $file) {
//            $exp = explode('/', $file);
//            if ("{$type}_{$this->id}" === explode('.', array_pop($exp))[0])
//                return $file;
//        }
//        return null;
//    }
}
