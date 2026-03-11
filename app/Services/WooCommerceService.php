<?php
namespace App\Services;
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
                'stock_quantity' =>$new_stock,
                'manage_stock' => true
            ]
        ]);

        // /


        // $url = config('services.woocommerce.url');

        // $client = new Client();

        // $response = $client->request('PUT', $url . "/wp-json/wc/v3/products/$product_id/variations/$variation_id", [
        //     'auth' => [config('services.woocommerce.key'), config('services.woocommerce.secret')],
        //     'json' => [
        //         'stock_quantity' => $quantity,
        //         'manage_stock' => true,
        //     ],
        // ]);

        $result = json_decode($response->getBody(), true);
    }
}