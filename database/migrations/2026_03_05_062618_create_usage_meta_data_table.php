<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_meta_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('analysis_token')->unique();
            $table->integer('prompt_token_count')->nullable();
            $table->integer('total_token_count')->nullable();
            $table->integer('candidates_token_count')->nullable();
            $table->integer('cached_content_token_count')->nullable();
            $table->integer('tool_use_prompt_token_count')->nullable();
            $table->integer('thoughts_token_count')->nullable();
            $table->json('prompt_tokens_details')->nullable();
            $table->json('cache_tokens_details')->nullable();
            $table->json('candidates_tokens_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_meta_data');
    }
};
