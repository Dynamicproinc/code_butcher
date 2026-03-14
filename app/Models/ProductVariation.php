<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id', 'wc_product_id', 'wc_variation_id', 'attr_name', 'quantity', 'variation_code'
    ];


    public function product(){
        return $this->belongsTo(Product::class);
    }

    
}
