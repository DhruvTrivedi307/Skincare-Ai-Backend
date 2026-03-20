<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeJob;
use App\Models\ActivityLog;
use App\Models\Products;
use App\Models\SkinAnalysis;
use App\Models\SkinConcerns;
use App\Models\User;
use Illuminate\Http\Request;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GeminiAuthController extends Controller
{

    public function analyze(Request $request)
    {
        // if ($response = $this->ensureIsNotRateLimited($request)) {
        //     return $response;
        // }
        // RateLimiter::hit($this->throttleKey($request), 60);


        Log::info('Analyze request received', ['ip' => $request->ip()]);

        Log::info('Request data', ['ip' => $request->ip(), 'data' => $request->all()]);

        Log::info('Incoming files', [
            'has_file' => $request->hasFile('image'),
            'files' => $request->allFiles()
        ]);

        // $request->validate([
        //     'image' => 'required|image|max:5120',
        // ]);

        // Log::info('Image validation passed');

        $file = $request->file('image');

        if (!$file) {
            Log::error('Image upload missing or invalid', [
                'has_file' => $request->hasFile('image'),
                'all_files' => $request->allFiles()
            ]);

            return response()->json([
                'status' => 'failed',
                'error' => true,
                'title' => 'Image Upload Failed',
                'message' => 'No valid image file was received. Please upload the image again.'
            ], 400);
        }

        Log::info('Image file detected', [
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size_kb' => round($file->getSize() / 1024, 2)
        ]);

        $path = $file->store('uploads', 'public');
        Log::info('Image stored successfully', ['path' => $path]);

        $filePath = storage_path('app/public/' . $path);
        Log::info('Resolved file path', ['filePath' => $filePath]);

        // $ip = $request->ip();
        // $user_ip = SkinAnalysis::inRandomOrder()->where('ip_address', $ip)->first();

        // $result = is_string($user_ip->result) ? json_decode($user_ip->result, true) : $user_ip->result;

        // $regions = ['forehead', 'left_cheek', 'right_cheek', 'nose', 'left_eye_bottom', 'right_eye_bottom', 'chin'];

        // $issues = [];

        // foreach ($regions as $region) {
        //     if (isset($result[$region]['issue'])) {
        //         $issues[] = $result[$region]['issue'];
        //     }
        // }

        // $merged = collect($issues)->flatten()->unique()->map(fn($item) => ucwords(strtolower($item)))->values()->toArray();

        // Log::info('Extracted Issues', [$merged]);

        // $concern_ids = SkinConcerns::all()->whereIn('concern', $merged)->pluck('id');

        // Log::info('Concern IDs', [$concern_ids]);

        // $products = DB::table('product_concerns')
        //     ->whereIn('concern_id', $concern_ids)
        //     ->join('products', 'products.id', '=', 'product_concerns.product_id')
        //     ->select('products.*')
        //     ->distinct()
        //     ->get();

        // Log::info('Recommended Products', [$products]);

        // if ($user_ip) {
        //     return response()->json([
        //         'status' => 'completed',
        //         'data' => is_string($user_ip->result)
        //             ? json_decode($user_ip->result, true)
        //             : $user_ip->result,
        //         'products' => $products
        //     ]);
        // }

        // $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
        //     ->generateContent([
        //         $prompt,
        //         new \Gemini\Data\Blob(
        //             mimeType: \Gemini\Enums\MimeType::from($file->getMimeType()),
        //             data: $image
        //         )
        //     ]);

        // AnalyzeJob::dispatch($path);


        $id = User::where('token', $request->header("auth"))->value('id');

        $analysis = SkinAnalysis::create([
            'ip_address' => $request->ip(),
            'user_token' => $request->input("token"),
            'admin_id' => $id,
            'image_path' => $path,
            'status' => 'processing'
        ]);

        AnalyzeJob::dispatch($analysis->id, $path);

        Log::info('AnalyzeJob dispatched', [
            'analysis_id' => $analysis->id,
            'path' => $path
        ]);

        // RateLimiter::clear($this->throttleKey($request));

        // return response()->json([
        //     "analysis_id" => $analysis->id,
        //     "status" => "processing"
        // ], 202);


        return $this->result($analysis->id);

        // $response = trim($result->text());

        // SkinAnalysis::create([
        //     'ip_address' => $request->getClientIp(),
        //     'image_path' => $filePath,
        //     'result' => $response,
        //     'status' => 'completed'
        // ]);

        // return response()->json(
        //     json_decode($response, true),
        //     200,
        //     [],
        //     JSON_THROW_ON_ERROR
        // );
    }

    public function result($id)
    {
        // $analysis = SkinAnalysis::find($id);
        // $count = 0;

        // if (!$analysis) {
        //     return response()->json([
        //         'status' => 'not_found'
        //     ], 404);
        // }

        // if ($analysis->status === 'processing') {
        //     return response()->json([
        //         'status' => 'processing'
        //     ]);
        // }

        // if ($analysis->status === 'failed') {
        //     return response()->json([
        //         'status' => 'failed'
        //     ]);
        // }

        // if ($analysis->status === 'completed') {
        //     $decoded = is_string($analysis->result)
        //         ? json_decode($analysis->result, true)
        //         : $analysis->result;

        //     if (is_array($decoded) && isset($decoded['error']) && $decoded['error'] === true) {
        //         ActivityLog::create([
        //             'user_id' => 0,
        //             'activity' => 'Error - ' . ($decoded['message'] ?? 'Unknown error'),
        //             'ip_address' => $request->ip(),
        //             'throttle_key' => $request->ip()
        //         ]);
        //     }
        // }

        // return response()->json([
        //     'status' => 'completed',
        //     'data' => is_string($analysis->result)
        //         ? json_decode($analysis->result, true)
        //         : $analysis->result
        // ]);


        $timeout = 30;
        $start = time();

        $token = request()->header("auth");

        if (!$token) {
            return response()->json([
                'error' => true,
                'title' => 'Missing token',
                'message' => "Missing token. Try again with a valid token."
            ], 400);
        }

        while (time() - $start < $timeout) {
            $analysis = SkinAnalysis::find($id);

            if ($analysis->status === "failed") {
                return response()->json([
                    "status" => "failed"
                ]);
            }

            if ($analysis->status === "completed") {

                $result = is_string($analysis->result) ? json_decode($analysis->result, true) : $analysis->result;

                $regions = ['forehead', 'left_cheek', 'right_cheek', 'nose', 'left_eye_bottom', 'right_eye_bottom', 'chin'];

                $issues = [];

                foreach ($regions as $region) {
                    if (isset($result[$region]['issue'])) {
                        $issues[] = $result[$region]['issue'];
                    }
                }

                $merged = collect($issues)->flatten()->unique()->map(fn($item) => ucwords(strtolower($item)))->values()->toArray();

                Log::info('Extracted Issues', [$merged]);

                $concern_ids = SkinConcerns::all()->whereIn('concern', $merged)->pluck('id');

                Log::info('Concern IDs', [$concern_ids]);

                $products = DB::table('product_concerns')
                    ->whereIn('concern_id', $concern_ids)
                    ->join('products', 'products.id', '=', 'product_concerns.product_id')
                    ->select('products.*')
                    ->distinct()
                    ->get();

                Log::info('Recommended Products', [$products]);

                Log::info("Skin Analysis Complete");
                
                return response()->json([
                    'id' => $id,
                    'token' => $token,
                    'status' => 'completed',
                    'data' => $result,
                    'products' => $products
                ]);


            }
            sleep(1);
        }

        return response()->json([
            "status" => "processing"
        ]);
    }
}
