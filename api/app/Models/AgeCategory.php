<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeCategory extends Model
{
    protected $table = 'age_category';
    use HasFactory;

    public function photos(): HasMany
    {
        return $this->hasMany(PhotoKids::class, 'age_category_id');
    }

}
