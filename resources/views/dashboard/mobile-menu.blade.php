@extends('mobile')
@section('title', "Add stock")
@section('content-mobile')
<div style="height: 100vh;">
    <div class="bg-dark p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex text-white">
                <div class="me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
</svg>
                </div>
                <h4 class="text-white">WEBSHOP MGT</h4>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="p-3">
            <div class="row">
                <div class="col-4">
                    <div class="menu-button">
                        
                        <a href="{{route('dashboard.product.mobile-scan-add')}}"><i class="bi bi-box-arrow-in-down me-2"></i> {{__('Stock-In')}}</a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="menu-button">
                        <a href="{{route('dashboard.product.mobile-scan-dispatch')}}"><i class="bi bi-box-arrow-up me-2"></i> {{__('Dispatch')}}</a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="menu-button">
                        <a href="{{route('dashboard.logs')}}"><i class="bi bi-file-text me-2"></i> {{__('Logs')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
