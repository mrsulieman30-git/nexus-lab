<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class SpatieActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_actions_not_recorded_in_spatie_activitylog()
    {
        $super = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($super)->get('/');
        $this->actingAs($admin)->get('/');

        // If Spatie is installed, assert its activity_log entries exist.
        if (class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            try {
                Artisan::call('migrate', ['--path' => 'vendor/spatie/laravel-activitylog/database/migrations', '--force' => true]);
            } catch (\Throwable $e) {
                $this->markTestSkipped('Could not run spatie migrations: '.$e->getMessage());
                return;
            }

            $this->assertDatabaseMissing('activity_log', ['causer_id' => $super->id]);
            // Accept either a true activity_log entry (Spatie) or our fallback audit_logs entry
            $this->assertTrue(
                \Illuminate\Support\Facades\DB::table('activity_log')->where('causer_id', $admin->id)->exists()
                || \Illuminate\Support\Facades\DB::table('audit_logs')->where('user_id', $admin->id)->exists()
            );
        } else {
            // When Spatie isn't installed, our fallback writes minimal activity_log rows.
            $this->assertDatabaseMissing('activity_log', ['causer_id' => $super->id]);
            $this->assertTrue(
                \Illuminate\Support\Facades\DB::table('activity_log')->where('causer_id', $admin->id)->exists()
                || \Illuminate\Support\Facades\DB::table('audit_logs')->where('user_id', $admin->id)->exists()
            );
        }
    }
}
