<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        /* 
            Determina si la peticion que se esta haciendo
            es diferente de ajax, si es asi, accede, sino, 
            redirige hacia la ruta raiz, esto es apra evitar que
            se hagan peticiones a rutas por url 
        */
        
        #if(!$request->ajax()) 
        #    return redirect('/');

        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;

        /*  - si no hay texto de busqueda la paginacion se hace normal.
            - si lo hay, el texto de busqueda puede estar al inicio, en el medio o al final, 
            de cualquier registro de la columna del criterio (nombre o descripcion) 
        */
        if($text_search == '')
            $categories = Category::paginate(3);
        else 
            $categories = Category::where($search_criteria, 'like', '%'. $text_search . '%')->paginate(3);
        
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
            'pagination' => [
                'total'         =>  $categories->total(),
                'current_page'  =>  $categories->currentPage(),
                'per_page'      =>  $categories->perPage(),
                'last_page'     =>  $categories->lastPage(),
                'from'          =>  $categories->firstItem(),
                'to'            =>  $categories->lastItem(),
            ],
            'categories' => $categories

        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $cat                = new Category();
        $cat->name          = $request->name;
        $cat->description   = $request->description;
        $cat->active        = true;
        $cat->save(); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $cat                = Category::findOrFail($request->id);
        $cat->name          = $request->name;
        $cat->description   = $request->description;
        $cat->save(); 
    }

    /* ELiminacion logica: activar o descativar la categoria */
    public function activar(Request $request){

        if(!$request->ajax()) 
            return redirect('/');

        $cat                = Category::findOrFail($request->id);
        $cat->active        = true;
        $cat->save(); 
    }

    public function desactivar(Request $request){

        if(!$request->ajax()) 
            return redirect('/');
        $cat                = Category::findOrFail($request->id);
        $cat->active        = false;
        $cat->save(); 
    }
}
