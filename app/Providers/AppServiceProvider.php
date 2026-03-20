<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        RateLimiter::for('ai-analyze', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip() . '|' . $request->userAgent())
                ->response(function (Request $request, array $headers) {

                    $seconds = $headers['Retry-After'] ?? 60;
                    
                    $token = $request->header('auth');
                    $user_id = User::where('token', $token)->value('id');

                    ActivityLog::create([
                        'user_id' => $user_id ?? null,
                        'activity' => 'Rate-limited login attempt - Too many failed login attempts.',
                        'ip_address' => $request->ip(),
                        'throttle_key' => $this->throttleKey($request),
                        'created_at' => now(),
                        'is_read' => 1,
                    ]);

                    return response()->json([
                        'error' => true,
                        'title' => 'Too Many Requests',
                        'message' => "Too many attempts. Try again in {$seconds} seconds."
                    ], 429);
                });
        });
    }

    protected function throttleKey(Request $request)
    {
        return strtolower($request->ip());
    }
}
