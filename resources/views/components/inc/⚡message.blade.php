<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $message_body;


    public $message = false;
    
     #[On('showMessage')]
    public function handleShowMessage($data){
        $this->message = true;
        $this->message_body = $data;
       
    }
};
?>

<div>
   @if($message)
    <div 
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 2000)"
    x-show="show"
    x-transition
    class="top-alert-bar"
>
    <div class="noti-bar noti-success d-flex align-items-center">
        <div class="d-flex align-items-center">
            <div class="not-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </svg>
            </div>
            <div class="noti-content mx-2">
                <span>{{$message_body['message']}}</span>
            </div>
        </div>
    </div>
</div>
   @endif
</div>