<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariation;
new class extends Component
{
     public $product;
    public $product_name, $product_code, $threshold, $wc_product_id, $variation;
    public $product_variations = [];
    public $variation_id, $local_code, $attr_name;

    public function mount($product){
        $this->product = $product;
        $this->product_name = $product->product_name;
        $this->product_code = $product->product_code;
        $this->threshold = $product->threshold;
        $this->wc_product_id = $product->wc_product_id;
        $this->variation = (bool) $product->variation;
        if(count($product->variations)){
            $this->product_variations = $product->variation;
        }
    }

    public function update(){
        $this->validate([
                        'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100',
            // 'price' => 'required|numeric|min:0',
            // 'quantity' => 'required|integer|min:0',
            'variation' => 'boolean',
            'threshold' => 'nullable|min:0',
            'wc_product_id' => 'required|nullable|integer',
        ]);

        $product = Product::where('id', $this->product->id)->first();
        $product->product_name = $this->product_name;
        $product->product_code = $this->product_code;
        $product->threshold = (int) $this->threshold;
        $product->wc_product_id = $this->wc_product_id;
        $product->variation = (bool) $this->variation;
        $product->save();


    
    }

    public function addVariation(){
        $this->validate([
            'variation_id'=> 'required|integer',
            'local_code'=> 'required|integer',
            'attr_name'=> 'required'
        ]);

            ProductVariation::updateOrCreate(
            [
                'wc_variation_id' => $this->variation_id,
            ],
            [
                'product_id' => $this->product->id,
                'wc_product_id' => $this->product->wc_product_id,
                'attr_name' => $this->attr_name,
                'quantity' => 0,
                'variation_code' => $this->local_code,
            ]
        );

         $this->product_variations = $this->product->variations;
         session()->flash('success', 'Updated');
    }

    public function delete($id){
        $variation = ProductVariation::findOrFail($id);
        $variation->delete();
         $this->product_variations = $this->product->variations;
    }



    
};
?>

<div>
    <div>
        <form wire:submit="update">

            <div class="mb-3">
                <label>{{ __('Product name') }}</label>
                <input type="text" wire:model="product_name" class="form-control border p-2">
                @error('product_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>{{ __('Local product code') }}</label>
                <input type="text" wire:model="product_code" class="form-control border p-2">
                @error('product_code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>



            <div class="mb-3">
                <label>{{ __('Threshold (round by)') }}</label>
                <input type="number" wire:model="threshold" class="form-control border p-2">
                @error('threshold')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label>{{ __('WC product ID') }}</label>
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

                {{-- <div class="py-3">
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
                </div> --}}
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
    <div>
        <div class="p-3 bg-light">
            <form wire:submit="addVariation" wire:confirm="{{__('Are you sure?')}}">
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="WC Variation ID"
                            wire:model="variation_id">
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Attr name" wire:model="attr_name">
                    </div>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" placeholder="Local variation code"
                            wire:model="local_code">
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </div>
            </form>
                    @if (session()->has('success'))
                <div class="text-success">{{ session('success') }}</div>
            @endif
        </div>
    </div>
    <div class="mt-3">
        @if (count($product->variations))
            <h5 class="">{{ __('Product Variations') }}</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Local Product ID</th>
                        <th scope="col">WC Product ID</th>
                        <th scope="col">WC Variation ID</th>
                        <th scope="col">Attr Name</th>
                        <th scope="col">Local Vr. Code</th>
                        <th scope="col">Quantity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($product->variations as $item)
                        <tr>
                            <td scope="row">{{ $item->product_id }}</td>
                            <td scope="row">{{ $item->wc_product_id }}</td>
                            <td scope="row">
                                {{ $item->wc_variation_id }}
                            </td>
                            <td scope="row">{{ $item->attr_name }}</td>
                            <td scope="row">{{ $item->variation_code }}</td>
                            <td scope="row">{{ $item->quantity }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" wire:click="delete({{$item->id}})" wire:confirm="{{__('Are you sure?')}}">{{ __('Delete') }}</button>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        @endif
    </div>
</div>
