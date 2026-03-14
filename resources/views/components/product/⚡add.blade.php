<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\WooCommerceService;

new class extends Component {
    public $product_name;
    public $product_code;
    public $price;
    public $quantity;
    public $variation = false;
    public $threshold;
    public $wc_product_id;
    public $product_variations = [];

    protected function rules()
    {
        return [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'variation' => 'boolean',
            'threshold' => 'nullable|integer|min:0',
            'wc_product_id' => 'required|nullable|integer',
        ];
    }

    public function save()
    {
        $this->validate();

        $product = Product::create([
            'user_id' => auth()->user()->id,
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'variation' => $this->variation,
            'threshold' => $this->threshold,
            'wc_product_id' => $this->wc_product_id,
            'status' => 'pending',
        ]);

        if ($product) {
            foreach ($this->product_variations as $variation) {
                ProductVariation::create([
                    'product_id' => $product->id,
                    'wc_product_id' => $product->wc_product_id,
                    'wc_variation_id' => $variation['id'],
                    'attr_name' => $variation['name'],
                    'quantity' => 0,
                    'variation_code' => $variation['local_code'],
                ]);
            }
        }

        $this->reset();

        session()->flash('message', 'Product created successfully');
    }

    public function updatedWcProductId()
    {
        $wc = new WooCommerceService();

        $response = $wc->getVariation($this->wc_product_id); // Guzzle Response
        $json = $response->getBody()->getContents(); // Get raw JSON string

        $this->product_variations = json_decode($json, true) ?? [];
    }

    // public function updatedProductCode()
    // {
        
    //     $wc = new WooCommerceService();
    //     $product = Product::where('product_code', $this->product_code)->first();
    //     if($product){
    //         $response = $wc->getVariation($product->wc_product_id); 
    //     $json = $response->getBody()->getContents(); 

    //     $this->product_variations = json_decode($json, true) ?? [];
    //     $this->validate([
    //        'product_code' => 'unique:products,product_code',
    //     ]);
    //     }
        
    // }


};
?>

<div>
    <div class="">



        <form wire:submit="save">

            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" wire:model="product_name" class="form-control border p-2">
                @error('product_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>Product Code</label>
                <input type="text" wire:model="product_code" class="form-control border p-2">
                @error('product_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>Price</label>
                <input type="number" step="0.01" wire:model="price" class="form-control border p-2">
                @error('price')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" wire:model="quantity" class="form-control border p-2">
                @error('quantity')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>Threshold</label>
                <input type="number" wire:model="threshold" class="form-control border p-2">
                @error('threshold')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>WooCommerce Product ID</label>
                <input type="number" wire:model.lazy="wc_product_id" class="form-control border p-2">
                @error('wc_product_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <div wire:loading="wc_product_id">
                    <span class="spinner-border spinner-border-sm" role="status">
                        {{-- <span class="visually-hidden">Loading...</span> --}}
                    </span> loading...
                </div>

                <div class="py-3">
                    @if ($product_variations)
                        <table border="1" class="table">
                            <tr>
                                <th>WC Id</th>
                                <th>Attr</th>
                                <th>Local Code</th>
                                <th>Quantity</th>
                            </tr>

                            @foreach ($product_variations as $index => $variation)
                                <tr>
                                    <td>{{ $variation['id'] ?? 'N/A' }}</td>
                                    <td>{{ $variation['name'] ?? 'N/A' }}</td>
                                    <td>
                                        <input type="text"
                                            wire:model="product_variations.{{ $index }}.local_code" required>
                                    </td>
                                    <td>{{ $variation['stock_quantity'] }}</td>
                                </tr>
                            @endforeach


                        </table>
                    @endif
                </div>
                {{-- @if ($product_variations)
                    <ul>
                        @foreach ($product_variations as $variation)
                            <li>{{ $variation['name'] ?? 'N/A' }} - ${{ $variation['price'] ?? '0' }}</li>
                        @endforeach
                    </ul>
                @endif --}}
            </div>
            <div class="mb-3">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="variation" class="mr-2">
                    Has Variation
                </label>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 mb-3">
                Save Product
            </button>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

        </form>

    </div>
</div>
