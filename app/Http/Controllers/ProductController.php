<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartProduct;
use App\Models\Invoice;
use App\Validations\Validation;
use App\Calculations\Calculation;
use App\DatabaseProcess\InsertToInvoiceProcess;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\ProductExcel;

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
  
        ProductExcel::import($file);

        return redirect()->route('stock');
    }

    public function addProduct(Request $request){

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        $validator = Validation::validationRulesOfProducts($request, $name, $price, $quantity, $barcode);
        //dd($validator);

        if($validator->message){
            return  $validator;
        }

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
        
        if($barcode == null){        
            session()->flash('untypedBarCode', 'Favor digite algum código de barras.');
            return redirect()->route('searchProductInStock');
        }

        $allProducts = Product::where([['bar_code', 'like', '%'.$barcode.'%']])->paginate(10);
            
        if($allProducts->total() != 0){      
            session()->forget('productDontExistInStock');

            return view('products.stock', compact('allProducts'));
        }

        session()->flash('productDontExistInStock', 'Nenhum produto foi localizado.');
        return view('products.stock', compact('allProducts'));     
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
        
        if($catchProduct == null){
            session()->flash('productDontExistInStock', 'Esse produto não existe em estoque.');
            return redirect()->route('stock');
        }

        return view('products.editProduct', compact('catchProduct'));
    }

    public function editProduct(Request $request, $id){

        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $barcode = $request->input('bar_code');

        Validation::validationRulesOfProducts($request, $name, $price, $quantity, $barcode);

        $product = Product::find($id);

        if($product == null){
            session()->flash('productDontExistInStock', 'Produto não existe em estoque.');
            return redirect()->route('stock');
        }
        
        $product->update([
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
        
        if($barcode == null or $quantity == null){
            session()->flash('unavailableQuantity', 'Verifique o código de barras e quantidade em estoque.'); 
            return redirect()->route('cart');   
        }

        $checkProduct = Product::where('bar_code', $barcode)
                                ->where('quantity', '>=', $quantity)
                                ->get()
                                ->first();   
            
        if($checkProduct == null){
            session()->flash('ProductIsNotInStock', 'Produto não existe em estoque.');
            return redirect()->route('cart');
        }   

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

    public function deleteProductCart($id, Request $request){

        $cartProduct = CartProduct::find($id); 
        
        if($cartProduct == null){  
            session()->flash('productDontExistInCart', 'Produto não existe em carrinho.');
            return redirect()->route('cart');      
        }
        
        $product = Product::where('bar_code', $cartProduct->bar_code)->first(); 

        if($product == null){
            session()->flash('thereIsNoProductToReturnToStock', 'Não existe produto para ser retornado para estoque');
            return redirect()->route('cart');         
        }

        $product->increment('quantity', $cartProduct->quantity); 
        $cartProduct->delete();

        session()->flash('successReturningToStock', "$cartProduct->quantity unidades de $cartProduct->name retornou para estoque.");
        return redirect()->route('cart');
            
        
    }

    public function generateInvoice(){

        $cartProducts = CartProduct::where('user_id', auth()->user()->id)
                                    ->get();
        
        if(count($cartProducts) <= 0){
            session()->flash('thereAreProductsInTheCart', 'Não existem produtos para ser gerado comprovante.');
            return redirect()->route('cart');
        }

        $invoice = md5(rand(0,1000).time());
        $invoiceProducts = InsertToInvoiceProcess::productOnInvoice(auth()->user()->id, $cartProducts, $invoice);
        
        Invoice::insert($invoiceProducts);
        CartProduct::where('user_id', auth()->user()->id)->delete();
        
        $displayValue = Invoice::where('invoice', $invoice)->get()->first();

        $pdf = PDF::loadView('products.finishingSale', compact('invoiceProducts', 'displayValue'));
        return $pdf->setPaper('a6')->stream("comprovante $displayValue->invoice.pdf");
    }

    public function searchInvoice(Request $request){

        $codeInvoice = $request->input('invoice');

        if($codeInvoice == null){
            session()->flash('codeDontTyped', 'Favor digite o código do comprovante.');
            return redirect()->route('searchInvoiceView');       
        }

        $invoiceProducts = Invoice::where('invoice', $codeInvoice)
                                    ->get();
            
        if(count($invoiceProducts) <= 0){
            session()->flash('invoiceDontExist', 'Comprovante não localizado, favor verifique o código digitado.');
            return redirect()->route('searchInvoiceView');  
        }

        $displayValue = Invoice::where('invoice', $codeInvoice)
                                ->get()
                                ->first();

        $pdf = PDF::loadView('products.finishingSale', compact('invoiceProducts', 'displayValue'));
        return $pdf->setPaper('a6')->stream("comprovante $displayValue->invoice.pdf");  
    }
}
