<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_non_platform_admin_user_gets_403_forbidden_when_accessing_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::TeamMember,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_platform_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::PlatformAdmin,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }
}
