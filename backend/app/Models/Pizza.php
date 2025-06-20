<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    protected $table = 'pizzas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function pizzaType()
    {
        return $this->belongsTo(PizzaType::class, 'pizza_type_id', 'pizza_type_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'pizza_id', 'pizza_id');
    }
}
