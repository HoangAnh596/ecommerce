<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Language extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'languages';

    protected $fillable = [
        'name',
        'canonical',
        'publish',
        'user_id',
        'image',
        'current'
    ];

    public function languages(){
        return $this->belongsToMany(PostCatalogue::class, 'post_catalogue_language' , 'language_id', 'post_catalogue_id')
        ->withPivot(
            'name',
            'canonical',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'description',
            'content'
        )->withTimestamps();
    }

    public function menus(){
        return $this->belongsToMany(Menu::class, 'menu_language' , 'language_id', 'menu_id')
        ->withPivot(
            'name',
            'canonical',
        )->withTimestamps();
    }
}
