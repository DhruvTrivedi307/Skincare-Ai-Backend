<?php

namespace App\Jobs;

use App\Models\SkinAnalysis;
use App\Models\SkinConcerns;
use App\Models\UsageMetaData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;

class AnalyzeJob implements ShouldQueue
{
    use Queueable;

    public int $analysisId;
    public string $imagePath;
    public $tries = 3;
    public $timeout = 120;

    public function __construct(int $analysisId, string $imagePath)
    {
        $this->analysisId = $analysisId;
        $this->imagePath = $imagePath;
    }

    /**
     * Create a new job instance.
     */
    public function handle(): void
    {
        Log::info('AnalyzeJob started', ['imagePath' => $this->imagePath]);

        $filePath = storage_path("app/public/" . $this->imagePath);

        Log::info('Resolved storage file path', ['filePath' => $filePath]);

        if (!file_exists($filePath)) {
            Log::error('AnalyzeJob: Image not found', ['path' => $filePath]);
            Log::error('AnalyzeJob aborted because file does not exist');
            return;
        }

        $image = file_get_contents($filePath);

        Log::info('Image loaded from disk', [
            'size_bytes' => filesize($filePath)
        ]);

        $issues = SkinConcerns::all();
        $concernList = $issues->pluck('concern')->implode(', ');

        $prompt = <<<PROMPT
            You are a deterministic AI for cosmetic skin analysis (NOT medical).

            ========================
            PHASE 1 — VALIDATION
            ========================

            Reject immediately if ANY fails. No analysis if rejected.

            1. HUMAN CHECK  
            Must have:
            - Human face structure (eyes with sclera, nose bridge, lips, chin, skin texture)
            Reject if:
            - Animal features, filters, fur, non-human geometry

            2. REAL FACE CHECK  
            Reject if:
            - Cartoon, drawing, AI-generated, mannequin, mask, statue, screen, printed photo

            3. QUALITY CHECK  
            Reject if:
            - Blurry, dark, overexposed, obstructed (>40%), extreme angle, low resolution

            4. REGION VISIBILITY  
            Must see:
            - Forehead, cheeks, nose, chin, under-eyes  
            Else reject

            ========================
            ERROR OUTPUT (STRICT JSON)
            ========================

            Return ONLY one:

            NOT HUMAN:
            {"error":true,"title":"No Human Face Detected","message":"This does not appear to be a human face. Please upload a clear photo of a real human face for skin analysis."}

            NOT REAL:
            {"error":true,"title":"No Human Face Detected","message":"This does not appear to be a real human face. Please upload an original, unedited photo of a real human face."}

            LOW QUALITY:
            {"error":true,"title":"Low Image Quality","message":"The image quality is not sufficient for analysis. Please upload a better, well-lit, front-facing photo of your face with no obstructions."}

            MISSING REGIONS:
            {"error":true,"title":"Please Upload a Image with all Face Regions","message":"Some facial regions are not visible. Please upload a front-facing photo where your full face including forehead, cheeks, nose, chin, and under-eye areas are clearly visible."}

            Priority: Human > Real > Quality > Regions  
            Return ONE only.

            ========================
            PHASE 2 — ANALYSIS
            ========================

            Only if validation passes.

            Rules:
            - No guessing
            - No explanations
            - No extra keys
            - JSON only

            Regions:
            forehead, left_cheek, right_cheek, nose, chin, left_eye_bottom, right_eye_bottom

            For EACH region:
            - issue (1–2 items, must choose from list below)
            - confidence (0–100)
            - rating (0–100, higher = worse)
            - skin_rating:
                0–40 → Healthy
                41–60 → Medium
                61–100 → Poor
            - result:
                0–40 → Good
                41–60 → Average
                61–100 → Poor

            Rules:
            - At least 1 issue per region
            - Do NOT mark all regions perfect
            - Use only these issues:
            $concernList

            ========================
            OUTPUT FORMAT
            ========================

            {
                "forehead":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "left_cheek":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "right_cheek":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "nose":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "chin":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "left_eye_bottom":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                },
                "right_eye_bottom":{
                    "issue":["string"],
                    "confidence":0,
                    "rating":0,
                    "skin_rating":"Healthy | Medium | Poor",
                    "result":"Good | Average | Poor"
                }
            }
        PROMPT;

        try {

            $mimeType = mime_content_type($filePath);

            Log::info('Sending request to Gemini', [
                'model' => 'gemini-2.5-flash',
                'mimeType' => $mimeType
            ]);

            $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->generateContent([
                    $prompt,
                    new Blob(
                        mimeType: MimeType::from($mimeType),
                        data: base64_encode(file_get_contents($filePath))
                    )
                ]);

            Log::info('Gemini response received', [
                'response_preview' => $result->usageMetadata ?? null
            ]);

            $meta_data = UsageMetaData::create([
                'analysis_token' => $this->analysisId,
                'prompt_token_count' => $result->usageMetadata->promptTokenCount ?? null,
                'total_token_count' => $result->usageMetadata->totalTokenCount ?? null,
                'candidates_token_count' => $result->usageMetadata->candidatesTokenCount ?? null,
                'cached_content_token_count' => $result->usageMetadata->cachedContentTokenCount ?? null,
                'tool_use_prompt_token_count' => $result->usageMetadata->toolUsePromptTokenCount ?? null,
                'thoughts_token_count' => $result->usageMetadata->thoughtsTokenCount ?? null,
                'prompt_tokens_details' => json_encode($result->usageMetadata->promptTokensDetails ?? []),
                'cache_tokens_details' => json_encode($result->usageMetadata->cacheTokensDetails ?? []),
                'candidates_tokens_details' => json_encode($result->usageMetadata->candidatesTokensDetails ?? [])
            ]);

            Log::info('Updated usage metadata', [
                'analysis' => $meta_data->toArray()
            ]);

            $response = trim($result->text());

            $response = preg_replace('/```json|```/', '', $response);
            $response = trim($response);

            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');

            $response = iconv('UTF-8', 'UTF-8//IGNORE', $response);

            Log::info('Raw Gemini response received', [
                'preview' => $response
            ]);

            $decoded = json_decode($response, true);

            $failed = [
                "error" => true,
                "title" => "Analysis Failed",
                "message" => "Failed to get response from analysis engine."
            ];

            if (json_last_error() !== JSON_ERROR_NONE) {

                Log::error('Invalid JSON from Gemini', [
                    'error' => json_last_error_msg(),
                    'response' => $response
                ]);

                SkinAnalysis::where('id', $this->analysisId)
                    ->update(['status' => 'failed', 'result' => $failed]);

                return;
            }

            SkinAnalysis::where('id', $this->analysisId)
                ->update([
                    'result' => $decoded,
                    'token_usage' => $result->usageMetadata->totalTokenCount ?? null,
                    'status' => 'completed'
                ]);

            $data = SkinAnalysis::find($this->analysisId);

            Log::info('Updated analysis record', [
                'analysis' => $data
            ]);

            Log::info('AnalyzeJob completed', [
                'analysis_id' => $this->analysisId
            ]);
        } catch (\Throwable $e) {

            Log::error('AnalyzeJob failed', [
                'analysis_id' => $this->analysisId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $failed = [
                "error" => true,
                "title" => "Analysis Failed",
                "message" => "Failed to get response from analysis engine."
            ];

            SkinAnalysis::where('id', $this->analysisId)
                ->update(['status' => 'failed', 'result' => $failed]);
        }
    }
}
