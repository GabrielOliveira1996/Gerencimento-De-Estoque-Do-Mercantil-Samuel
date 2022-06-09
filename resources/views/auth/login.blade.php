@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        
        <div class="card">
            <div class="card-header">{{ __('Entrar') }}</div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="row mb-3 d-flex justify-content-center">     
                        <div class="col-md-4 inputBox">
                            <input id="email" class="input" name="email" required autocomplete="off">
                            <label class="labelInput">Email</label>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3 d-flex justify-content-center">
                        
                        <div class="col-md-4 inputBox">
                            <input id="password" type="password" class="input" name="password" required autocomplete="off">
                            <label class="labelInput">Senha</label>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Lembrar de mim.') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Esqueceu sua senha?') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>
@endsection
