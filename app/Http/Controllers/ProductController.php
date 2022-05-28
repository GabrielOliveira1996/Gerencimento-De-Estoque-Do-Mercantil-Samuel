<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartProduct;
use App\Models\Invoice;
use App\Rules\ValidationRules\Validation;
use App\Rules\Process\Calculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;


class ProductController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function addProductByExcel(Request $request){

        $file = $request->file('file');
        
        if($file == null){ 
            session()->flash('fileNotSelected', 'Favor selecione algum arquivo Excel(xlsx) com produtos para serem cadastrados.');
            
            return redirect()->route('addProductView');
        }
        
        $read = (new FastExcel)->import($file, function($line){
            
            //Validation::validationRulesOfProducts($request, $line['nome'], $line['valor'], $line['quantidade'], $line['código de barras']);

            $product = Product::where('bar_code', $line['código de barras'])->get()->first();

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

        return redirect()->route('stock');
    }

    public function addProduct(Request $request){

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        Validation::validationRulesOfProducts($request, $name, $price, $quantity, $barcode);

        $productVerify = Product::where('bar_code', $barcode)
                                ->get()
                                ->first();
        
        if($productVerify){
            $productVerify->increment('quantity', $quantity);
            $amountAddedToExistingProductMessage = "Quantidade adicionada 
                                                    a produto já existente 
                                                    em estoque. Uma quantidade 
                                                    de $quantity produtos foram 
                                                    adicionados a $productVerify->name.";

            session()->flash('amountAddedToExistingProduct', $amountAddedToExistingProductMessage);

            return redirect()->route('stock');
        }  
                   
        Product::create([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'bar_code' => $barcode,
        ]);

        $productSuccessfullyAddedToStockMessage = "$name adicionado com suceso a estoque.";

        session()->flash('productSuccessfullyAddedToStock', $productSuccessfullyAddedToStockMessage);

        return redirect()->route('stock');       
    }

    public function searchProductInStock(Request $request){

        $barcode = $request->input('bar_code');

        if($barcode){
            $allProducts = Product::where([['bar_code', 'like', '%'.$barcode.'%']])->paginate(10);
            
            if($allProducts->total() != 0){      
                session()->forget('productDontExistInStock');

                return view('products.stock', compact('allProducts'));
            }

            session()->flash('productDontExistInStock', 'Nenhum produto foi localizado.');
            return view('products.stock', compact('allProducts'));     
        }

        session()->flash('untypedBarCode', 'Favor digite algum código de barras.');
        return redirect()->route('searchProductInStock');
    }

    public function ShowAllProductsInStock(){
        $allProducts = Product::where('quantity', '>', 0)->paginate(10); 
        return view('products.stock', compact('allProducts'));
    }   

    public function deleteProduct($id){
        Product::where('id', $id)->delete();
        return redirect()->route('stock');
    }

    public function editProductView($id){
        $catchProduct = Product::where('id', $id)->get()->first();
        return view('products.editProduct', compact('catchProduct'));
    }

    public function editProduct(Request $request, $id){

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        Validation::validationRulesOfProducts($request, $name, $price, $quantity, $barcode);

        Product::find($id)->update([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'bar_code' => $barcode,
        ]);

        $successfulProductChangeMessage = "Alteração em $name foi concluída com sucesso.";

        session()->flash('successfulProductChange', $successfulProductChangeMessage);
        return redirect()->route('stock');
    }

    public function cart(){

        $allCartProducts = CartProduct::where('user_id', auth()->user()->id)
                                        ->get();
        
        $subTotalPrice = Calculation::subTotalPrice($allCartProducts);                                
        $totalPrice = Calculation::totalPrice($allCartProducts);
        $discountValue = $subTotalPrice - $totalPrice;
       
        return view('products.cart', [
            'allCartProducts' => $allCartProducts, 
            'subTotalPrice' => $subTotalPrice,
            'totalPrice' => $totalPrice,
            'discountValue' => $discountValue,
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
            
            if($checkProduct){
                $checkProductCart = CartProduct::where('name', $checkProduct->name)
                                ->where('user_id', auth()->user()->id)
                                ->get()
                                ->first();

                $checkProduct->decrement('quantity', $quantity);
                
                if($checkProductCart != null){ 
                    $checkProductCart->increment('quantity', $quantity);
                    
                    return redirect()->route('cart');
                }

                CartProduct::create([ 
                    'user_id' => auth()->user()->id,
                    'name' => $checkProduct->name,
                    'quantity' => $quantity,
                    'price' => $checkProduct->price,
                    'bar_code' => $checkProduct->bar_code,
                ]);

                return redirect()->route('cart');
                }
            }

        session()->flash('unavailableQuantity', 'Verifique o código de barras e quantidade em estoque.'); 
        return redirect()->route('cart');      
    }

    public function deleteProductCart($id, Request $request){

        $cartProduct = CartProduct::find($id); 
        
        if($cartProduct){ 
            
            $product = Product::where('bar_code', $cartProduct->bar_code)->first(); 

            if($product){
                $product->increment('quantity', $cartProduct->quantity); 
                $cartProduct->delete();

                session()->flash('successReturningToStock', "$cartProduct->quantity unidades de $cartProduct->name retornou para estoque.");
                return redirect()->route('cart');
                
            }else{
                return 'Erro 001 - Produto em estoque não existe.';
            }
             
        }
            
        session()->flash('productDontExistInCart', 'Produto não existe em carrinho.');
        return redirect()->route('cart');  
    }

    public function generateInvoice(){

        $cartProducts = CartProduct::where('user_id', auth()->user()->id)
                                ->get();
        
        if(count($cartProducts)){
            
            $invoice = md5(rand(0,1000).time());
            
            foreach($cartProducts as $product){      
                Invoice::create([
                    'user_id' => auth()->user()->id,
                    'invoice' => $invoice,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'bar_code' => $product->bar_code
                ]);

                $date = $product->updated_at;                
            }

            CartProduct::where('user_id', auth()->user()->id)->delete();
 
            $invoiceProducts = Invoice::where('user_id', auth()->user()->id)
                                        ->where('invoice', $invoice)
                                        ->get();
            $subTotalPrice = Calculation::subTotalPrice($invoiceProducts);                                
            $totalPrice = Calculation::totalPrice($invoiceProducts);
            $discountValue = $subTotalPrice - $totalPrice;

            $pdf = PDF::loadView('products.finishingSale', compact('invoiceProducts', 'date', 'invoice', 'subTotalPrice', 'totalPrice', 'discountValue'));
            return $pdf->setPaper('a6')->stream("comprovante $invoice.pdf");
        }

        session()->flash('thereAreProductsInTheCart', 'Não existem produtos para ser gerado comprovante.');
        return redirect()->route('cart');
    }

    public function searchInvoiceView(){

        return view('products.searchInvoice');
    }

    public function searchInvoice(Request $request){

        $requestCodeInvoice = $request->input('invoice');

        if($requestCodeInvoice){
            
            $invoiceProducts = Invoice::where('invoice', $requestCodeInvoice)
                                    ->get();
            
            if(count($invoiceProducts) <= 0){

                session()->flash('invoiceDontExist', 'Comprovante não localizado, favor verifique o código digitado.');
                return redirect()->route('searchInvoiceView');  
            }
                
            $firstProductInvoice = Invoice::where('invoice', $requestCodeInvoice)
                                                ->get()
                                                ->first();

            $date = $firstProductInvoice->updated_at;
            $invoice = $firstProductInvoice->invoice;
            $subTotalPrice = Calculation::subTotalPrice($invoiceProducts);                                
            $totalPrice = Calculation::totalPrice($invoiceProducts);
            $discountValue = $subTotalPrice - $totalPrice;

            $pdf = PDF::loadView('products.finishingSale', compact('invoiceProducts', 'date', 'invoice', 'subTotalPrice', 'totalPrice', 'discountValue'));
            return $pdf->setPaper('a6')->stream("comprovante $invoice.pdf");  
        }
        
        session()->flash('codeDontTyped', 'Favor digite o código do comprovante.');
        return redirect()->route('searchInvoiceView');  
    }
}
