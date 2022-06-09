@extends('layouts.app')

@section('content')
<div class="container">

    @if(!empty(session('nameRequired')))
        <div class="align-errors">
            {{session('nameRequired')}}
        </div>
    @endif

    @if(!empty(session('ProductIsNotInStock')))
        <div class="align-errors">
            {{session('ProductIsNotInStock')}}
        </div>
    @endif
    
    @if(!empty(session('successfulProductChange')))
        <div class="align-success-request">
            {{session('successfulProductChange')}}
        </div>
    @endif

    @if(!empty(session('productSuccessfullyAddedToStock')))
        <div class="align-success-request">
            {{session('productSuccessfullyAddedToStock')}}
        </div>
    @endif

    @if(!empty(session('amountAddedToExistingProduct')))
        <div class="align-success-request">
            {{session('amountAddedToExistingProduct')}}
        </div>
    @endif

    @if(!empty(session('successRegisteringToStock')))
        <div class="align-success-request">
            {{session('successRegisteringToStock')}}
        </div>
    @endif

    @if(!empty(session('productDontExistInStock')))
        <div class="align-errors">
            {{session('productDontExistInStock')}}
        </div>
    @endif

    @if(!empty(session('untypedBarCode')))
        <div class="align-errors">
            {{session('untypedBarCode')}}
        </div>
    @endif

    
    <p class="d-flex justify-content-center">Busque produtos através do seu nome ou código de barras.</p>

    <form method="POST">
        @csrf
        <div class="d-flex justify-content-center">       
            <div class="row">
                <div class="col-lg-9 inputBox">
                    <input type="text" class="input" name="bar_code" maxlength="13" required autocomplete="off">
                    <label class="labelInput">Código de barras</label>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>  
            </div>
        </div>
    </form>

    <table class="table table-light table-bordered mt-5">
        <thead>
            <tr>
                <th scope="col">Produto</th>
                <th scope="col">Quantidade</th>
                <th scope="col">Valor p/unidade</th>
                <th scope="col">Código de barras</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($allProducts as $product)           
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->quantity }}{{ __(' unidades') }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->bar_code }}</td> 
                    <td><a type="submit" class="btn btn-danger" href="{{route('deleteProduct', ['id' => $product->id])}}">Apagar</a></td> 
                    <td><a type="submit" class="btn btn-primary" href="{{route('editProductView', ['id' => $product->id])}}">Editar</a></td> 
                </tr>         
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-5">
        {{$allProducts->links()}}
    </div>
     
</div>
@endsection
