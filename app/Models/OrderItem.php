<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model { public $timestamps = false; protected $guarded = []; protected $casts = ['unit_price' => 'decimal:2', 'subtotal' => 'decimal:2']; }
