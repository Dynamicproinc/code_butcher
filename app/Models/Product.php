<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $casts = [
        'levels' => 'array',
    ];

    public function variations(){
       return $this->hasMany(ProductVariation::class);
    }
}
