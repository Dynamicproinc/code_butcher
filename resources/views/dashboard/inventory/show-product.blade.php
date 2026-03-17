@extends('layouts.dashboard')
@section('title', "Edit product")
@section('content')
<div>
  @livewire('product.edit-product',['product' => $product])
</div>
@endsection