<?php

namespace Tests\Feature;

use App\Models\FlashSaleCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_recurring_flash_sale_plan(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.flash-sales.store'), [
            'name' => 'Ngày đôi tháng 10',
            'schedule_type' => 'recurring',
            'recurring_dates' => '01/01, 02/02, 10/10',
            'daily_starts_at' => '20:00',
            'daily_ends_at' => '23:59',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.flash-sales.index'));
        $this->assertDatabaseHas('flash_sale_campaigns', [
            'name' => 'Ngày đôi tháng 10',
            'schedule_type' => 'recurring',
            'is_active' => true,
        ]);
        $this->assertSame(
            ['01/01', '02/02', '10/10'],
            FlashSaleCampaign::firstOrFail()->recurring_dates
        );
    }

    public function test_admin_can_create_an_automatic_plan_for_all_matching_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($admin)->post(route('admin.flash-sales.store'), [
            'name' => 'Ngày đôi cả năm',
            'schedule_type' => 'recurring',
            'all_matching_dates' => 1,
            'daily_starts_at' => '20:00',
            'daily_ends_at' => '23:59',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.flash-sales.index'));
        $this->assertSame(
            ['01/01', '02/02', '03/03', '04/04', '05/05', '06/06', '07/07', '08/08', '09/09', '10/10', '11/11', '12/12'],
            FlashSaleCampaign::firstOrFail()->recurring_dates
        );
    }
}
