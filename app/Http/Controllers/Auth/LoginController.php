<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //retorna la vista de Login
    public function showLoginForm(){
        return view('auth.login');
        //borramos la ruta que laravel genera por defecto
    }

    //Verificar credenciales del usuario
    // valida la peticion de inicio de sesion del usuario,
    // si son correctas las credenciales
    public function login(Request $request){
        
        $this->validateLogin($request);
        //comprueba que los datos existan en la base de datos
        if(Auth::attempt(['username' => $request->username,
                        'password' => $request->password,
                        'active' => 1])){
            
            return redirect()->route('main');
        }

        // retornamos a la vista anterior (login), con un mensaje de error
        // segun corresponda, ademas del input que el usuario ha escrito
        // para que esto funcione, en el campo input del userame se debe agregar:
        // value="{{ old('username) }}"
        return back()
            ->withErrors(['username'=> trans('auth.failed')])
            ->withInput(request(['username']));
    }


    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
    
    
    protected function validateLogin(Request $req){
        $this->validate($req,[
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
    } 


}
