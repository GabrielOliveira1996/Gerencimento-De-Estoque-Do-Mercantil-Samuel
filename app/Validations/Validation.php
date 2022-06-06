<?php

namespace App\Validations;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

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

    public static function nameRequiredToExcel($name, $price, $quantity, $barcode){

        //dd($name);
        if(empty($name)){
            session()->flash('nameRequired', 'Um dos produtos não possui nome, então não foi cadastrado em estoque.');
            return true;
        }

    }


    public static function validationRulesOfProductsAPI($request){

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


