
@extends('layouts.dashboard')
@section('title', 'Inventory')
@section('content')
<div class="container mt-4">
  <div class="d-flex flex-row-reverse mb-3">
    <a href="{{route('dashboard.add-stock')}}" class="btn btn-primary">{{__('Add stock')}}</a>
  </div>
    @livewire('dashboard.inventory')
</div>
@endsection

