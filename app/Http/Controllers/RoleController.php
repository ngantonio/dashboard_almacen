<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;

class RoleController extends Controller
{
    //
    public function index(Request $request){
        if(!$request->ajax()) 
            return redirect('/');

        $text_search     = $request->search;
        $search_criteria = $request->criteria;

        if($text_search == '')
            $roles = Role::orderBy('id','DESC')->paginate(6);
        else 
            $roles = Role::where($search_criteria, 'like', '%'. $text_search . '%')
                ->orderBy('id','DESC')
                ->paginate(6);
 
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
            'pagination' => [
                'total'         =>  $roles->total(),
                'current_page'  =>  $roles->currentPage(),
                'per_page'      =>  $roles->perPage(),
                'last_page'     =>  $roles->lastPage(),
                'from'          =>  $roles->firstItem(),
                'to'            =>  $roles->lastItem(),
            ],
            'roles' => $roles

        ];
    }

    public function selectRole(Request $request){
        $roles = Role::where('active','=','1')
            ->select('id','rolename')
            ->orderBy('rolename','ASC')->get();
        return ['roles' => $roles];
    }

}
