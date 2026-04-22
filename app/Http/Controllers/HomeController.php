<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Services\WooCommerceService;

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
    public function home()
    {
        return view('dashboard.mobile-menu');
    }

    public function inventory(Request $request){
    

         $page = $request->get('page', 1);

    $response = Http::withBasicAuth(
        config('services.woocommerce.key'),
        config('services.woocommerce.secret')
    )->get(config('services.woocommerce.url') . '/wp-json/wc/v3/products', [
        'per_page' => 20,
        'page' => $page,
         'status' => 'publish',
         
    ]);

    // $response
    // $wc = new WooCommerceService;

    //  $response = $wc->get(
    //         "/wp-json/wc/v3/products",
    //         [
    //             'query' => [
    //                 'per_page' => 20,
    //     'page' => $page,
    //      'status' => 'publish'
    //             ]
    //         ]
    //     );

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
                ['per_page' => 20]
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

    public function addProduct(){
        return view('dashboard.products.add-new');
    }
    public function dispatchStock(){
        return view('dashboard.dispatch-stock');
    }
    public function logs(){
        $logs = StockTransaction::latest()->paginate(50);
        return view('dashboard.log', compact('logs'));
    }

    public function perOrderDispatch(){
        return view('dashboard.inventory.dispatch-per-item');
    }

    public function showProduct($id){

        $product = Product::findOrFail($id);

        return view('dashboard.inventory.show-product', compact(['product']));
    }

    public function mobileScanDispatch(){
        
        return view('dashboard.mobile-dispatch-stock');
    }
    
    public function mobileScanAdd(){
        
        return view('dashboard.mobile-add-stock');
    }

}
