<?php

use Livewire\Component;
use App\Services\BarcodeService;
use App\Services\WooCommerceService;
use App\Models\Product;
use App\Models\ProductVariation;

new class extends Component {
    public $barcode;
    public $error_message;
    public $log = [];
    public $client_message;

    public function addDispatchCart()
    {
        $this->error_message = '';

        $this->validate([
            'barcode' => 'required|digits:13',
        ]);
        $barcode = new BarcodeService();
        $barcode_decode = $barcode->decodeBarcode($this->barcode);
        // get product details
        $product_code = $barcode_decode['product_code'];
        $weight_in_kg = $barcode_decode['weight_in_kg'];
        $weight = $barcode_decode['weight'];
        if ($product = Product::where('product_code', $product_code)->first()) {
            // existing quantity validation

            // now if product has variation
            $variation = '0';
            if ($product->variation) {
                $variation = $weight - ($weight % $product->threshold);

                // check the online store quantity, and if it is 0 then show error message and cancel the process.
                // $wc_product_with_variation = new WooCommerceService();
                // $local_variation = ProductVariation::where('variation_code', $variation)->first();

                // $variation_quantity = $wc_product_with_variation->getVariationQuantity($product->wc_product_id, $local_variation->wc_variation_id);

                // if ($variation_quantity == 0) {
                //     $this->error_message = __('Sold out!');
                //     return null;
                // }
                // ////////////////////////////////////////////////////////////////////////////////////

                $cartItems = session()->get('cart_items_for_dispatch', []);
                $found = false;

                foreach ($cartItems as $key => $item) {
                    if ($item['variation'] == $variation) {
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

                session()->put('cart_items_for_dispatch', $cartItems);
            } else {
                //if dont have variation product

                $citems = session()->get('cart_items_for_dispatch', []);
                $found_item = false;

                foreach ($citems as $key => $item) {
                    if ($item['code'] == $product_code) {
                        $citems[$key]['quantity'] += 1;
                        $citems[$key]['weight'] += $weight_in_kg;
                        $found_item = true;
                        break;
                    }
                }

                if (!$found_item) {
                    $citems[] = [
                        'barcode' => $this->barcode,
                        'code' => $product->product_code,
                        'description' => $product->product_name,
                        'variation' => $variation,
                        'quantity' => 1,
                        'weight' => $weight_in_kg,
                    ];
                }

                session()->put('cart_items_for_dispatch', $citems);
            }
        } else {
            $this->error_message = __('Invalid Barcode');
        }

        $this->barcode = '';
    }

    public function removeItem($key)
    {
        $cart_items = session('cart_items_for_dispatch', []);
        if (isset($cart_items[$key])) {
            unset($cart_items[$key]);
            session(['cart_items_for_dispatch' => array_values($cart_items)]);
        }
    }

    public function update()
    {
        try {
            $cart_items = session()->get('cart_items_for_dispatch', []);
        $wc = new WooCommerceService();
        foreach ($cart_items as $item) {
            $product = Product::where('product_code', $item['code'])->first();
            if ($product) {
                // check product has variation
                if ($product->variation) {
                    $v = ProductVariation::where('wc_product_id', $product->wc_product_id)->where('variation_code', $item['variation'])->first();

                    if ($v) {
                        $wc->dispatchStock($v->wc_product_id, $v->wc_variation_id, $item['quantity']);
                        $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update success.');
                        // $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update success.');
                    } else {
                        $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update faild.');
                    }
                } else {
                    $wc->dispatchStock($product->wc_product_id, null, $item['quantity']);
                    $product->quantity -= $item['quantity'];
                    $product->save();
                    $this->writeLog("Product ID: {$item['code']} Variation ID: " . ($item['variation'] ?? 0) . ' Update success.');
                }
            } else {
                $this->writeLog("Product ID: {$item['code']} Variation ID: " . ($item['variation'] ?? 0) . ' Update failed.');
            }
            session()->forget('cart_items_for_dispatch');
        }
        } catch (\Throwable $th) {
            //  $this->client_message = $th->getMessage();
              $this->client_message = 'The process could not be completed due to an issue connecting to the WooCommerce server.';
        }
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

    public function increment($id)
    {
        $cart_items = session('cart_items_for_dispatch', []);
        if (isset($cart_items[$id])) {
            // dd( $cart_items[$id]['quantity']);
            $cart_items[$id]['quantity'] = $cart_items[$id]['quantity'] + 1;
            session(['cart_items_for_dispatch' => array_values($cart_items)]);
        }
        // foreach($cart_items as $key => $item){

        // }
    }
    public function decrement($id)
    {
        $cart_items = session('cart_items_for_dispatch', []);
        if (isset($cart_items[$id])) {
            // dd( $cart_items[$id]['quantity']);
            $cart_items[$id]['quantity'] = $cart_items[$id]['quantity'] - 1;
            if ($cart_items[$id]['quantity'] <= 0) {
                $cart_items[$id]['quantity'] = 1;
            }
            session(['cart_items_for_dispatch' => array_values($cart_items)]);
        }
        // foreach($cart_items as $key => $item){

        // }
    }

    public function clearCart()
    {
        session()->forget('cart_items_for_dispatch');
    }
};
?>

<div>
    <div class="p-3">

        <div class="row">
            <div class="form-group mb-3 col-12">
                <input type="text" class="form-control mb-2" placeholder="{{ __('Scan Barcode') }}" wire:model="barcode"
                    wire:keydown.enter="addDispatchCart">
                @if ($error_message)
                    <small class="text-danger note">{{ $error_message }}</small>
                @endif
                @error('barcode')
                    <small class="text-danger note">{{ $message }}</small>
                @enderror
            </div>
            {{-- <div class="col-2">
                <div class="spinner-border" role="status" wire:loading wire:target="addDispatchCart">
                    
                </div>
            </div> --}}
        </div>
        <div class="cart-table mb-5">
            <table class="table table-sm table-striped table-responsive c-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('BARCODE') }}</th>
                        <th scope="col">{{ __('CODE') }}</th>
                        <th scope="col">{{ __('DESC.') }}</th>
                        <th scope="col">{{ __('VA.') }}</th>
                        <th scope="col">{{ __('QUANTITY') }}</th>
                        {{-- <th scope="col">{{ __('WEIGHT') }}</th> --}}
                        <th scope="col"></th>

                    </tr>
                </thead>
                <tbody>

                    @if (session('cart_items_for_dispatch', []))
                        @foreach (session('cart_items_for_dispatch') as $key => $item)
                            <tr>

                                <td>{{ $item['barcode'] }}</td>
                                <td>{{ $item['code'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['variation'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                {{-- <td>{{ $item['weight'] }}</td> --}}
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="decrement({{ $key }})">-</button>
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="increment({{ $key }})">+</button>
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
        <div class="mb-5">
            <div class="d-flex mb-3 justify-content-between align-items-center">
                <div class=" sm-font">
                    {{ __('Total Items') }}: <strong>{{ count(session('cart_items_for_dispatch', [])) }}</strong>
                    {{-- {{ __('Total Weight') }}: <strong>{{ collect(session('cart_items', []))->sum('weight') }}
                        kg</strong> --}}
                </div>
                <div class="">
                    <button class="btn btn-link" wire:click="clearCart" wire:confirm="Are you sure?"
                        @if (count(session('cart_items_for_dispatch', [])) == 0) disabled @endif>{{ __('Clear all') }}</button>

                </div>
            </div>
        </div>
        <div class="d-flex flex-row-reverse">
            <button class="btn btn-primary" wire:confirm="{{ __('Are you sure?') }}" wire:click="update"
                @disabled(!count(session('cart_items_for_dispatch', []))) wire:loading.attr="disabled">
                {{-- <i class="bi bi-send-arrow-down-fill"></i> --}}
                <span class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="update">
                    {{-- <span class="visually-hidden">Loading...</span> --}}
                </span>
                {{ __('Dispatch') }}
            </button>
        </div>
         <div class="log-box text-danger">
               @if($client_message)
                <div class="simple-alert-danger">

                    {{$client_message}}
                </div>
                @endif
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
