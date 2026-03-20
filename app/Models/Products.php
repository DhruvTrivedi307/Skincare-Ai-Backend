<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = ['name', 'description', 'price', 'image'];
    
    public function concerns()
    {
        return $this->belongsToMany(
            \App\Models\SkinConcerns::class,
            'product_concerns',
            'product_id',
            'concern_id'
        );
    }
}
