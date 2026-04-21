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
    public $weight;
    // public $product;


    public function setCode($barcode)
    {
        $this->barcode = $barcode;
        // // search the product :
       
            
             

             


    }

    public function add() {
        
        $quantity_per_item = 1;
            $barcode_service = new BarcodeService();
            $wc = new WooCommerceService();
             $barcode_decode = $barcode_service->decodeBarcode($this->barcode);
            // get product details
            $product_code = $barcode_decode['product_code'];
            $weight_in_kg = $barcode_decode['weight_in_kg'];
            $weight = $barcode_decode['weight'];
            

            //  $product = Product::where('product_code', $product_code)->first();
            //     $this->product_name = $product->product_name;
                $this->weight = $weight;
                dd($this->weight);
    }
};
?>

<div>
    <div>
        {{-- for camera --}}
        <div wire:ignore>


            <div id="reader" class="camera mb-3"></div>
            <div class="container">
                <div class="">
                    <div class="row mb-3">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-lg" wire:model="barcode">
                        </div>
                        <div class="col-4">
                            <button class="btn btn-primary w-100 btn-lg" wire:click="add">ADD</button>
                        </div>
                    </div>
                    <div class="p-2 bg-light">
                      
                        <h6> {{ $product_name  }}- 0.355 kg - <strong>{{ $weight }} kg</strong></h6>
                       
                    </div>
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
    </div>
</div>
