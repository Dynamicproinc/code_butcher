<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductVariation;
use App\Mail\Invoice as InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use GuzzleHttp\Client;
use App\Services\WooCommerceService;

new class extends Component {
    public $barcode;
    public $item_code, $description;
    public $product = [];
    public $error_message;
    public $remark;
    public $customer_id;
    public $customers = [];
    public $log = [];

    public function addItem()
    {
        // $threshold = 20;
        // $last_number = floor($last_number / $threshold) * $threshold;
        // dd($last_number);

        $product_code = substr($this->barcode, 4, 3);
        $weight = substr($this->barcode, 7, 5);
        $weight_in_kg = intval($weight) / 1000;
        // dd($weight_in_kg);

        if ($product = Product::where('product_code', $product_code)->first()) {
            $this->error_message = null;
            $this->product = $product;
            $cartItems = session()->get('cart_items', []);

            // if (!empty($cartItems)) {
            //     if ($cartItems[0]['code'] !== $product->product_code) {
            //         $this->error_message = __('You cannot add different items in manual mode');

            //         return;
            //     }
            // }

            $variation = '0';

            if ($product->variation) {
                $variation = $weight - ($weight % $product->threshold);
                $cartItems = session()->get('cart_items', []);

                $found = false;

                foreach ($cartItems as $key => $item) {
                    if ($item['variation'] == $variation && $item['code'] == $product->product_code) {
                        $cartItems[$key]['quantity'] += 1;
                        $cartItems[$key]['weight'] += $weight_in_kg;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $cartItems[] = [
                        'barcode' => $this->barcode,
                        'code' => $product->product_code,
                        'description' => $product->product_name,
                        'variation' => $variation,
                        'quantity' => 1,
                        'weight' => $weight_in_kg,
                    ];
                }

                session()->put('cart_items', $cartItems);
            } else {
                 $citems = session()->get('cart_items', []);
                $found_item = false;

                foreach($citems as $key => $item){
                    if($item['code'] == $product_code){
                         $citems[$key]['quantity'] += 1;
                        $citems[$key]['weight'] += $weight_in_kg;
                        $found_item = true;
                        break;
                    }
                }

                if(!$found_item){
                     $citems[] = [
                    'barcode' => $this->barcode,
                    'code' => $product->product_code,
                    'description' => $product->product_name,
                    'variation' => $variation,
                    'quantity' => 1,
                    'weight' => $weight_in_kg,
                ];
                }

               
                session()->put('cart_items', $citems);
            }
            // if product has variations
        } else {
            $this->error_message = __('Invalid barcode');
        }

        $this->barcode = '';
    }

    public function removeItem($key)
    {
        $cart_items = session('cart_items', []);
        if (isset($cart_items[$key])) {
            unset($cart_items[$key]);
            session(['cart_items' => array_values($cart_items)]);
        }
    }

    public function clearCart()
    {
        session()->forget('cart_items');
    }

    public function update()
    {
        $cart_items = session()->get('cart_items', []);
         $wc = new WooCommerceService();
        foreach ($cart_items as $item) {
            // dd($item['code']);
            // find the prpoduct
            $product = Product::where('product_code', $item['code'])->first();
            if ($product) {
                // check product has variation
                if ($product->variation) {
                    // dd($product->wc_product_id);
                    // need update variation quantity
                    $v = ProductVariation::where('wc_product_id', $product->wc_product_id)->where('variation_code', $item['variation'])->first();

                    if ($v) {
                        // $v->quantity += $item['quantity'];
                        // $v->save();

                        // updating quantites in wc
                        // $this->updateWc($v->wc_product_id, $v->wc_variation_id, $v->quantity);
                       
                        $wc->updateStock($v->wc_product_id, $v->wc_variation_id, $item['quantity']);
                        $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update success.');
                    } else {
                        $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update faild.');
                    }
                } else {

                    $wc->updateStock($product->wc_product_id, null, $item['quantity']);
                    $product->quantity += $item['quantity'];
                    $product->save();
                     $this->writeLog("Product ID: {$item['code']} Variation ID: " . ($item['variation'] ?? 0) . " Update success.");
                }
            }else{
                $this->writeLog("Product ID: {$item['code']} Variation ID: " . ($item['variation'] ?? 0) . " Update failed.");
            }
        }
        session()->forget('cart_items');
        
    }

    public function updateWc($product_id, $variation_id, $quantity)
    {
        $url = config('services.woocommerce.url');

        $client = new Client();

        $response = $client->request('PUT', $url . "/wp-json/wc/v3/products/$product_id/variations/$variation_id", [
            'auth' => [config('services.woocommerce.key'), config('services.woocommerce.secret')],
            'json' => [
                'stock_quantity' => $quantity,
                'manage_stock' => true,
            ],
        ]);

        $result = json_decode($response->getBody(), true);
    }

    function writeLog($message)
    {
        $file = 'log.txt';

        $time = date('Y-m-d H:i:s');

        $logMessage = '[' . $time . '] ' . $message . PHP_EOL;

        file_put_contents($file, $logMessage, FILE_APPEND);

        $this->log[] = [
            'status' => $logMessage,
        ];
    }
};
?>

<div>
    <div>
        <div class="mb-3">
            <div class="">
                <div class="mb-3">

                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter barcode here..." wire:model="barcode"
                            wire:keydown.enter="addItem">
                        @if ($error_message)
                            <small class="text-danger">{{ $error_message }}</small>
                        @endif
                    </div>


                </div>
            </div>
        </div>
        <div>
            <div class="cart-table mb-3">
                <table class="table table-sm table-striped table-responsive c-table">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('BARCODE') }}</th>
                            <th scope="col">{{ __('CODE') }}</th>
                            <th scope="col">{{ __('DESC.') }}</th>
                            <th scope="col">{{ __('VA.') }}</th>
                            <th scope="col">{{ __('QUANTITY') }}</th>
                            <th scope="col">{{ __('WEIGHT') }}</th>
                            <th scope="col"></th>

                        </tr>
                    </thead>
                    <tbody>

                        @if (session('cart_items', []))
                            @foreach (session('cart_items') as $key => $item)
                                <tr>

                                    <td>{{ $item['barcode'] }}</td>
                                    <td>{{ $item['code'] }}</td>
                                    <td>{{ $item['description'] }}</td>
                                    <td>{{ $item['variation'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ $item['weight'] }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger"
                                            wire:click="removeItem('{{ $key }}')">{{ __('Remove') }}</button>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No items added yet.') }}</td>
                            </tr>
                        @endif


                    </tbody>
                </table>

            </div>
        </div>
        <div class="mb-5">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <div class=" sm-font">
                    {{ __('Total Items') }}: <strong>{{ count(session('cart_items', [])) }}</strong>
                    {{ __('Total Weight') }}: <strong>{{ collect(session('cart_items', []))->sum('weight') }}
                        kg</strong>
                </div>
                <div class="">
                    <button class="btn btn-link" wire:click="clearCart" wire:confirm="Are you sure?"
                        @if (count(session('cart_items', [])) == 0) disabled @endif>{{ __('Clear all') }}</button>

                </div>
            </div>
        </div>
        <div>

        </div>
        <div class="d-flex flex-row-reverse mb-5">
            <div>
                <button wire:confirm="{{ __('Are you sure?') }}" class="btn btn-primary" @disabled(!count(session('cart_items', [])))
                    wire:click="update" wire:loading.attr="disabled">
                    <span class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="update">
                        {{-- <span class="visually-hidden">Loading...</span> --}}
                    </span>
                    {{ __('Add To Stock') }}
                </button>
            </div>
        </div>
        <div class="log-box">
            @if (count($log))
                <h6>Log</h6>
                @foreach ($log as $item)
                    <div class="{{ str_contains(strtolower($item['status']), 'faild') ? 'text-danger' : '' }}">
                        {{ $item['status'] }}
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
