@extends('mobile')
@section('title', "Add stock")
@section('content-mobile')
<div>
    <div class="">
        @livewire('mobile-scan-add')
    </div>
    <div class="close-button-area">
        <a class="close-link" href="/"></a>
    </div>
</div>
@endsection