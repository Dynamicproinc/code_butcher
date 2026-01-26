<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Mail\Invoice as InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
new class extends Component {
    public $barcode;
    public $item_code, $description;
    public $product = [];
    public $error_message;
    public $remark;
    public $customer_id;
    public $customers = [];


    public function mount(){
        $this->customers = Customer::all();
    }
    public function addItem()
    {
        // split number
        //2800104080650

        $product_code = substr($this->barcode, 4, 3);
        $weight = substr($this->barcode, 7, 5);
        $weight_in_kg = intval($weight) / 1000;
        // dd($weight_in_kg);

        if ($product = Product::where('product_code', $product_code)->first()) {
            $this->error_message = null;
            $this->product = $product;
            $cartItems = session()->get('cart_items', []);
            if (!empty($cartItems)) {

    if ($cartItems[0]['code'] !== $product->product_code) {

        $this->error_message = __('You cannot add different items in manual mode');

        return;
    }
}
            session()->push('cart_items', [
                'barcode' => $this->barcode,
                'code' => $product->product_code,
                'description' => $product->product_name,
                'variation' => $product->variation,
                'quantity' => 1,
                'weight' => $weight_in_kg,
            ]);
        }else{

            $this->error_message = __('Invalid barcode');
        }

        $this->barcode = '';

    }

    public function removeItem($key)
    {
        $cart_items = session('cart_items', []);
        if (isset($cart_items[$key])) {
            unset($cart_items[$key]);
            session(['cart_items' => array_values($cart_items)]);
        }
    }

    public function clearCart()
    {
        session()->forget('cart_items');
    }

    public function addToInvoice()
    {
        $this->validate([
            'remark' => 'required'
        ]);
        // manual mode
        if ($cart_items = session('cart_items', [])) {

            //lets_findouthe product now
            $code = $cart_items[0]['code'];
            $product = Product::where('product_code', $code)->first();
            if($product){

                $invoice_items = session()->push('invoice_items',[
                    'product_id' => $product->id,
                    'product_code' => $code,
                    'remark'=> $this->remark,
                    'product_description'=> $product->product_name,
                    'quantity' => count(session('cart_items', [])),
                    'total_weight' => collect(session('cart_items', []))->sum('weight'),
                    'items' => session('cart_items', []),
                ]);
            }
            //clear cart
            session()->forget('cart_items');
            $this->remark = '';


        }

        return null;
    }

    public function removeInvoiceItem($key)
    {
        $invoice_items = session('invoice_items', []);
        if (isset($invoice_items[$key])) {
            unset($invoice_items[$key]);
            session(['invoice_items' => array_values($invoice_items)]);
        }
    }

    public function clearCartInvoice()
    {
        session()->forget('invoice_items');
    }

    public function saveInvoice()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);
        if ($invoice_items = session('invoice_items', [])) {
            $invoice = Invoice::create([
                'user_id' => auth()?->id() ?? 0,
                'customer_id' => $this->customer_id ?? null,
                'total_weight' => collect($invoice_items)->sum('total_weight'),
            ]);

            foreach ($invoice_items as $inv_item) {
                $pname = $inv_item['product_code'].' - '.$inv_item['product_description'].' - '. $inv_item['remark'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $inv_item['product_id'],
                    'product_name' => $pname,
                    'quantity' => $inv_item['quantity'],
                    'total_weight' => $inv_item['total_weight'],
                    'items' => json_encode($inv_item['items']),
                ]);
            }
            //email to to admin or customer
            // dd($invoice->items);
            Mail::to('samdynmic@gmail.com')->send(new InvoiceMail($invoice));
            //clear invoice items
            session()->forget('invoice_items');

        }
    }
};
?>

