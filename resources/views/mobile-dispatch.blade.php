{{-- <!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan Barcode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        .camera {
            width: 100%;
            /* height: 300px; */
            height: 300px;
            background: #000;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #html5-qrcode-button-camera-stop {
            display: none !important;
        }

        #reader__dashboard_section {
            display: none !important;
        }

        .item-details {
            max-height: 230px;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.08),
                inset 0 -2px 6px rgba(255, 255, 255, 0.6);
            border: 1px solid #eee;
            background: #fff;

        }

        /* .bg-dsbl{
      background-color: #EAE6DD !important;
    
    } */
    </style>
    @livewireScripts
    @livewireStyles
</head>

<body>
    <div class="bg-light">
        @livewire('mobile-scan')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html> --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
        }

        #reader {
            width: 320px;
            margin: auto;
        }

        .result-box {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .barcode {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

    <h2>📷 Barcode Scanner</h2>

    <div id="reader"></div>

    <div class="result-box">
        <div>Last Scan:</div>
        <div id="result" class="barcode">---</div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        let lastScanned = null;
        let scanLock = false;

        function onScanSuccess(decodedText) {

            // Prevent rapid duplicate scans
            if (scanLock) return;

            // Prevent same barcode repeating
            if (decodedText === lastScanned) return;

            scanLock = true;
            lastScanned = decodedText;

            console.log("Scanned:", decodedText);

            // Show result
            document.getElementById("result").innerText = decodedText;

            // Optional: beep sound
            beep();

            // Unlock after 2 seconds
            setTimeout(() => {
                scanLock = false;
            }, 2000);
        }

        function onScanFailure(error) {
            // ignore errors
        }

        function beep() {
            const audio = new Audio("https://actions.google.com/sounds/v1/beeps/beep_short.ogg");
            audio.play();
        }

        const scanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E
            ]
        });

        scanner.render(onScanSuccess, onScanFailure);
    </script>

</body>
</html>