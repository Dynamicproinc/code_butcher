<?php

namespace App\Services;
use App\Models\StockTransaction;
use GuzzleHttp\Client;


class WooCommerceService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.woocommerce.url'),
            'auth' => [config('services.woocommerce.key'), config('services.woocommerce.secret')]
        ]);
    }

    public function updateStock($productId, $variationId, $stock)
    {
        if ($variationId) {
            $endpoint = "/wp-json/wc/v3/products/$productId/variations/$variationId";
            $current_stock = $this->client->get("/wp-json/wc/v3/products/$productId/variations/$variationId");
        } else {
            $endpoint = "/wp-json/wc/v3/products/$productId";
            $current_stock = $this->client->get("/wp-json/wc/v3/products/$productId");
        }

        $product = json_decode($current_stock->getBody(), true);

        // dd($product->stock_quantity);

        $new_stock = ($product['stock_quantity'] ?? 0) + $stock;



        $response = $this->client->put($endpoint, [
            'json' => [
                'stock_quantity' => $new_stock,
                'manage_stock' => true
            ]
        ]);
        if($variationId){

            $wc_updated =  $this->client->get("/wp-json/wc/v3/products/$productId/variations/$variationId");
            $wc_update_decode = json_decode($wc_updated->getBody(), true);
            $wc_update_stock = $wc_update_decode['stock_quantity'];
        }else{
             $wc_updated =  $this->client->get("/wp-json/wc/v3/products/$productId");
            $wc_update_decode = json_decode($wc_updated->getBody(), true);
            $wc_update_stock = $wc_update_decode['stock_quantity'];
        }
        $type = "IN";
        // update stock transactions
        $st = StockTransaction::addTransaction($productId, $variationId ?? 0,$product['name'],$product['stock_quantity'] ?? 0,$stock, $wc_update_stock, $type );


       
        // $result = json_decode($response->getBody(), true);
    }

    
    public function dispatchStock($productId, $variationId, $stock)
    {
        if ($variationId) {
            $endpoint = "/wp-json/wc/v3/products/$productId/variations/$variationId";
            $current_stock = $this->client->get("/wp-json/wc/v3/products/$productId/variations/$variationId");
        } else {
            $endpoint = "/wp-json/wc/v3/products/$productId";
            $current_stock = $this->client->get("/wp-json/wc/v3/products/$productId");
        }

        $product = json_decode($current_stock->getBody(), true);

        // dd($product->stock_quantity);
        
            // if dispatch stock greater thean webshop stock , webstock must be 0;

                $prd = $product['stock_quantity'] ?? 0;
            if($stock > $prd){
                $new_stock = 0;
            }else{

                $new_stock = $prd - $stock;
            }



        $response = $this->client->put($endpoint, [
            'json' => [
                'stock_quantity' => $new_stock,
                'manage_stock' => true
            ]
        ]);
        if($variationId){

            $wc_updated =  $this->client->get("/wp-json/wc/v3/products/$productId/variations/$variationId");
            $wc_update_decode = json_decode($wc_updated->getBody(), true);
            $wc_update_stock = $wc_update_decode['stock_quantity'];
        }else{
            $wc_updated =  $this->client->get("/wp-json/wc/v3/products/$productId");
            $wc_update_decode = json_decode($wc_updated->getBody(), true);
            $wc_update_stock = $wc_update_decode['stock_quantity']; 
        }
     $type = "OUT";
        // update stock transactions
        $st = StockTransaction::addTransaction($productId, $variationId ?? 0,$product['name'],$product['stock_quantity'] ?? 0,-$stock, $wc_update_stock, $type );


       
        // $result = json_decode($response->getBody(), true);
    }



    public function getVariation($wc_product_id)
    {
        
       if($wc_product_id){
         $variations = $this->client->get(
            "/wp-json/wc/v3/products/$wc_product_id/variations",
            [
                'query' => [
                    'per_page' => 100
                ]
            ]
        );
        return $variations;
       }

       

    }

    public function getVariationQuantity($product_id, $variation_id){
        $variation = $this->client->get("/wp-json/wc/v3/products/$product_id/variations/$variation_id");
        $variation_data = json_decode($variation->getBody(), true);
        return $variation_data['stock_quantity'];
    }


    
}
