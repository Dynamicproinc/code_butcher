@extends('layouts.dashboard')
@section('title', "Logs")
@section('content')
<div>
    <div class="">
       <table class="table">
  <thead>
    <tr>
      <th scope="col">{{__('Date')}}</th>
      <th scope="col">{{__('User')}}</th>
      <th scope="col">{{__('Type')}}</th>
      <th scope="col">{{__('Product ID')}}</th>
      <th scope="col">{{__('Variation ID')}}</th>
      <th scope="col">{{__('WC Stock')}}</th>
      
      <th scope="col">{{__('Quantity')}}</th>
      <th scope="col">{{__('WC Balance')}}</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($logs as $item)
        
    <tr class="@if($item->type == 'OUT') tbl-red @else tbl-green  @endif">
      <td>{{ $item->created_at }}</td>
      <td>{{ $item->user->name }}</td>
      <td>{{ $item->type }}</td>
      <td>{{ $item->getProduct()->product_name }}</td>
      <td>{{ $item->variation_id }} | {{ $item->variation_code }}</td>
      <td>{{ $item->wc_stock }}</td>
      <td>{{ $item->quantity }}</td>
      <td>{{ $item->balance }}</td>
     
    </tr>
    @endforeach
    
  </tbody>
</table>
<div>
    {{$logs->links()}}
</div>
    </div>
</div>
@endsection
