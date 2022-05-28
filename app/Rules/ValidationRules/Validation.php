<?php

namespace App\Rules\ValidationRules;

class Validation{

    public static function validationRulesOfProducts($request, $name, $price, $quantity, $barcode){

        $rules = [
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'quantity' => 'required|numeric|min:1',
            'bar_code' => 'required|digits:13' 
        ];
    
        $message = [
            'name.required' => 'Necessário que o produto tenha um nome.',
            'price.required' => 'Favor preencha o campo valor.',
            'price.numeric' => 'O campo valor precisa receber números.',
            'price.min' => 'Valor de produto não pode receber zero ou ser negativo.',
            'quantity.required' => 'Necessário que o produto tenha um valor.',
            'quantity.numeric' => 'Quantidade precisa ser preechido com números.',
            'quantity.min' => 'Quantidade de produto não pode receber zero ou ser negativo.',
            'bar_code.required' => 'Necessário que o produto tenha código de barras.',
            'bar_code.digits' => 'Código de barras precisa ser numérico e ter no máximo 13 digitos.'
        ];

        return $request->validate($rules, $message);
    }
}


