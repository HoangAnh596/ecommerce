<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Source extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'sources';

    protected $fillable = [
        'name',
        'keyword',
        'description',
        'publish',
        'user_id',
    ];
}
