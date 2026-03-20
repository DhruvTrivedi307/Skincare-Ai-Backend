<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];
}
