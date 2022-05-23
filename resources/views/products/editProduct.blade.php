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
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-4">
                        <input type="text" name="name" value="{{ $catchProduct->name }}" class="form-control mt-3" placeholder="Nome">
                    </div>
                    <div class="col-lg-2">
                        <input type="text" name="price" value="{{ $catchProduct->price }}" class="form-control mt-3" placeholder="Valor">
                    </div>
                    <div class="col-lg-2">
                        <input type="number" name="quantity" value="{{ $catchProduct->quantity }}" class="form-control mt-3" placeholder="Quantidade">
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <input type="text" name="bar_code" value="{{ $catchProduct->bar_code }}" class="form-control mt-3" maxlength="13" placeholder="Código de barras">
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
