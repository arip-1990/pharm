<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $extension
 * @property int $type
 * @property int $sort
 * @property int $status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property User $creator
 * @property User $destroyer
 * @property Collection<Product> $products
 */
class Photo extends Model
{
    use SoftDeletes;

    const TYPE_PICTURE = 0;
    const TYPE_CERTIFICATE = 1;

    const STATUS_NOT_CHECKED = 0;
    const STATUS_CHECKED = 1;

    protected $fillable = ['name', 'title', 'extension', 'type', 'status', 'sort'];

    public function getSize(): array
    {
        $data = [0, 0];
        $file = "images/original/{$this->name}.{$this->extension}";
        if (Storage::exists($file)) {
            list($width, $height) = getimagesize(Storage::path($file));
            $data = [$width, $height];
        }

        return $data;
    }

    public function getUrl(): ?string
    {
        $file = "images/original/{$this->name}.{$this->extension}";
        if (Storage::exists($file)) return Storage::url($file);
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function destroyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destroyer_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
