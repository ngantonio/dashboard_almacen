<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Provider;
use App\Person;


class ProviderController extends Controller
{
    //
    public function index(Request $request){
       
        if(!$request->ajax()) 
            return redirect('/');

        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;

        if($text_search == '')
            $providers = Provider::join('persons','persons.id','=','providers.id')
                ->select('*')
                ->paginate(3);
        else 
            $providers = Provider::join('persons','persons.id','=','providers.id')
                ->where('persons.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->select('*')
                ->paginate(3);
        
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
            'pagination' => [
                'total'         =>  $providers->total(),
                'current_page'  =>  $providers->currentPage(),
                'per_page'      =>  $providers->perPage(),
                'last_page'     =>  $providers->lastPage(),
                'from'          =>  $providers->firstItem(),
                'to'            =>  $providers->lastItem(),
            ],
            'persons' => $providers

        ];

        /*'persons.id','persons.name'
                ,'persons.document_type'
                ,'persons.document_number'
                ,'persons.address'
                ,'persons.phone_number'
                ,'persons.email','provider.contact_name','provider.contact_phone' */
    }

    public function store(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        try{
            // Iniciamos una transaccion
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
            // registramos a un proveedor
            $provider                 =   new Provider();
            $provider->id             =   $person->id;
            $provider->contact_name   =   $request->contact_name;
            $provider->contact_phone  =   $request->contact_phone;
            $provider->save(); 
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

          try{
            // Iniciamos una transaccion
            DB::beginTransaction();

            // Buscamos al proveedor y la persona a modificar
            $provider               =   Provider::findOrFail($request->id);
            $person                 =   Person::findOrFail($provider->id);
            
            $person->name           =   $request->name;
            $person->document_type  =   $request->document_type;
            $person->document_number=   $request->document_number;
            $person->address        =   $request->address;
            $person->phone_number   =   $request->phone_number;
            $person->email          =   $request->email;
            $person->save(); 
            // Actualizamos el proveedor
            $provider->contact_name   =   $request->contact_name;
            $provider->contact_phone  =   $request->contact_phone;
            $provider->save(); 
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    /* Obtiene la lista de todos los proveedores necesaria para enviarla mediante ajax
    a la vista, Income.vue al momento de agregar una nueva venta / ingreso */

    public function getProviders(Request $request){
        
        if(!$request->ajax()) 
            return redirect('/');

        $providers = Provider::join('persons','providers.id','=','persons.id')
            ->select('persons.id','persons.name','persons.document_number')
            ->orderBy('persons.name','asc')->get();
    
            return ['providers' => $providers];
        
        /*
        En caso de permitir buscar provvedores por filtros: ...
        
        $filter =  $request->filter;
        // proveedores cuyo nombre o CI coincida con el filtro
        $providers = Provider::join('persons','providers.id','=','persons.id')
        ->where('persons.name','like','%'. $filter. '%')
        ->orWhere('persons.document_number', 'like', '%' .$filter. '%')
        ->select('persons.id','persons.name','persons.document_number')
        ->orderBy('persons.name','asc')->get();

        return ['providers' => $providers];*/
    }
}
