<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    use HasFactory;

    protected $table = 'pizzas';
    protected $primaryKey = 'pizza_id';
    protected $fillable = ['pizza_id', 'pizza_type_id', 'size', 'price'];
    protected $casts = ['price' => 'float'];
}
