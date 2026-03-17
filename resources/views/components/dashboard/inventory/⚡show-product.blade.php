<?php

use Livewire\Component;

new class extends Component
{
    public $product;


    public function mount($product){
        $this->product = $product;
    }


};
?>

<div>
   <div>
    {{ dd($product) }}
   </div>
</div>