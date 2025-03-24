<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];

    /**
     * Get all of the orderItem for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
