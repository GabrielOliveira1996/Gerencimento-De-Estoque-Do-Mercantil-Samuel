<?php

namespace App\Imports;

use App\Models\Product;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Validations\Validation;
use Illuminate\Http\Request;

class ProductExcel {
   
    public static function import($file){

        (new FastExcel)->import($file, function($line){

            $product = Product::where('bar_code', $line['código de barras'])->get()->first();
            
            $validator = Validation::nameRequiredToExcel($line['nome'], $line['valor'], $line['quantidade'], $line['código de barras']);
            //dd($validator);
            
            if($validator == true){
                
                return $validator;
            }

            if($product){ 
                
                Product::where('bar_code', $line['código de barras'])->update([
                    'quantity' => $product['quantity'] + $line['quantidade'],
                ]);

                session()->flash('successRegisteringToStock', 'Produtos adicionados com sucesso ao estoque.');

                return redirect()->route('stock');
            }

            Product::create([
                'name' => $line['nome'],
                'price' => $line['valor'],
                'quantity' => $line['quantidade'],
                'bar_code' => $line['código de barras']
            ]);

            session()->flash('successRegisteringToStock', 'Produtos adicionados com sucesso ao estoque.');

            return redirect()->route('stock');
        });      
    }
}