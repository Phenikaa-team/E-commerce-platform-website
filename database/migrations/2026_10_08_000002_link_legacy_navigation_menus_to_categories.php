<?php

use App\Models\Category;
use App\Models\NavigationMenu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        NavigationMenu::query()
            ->whereNull('category_id')
            ->where(function ($query): void {
                $query->whereNull('url')->orWhere('url', '!=', '__quick__');
            })
            ->get()
            ->each(function (NavigationMenu $menu): void {
                $slug = Str::slug($menu->slug ?: $menu->title);
                $category = Category::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $menu->title, 'parent_id' => null]
                );

                $menu->update(['category_id' => $category->id]);
            });
    }

    public function down(): void
    {
        NavigationMenu::query()
            ->whereIn('slug', ['lam-dep-va-suc-khoe', 'laptop-va-cac-thiet-bi-so', 'thoi-trang-va-phu-kien'])
            ->update(['category_id' => null]);
    }
};
