<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\AuditLog;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_is_not_audited()
    {
        $super = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($super)->get('/');

        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_non_super_admin_is_audited()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'user_role' => 'admin',
        ]);
    }
}
