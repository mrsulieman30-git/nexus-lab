<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Log;

class AuditRequest
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // If Spatie activitylog exists, avoid duplicating logs from this middleware
            if (class_exists(SpatieActivity::class)) {
                return $response;
            }

            $user = Auth::user();

            if ($user && data_get($user, 'role') === 'super_admin') {
                return $response;
            }

            // Only audit authenticated users
            if ($user) {
                $data = [
                    'user_id' => $user->id,
                    'user_role' => $user->role ?? null,
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'payload' => json_encode($this->filterPayload($request->except(['password', 'password_confirmation']))),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Use direct DB insert to avoid model-level surprises during tests
                try {
                    DB::table('audit_logs')->insert($data);

                    // Also insert a minimal activity_log row when Spatie isn't present.
                    if (! class_exists('\\Spatie\\Activitylog\\Models\\Activity')) {
                        try {
                            DB::table('activity_log')->insert([
                                'log_name' => 'default',
                                'description' => 'Visited '.$request->path(),
                                'subject_type' => null,
                                'subject_id' => null,
                                'causer_type' => 'App\\Models\\User',
                                'causer_id' => $user->id,
                                'properties' => json_encode(['payload' => $request->except(['password', 'password_confirmation'])]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            @file_put_contents(storage_path('logs/activity_debug.log'), "AUDIT_MIDDLEWARE_ACTIVITY_INSERT: user={$user->id},path={$request->path()}\n", FILE_APPEND);
                        } catch (\Throwable $e) {
                            @file_put_contents(storage_path('logs/activity_debug.log'), "AUDIT_MIDDLEWARE_ACTIVITY_ERROR: user={$user->id},err=". $e->getMessage() ."\n", FILE_APPEND);
                            // ignore activity_log insert failure
                        }
                    }

                    @file_put_contents(storage_path('logs/audit_debug.log'), "AUDIT_INSERTED: user={$user->id},role={$user->role}\n", FILE_APPEND);
                } catch (\Throwable $e) {
                    @file_put_contents(storage_path('logs/audit_debug.log'), "AUDIT_ERROR: user={$user->id},role={$user->role},err=". $e->getMessage() ."\n", FILE_APPEND);
                    throw $e;
                }
            }
        } catch (\Throwable $e) {
            if (AppFacade::runningUnitTests()) {
                throw $e;
            }

            Log::error('AuditRequest failed: '.$e->getMessage());
        }

        return $response;
    }

    protected function filterPayload(array $payload): array
    {
        array_walk_recursive($payload, function (&$value) {
            if (is_string($value) && strlen($value) > 1000) {
                $value = substr($value, 0, 1000);
            }
        });

        return $payload;
    }
}
