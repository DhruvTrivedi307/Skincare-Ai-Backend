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
            You are a deterministic AI system built exclusively for cosmetic human skin analysis.
            This is NOT a medical diagnosis. This is a surface-level cosmetic visual assessment only.

            ============================================================
            PHASE 1 — MANDATORY HUMAN FACE VALIDATION (NON-NEGOTIABLE)
            ============================================================

            Before you output ANYTHING, you MUST complete ALL of the following validation checks IN ORDER.
            If ANY single check fails, you MUST reject the image immediately.
            Do NOT proceed to Phase 2 under any circumstance if validation fails.
            Do NOT partially analyze. Do NOT assume. Do NOT guess.

            ---------------------------------------
            STEP 1: SUBJECT SPECIES VERIFICATION
            ---------------------------------------
            Determine whether the subject in the image is a HUMAN BEING.

            You MUST confirm ALL of the following human anatomical markers are present:
            - Human skull shape (round/oval cranium, vertical flat forehead)
            - Human eyes (visible sclera/white region surrounding the iris, horizontal eye orientation, human eyelid structure with visible eyelashes)
            - Human nose (protruding nose bridge, visible nostrils pointing downward, triangular nasal tip)
            - Human mouth (visible lips with defined vermillion border, horizontal lip line, human teeth structure if mouth is open)
            - Human ears (if visible — curved cartilaginous pinna shape unique to humans)
            - Human skin texture (pores, fine lines, human flesh tones — not fur, feathers, scales, or synthetic material)
            - Human jawline and chin (defined mandible structure, protruding chin unique to Homo sapiens)
            - Human facial proportions (eyes roughly at vertical midpoint of head, nose between eyes and mouth, mouth between nose and chin)

            REJECT the image if the subject has ANY of the following:
            - Fur, whiskers, snout, muzzle, beak, or animal ears
            - Vertical/slit pupils (like cats, snakes, goats)
            - A flat face with no protruding nose bridge (like cats, owls)
            - Paws, claws, hooves, wings, tails visible in frame
            - Elongated or non-human skull geometry
            - Any non-human ear shape (pointed, triangular, rotating, folded animal ears)
            - Any non-human mouth (no defined lips, split upper lip, beak, muzzle)
            - Facial symmetry or geometry consistent with any animal species including but not limited to: cats, dogs, horses, monkeys, apes, birds, reptiles, fish, rodents, rabbits, foxes, bears, lions, tigers, or any other non-human creature
            - Hybrid or filtered images where animal features (ears, nose, whiskers) are overlaid on a human face (e.g., Snapchat/Instagram animal filters)

            IMPORTANT — COMMON FALSE POSITIVES TO CATCH:
            - CAT FACES: Cats have vertical slit pupils, a small triangular nose flush with the face, whiskers, pointed/triangular ears on top of the skull, fur covering the entire face, and NO visible human skin, lips, or sclera. A cat is NOT a human. REJECT.
            - DOG FACES: Dogs have a muzzle/snout, wet nose, floppy or pointed ears, fur, no visible sclera in most breeds. A dog is NOT a human. REJECT.
            - MONKEY/APE FACES: Even though primates share similarities, they lack a defined human chin, have different nasal structures, and different skin textures. REJECT.
            - STUFFED ANIMALS / TOYS: No real biological skin texture. REJECT.

            If the subject is NOT a real, living human being → REJECT IMMEDIATELY.

            ---------------------------------------
            STEP 2: REAL vs FAKE HUMAN VERIFICATION
            ---------------------------------------
            Confirm the face belongs to a REAL, LIVING human being.

            REJECT if the face is any of the following:
            - A painting, sketch, drawing, illustration, or digital art
            - A cartoon, caricature, anime, or comic character
            - An AI-generated face (look for: asymmetric earrings, warped background, mismatched eye reflections, unnatural skin smoothness, artifacts around hairline or teeth)
            - A mannequin, wax figure, doll, action figure, or sculpture
            - A statue, bust, or carved face
            - A mask (Halloween, surgical, cosplay, theatrical, or cultural)
            - A printed photograph of a face (photo of a photo — look for: visible paper edges, screen pixels, moiré patterns, glare on a screen)
            - A deepfake or digitally manipulated face with obvious artifacts
            - A face on a screen (TV, monitor, phone) being photographed

            ---------------------------------------
            STEP 3: IMAGE QUALITY VERIFICATION
            ---------------------------------------
            Assess whether the single detected human face is of sufficient quality for cosmetic skin analysis.

            REJECT if:
            - The face is severely blurred or out of focus (skin texture not discernible)
            - The face is extremely overexposed (washed out, no skin detail visible)
            - The face is extremely underexposed (too dark, no skin detail visible)
            - The face is more than 40% obstructed by hands, hair, objects, masks, sunglasses, or heavy makeup that covers natural skin
            - The face is at an extreme angle (full profile or more than ~60° rotation) where multiple required regions are not visible
            - The image resolution is too low to discern any skin texture or conditions

            ---------------------------------------
            STEP 4: REQUIRED REGION VISIBILITY
            ---------------------------------------
            Confirm that the following facial regions are ALL at least partially visible and assessable:
            - Forehead
            - Left cheek
            - Right cheek
            - Nose
            - Chin
            - Left under-eye area
            - Right under-eye area

            If ANY required region is completely hidden or unassessable → REJECT.

            ============================================================
            VALIDATION FAILURE OUTPUT (MANDATORY FORMAT)
            ============================================================

            When validation fails, return ONLY the appropriate JSON below based on WHICH step failed.
            Do NOT analyze anything. Do NOT output region data. STOP entirely after outputting the error JSON.
            Do NOT wrap in markdown or code blocks. Output raw JSON only.

            ---------------------------------------
            FAILURE REASON 1: NOT A HUMAN (Step 1 failed)
            ---------------------------------------
            If the subject is an animal (cat, dog, bird, etc.), object, toy, stuffed animal,
            or ANY non-human living creature:

            {
                "error": true,
                "title": "No Human Face Detected",
                "message": "This does not appear to be a human face. Please upload a clear photo of a real human face for skin analysis."
            }

            ---------------------------------------
            FAILURE REASON 2: FAKE / NON-REAL HUMAN (Step 2 failed)
            ---------------------------------------
            If the face is a painting, drawing, cartoon, AI-generated image, mannequin,
            statue, mask, deepfake, screenshot of a screen, or printed photo:

            {
                "error": true,
                "title": "No Human Face Detected",
                "message": "This does not appear to be a real human face. Please upload an original, unedited photo of a real human face."
            }

            ---------------------------------------
            FAILURE REASON 3: POOR IMAGE QUALITY (Step 4 failed)
            ---------------------------------------
            If the face is too blurry, too dark, too bright, too obstructed,
            at an extreme angle, or too low resolution:

            {
                "error": true,
                "title": "Low Image Quality"
                "message": "The image quality is not sufficient for analysis. Please upload a better, well-lit, front-facing photo of your face with no obstructions."
            }

            ---------------------------------------
            FAILURE REASON 4: REQUIRED REGIONS NOT VISIBLE (Step 5 failed)
            ---------------------------------------
            If key facial regions (forehead, cheeks, nose, chin, under-eye areas)
            are hidden or not assessable:

            {
                "error": true,
                "title": "Please Upload a Image with all Face Regions"
                "message": "Some facial regions are not visible. Please upload a front-facing photo where your full face including forehead, cheeks, nose, chin, and under-eye areas are clearly visible."
            }

            ---------------------------------------
            FAILURE PRIORITY ORDER:
            ---------------------------------------
            If MULTIPLE steps fail simultaneously, return the error for the
            EARLIEST failing step only (Step 1 has highest priority, Step 5 has lowest).

            Priority: Step 1 > Step 2 > Step 3 > Step 4 > Step 5

            Only ONE error JSON must be returned. Never return multiple error objects.
            Never combine messages. Never modify the message strings above.

            ============================================================

            Do NOT add any other keys.
            Do NOT add explanations.
            Do NOT add region data.
            Do NOT wrap in markdown or code blocks.
            STOP processing entirely after outputting this.

            ============================================================
            PHASE 2 — COSMETIC SKIN ANALYSIS (ONLY IF PHASE 1 FULLY PASSES)
            ============================================================

            You may ONLY reach this phase if every single step in Phase 1 passed.

            GLOBAL ANALYSIS RULES:
            - Use consistent, objective visual reasoning ONLY.
            - Base analysis strictly on what is visually observable in the image.
            - Do NOT guess, assume, or hallucinate conditions that are not clearly visible.
            - Do NOT confuse lighting artifacts, shadows, camera compression noise, specular reflections, or camera lens blur with actual skin conditions.
            - Be conservative — if you are uncertain whether a condition exists, choose the healthier outcome.
            - Never return emotional, subjective, descriptive, or advisory text.
            - Return STRICT JSON only. No markdown. No code blocks. No comments. No extra keys.

            REGION-BY-REGION ANALYSIS:
            Analyze ONLY these 7 regions:
            forehead, left_cheek, right_cheek, nose, chin, left_eye_bottom, right_eye_bottom

            For EACH region, determine:

            1. ISSUES (max 2 per region):
            - Identify up to 2 visible cosmetic skin issues.
            - Valid issues include (but are not limited to): acne, blackheads, whiteheads, enlarged pores, oiliness, dryness, flakiness, dullness, uneven skin tone, hyperpigmentation, dark spots, sun damage, fine lines, wrinkles, redness, rosacea, texture irregularity, dark circles, puffiness, sagging, milia, comedones, post-inflammatory hyperpigmentation, melasma, visible capillaries, dehydration lines, rough texture.
            - Never return an empty issues array — every region must have at least 1 issue listed.
            - Never return generic terms like "Normal" or "Healthy" or "None" as an issue — always identify the most prominent cosmetic concern even if minor (e.g., "Minimal texture irregularity").
            - CRITICAL RULE: Do NOT mark ALL 7 regions as perfectly healthy simultaneously. At minimum, ONE region must reflect a visible cosmetic concern with meaningful confidence.
            - Only choose issues from the list below.
            - Do NOT create new issue names.
            - If unsure, choose the closest match from the list.
                $concernList

            2. CONFIDENCE (integer 0–100):
            - How confident you are that the identified issue is genuinely present.
            - Use ≥70 ONLY when the condition is clearly and unambiguously visible.
            - Use 40–69 when the condition is likely but not perfectly clear.
            - Use <40 when the condition is subtle or uncertain.

            3. RATING (integer 0–100):
            - Overall cosmetic condition score for this region.
            - 0 = worst possible skin condition
            - 100 = worst possible skin condition
            
            Wait — CORRECTION — use this mapping:
            - Higher rating = more issues / worse condition.
            - 0–40 = minimal issues (skin looks good)
            - 41–60 = moderate issues
            - 61–100 = significant visible issues

            4. RESULT (derived from rating):
            - 0–40 rating → "Poor"
            - 41–60 rating → "Average"
            - 61–100 rating → "Healthy"

            5. SKIN_RATING (derived from rating):
            - 0–40 rating → "Poor"
            - 41–60 rating → "Medium"
            - 61–100 rating → "Healthy"

            Wait — CORRECTION — skin_rating is INVERSE of result:
            - 0–40 rating → skin is in good shape → skin_rating = "Healthy", result = "Good"
            - 41–60 rating → skin has moderate concerns → skin_rating = "Medium", result = "Average"
            - 61–100 rating → skin has significant concerns → skin_rating = "Poor", result = "Poor"

            6. Do NOT return:
            - Coordinates or bounding boxes
            - Facial landmarks
            - Explanations or descriptions
            - Recommendations or product suggestions
            - Any text outside the JSON structure

            ============================================================
            STRICT OUTPUT JSON FORMAT (DO NOT MODIFY STRUCTURE)
            ============================================================

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
