<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = ['order_id', 'date', 'time'];
    protected $primaryKey = 'id'; // Default auto-incrementing PK
    public $incrementing = true; // Auto-incrementing id
}
