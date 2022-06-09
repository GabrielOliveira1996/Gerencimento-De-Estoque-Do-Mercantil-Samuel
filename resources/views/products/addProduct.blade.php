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
                <div class="row mb-3">
                    <div class="col-md-4 inputBox">
                        <input type="text" name="name" class="input" required autocomplete="off">
                        <label class="labelInput">Nome</label>
                    </div>
                    <div class="col-lg-2 inputBox">
                        <input type="text" name="price" class="input" required autocomplete="off">
                        <label class="labelInput">Valor</label>
                    </div>
                    <div class="col-lg-2 inputBox">
                        <input type="number" name="quantity" class="input" required autocomplete="off">
                        <label class="labelInput">Quantidade</label>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-lg-8 inputBox">
                        <input type="text" name="bar_code" class="input" maxlength="13" required autocomplete="off">
                        <label class="labelInput">Código de barras</label>
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
