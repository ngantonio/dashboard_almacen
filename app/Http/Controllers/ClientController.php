<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;

class ClientController extends Controller
{
    //Permite gestionar a todas las personas,
    // todas las personas son clientes

    public function index(Request $request){
       
        if(!$request->ajax()) 
            return redirect('/');

        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;

        if($text_search == '')
            $persons = Person::paginate(3);
        else 
            $persons = Person::where($search_criteria, 'like', '%'. $text_search . '%')->paginate(3);
        
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
            'persons' => $persons

        ];
    }

    public function store(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $person                 =   new Person();
        $person->name           =   $request->name;
        $person->document_type  =   $request->document_type;
        $person->document_number=   $request->document_number;
        $person->address        =   $request->address;
        $person->phone_number   =   $request->phone_number;
        $person->email          =   $request->email;
        $person->save(); 

    }

    public function update(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $person                 =   Person::findOrFail($request->id);
        $person->name           =   $request->name;
        $person->document_type  =   $request->document_type;
        $person->document_number=   $request->document_number;
        $person->address        =   $request->address;
        $person->phone_number   =   $request->phone_number;
        $person->email          =   $request->email;
        $person->save();
    }


    // Permite obtener un cliente dado su documento de identidad 
    //desde Sale.vue, al realizar una venta
    public function getClient(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $doc_number = $request->doc_number;

        $client = Person::where('persons.document_number','=', $doc_number)
            ->select('persons.id','persons.name','persons.document_number')
            ->get();
    
            return ['client' => $client];
    }

}
