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
        'name', 'code',
        'description', 'method',
        'discountValue',
        'discountType',
        'maxDiscountValue',
        'discountInformation',
        'neverEndDate', 'startDate',
        'endDate', 'publish',
        'order', 'user_id',
    ];

    protected $casts = [
        'discountInformation' => 'json',
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'promotion_product_variant', 'promotion_id', 'product_id')
        ->withPivot(
            'variant_uuid',
            'model',
        )->withTimestamps();
    }
}
