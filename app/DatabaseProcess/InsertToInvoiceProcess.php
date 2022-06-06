<?php

namespace App\DatabaseProcess;

use App\Models\Invoice;
use App\Calculations\Calculation;
use Illuminate\Support\Facades\Auth;

class InsertToInvoiceProcess {
    
    public static function productOnInvoice($id, $cartProducts, $invoice){
 
        $subTotalPrice = Calculation::subTotalPrice($cartProducts);                                
        $totalPrice = Calculation::totalPrice($cartProducts);
        $discountValue = $subTotalPrice - $totalPrice;     

        $data = array();
        foreach($cartProducts as $cartProduct){
            $data[] = [
                'user_id' => $id,
                'invoice' => $invoice,
                'name' => $cartProduct->name,
                'quantity' => $cartProduct->quantity,
                'price' => $cartProduct->price,
                'bar_code' => $cartProduct->bar_code,
                'subtotal' => $subTotalPrice,
                'discount' => $discountValue,
                'total' => $totalPrice,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }      
        
        return $data;
    }
}