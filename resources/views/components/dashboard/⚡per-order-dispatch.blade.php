<?php

use Livewire\Component;
use App\Models\StockTransaction;
use App\Services\BarcodeService;
use App\Services\WooCommerceService;
use App\Models\Product;
use App\Models\ProductVariation;

new class extends Component {
    public $stock_transactions;
    public $barcode;
    public $error_message = '';
    public $success_message = '';
    public $client_message;

    public function mount()
    {
        $this->stock_transactions = StockTransaction::latest()->where('type', 'OUT')->whereDate('created_at', today())->limit(20)->get();
    }

    public function getBarcode($value)
    {
        $this->barcode = $value;
        $this->add();
    }

    public function add()
    {
        try {
            $this->error_message = '';
            $this->success_message = '';
            $this->validate([
                'barcode' => 'required|digits:13',
            ]);
            $quantity_per_item = 1;
            $barcode = new BarcodeService();
            $wc = new WooCommerceService();

            $barcode_decode = $barcode->decodeBarcode($this->barcode);
            // get product details
            $product_code = $barcode_decode['product_code'];
            $weight_in_kg = $barcode_decode['weight_in_kg'];
            $weight = $barcode_decode['weight'];

            // apply logic
            if ($product = Product::where('product_code', $product_code)->first()) {
                // existing quantity validation

                // now if product has variation
                $variation = '0';
                if ($product->variation) {
                    $variation = $weight - ($weight % $product->threshold);

                    // find the variation
                    $v = ProductVariation::where('wc_product_id', $product->wc_product_id)->where('variation_code', $variation)->first();
                    if ($v) {
                        $wc->dispatchStock($v->wc_product_id, $v->wc_variation_id, $quantity_per_item);
                        $this->success_message = __('Item quantity updated succesfully');
                        // $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update success.');
                        // $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update success.');
                    } else {
                        // $this->writeLog('Product ID: ' . $item['code'] . ' Variation ID: ' . $item['variation'] . ' Update faild.');
                        $this->error_message = __('Process coudnt processed, some thing went wrong!');
                    }
                } else {
                    $wc->dispatchStock($product->wc_product_id, null, $quantity_per_item);
                    $this->success_message = __('Item quantity updated succesfully');
                }
            } else {
                $this->error_message = __('Invalid barcode');
            }

            $this->barcode = '';
            // end logic
            $this->stock_transactions = StockTransaction::latest()->where('type', 'OUT')->whereDate('created_at', today())->limit(20)->get();
        } catch (\Throwable $th) {
            // $this->client_message = $th->getMessage();
            $this->client_message = 'The process could not be completed due to an issue connecting to the WooCommerce server.';
        }
    }
};
?>

<div>
    <div class="">
        <div class="">
            <div class="mb-3">

                <div>
                  <script src="https://unpkg.com/html5-qrcode"></script>

<div id="reader" style="width:300px; "></div>
<h2>scan</h2>

<script>
function onScanSuccess(decodedText, decodedResult) {
    alert("Barcode: " + decodedText);
}

function onScanFailure(error) {
    // ignore errors
}

const html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    {
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

                    {{-- <script src="https://unpkg.com/html5-qrcode"></script>

                    <div id="reader" style="width:300px"></div>
                    
                    
                    <script>
                        
                        const html5QrCode = new Html5Qrcode("reader");

                        let isScanning = false; // 🔒 lock to prevent duplicates

                        function onScanSuccess(decodedText) {
                            if (isScanning) return; // 🚫 ignore duplicates
                            isScanning = true;

                            // alert(`Scanned: ${decodedText}`); // ✅ Show scanned code

                            // ✅ Put value into input (optional)
                          

                            //set values to livewire component input and trigger add method
                               $wire.getBarcode(decodedText);
                            // ✅ Stop camera immediately
                            // html5QrCode.stop().then(() => {

                            //     // ✅ Clear camera UI
                            //     html5QrCode.clear();

                            //     // ✅ Hide scanner div
                            //     document.getElementById("reader").style.display = "none";

                            //     console.log("Scanner stopped & hidden");

                            // }).catch(err => {
                            //     console.error("Stop failed:", err);
                            // });
                        }

                        // Start scanner
                        Html5Qrcode.getCameras().then(devices => {

                            // Try to find back camera
                            let backCamera = devices.find(device =>
                                device.label.toLowerCase().includes("back") ||
                                device.label.toLowerCase().includes("rear")
                            );

                            let cameraId = backCamera ? backCamera.id : devices[0].id;

                            html5QrCode.start(
                                cameraId, {
                                    fps: 10,
                                    qrbox: 250
                                },
                                onScanSuccess
                            );
                        });
                    </script> --}}


                </div>
                <input type="text" wire:model="barcode" class="form-control mb-2"
                    placeholder="{{ __('Scan barcode here...') }}" wire:keydown.enter="add">

            </div>
            <div style="height:40px">
                <div class="">
                    <div>
                        <div wire:loading wire:target="add" class="w-100">
                            {{-- <span class="spinner-grow spinner-grow-sm mx-3" role="status">
                               
                            </span> --}}
                            {{-- Updating... --}}
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    Updating...</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if ($success_message)
                            <span class="badge bg-success"> {{ $success_message }}</span>
                        @endif
                        @if ($error_message)
                            <span class="badge bg-danger"> {{ $error_message }}</span>
                        @endif

                        @error('barcode')
                            <small class="text-danger note">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>

                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <div>
                <div class="">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('User') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Pr. ID') }}</th>
                                <th scope="col">{{ __('Va. ID') }}</th>
                                <th scope="col">{{ __('WC Stock') }}</th>

                                <th scope="col">{{ __('Quantity') }}</th>
                                <th scope="col">{{ __('WC Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stock_transactions as $item)
                                <tr class="">
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->getProduct()->product_name }}</td>
                                    <td>{{ $item->variation_id }} | {{ $item->variation_code }}</td>
                                    <td>{{ $item->wc_stock }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->balance }}</td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <div>
                        {{-- {{$stock_transactions->links()}} --}}
                    </div>
                </div>

                <div class="log-box text-danger">
                    @if ($client_message)
                        <div class="simple-alert-danger">

                            {{ $client_message }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
