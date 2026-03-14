@extends('layouts.dashboard')
@section('title', "Add Product")
@section('content')
<div>
    <div class="row">
        <div class="col-lg-6">
            <div class="">
        
        <div class="p-3">
                @livewire('product.add')
        </div>
    </div>
        </div>
    </div>
</div>
@endsection