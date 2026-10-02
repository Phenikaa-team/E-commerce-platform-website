<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationMenu extends Model
{
    protected $table = 'navigation_menus';

    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
