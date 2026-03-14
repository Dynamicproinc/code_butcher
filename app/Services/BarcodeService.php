<?php
namespace App\Services;

class BarcodeService{


    public function __construct()
    {
        
    }

    public function decodeBarcode($barcode){
    // get product id
    $product_code = substr($barcode, 4, 3);

    // get weight
    $weight = substr($barcode, 7, 5);
    $weight_in_kg = intval($weight) / 1000;

    return compact('product_code', 'weight', 'weight_in_kg');
}

}