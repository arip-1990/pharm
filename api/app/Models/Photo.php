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

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    protected $fillable = ['type', 'product_id', 'sort'];

    public function getSize(): array
    {
        $data = [0, 0];
        if (Storage::exists('images/original/' . $this->file)) {
            list($width, $height) = getimagesize(Storage::path('images/original/' . $this->file));
            $data = [$width, $height];
        }

        return $data;
    }

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
//        foreach (Storage::files('images/original') as $file) {
//            $exp = explode('/', $file);
//            if ("{$type}_{$this->id}" === explode('.', array_pop($exp))[0])
//                return $file;
//        }
//        return null;
//    }
}
