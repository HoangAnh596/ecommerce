<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCataloguePermission extends Model
{
    use HasFactory;

    protected $table = 'user_catalogue_permission';

    // protected $fillable = [
    //     'name',
    //     'description',
    //     'publish'
    // ];

    public function user_catalogues() {
        return $this->belongsTo(UserCatalogue::class, 'user_catalogue_id');
    }

    public function permissions() {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
