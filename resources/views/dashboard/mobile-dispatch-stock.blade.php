@extends('mobile')
@section('title', "Dispatch stock")
@section('content-mobile')
<div>
    <div class="">
        @livewire('mobile-scan')
    </div>
    <div class="close-button-area">
        <a class="close-link" href="/"></a>
    </div>
</div>
@endsection
