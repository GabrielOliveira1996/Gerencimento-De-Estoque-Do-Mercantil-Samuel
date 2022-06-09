@extends('layouts.app')

@section('content')
<div class="container">

    <div class="card col-lg-6 offset-lg-3">
        <div class="card-body">

            @if($errors->any())
                <div class="align-errors">
                    @foreach($errors->all() as $error)
                        {{$error}}
                        <br>
                    @endforeach
                </div>
            @endif
            
            <p class="d-flex justify-content-center">Cadastre um produto de cada vez.</p>
            <form method="POST">
                @csrf
                <div class="row d-flex justify-content-center mb-3">
                    <div class="col-lg-4 inputBox">
                        <input type="text" name="name" value="{{ $catchProduct->name }}" class="input">
                    </div>
                    <div class="col-lg-2 inputBox">
                        <input type="text" name="price" value="{{ $catchProduct->price }}" class="input">
                    </div>
                    <div class="col-lg-2 inputBox">
                        <input type="number" name="quantity" value="{{ $catchProduct->quantity }}" class="input">
                    </div>
                </div>
                <div class="row d-flex justify-content-center mb-3">
                    <div class="col-lg-8 inputBox">
                        <input class="input" type="text" name="bar_code" value="{{ $catchProduct->bar_code }}" maxlength="13">
                        <label class="labelInput">Código de barras</label>
                    </div>
                </div>

                <div class="row">
                    <div class="offset-lg-2">
                        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
