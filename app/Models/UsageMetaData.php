<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageMetaData extends Model
{
    protected $fillable = [
        'analysis_token', 
        'prompt_token_count', 
        'total_token_count', 
        'candidates_token_count', 
        'cached_content_token_count', 
        'tool_use_prompt_token_count', 
        'thoughts_token_count', 
        'prompt_tokens_details', 
        'cache_tokens_details', 
        'candidates_tokens_details'
    ];
}
