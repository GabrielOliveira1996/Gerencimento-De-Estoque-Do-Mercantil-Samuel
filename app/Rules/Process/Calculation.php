<?php

namespace App\Rules\Process;

class Calculation{

    public static function subTotalPrice($colletionObjects){

        $subTotalPrice = 0; //valor total sem desconto

        foreach($colletionObjects as $object){
            $subTotalPrice += $object->quantity * $object->price;
        }

        return $subTotalPrice;
    }

    public static function totalPrice($colletionObjects){

        $totalPrice = 0; //valor total final
        $discountPercentage = 0.10; //desconto em porcentagem 0.10 = 10%;
        $totalUnits = 0; //unidades totais da compra

        foreach($colletionObjects as $object){
            $totalPrice += $object->quantity * $object->price;
            $totalUnits += $object->quantity; 
        }
        
        if($totalUnits > 10){
            $totalPrice = $totalPrice - ($totalPrice * $discountPercentage);
        }
        
        return $totalPrice;
    }
}