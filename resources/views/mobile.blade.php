<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        .camera {
            width: 100%;
            /* height: 300px; */
            height: 400px;
            background: #000;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* #html5-qrcode-button-camera-stop {
            display: none !important;
        }

        #reader__dashboard_section {
            display: none !important;
        } */

        .item-details {
            max-height: 230px;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.08),
                inset 0 -2px 6px rgba(255, 255, 255, 0.6);
            border: 1px solid #eee;
            background: #fff;

        }

        .loading-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255);
            /* display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; */
        }

        .loading-modal-content {
            width: 100%;
            height: 100%;
            display: flex;
            /* flex-direction: column; */
            justify-content: center;
            align-items: center;
        }

        .menu-button {
            width: 100%;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #cbcaca;
            border-radius: 8px;
            text-align: center;
        }

        .menu-button a {
            text-decoration: none;
            color: #333;
            display: inline-block;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size:14px;
                font-family: 'JetBrains Mono', monospace !important;
                text-transform: uppercase;
                font-weight: 600;
                background: #eee;
                border-radius: 8px;

        }

        /* Apply hover to parent AND affect child */
        .menu-button:hover {
            /* background: #333; */
            cursor: pointer;
        }

        /* Change text color when hovering */
        .menu-button:hover a {
            /* color: #fff; */
        }

        /* .bg-dsbl{
      background-color: #EAE6DD !important;
    
    } */

        .close-link {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #f0f0f0;
            position: relative;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .close-link:hover {
            background-color: #e0e0e0;
        }

        .close-link::before,
        .close-link::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 12px;
            height: 2px;
            background-color: #333;
            transform-origin: center;
        }

        .close-link::before {
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .close-link::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        .close-button-area {
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 9999;
        }

        .tbl-red td{
            background: rgba(147, 10, 10, 0.049) !important;
            color: rgb(147, 10, 10) !important;
        }

        .tbl-green td {
            background: rgb(7, 76, 23, 0.049);
            color: rgb(7, 76, 23);
        }

        td,th {
            font-size: 14px;
            text-transform: uppercase;
            white-space: nowrap !important;
            /* font-family: Verdana, Geneva, Tahoma, sans-serif */
            font-family: 'JetBrains Mono', monospace !important;
        }
    </style>

    @livewireStyles
</head>

<body>
    <div class="bg-light">


        @yield('content-mobile')



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    @livewireScripts
</body>


</html>
