<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $casts = [
        'levels' => 'array',
    ];
    protected $fillable = [
      'user_id',
    'product_name',
    'product_code',
    'price',
    'quantity',
    'variation',
    'threshold',
    'wc_product_id',
    'status',
];

    public function variations(){
       return $this->hasMany(ProductVariation::class);
    }

    

    
}
