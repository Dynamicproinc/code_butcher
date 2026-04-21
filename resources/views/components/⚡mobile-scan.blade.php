<?php

use Livewire\Component;
use App\Models\StockTransaction;
use App\Services\BarcodeService;
use App\Services\WooCommerceService;
use App\Models\Product;
use App\Models\ProductVariation;

new class extends Component {
    public $barcode;
    public $product_name;
    public $variation_weight;
    public $find_product = false;
    // public $product;

    public function setCode($barcode)
    {
        // // search the product :

        $quantity_per_item = 1;
        $barcode_service = new BarcodeService();
        $wc = new WooCommerceService();
        $barcode_decode = $barcode_service->decodeBarcode($barcode);
        // get product details
        $product_code = $barcode_decode['product_code'];
        $weight_in_kg = $barcode_decode['weight_in_kg'];
        $weight = $barcode_decode['weight'];

        $product = Product::where('product_code', $product_code)->first();
        if ($product) {
            $this->find_product = true;
            $this->product_name = $product->product_name;

            $this->variation_weight = $weight - ($weight % $product->threshold);
            $this->barcode = $barcode;
        } else {
            $this->find_product = false;
        }
    }

    public function add()
    {
        $this->error_message = '';

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
        $this->find_product = false;
        $this->variation_weight = '';
        $this->product_name = '';
    }

    public function removeItem($key)
    {
        $cart_items = session('cart_items_for_dispatch', []);
        if (isset($cart_items[$key])) {
            unset($cart_items[$key]);
            session(['cart_items_for_dispatch' => array_values($cart_items)]);
        }
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
};
?>

<div>
    <div>
        {{-- for camera --}}
        <div>


            <div wire:ignore class="camera mb-3">
                <div id="reader" class="" style="width:350px;"></div>
            </div>
            <div class="container">
                <div class="">
                    <div class=" mb-3">
                        <div class="">
                            <input type="text" class="form-control form-control-lg" wire:model="barcode" placeholder="{{ __('Enter Barcode') }}" autofocus readonly>
                        </div>

                    </div>
                    <div class="p-2 bg-light mb-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="">
                                    @if ($find_product)
                                        <h6 class="text-uppercase"> {{ $product_name ?? 'No product' }} -
                                            <strong>{{ $variation_weight }}</strong>
                                        </h6>
                                    @endif
                                </div>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-primary w-100 btn-lg" wire:click="add">{{ __('Add') }}</button>
                            </div>
                        </div>


                    </div>
                    {{-- table --}}

                    <div>
                        <div class="item-details p-2">
                            @if (session('cart_items_for_dispatch', []))

                                @foreach (session('cart_items_for_dispatch') as $key => $item)
                                    {{-- <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold text-uppercase">{{ $item['description'] }}</div>
                                                <div class="small text-muted fw-bold">{{ $item['variation'] }}</div>
                                            </div>
                                            <span
                                                class="badge text-bg-primary rounded-pill">{{ $item['quantity'] }}</span>
                                        </li> --}}
                                    <div class="rounded-4 bg-white shadow p-2 px-3 my-3">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <div class="fw-bold text-uppercase">{{ $item['description'] }}</div>
                                                <div class="small text-muted fw-bold">{{ $item['variation'] }}</div>
                                            </div>
                                            <div class="col-5">
                                                <button class="btn btn-sm btn-outline-primary m-2"
                                                    wire:click="decrement({{ $key }})">-</button>
                                                <strong
                                                    style="width:50px;text-align:center">{{ $item['quantity'] }}</strong>
                                                <button class="btn btn-sm btn-outline-primary m-2"
                                                    wire:click="increment({{ $key }})">+</button>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-default text-danger" wire:confirm="{{__('Are you sure?')}}" wire:click="removeItem('{{ $key }}')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted">
                                    No items added yet.
                                </div>

                            @endif




                        </div>
                    </div>

                    {{-- end table --}}
                </div>
            </div>


            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                function onScanSuccess(decodedText, decodedResult) {
                    // Handle on success condition with the decoded text or result.
                    console.log(`Scan result: ${decodedText}`, decodedResult);
                    // document.getElementById('barcode-result').innerText = `Scan result: ${decodedText}`;
                    //set wire:model value
                    $wire.setCode(decodedText);
                    // html5QrcodeScanner.clear();
                }

                function onScanFailure(error) {
                    // ignore errors
                }

                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: 250,
                        formatsToSupport: [
                            Html5QrcodeSupportedFormats.CODE_128,
                            Html5QrcodeSupportedFormats.EAN_13,
                            Html5QrcodeSupportedFormats.EAN_8,
                            Html5QrcodeSupportedFormats.UPC_A,
                            Html5QrcodeSupportedFormats.UPC_E
                        ]
                    }
                );

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            </script>


        </div>
        {{-- eend camera --}}

        <div class="fixed-bottom p-3 bg-light">
            <div class="row">
                
                <div class="col-4">
                    <button class="btn btn-default btn-lg w-100">{{__('Cancel')}}</button>
                </div>
                <div class="col-8">
                    <button class="btn btn-primary btn-lg w-100">{{__('Update WC')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
