<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'routers';

    protected $fillable = [
        'canonical',
        'module_id',
        'language_id',
        'controllers'
    ];
}
