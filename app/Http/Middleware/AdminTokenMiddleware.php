<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('auth');

        if (!$token) {
            Log::warning('AdminTokenMiddleware: Missing token', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);

            $user_id = User::where('token', $token)->value('id');
            
            ActivityLog::create([
                'user_id' => $user_id ?? null,
                'ip_address' => $request->ip(),
                'activity' => 'AdminTokenMiddleware: Missing token in request header',
                'throttle_key' => $request->ip(),
            ]);

            return response()->json([
                'error' => true,
                'title' => 'Missing token',
                'message' => "Missing token. Try again with a valid token."
            ], 400);
        }

        $user = User::where('token', $token)->first();

        if (!$user) {
            Log::warning('AdminTokenMiddleware: Invalid token', [
                'ip' => $request->ip(),
                'token' => $token,
                'url' => $request->fullUrl()
            ]);

            $user_id = User::where('token', $token)->value('id');
            
            ActivityLog::create([
                'user_id' => $user_id ?? null,
                'ip_address' => $request->ip(),
                'activity' => 'AdminTokenMiddleware: Invalid token in request header',
                'throttle_key' => $request->ip(),
            ]);

            return response()->json([
                'error' => true,
                'title' => 'Invalid token',
                'message' => "Invalid token. Try again with a valid token."
            ], 400);
        }

        $request->attributes->set('admin_user', $user);

        Log::info('AdminTokenMiddleware: Authenticated request', [
            'ip' => $request->ip(),
            'user_id' => $user->id,
            'url' => $request->fullUrl()
        ]);

        // ActivityLog::create([
        //     'user_id' => $user->id,
        //     'ip_address' => $request->ip(),
        //     'activity' => 'AdminTokenMiddleware: Authenticated request',
        //     'throttle_key' => $request->ip(),
        // ]);

        return $next($request);
    }
}
