<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Person;

class UserController extends Controller
{
       
    public function index(Request $request){
         
        if(!$request->ajax()) 
            return redirect('/');
 
        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;
 
 
        if($text_search == '')
            $persons = User::join('persons','persons.id', '=','users.id' )
                 ->join('roles','roles.id','=','users.id_role')
                 ->select('*')
                 ->paginate(6);
        else    
            $persons = User::join('persons','persons.id', '=','users.id' )
                ->join('roles','roles.id','=','users.id_role')
                ->select('*')                
                ->where('persons.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->paginate(6);
 
         
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
             'pagination' => [
                 'total'         =>  $persons->total(),
                 'current_page'  =>  $persons->currentPage(),
                 'per_page'      =>  $persons->perPage(),
                 'last_page'     =>  $persons->lastPage(),
                 'from'          =>  $persons->firstItem(),
                 'to'            =>  $persons->lastItem(),
            ],
             'users' => $persons
 
        ];
    }
 
     public function store(Request $request){
         
         if(!$request->ajax()) 
             return redirect('/');
 
         try{
            DB::beginTransaction();
            // registramos a una persona
            $person                 =   new Person();
            $person->name           =   $request->name;
            $person->document_type  =   $request->document_type;
            $person->document_number=   $request->document_number;
            $person->address        =   $request->address;
            $person->phone_number   =   $request->phone_number;
            $person->email          =   $request->email;
            $person->save(); 
            
            $user          = new User();
            $user->id      = $person->id;
            $user->username= $request->username;
            $user->password= bcrypt($request->password);
            $user->active  = true;
            $user->id_role = $request->id_role;
            $user->save();
            DB::commit();
         }
         catch(Exception $e){
            DB::rollBack();
         }
     }
 

     public function update(Request $request){
         if(!$request->ajax()) 
             return redirect('/');

        try{
            DB::beginTransaction();
            // buscamos a una persona
            $user                   =   User::findOrFail($request->id);
            $person                 =   Person::findOrFail($user->id);

            $person->name           =   $request->name;
            $person->document_type  =   $request->document_type;
            $person->document_number=   $request->document_number;
            $person->address        =   $request->address;
            $person->phone_number   =   $request->phone_number;
            $person->email          =   $request->email;
            $person->save(); 
        
            $user->username= $request->username;
            $user->password= bcrypt($request->password);
            $user->active  = true;
            $user->id_role = $request->id_role;
            $user->save();
            DB::commit();
         }
         catch(Exception $e){
            DB::rollBack();
         }
     }
 
 
     public function changeStatus(Request $request){
 
        if(!$request->ajax()) 
            return redirect('/');
 
        $user = User::findOrFail($request->id);
        $user->active = false;
         /*
        if($user->active)
             $user->active = false;
         else
             $user->active = true;*/
        
         $user->save(); 
     }
}
