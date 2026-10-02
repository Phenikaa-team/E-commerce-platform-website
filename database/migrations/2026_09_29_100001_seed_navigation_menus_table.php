<?php

use App\Models\Category;
use App\Models\NavigationMenu;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (NavigationMenu::count() > 0) {
            return;
        }
        Category::orderBy('id')->get()->each(function (Category $category, int $index): void {
            NavigationMenu::create(['category_id' => $category->id, 'title' => $category->name, 'slug' => $category->slug, 'sort_order' => $index, 'is_active' => true]);
        });
    }

    public function down(): void
    {
        NavigationMenu::query()->delete();
    }
};
