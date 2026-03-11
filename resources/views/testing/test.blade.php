<div class="container mt-4">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th style="width:20%">Product</th>
                <th style="width:10%">Product ID</th>
                <th style="width:15%">Variation ID</th>
                <th style="width:35%">Attribute</th>
                <th style="width:20%">Stock</th>
            </tr>
        </thead>
        <tbody>

        @foreach($products as $product)

            @if(isset($product['variations']))
                @foreach($product['variations'] as $key => $variation)
                    <tr>

                        {{-- Show product name only on first variation row --}}
                        @if($key == 0)
                            <td rowspan="{{ count($product['variations']) }}" class="fw-bold text-primary">
                                {{ $product['name'] }}
                            </td>

                            <td rowspan="{{ count($product['variations']) }}">
                                {{ $product['id'] }}
                            </td>
                        @endif

                        <td>{{ $variation['id'] }}</td>

                        <td>
                            {{ $variation['attributes'][0]['option'] ?? '-' }}
                        </td>

                        <td>
                            <span class="badge bg-success">
                                {{ $variation['stock_quantity'] }}
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