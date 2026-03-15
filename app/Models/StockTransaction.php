<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'variation_id',
        'variation_code',
        'wc_stock',
        'quantity',
        'balance',
        'type'
    ];

    public static function addTransaction($product_id, $variation_id, $variation_code, $wc_stock, $quantity, $balance, $type)
    {
        return self::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'variation_code' => $variation_code,
            'wc_stock' => $wc_stock,
            'quantity' => $quantity,
            'balance' => $balance,
            'type' => $type,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getProduct()
    {
        return Product::where('wc_product_id', $this->product_id)->first();
    }

    // public function getVariation(){
    //     return ProductVariation::where('wc_variation_id', $this->)
    // }

    
}
