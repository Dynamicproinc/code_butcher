@extends('layouts.dashboard')
@section('title', "Dispatch Per Item")
@section('content')
<div>
    <div class="">
        @livewire('dashboard.per-order-dispatch')
    </div>
</div>
@endsection