<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = true;

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'order_id');
    }
}
