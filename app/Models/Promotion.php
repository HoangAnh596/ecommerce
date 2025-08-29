<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'promotions';

    protected $fillable = [
        'name',
        'keyword',
        'description',
        'album',
        'model_id',
        'model',
        'short_code',
        'publish',
        'user_id',
    ];

    protected $casts = [
        'model_id' => 'json',
        'album' => 'json',
        'description' => 'json'
    ];
}
