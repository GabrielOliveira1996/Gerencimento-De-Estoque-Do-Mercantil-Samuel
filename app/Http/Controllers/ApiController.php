<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Validations\Validation;

//-controller para controle de estoque
//--visualização de todos os produtos em estoque.
//--visualização de um produto em estoque.
//--adição de produto a estoque.
//--editar produto em estoque.
//--deletar produto em estoque.

class ApiController extends Controller
{

    /*
    public function __construct(){
        
        $this->middleware('auth');
    }*/

    public function getAllProducts(){
        
        $arrayProducts = ['error' => ''];

        $products = Product::all();

        if($products == null){
            $arrayProducts['error'] = 'Não foi localizado nenhum produto em estoque.';
            return $arrayProducts['error'];
        }

        $arrayProducts['success'] = $products;

        return $arrayProducts['success'];
    }

    public function getProduct($id){

        $arrayProduct = ['error' => ''];

        $product = Product::find($id);

        if($product == null){
            $arrayProduct['error'] = 'Produto não existe em estoque.';
            return $arrayProduct['error'];
        }

        $arrayProduct['success'] = $product;

        return $arrayProduct['success'];
    }

    public function updateProduct(Request $request, $id){

        $arrayProduct['error'] = '';
        
        $rules = [
            'name' => 'min:5',
            'price' => 'numeric|min:1',
            'quantity' => 'numeric|min:1',
            'bar_code' => 'digits:13' 
        ];
        
        $message = [
            'name.min' => 'É necessário que o nome tenha no mínimo 5 caracteres.',
            'price.numeric' => 'O campo valor precisa receber números.',
            'price.min' => 'Valor de produto não pode receber zero ou ser negativo.',
            'quantity.numeric' => 'Quantidade precisa ser preechido com números.',
            'quantity.min' => 'Quantidade de produto não pode receber zero ou ser negativo.',
            'bar_code.digits' => 'Código de barras precisa ser numérico e ter no máximo 13 digitos.'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if($validator->fails()){

            $arrayProduct['error'] = $validator->messages();
            return $arrayProduct['error'];
        }
            
        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        $product = Product::find($id);

        if($product == null){

            $arrayProduct['error'] = 'Produto não encontrado em estoque.';
            return $arrayProduct['error'];
        }

        if($name or $price or $quantity or $barcode){
            $product->update([
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'bar_code' => $barcode
            ]);

            $arrayProduct['success'] = 'Produto atualizado com sucesso.';

            return $arrayProduct['success'];
        }
        
    }

    public function addProduct(Request $request){

        $arrayProduct['error'] = '';

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
        
        $validator = Validator::make($request->all(), $rules, $message);
        
        
        if($validator->fails()){

            $arrayProduct['error'] = $validator->messages();
            return $arrayProduct['error'];
        }
            
        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        $checkBarCode = Product::where('bar_code', $barcode)->get()->first();      

        if($checkBarCode){
            
            $checkBarCode->update([
                'quantity' => $checkBarCode->quantity + $quantity,
            ]);

            $arrayProduct['success'] = 'Produto existente em estoque, quantidade acrescentada ao produto já existente.';
            
            return $arrayProduct['success'];
        }
        
        Product::create([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'bar_code' => $barcode
        ]);

        $arrayProduct['success'] = 'Produto cadastrado com sucesso em estoque.';
        
        return $arrayProduct['success']; 
    }

    public function deleteProduct($id){

        $arrayProduct['error'] = ''; 

        $productToDelete = Product::find($id);

        if($productToDelete == null){

            $arrayProduct['error'] = 'Produto não encontrado em estoque.';

            return $arrayProduct['error'];
        }

        $productToDelete->delete();

        $arrayProduct['success'] = 'Produto excluído com sucesso.';

        return $arrayProduct['success'];
    }

}
