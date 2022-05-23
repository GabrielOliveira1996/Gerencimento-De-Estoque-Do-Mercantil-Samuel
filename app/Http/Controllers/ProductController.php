<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;

use Ark4ne\XlReader\Factory;

class ProductController extends Controller
{
    
    public function __construct(){

        $this->middleware('auth');

    }

    public function addProductView(){

        return view('products.addProduct');
    }

    public function addProductByExcel(Request $request){

        $file = $request->file('file');
        
        if($file){ //verifica se o arquivo foi enviado no input
            $read = (new FastExcel)->import($file, function($line){

                $product = Product::where('bar_code', $line['código de barras'])->get()->first();

                if($product != null){ 

                    session()->flash('successRegisteringToStock', 'Produtos adicionados com sucesso ao estoque.');

                    Product::where('bar_code', $line['código de barras'])->update([
                        'quantity' => $product['quantity'] + $line['quantidade'],
                    ]);

                }else{

                    session()->flash('successRegisteringToStock', 'Produtos adicionados com sucesso ao estoque.');

                    Product::create([
                        'name' => $line['nome'],
                        'price' => $line['valor'],
                        'quantity' => $line['quantidade'],
                        'bar_code' => $line['código de barras']
                    ]);

                }
                    
                
            });

            return redirect()->route('stock');

        }else{

            session()->flash('fileNotSelected', 'Favor selecione algum arquivo Excel(xlsx) com produtos para serem cadastrados.');

            return redirect()->route('addProductView');
        }

        //return view('products.addProductByExecel', compact('rows'));

    }

    public function addProduct(Request $request){

        $rules = [
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'quantity' => 'required|numeric|min:0',
            'bar_code' => 'required|numeric|min:13' ///não funciona corretamente
        ];

        $message = [
            'name.required' => 'Necessário que o produto tenha um nome.',
            'price.required' => 'Necessário que o produto tenha um valor.',
            'price.numeric' => 'Valor precisa receber números.',
            'price.min' => 'Valor de produto não pode receber zero ou ser negativo.',
            'quantity.required' => 'Necessário que o produto tenha um valor.',
            'quantity.numeric' => 'Quantidade precisa receber números.',
            'quantity.min' => 'Quantidade de produto precisa ser igual ou maior que zero.',
            'bar_code.required' => 'Necessário que o produto tenha código de barras.',
            'bar_code.numeric' => 'Código de barras precisa receber números.',
            'bar_code.min' => 'Necessário que o código de barras tenha 13 digitos.'
        ];

        $request->validate($rules, $message);

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        //refazer código
        //verificar se o código de barras e o nome do produto já existem em sistema.
        //se o nome existir, quantidade será adicionada a produto já existente em db.
        //se código de barras existir, será gerado uma sessão flash no qual será informado que código já existe.
        //sou mto pirocudo vei
        $productVerify = Product::where('name', $name)
                                ->get()
                                ->first();
  
        if(isset($productVerify->name)){
            if($name == $productVerify->name){//corrigir usando orm, ~~método update
                $productVerify->quantity = $productVerify->quantity + $quantity;
                $productVerify->save();
    
                return redirect()->route('stock');
            }
        }else{                 
            Product::create([
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'bar_code' => $barcode,
            ]);

            return redirect()->route('stock');
        }
         
    }

    public function searchProductInStock(Request $request){

        $barcode = $request->input('bar_code');

        //dd($barcode);
        if($barcode){
            $allProducts = Product::where([['bar_code', 'like', '%'.$barcode.'%']])->paginate(10);
            //dd($allProducts->total());
            if($allProducts->total() != 0){      
                session()->forget('productDontExistInStock');

                return view('products.stock', ['allProducts' => $allProducts]);
            }else{
                session()->flash('productDontExistInStock', 'Nenhum produto foi localizado.');

                return view('products.stock', ['allProducts' => $allProducts]);
            }  
        }else{
            session()->flash('untypedBarCode', 'Favor digite algum código de barras.');

            return redirect()->route('searchProductInStock');
        }
        
    }

