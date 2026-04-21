<?php

use Livewire\Component;

new class extends Component {

    public $barcode;

    public function setCode($barcode) {
        $this->barcode = $barcode;
    }

    public function add(){

    }
};
?>

<div>
    <div>
        {{-- for camera --}}
        <div wire:ignore>
            

            <div id="reader" class="camera mb-3"></div>
            <div class="container">
                <div class="row">
                <div class="col-8">
                    <input type="text" class="form-control form-control-lg" wire:model="barcode">
                </div>
                <div class="col-4">
                    <button class="btn btn-primary w-100 btn-lg">ADD</button>
                </div>
            </div>
            <p id="barcode-result"></p>
            </div>
            

            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                function onScanSuccess(decodedText, decodedResult) {
                    // Handle on success condition with the decoded text or result.
                    console.log(`Scan result: ${decodedText}`, decodedResult);
                    document.getElementById('barcode-result').innerText = `Scan result: ${decodedText}`;
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
