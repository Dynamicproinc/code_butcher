<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Models\ProcessedOrder;
use Illuminate\Support\Facades\DB;


class WooCommerceController extends Controller
{
   public function handle(Request $request)
{
    return response()->json(['status'=>'ok']);
    
    $order = $request->all();

    if (!isset($order['line_items'])) {
        return response()->json(['message'=>'No items']);
    }

    $orderId = $order['id'];

    if (ProcessedOrder::where('wc_order_id',$orderId)->exists()) {
        return response()->json(['message'=>'Already processed']);
    }

    DB::transaction(function () use ($order) {

        foreach ($order['line_items'] as $item) {

            $productId = $item['product_id'];
            $variationId = $item['variation_id'];
            $qty = $item['quantity'];

            $product = ProductVariation::where('wc_product_id', $productId)
                ->where('wc_variation_id', $variationId ?: null)
                ->first();

            if ($product) {

                $product->stock -= $qty;
                $product->save();

            }
        }

        ProcessedOrder::create([
            'wc_order_id'=>$order['id']
        ]);

    });

    return response()->json(['status'=>'ok']);
}
}
