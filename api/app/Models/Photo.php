<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $type
 * @property string $file
 * @property int $sort
 * @property int $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Product $product
 */
class Photo extends Model
{
    use SoftDeletes;

    const DEFAULT_FILE = '/images/default.png';

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    const STATUS_NOT_CHECKED = 0;
    const STATUS_CHECKED = 1;

    protected $fillable = ['type', 'file', 'status', 'product_id', 'sort'];

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
