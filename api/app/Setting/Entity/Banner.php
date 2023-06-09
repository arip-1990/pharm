<?php

namespace App\Setting\Entity;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property ?string $description
 * @property string $picture
 * @property string $path
 * @property ?string $link
 * @property BannerType $type
 * @property int $sort
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 *
 * @property ?User $creator
 * @property ?User $destroyer
 */
class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'picture', 'path', 'link', 'type', 'sort'];
    protected $casts = [
        'type' => BannerType::class
    ];

    public static function getPath(string $path = '/', string $fileName = ''): string
    {
        if ($fileName) $fileName = "/{$fileName}";
        return "banners{$path}" . $fileName;
    }

    public function getUrl(bool $mobile = false): ?string
    {
        $file = self::getPath($this->path, $mobile ? "mobile_{$this->picture}" : $this->picture);
        if (Storage::exists($file)) return Storage::url($file);
        return null;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function destroyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destroyer_id');
    }
}
