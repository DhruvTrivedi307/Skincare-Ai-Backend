<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $fillable = [
        'type',
        'price',
        'duration',
        'scans',
        'tokens',
    ];
}
