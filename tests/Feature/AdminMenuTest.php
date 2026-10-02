<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_navigation_menu(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Điện tử',
            'slug' => 'dien-tu',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.menus.store'), [
            'title' => 'Điện tử',
            'category_id' => $category->id,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('navigation_menus', [
            'title' => 'Điện tử',
            'category_id' => $category->id,
            'slug' => 'dien-tu',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }
}
