<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\WooCommerceController;
use GuzzleHttp\Client;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/dashboard/test', 'dashboard.test')->name('dashboard.test');
// Route::livewire('/dashboard/add-stock', 'dashboard.add_stock')->name('dashboard.add-stock');
// Route::livewire('/dashboard/inventory', 'dashboard.inventory')->name('dashboard.inventory');
// Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/testing-wc', function (){
//  $url = env('WC_URL') . '/wp-json/wc/v3/products';

//    $response = Http::withBasicAuth(
//         config('services.woocommerce.key'),
//         config('services.woocommerce.secret')
//     )->get($url);

//     $products = $response->json();

//      return view('testing.test', compact('products'));

$url = config('services.woocommerce.url') . '/wp-json/wc/v3/products/variations';

    $response = Http::withBasicAuth(
        config('services.woocommerce.key'),
        config('services.woocommerce.secret')
    )->get($url, [
        'per_page' => 100
    ]);

    return $response->json();
});

Route::get('/testing-wc-variation', function (){

$url = config('services.woocommerce.url');

  $client = new Client();

$productId = 187;
$variationId = 486;
$newStock = 8;

$response = $client->request('PUT', $url."/wp-json/wc/v3/products/$productId/variations/$variationId", [
    'auth' => [config('services.woocommerce.key'), config('services.woocommerce.secret')],
    'json' => [
        'stock_quantity' => $newStock,
        'manage_stock' => true
    ]
]);

$result = json_decode($response->getBody(), true);
});

Route::get('dashboard/inventory',[HomeController::class, 'inventory'])->name('dashboard.inventory');
Route::get('dashboard/add-stock',[HomeController::class, 'addStock'])->name('dashboard.add-stock');
Route::get('dashboard/new-stock',[HomeController::class, 'newStock'])->name('dashboard.new-stock');
Route::get('dashboard/local-inventory',[HomeController::class, 'localInventory'])->name('dashboard.local-inventory');
Route::get('dashboard/product/add-product',[HomeController::class, 'addProduct'])->name('dashboard.product.add-product');
Route::get('dashboard/dispatch-stock',[HomeController::class, 'dispatchStock'])->name('dashboard.product.dispatch-stcok');
Route::get('dashboard/dispatch-stock-per-item',[HomeController::class, 'perOrderDispatch'])->name('dashboard.product.dispatch-per-item');
Route::get('dashboard/logs',[HomeController::class, 'logs'])->name('dashboard.logs');
Route::get('dashboard/product/{id}',[HomeController::class, 'showProduct'])->name('dashboard.product.show-product');
Route::get('dashboard/mobile-scan',[HomeController::class, 'mobileScan'])->name('dashboard.product.mobile-scan');

Route::get('/abc123', function () {
    Artisan::call('migrate', ['--force' => true]);

    return response()->json([
        'status' => 'Migration completed'
    ]);
});
