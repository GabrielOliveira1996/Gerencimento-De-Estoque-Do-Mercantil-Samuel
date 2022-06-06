@extends('layouts.app')

@section('content')
<div class="container">

    @if($errors->any())
        <div class="align-errors">
            @foreach($errors->all() as $error)
                {{$error}}
                <br>
            @endforeach
        </div>
    @endif
    
    @if(!empty(session('fileNotSelected')))
        <div class="align-errors">
            {{session('fileNotSelected')}}
        </div>
    @endif
            
    <div class="row mt-3">

        <div class="col-lg-6">

            <p>Cadastre um produto de cada vez.</p>

            <form method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" name="name" class="form-control mt-3" placeholder="Nome">
                    </div>
                    <div class="col-lg-2">
                        <input type="text" name="price" class="form-control mt-3" placeholder="Valor">
                    </div>
                    <div class="col-lg-2">
                        <input type="number" name="quantity" class="form-control mt-3" placeholder="Quantidade">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-8">
                        <input type="text" name="bar_code" class="form-control mt-3" maxlength="13" placeholder="Código de barras">
                    </div>
                </div>
                    
                <button type="submit" class="btn btn-primary mt-3">Adicionar</button>

            </form>
        </div>

        <div class="col-lg-6">

            <p>Cadastre diversos produtos através de um arquivo Excel.</p>

            <form method="POST" enctype="multipart/form-data" action="{{route('addProductByExcel')}}">
                @csrf     
                <div class="row">
                    <div class="col-lg-9">
                        <input type="file" class="form-control mt-3" name="file" placeholder="Código de barras ou nome">
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary mt-3">Adicionar</button>
                    </div> 
                </div>
            </form>
        </div>

    </div>

    <hr size="4px" style="color:black">
</div>
@endsection
