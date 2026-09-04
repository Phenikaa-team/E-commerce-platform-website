<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model { protected $guarded = []; protected $casts = ['price' => 'decimal:2']; public function store() { return $this->belongsTo(Store::class); } public function category() { return $this->belongsTo(Category::class); } }
