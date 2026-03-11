@extends('layouts.dashboard')
@section('title', "Local Inventory")
@section('content')
<div>
    <div>
        <div class="d-flex flex-row-reverse">
            <a href="{{route('dashboard.inventory')}}" class="btn btn-link">{{__('WC Inventory')}}</a>
        </div>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width:20%">Product</th>
                    <th style="width:20%">Product ID</th>
                    <th style="width:15%">Variation ID</th>
                    <th style="width:35%">Attribute</th>
                    <th style="width:20%">Stock</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($products as $product)
                    @if (isset($product->variations))
                        @foreach ($product->variations as $key => $variation)
                            <tr>

                                {{-- Show product name only on first variation row --}}
                                @if ($key == 0)
                                    <td rowspan="{{ count($product->variations) }}" class="fw-bold text-primary">
                                        {{ $product->product_name }}
                                    </td>

                                    <td rowspan="{{ count($product->variations) }}">
                                        {{ $product->id }} | <small class="text-muted"> WC PID: {{ $product->wc_product_id}}</small>
                                    </td>
                                @endif

                                <td>{{ $variation->id }} | <small class="text-muted"> WC VID: {{ $variation->wc_variation_id}}</small></td>

                                <td>
                                    {{ $variation->attr_name }}
                                </td>

                                <td>
                                    <span class="badge bg-success">
                                        {{ $variation->quantity }}
                                    </span>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="fw-bold text-primary">{{ $product['name'] }}</td>
                            <td>{{ $product['id'] }}</td>
                            <td colspan="3" class="text-muted text-center">
                                No Variations
                            </td>
                        </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </div>
</div>
@endsection