<div>
    <div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">

                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter barcode here..." wire:model="barcode"
                            wire:keydown.enter="addItem">
                        @if ($error_message)
                            <small class="text-danger">{{ $error_message }}</small>
                        @endif
                    </div>
                    {{-- <div>
                @if ($product)
                 <label for="levels">{{ __('Select Level') }}</label>
                    <select class="form-control" id="levels">
                        <option value="">{{ __('Select') }}</option>
                        @foreach ($product->levels as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                   
                @endif
            </div> --}}

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="cart-table mb-3">
                    <table class="table table-sm table-striped table-responsive c-table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('BARCODE') }}</th>
                                <th scope="col">{{ __('CODE') }}</th>
                                <th scope="col">{{ __('DESC.') }}</th>
                                <th scope="col">{{ __('VA.') }}</th>
                                <th scope="col">{{ __('QUANTITY') }}</th>
                                <th scope="col">{{ __('WEIGHT') }}</th>
                                <th scope="col"></th>

                            </tr>
                        </thead>
                        <tbody>

                            @if (session('cart_items', []))
                                @foreach (session('cart_items') as $key => $item)
                                    <tr>
                                        <td>{{ $item['barcode'] }}</td>
                                        <td>{{ $item['code'] }}</td>
                                        <td>{{ $item['description'] }}</td>
                                        <td>{{ $item['variation'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ $item['weight'] }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="removeItem('{{ $key }}')">{{ __('Remove') }}</button>
                                        </td>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No items added yet.') }}</td>
                                </tr>
                            @endif


                        </tbody>
                    </table>

                </div>
                <div class="d-flex mb-3 justify-content-between align-items-center">
                    <div class=" sm-font">
                        {{ __('Total Items') }}: <strong>{{ count(session('cart_items', [])) }}</strong>
                        {{ __('Total Weight') }}: <strong>{{ collect(session('cart_items', []))->sum('weight') }}
                            kg</strong>
                    </div>
                    <div class="">
                         <button class="btn btn-link" wire:click="clearCart" wire:confirm="Are you sure?" @if(count(session('cart_items', [])) == 0) disabled @endif>{{ __('Clear all') }}</button>
                       
                    </div>
                </div>
                <div>
                    <div class="row ">
                            <div class="col-lg-8">

                                <input type="text" wire:model="remark" class="form-control @error('remark') is-invalid @enderror" placeholder="{{__('Remark')}}">
                                {{-- @error('remark') <small class="text-danger">{{ $message }}</small> @enderror --}}
                            </div>
                            <div class="col-lg-4">
                               
                        <button class="btn btn-dark w-100" wire:click="addToInvoice"><i class="bi bi-plus-lg"></i> {{ __('Add') }}</button>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light p-3 shadow-sm border rounded">
                    <h6>{{ __('Summary') }}</h6>
                    <div class="mb-3">
                        <div class="form-group">
                            {{-- <label for="cus">{{ __('Customer') }}</label> --}}
                            <select name="" class="form-control @error('customer_id') is-invalid @enderror" id="cu" wire:model="customer_id">
                                <option value="">{{ __('Select Customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <table class="table table-sm table-striped c-table">
                            <thead>
                                <tr>

                                    <th scope="col">{{ __('ID') }}</th>
                                    <th scope="col">{{ __('CODE.') }}</th>
                                    <th scope="col">{{ __('DES.') }}</th>
                                    <th scope="col">{{ __('QTY') }}</th>
                                    <th scope="col">{{ __('WEIGHT') }}</th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (count(session('invoice_items', [])) > 0)
                                    @foreach (session('invoice_items', []) as $key => $inv_item)
                                        <tr>

                                            <td>{{ $inv_item['product_id'] }}</td>
                                            <td>{{ $inv_item['product_code'] }}</td>
                                            <td>{{ $inv_item['product_description'] .' - '. $inv_item['remark'] }}</td>
                                            <td>{{ $inv_item['quantity'] }}</td>
                                            <td>{{ $inv_item['total_weight'] }}</td>
                                             <td>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="removeInvoiceItem('{{ $key }}')">{{ __('Remove') }}</button>
                                        </td>
                                        </tr>
                                    @endforeach
                                    
                                @else
                                     <tr>
                                    <td colspan="5" class="text-center">{{ __('No items added yet.') }}</td>
                                </tr>
                                @endif
                               

                            </tbody>
                        </table>
                        <div class="d-flex mb-3 justify-content-between align-items-center">
                    <div class=" sm-font">
                        {{ __('Total Items') }}: <strong>{{ count(session('invoice_items', [])) }}</strong>
                        {{ __('Total Weight') }}: <strong>{{ collect(session('invoice_items', []))->sum('total_weight') }}
                            kg</strong>
                    </div>
                    <div class="">
                         <button class="btn btn-link" wire:click="clearCartInvoice" wire:confirm="Are you sure?" @if(count(session('invoice_items', [])) == 0) disabled @endif>{{ __('Clear all') }}</button>
                       
                    </div>
                </div>
                <div class="d-flex flex-row-reverse">
                    <button class="btn btn-primary btn-sm" wire:click="saveInvoice">{{ __('Save') }}</button>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