    public function ShowAllProductsInStock(){

        $allProducts = Product::where('quantity', '>=', 0)->paginate(10); //paginator esta usando produtos que não possuem em estoque

        return view('products.stock', compact('allProducts'));
    }   

    public function deleteProduct($id){

        Product::where('id', $id)->delete();

        return redirect()->route('stock');
    }

    //EDITAR PRODUTOS
    public function editProductView($id){

        $catchProduct = Product::where('id', $id)->get()->first();

        return view('products.editProduct', ['catchProduct' => $catchProduct]);
    }

    public function editProduct(Request $request, $id){

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        $rules = [
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'quantity' => 'required|numeric|min:0',
            'bar_code' => 'required|numeric|min:13'
        ];

        $message = [
            'name.required' => 'Necessário que o produto tenha um nome.',
            'price.required' => 'Necessário que o produto tenha um valor.',
            'price.numeric' => 'Valor precisa receber números.',
            'price.min' => 'Valor de produto não pode receber zero ou ser negativo.',
            'quantity.required' => 'Necessário que o produto tenha um valor.',
            'quantity.numeric' => 'Quantidade precisa receber números.',
            'quantity.min' => 'Quantidade de produto precisa ser igual ou maior que zero.',
            'bar_code.required' => 'Necessário que o produto tenha código de barras.',
            'bar_code.numeric' => 'Código de barras precisa receber números.',
            'bar_code.min' => 'Necessário que o código de barras tenha 13 digitos.'
        ];

        $request->validate($rules, $message);

        Product::find($id)->update([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'bar_code' => $barcode,
        ]);
        
        return redirect()->route('stock');
    }

