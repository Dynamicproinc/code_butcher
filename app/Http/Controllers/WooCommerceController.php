<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class WooCommerceController extends Controller
{
     public function handle(Request $request)
    {
        $order = $request->all();

        foreach ($order['line_items'] as $item) {

            $productId = $item['product_id'];
            $variationId = $item['variation_id'];
            $qty = $item['quantity'];

            $product = Product::where('wc_product_id', $productId)
                ->where('wc_variation_id', $variationId ?: null)
                ->first();

            if ($product) {
                $product->stock -= $qty;
                $product->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
