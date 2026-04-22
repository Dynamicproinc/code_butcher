<?php

use Livewire\Component;
use App\Models\StockTransaction;
use App\Services\BarcodeService;
use App\Services\WooCommerceService;
use App\Models\Product;
use App\Models\ProductVariation;

new class extends Component
{
    public $find_product = false;
     public $success_message;
    public $error_message;
    public $variation_weight;
    public $product_name;

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
            $this->error_message = 'Not registered barcode';
        }
    }

    public function add(){
          
         

        $barcode = new BarcodeService();
        $barcode_decode = $barcode->decodeBarcode($this->barcode);
        
        $product_code = $barcode_decode['product_code'];
        $weight_in_kg = $barcode_decode['weight_in_kg'];
        $weight = $barcode_decode['weight'];

      

        if ($product = Product::where('product_code', $product_code)->first()) {
            $this->error_message = null;
            $this->product = $product;
            $cartItems = session()->get('cart_items', []);

          

            $variation = '0';

            if ($product->variation) {
                $variation = $weight - ($weight % $product->threshold);
                $check_vari = ProductVariation::where('product_id', $product->id)->where('variation_code', $variation)->first();
                if (!$check_vari) {
                    $this->error_message = 'Variation code: ' . $variation . ' , not registered';
                    return null;
                }
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

                session()->put('cart_items', $citems);
            }
            // if product has variations
        } else {
            $this->error_message = __('Invalid barcode');
        }

        $this->barcode = '';
    }

    //
};
?>

<div>
   <div wire:ignore class="camera mb-3">
        <div id="reader" style="width:350px;"></div>
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
                console.warn(`Code scan error = ${error}`);
                // document.getElementById('barcode-result').innerText = `Scan error: ${error}`;
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
    <div>
        {{-- for camera --}}
        <div>





            <div class="container">
                <div class="">
                    <div class=" mb-3">
                        {{-- <div class="">
                            <input type="text" class="form-control form-control-lg" wire:model="barcode"
                                placeholder="{{ __('Enter Barcode') }}" autofocus readonly>
                        </div> --}}

                    </div>
                    <div class="p-2 mb-3">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="">
                                    @if ($find_product)
                                        <h5 class="text-uppercase"> {{ $product_name ?? 'No product' }} -
                                            <strong>{{ $variation_weight }}</strong>
                                        </h5>
                                    @endif
                                </div>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-warning w-100" wire:click="add"
                                    wire:loading.attr="disabled">{{ __('Add') }}</button>
                            </div>
                        </div>


                    </div>
                    {{-- table --}}

                    <div>
                        <div class="item-details p-2">
                            @if (session('cart_items', []))

                                @foreach (session('cart_items') as $key => $item)
                                   
                                    <div class="rounded-4 bg-white shadow p-2 px-3 my-3">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <div class="fw-bold text-uppercase">{{ $item['description'] }}</div>
                                                <div class="small text-muted fw-bold">{{ $item['variation'] }}</div>
                                            </div>
                                            <div class="col-5">
                                                <button class="btn btn-sm btn-outline-primary m-2"
                                                    wire:click="decrement({{ $key }})"
                                                    wire:loading.attr="disabled" wire:target="update">-</button>
                                                <strong
                                                    style="width:50px;text-align:center">{{ $item['quantity'] }}</strong>
                                                <button class="btn btn-sm btn-outline-primary m-2"
                                                    wire:click="increment({{ $key }})"
                                                    wire:loading.attr="disabled" wire:target="update">+</button>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-default text-danger"
                                                    wire:confirm="{{ __('Are you sure?') }}"
                                                    wire:click="removeItem('{{ $key }}')">
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





        </div>
        {{-- eend camera --}}
        {{-- succeess --}}
        @if ($success_message)
            <div x-data x-init="setTimeout(() => $wire.set('success_message', null), 2000)" class="fixed-top text-white bg-success p-1 text-center">
                <i class="bi bi-check-circle-fill"></i> {{ $success_message }}
            </div>
        @endif
        {{-- errror --}}
        @if ($error_message)
            <div x-data x-init="setTimeout(() => $wire.set('error_message', null), 2000)" class="fixed-top text-white bg-danger p-1 text-center">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ $error_message }}
            </div>
        @endif
        {{-- bottom buttons --}}
        <div class="fixed-bottom p-3 bg-light">
            <div class="row">

                <div class="col-4">
                    <button class="btn btn-default  w-100" wire:click="cancel" wire:loading.attr="disabled"
                        wire:confirm="{{ __('Are you sure?') }}">{{ __('Cancel') }}</button>
                </div>
                <div class="col-8">
                    <button class="btn btn-warning  w-100" wire:click="update" wire:loading.attr="disabled"
                        wire:confirm="{{ __('Are you sure?') }}">
                        {{ __('Update WC') }}
                    </button>
                </div>
            </div>
        </div>
        {{-- loading -modal --}}
        <div class="loading-modal" wire:loading wire:target="update">
            <div class="loading-modal-content">
                <div class="">
                    <div class="text-center">
                        {{-- <span class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </span> --}}
                        <img src="{{asset('uploading.gif')}}" alt="Uploading..." style="width: 150px">
                        <p class="mb-0">Please wait...</p>
                        <h5>Uploading data to WC server</h5>

                    </div>
                </div>
            </div>
        </div>
        {{-- end loading modal --}}
    </div>
</div>