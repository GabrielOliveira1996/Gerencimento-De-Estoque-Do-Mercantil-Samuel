@extends('layouts.app')

@section('content')
<div class="container">

    @if(!empty(session('thereIsNoProductToReturnToStock')))
        <div class="align-errors">
            {{session('thereIsNoProductToReturnToStock')}}
        </div>
    @endif

    @if(!empty(session('ProductIsNotInStock')))
        <div class="align-errors">
            {{session('ProductIsNotInStock')}}
        </div>
    @endif

    @if(!empty(session('unavailableQuantity')))
        <div class="align-errors">
            {{session('unavailableQuantity')}}
        </div>
    @endif

    @if(!empty(session('productDontExistInCart')))
        <div class="align-errors">
        {{session('productDontExistInCart')}}
        </div>
    @endif
    
    @if(!empty(session('successReturningToStock')))
        <div class="align-success-request">
        {{session('successReturningToStock')}}
        </div>
    @endif
    
    <form method="POST">
        @csrf
        <div class="row d-flex justify-content-center">
            
        <div class="row d-flex justify-content-center">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-2">
                    <input type="text" name="bar_code" class="form-control mt-3" maxlength="13" placeholder="Código de barras">
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-lg-2">
                    <input type="number" name="quantity" class="form-control mt-3" placeholder="Quantidade">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary mt-3">Adicionar</button>
            </div>
        </div>
    </form>

    @if(!empty(session('thereAreProductsInTheCart')))
        <div class="align-errors mt-3">
            {{session('thereAreProductsInTheCart')}}
        </div>
    @endif

    <table class="table table-light table-bordered mt-3">
        <thead>
            <tr>
                <th scope="col">Produto</th>
                <th scope="col">Quantidade</th>
                <th scope="col">Valor p/unidade</th>
                <th scope="col">Valor total</th>
                <th scope="col">Código de barras</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($allCartProducts as $product)           
                <tr>
                    <td>{{$product->name}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->price * $product->quantity}}</td>    
                    <td>{{$product->bar_code}}</td>
                    <td><a type="submit" class="btn btn-danger" href="{{ route('deleteProductCart', ['id' => $product->id]) }}">Apagar</a></td> 
                </tr>         
            @endforeach
                <tr>
                    <th scope="col">
                        <a href="{{route('finishingSale')}}" class="btn btn-primary" target="_blank">Gerar Nota</a>
                    </th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">Desconto: {{$discountValue}}</th>
                    <th scope="col">Subtotal: {{$subTotalPrice}}</th>
                    <th scope="col">Total: {{$totalPrice}}</th>
                </tr>
        </tbody>
    </table>
     
</div>
@endsection
