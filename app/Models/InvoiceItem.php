<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'total_weight',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
        
    ];
}
