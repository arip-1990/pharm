<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class numberOfChildren extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'children',
        'user_id'
    ];
}
