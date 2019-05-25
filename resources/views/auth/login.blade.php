@extends('auth.content')

@section('login')

<div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group mb-0">
          <div class="card p-4">

          <form class="form-horizontal was-validated" method="post" action="{{ route('login') }}">
            {{ csrf_field() }}
            <div class="card-body">
              <h1>Acceder</h1>

              <p class="text-muted">Acceso al sistema</p>
              
              <!--agregamos una verificacion si se lanza una excepcion en el campo usuario -->
              <div class="form-group mb-3 {{ $errors->has('username' ? 'is-invalid': '')}}"}>           
                <span class="form-group-addon"><i class="icon-user"></i></span>
                <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" value="{{ old('username') }}">
                {!!$errors->first('username','<span class="invalid-feedback">:message</span>')!!}
              </div>

              <div class="form-group mb-4 {{ $errors->has('password' ? 'is-invalid': '')}}">
                <span class="form-group-addon"><i class="icon-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                {!!$errors->first('password','<span class="invalid-feedback">:message</span>')!!} 
              </div>
              <!-- Verificacion de errores de laravel -->
              
              

              <div class="row">
                <div class="col-6">
                  <button type="submit" class="btn btn-primary px-4">Acceder</button>
                </div>

                <div class="col-6 text-right">
                  <button type="button" class="btn btn-link px-0">Olvidaste tu password?</button>
                </div>
              </div>
            </form>

            </div>
          </div>

          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div>
                <h2>Sistema de Compras, Ventas y almacén</h2>
                <p>Dashboard de administración desarrollado en PHP utilizando el Framework Laravel y Vue Js, con el gestor de base de datos MariaDB.</p>
              </div>

            </div>
          </div>
        </div>
      </div>
</div>

@endsection
