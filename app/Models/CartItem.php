<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CartItem extends Model { public $timestamps = false; protected $guarded = []; protected $casts = ['unit_price' => 'decimal:2']; public function product() { return $this->belongsTo(Product::class); } }
