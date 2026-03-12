<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard.index');
    }

    public function inventory(Request $request){
    //      $products = Http::withBasicAuth(
    //     config('services.woocommerce.key'),
    //     config('services.woocommerce.secret')
    // )->get(config('services.woocommerce.url').'/wp-json/wc/v3/products')->json();

    // foreach ($products as &$product) {

    //     if ($product['type'] == 'variable') {

    //         $variations = Http::withBasicAuth(
    //             config('services.woocommerce.key'),
    //             config('services.woocommerce.secret')
    //         )->get(config('services.woocommerce.url')."/wp-json/wc/v3/products/".$product['id']."/variations")->json();

    //         $product['variations'] = $variations;
    //     }
    // }

    // return view('dashboard.inventory', compact('products'));
        // return view('dashboard.inventory');

         $page = $request->get('page', 1);

    $response = Http::withBasicAuth(
        config('services.woocommerce.key'),
        config('services.woocommerce.secret')
    )->get(config('services.woocommerce.url') . '/wp-json/wc/v3/products', [
        'per_page' => 20,
        'page' => $page
    ]);

    if ($response->failed()) {
        dd($response->body());
    }

    $products = $response->json();
    $totalPages = $response->header('X-WP-TotalPages');

    foreach ($products as &$product) {

        if ($product['type'] == 'variable') {

            $variations = Http::withBasicAuth(
                config('services.woocommerce.key'),
                config('services.woocommerce.secret')
            )->get(
                config('services.woocommerce.url') . '/wp-json/wc/v3/products/' . $product['id'] . '/variations',
                ['per_page' => 100]
            )->json();

            $product['variations'] = $variations;
        }
    }

    return view('dashboard.inventory', [
        'products' => $products,
        'page' => $page,
        'totalPages' => $totalPages
    ]);
    }

    public function addStock(){
        return view('dashboard.add-stock');
    }

    public function newStock(){
        return view('dashboard.new-stock');
    }

    public function localInventory(){
        $products = Product::paginate(10);
        return view('dashboard.inventory.local-inventory', compact('products'));
    }
}
