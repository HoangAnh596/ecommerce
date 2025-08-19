<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Slide extends Model
{
    use HasFactory;

    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'slides';

    protected $fillable = [
        'name',
        'description',
        'keyword',
        'image',
        'icon',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'menu_language' , 'menu_id', 'language_id')
        ->withPivot(
            'menu_id',
            'language_id',
            'name',
            'canonical'
        )->withTimestamps();
    }
}
