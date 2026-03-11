<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedOrder extends Model
{
     protected $fillable = [
        'wc_order_id'
    ];
}
