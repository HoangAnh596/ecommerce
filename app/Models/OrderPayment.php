<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class OrderPayment extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'order_payment';

    protected $fillable = [
        'order_id',
        'method_name',
        'payment_id',
        'payment_detail'
    ];

    protected $casts = [
        'payment_detail' => 'json'
    ];

    public function orders() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
