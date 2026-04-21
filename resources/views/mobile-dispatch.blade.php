<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan Barcode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
   <style>
    .camera{
        width: 100%;
        /* height: 300px; */
        height: 300px ;
        background: #000;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #html5-qrcode-button-camera-stop{
        display: none !important;
    }
    #reader__dashboard_section{
      display: none !important;
    }
   </style>
    @livewireScripts
        @livewireStyles
</head>
 
  <body>
    <div>
    @livewire('mobile-scan')
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
