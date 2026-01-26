<?php

use Livewire\Component;

new class extends Component
{
    public $count = 0;

    public function increment(){
        $this->count ++;
    }
};
?>

<div>
    He who is contented is rich. - Laozi
    {{$count}}
    <button wire:click="increment">Increment</button>
</div>