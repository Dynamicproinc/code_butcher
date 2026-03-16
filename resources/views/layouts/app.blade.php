<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    {{-- data-bs-theme="dark" --}}
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        
        <title>@yield('title') :: {{ config('app.name') }}</title>
 
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
 
        @livewireStyles
    </head>
    <body>
        <nav class="navbar">
           <div class="container">
             <div class="navbar-cont">
                <div class="d-flex justify-content-between">
                    
                    <div>
                       <div class="logo">Code Butcher &lt;/&gt;</div>

                    </div>
                </div>
            </div>
           </div>
        </nav>
        <div class="container">
            {{-- {{ $slot }} --}}

            @yield('content')
        </div>
 
        @livewireScripts
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>