    public function cart(){

        $allCartProducts = CartProduct::where('user_id', auth()->user()->id)
                                        ->where('invoice', 0)
                                        ->get();
        $subTotalPrice = 0; //valor total sem desconto
        $totalUnits = 0; //quantidade total de unidades
        $discount = 0; //valor do desconto
        $totalPrice = 0; //valor total final

        foreach($allCartProducts as $p){
            $subTotalPrice += $p->quantity * $p->price;
            $totalUnits += $p->quantity;
            
        }

        if($totalUnits > 10){
            $discount = $subTotalPrice*0.10;
            $totalPrice = $subTotalPrice - $discount;
        }else{
            $totalPrice = $subTotalPrice;
        }

        return view('products.cart', [
            'allCartProducts' => $allCartProducts, 
            'subTotalPrice' => $subTotalPrice,
            'discount' => $discount,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function addProductCart(Request $request){

        $barcode = $request->input('bar_code');
        $quantity = $request->input('quantity');
        
        if($barcode && $quantity){
            $checkProduct = Product::where('bar_code', $barcode)
                                ->where('quantity', '>=', $quantity)
                                ->get()
                                ->first();   
            //dd($checkProduct);
            //checar se a variável retornou algo, ou seja, se a quantidade em estoque 
            if($checkProduct != null){
                $checkProductCart = CartProduct::where('name', $checkProduct->name)
                                ->where('user_id', auth()->user()->id)
                                ->where('invoice', 0)
                                ->get()
                                ->first();
            }else{
                session()->flash('unavailableQuantity', 'Verifique código de barras e quantidade em estoque.'); //sessão de quantidade em estoque inválida.
                return redirect()->route('cart');
            }
            
            //dd($checkProductCart);  
            if(!empty($checkProduct)){
                $checkProduct->decrement('quantity', $quantity);
                //dd($checkProductCart);
                //checar nome de produto, se esse produto existir na tabela cart e for o mesmo usuário que esta cadastrando, atualiza quantidade.
                if(!empty($checkProductCart) && $checkProduct->name == $checkProductCart->name){ 
                    $checkProductCart->update([ 
                        'quantity' => $quantity+$checkProductCart->quantity,
                    ]);

                    return redirect()->route('cart');
                }else{

                    CartProduct::create([ 
                        'user_id' => auth()->user()->id,
                        'name' => $checkProduct->name,
                        'quantity' => $quantity,
                        'price' => $checkProduct->price,
                        'bar_code' => $checkProduct->bar_code,
                    ]);

                    return redirect()->route('cart');
                }    
            }else{
                session()->flash('productDontExistToRegistration', 'Produto não existe, favor verifique o código de barrras.');
                return redirect()->route('cart');
            }
        }else{
            session()->flash('productDontExistToRegistration', 'Produto não existe, favor verifique o código de barrras.');
            return redirect()->route('cart');
        }  
    }

    //essa método precisa deletar itens do carrinho de compras e retornar quantidade para estoque.
    public function deleteProductCart($id, Request $request){

        $cartProduct = CartProduct::find($id); //pegando produto do carrinho através do id.
        
        //se o produto do carrinho existir, e o produto do estoque não existir, o que acontece?
        if(!empty($cartProduct)){ //se váriavel não estiver vazia.
            //buscando por produdo através de código de barras na table products.
            $product = Product::where('bar_code', $cartProduct->bar_code)->first(); 

            //retorna o "quantity" do produto do carrinho para o produto do estoque.
            if(!empty($product)){
                $product->update([  
                    'quantity' => $cartProduct->quantity + $product->quantity, 
                ]); 
                
                $cartProduct->delete();

                session()->flash('successReturningToStock', "$cartProduct->quantity unidades de $cartProduct->name retornou para estoque.");
                return redirect()->route('cart');
                
            }else{
                return 'Erro 001 - Produto em estoque não existe.';
            }
             
        }else{//Produto em carrinho não existe.
            session()->flash('productDontExistInCart', 'Produto não existe em carrinho.');
            //dd(session('erro'));
            return redirect()->route('cart');
        }
        
    }

    //gerando comprovante
    public function generateInvoice(){

        $products = CartProduct::where('user_id', auth()->user()->id)
                                    ->where('invoice', 0)
                                    ->get();
        
        //dd($hash);
        //condição pra não gerar nf se não existir produtos.  
        if(count($products)){

            $invoice = md5(rand(0,1000).time());
            
            foreach($products as $c){
                $c->invoice = $invoice;
                $c->save();
                $date = $c->updated_at;
            }
            
            $pdf = PDF::loadView('products.finishingSale', compact('products', 'date', 'invoice'));
            return $pdf->setPaper('a6')->stream('teste.pdf');
        }else{ 
            session()->flash('thereAreProductsInTheCart', 'Não existem produtos para ser gerado comprovante.');
            return redirect()->route('cart');
        }
    }

    public function searchInvoiceView(){

        return view('products.searchInvoice');
    }

    //buscando comprovantes
    public function searchInvoice(Request $request){

        $requestCodeInvoice = $request->input('invoice');

        if($requestCodeInvoice){

            $products = CartProduct::where('invoice', $requestCodeInvoice)
                                            ->get();

            if(count($products) > 0){
                $firstProductInvoice = CartProduct::where('invoice', $requestCodeInvoice)
                                                ->get()
                                                ->first();

                $date = $firstProductInvoice->updated_at;
                $invoice = $firstProductInvoice->invoice;

                $pdf = PDF::loadView('products.finishingSale', compact('products', 'date', 'invoice'));
                return $pdf->setPaper('a6')->stream('teste.pdf');
            }else{
                session()->flash('invoiceDontExist', 'Comprovante não localizado, favor verifique o código digitado.');
                return redirect()->route('searchInvoiceView');
            }
            
        }else{
            session()->flash('codeDontTyped', 'Favor digite o código do comprovante.');
            return redirect()->route('searchInvoiceView');
        }
        
        
    }
}
