<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    public function pizza()
    {
        return $this->belongsTo(Pizza::class, 'pizza_id', 'pizza_id');
    }
}
