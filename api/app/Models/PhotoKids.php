<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PhotoKids extends Model
{
    use HasFactory;

    protected $fillable = [
        "link",
        "photo_name",
        "birthdate",
        "first_name",
        "last_name",
        "middle_name",
        "published",
        "user_id",
        "age_category_id"
    ];

    public function users(): object {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function age_category(): BelongsTo
    {
        return $this->belongsTo(AgeCategoryModel::class, 'age_category_id');
    }

    public function UsersLikes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'photo_id', 'user_id');
    }


}
