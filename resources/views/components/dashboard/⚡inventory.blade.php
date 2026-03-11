<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component {
    public $products = [];

    public function loadData()
    {
        $response = Http::withBasicAuth(config('services.woocommerce.key'), config('services.woocommerce.secret'))->get(config('services.woocommerce.url') . '/wp-json/wc/v3/products', [
            'per_page' => 100,
        ]);

        if ($response->failed()) {
            dd($response->body());
        }

        $this->products = $response->json();

        foreach ($this->products as &$product) {
            if ($product['type'] == 'variable') {
                $variations = Http::withBasicAuth(config('services.woocommerce.key'), config('services.woocommerce.secret'))
                    ->get(config('services.woocommerce.url') . '/wp-json/wc/v3/products/' . $product['id'] . '/variations', ['per_page' => 100])
                    ->json();

                $product['variations'] = $variations;
            }
        }
    }
};
?>

<div>
    <div wire:init="loadData">

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

                @foreach ($products as $product)
                    @if (isset($product['variations']))
                        @foreach ($product['variations'] as $key => $variation)
                            <tr>

                                {{-- Show product name only on first variation row --}}
                                @if ($key == 0)
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
                                        {{ $variation['stock_quantity'].$variation['sku'] }}
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
        <div class="text-center">
            <div wire:loading class="text-muted">
                <h3>Loading data from API...</h3>
            </div>
        </div>
    </div>
</div>
