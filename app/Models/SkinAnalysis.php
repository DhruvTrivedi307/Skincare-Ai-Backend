<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinAnalysis extends Model
{
    protected $fillable = ['ip_address', 'user_token', 'admin_id' ,'image_path', 'result', 'status'];
    protected $casts = [
        'result' => 'array',
    ];
}
