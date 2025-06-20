<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PizzaType extends Model
{
    protected $table = 'pizza_types';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function pizzas()
    {
        return $this->hasMany(Pizza::class, 'pizza_type_id', 'pizza_type_id');
    }
}
