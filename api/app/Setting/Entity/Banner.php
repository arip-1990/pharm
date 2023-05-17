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
 * @property int $type
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

    const TYPE_WEB = 0;
    const TYPE_WEB_ALL = 1;
    const TYPE_MOBILE = 2;

    protected $fillable = ['title', 'description', 'picture', 'type', 'sort'];

    public static function getPath(string $fileName = ''): string
    {
        if ($fileName) $fileName = "/{$fileName}";
        return 'images/original/banners' . $fileName;
    }

    public function getUrl(bool $mobile = false): ?string
    {
        $file = self::getPath($mobile ? "mobile_{$this->picture}" : $this->picture);
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
