<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class ProductCatalogue extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'product_catalogues';

    protected $fillable = [
        'parent_id',
        'lft',
        'rgt',
        'level',
        'image',
        'icon',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'product_catalogue_language' , 'product_catalogue_id', 'language_id')
        ->withPivot(
            'product_catalogue_id',
            'language_id',
            'name',
            'canonical',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'description',
            'content'
        )->withTimestamps();
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'product_catalogue_product', 'product_catalogue_id', 'product_id');
    }

    /* Lấy ra cha con */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Đệ quy lấy toàn bộ nhánh con + kèm products của từng node
     * Có thể truyền $language, $limit qua scope tạm hoặc closure khi with()
     */
    public function childrenRecursive()
{
    return $this->children()->with([
        'childrenRecursive', 
        'products.languages'
    ]);
}

    public function product_catalogue_language(){
        return $this->hasMany(ProductCatalogueLanguage::class, 'product_catalogue_id', 'id');
    }

    public static function isNodeCheck($id = 0){
        $productCatalogue = ProductCatalogue::find($id);
        
        if($productCatalogue->rgt - $productCatalogue->lft !== 1){
            return false;
        }

        return true;
    }
}
