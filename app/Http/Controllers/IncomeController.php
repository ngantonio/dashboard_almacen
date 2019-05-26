<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Income;
use App\IncomeDetail;
use App\User;
use App\Notifications\NotifyAdmin;


class IncomeController extends Controller
{
          
    public function index(Request $request){
         
        if(!$request->ajax()) 
            return redirect('/');
 
        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;
 
        # Si hay una busqueda
        if($text_search == '')
            
            $incomes = Income::join('persons','incomes.provider_id','=','persons.id')
                ->join('users','incomes.user_id','=','users.id')
                ->select('incomes.id',
                'incomes.ticket_type',
                'incomes.ticket_serie',
                'incomes.ticket_number',
                'incomes.date',
                'incomes.tax',
                'incomes.total',
                'incomes.status',
                'users.username as user',
                'persons.name')
                ->paginate(10);

        else    
            $incomes = Income::join('persons','persons.id', '=','incomes.provider_id' )
                ->join('users','incomes.user_id','=','users.id')
                ->select('incomes.id',
                'incomes.ticket_type',
                'incomes.ticket_serie',
                'incomes.ticket_number',
                'incomes.date',
                'incomes.tax',
                'incomes.total',
                'incomes.status',
                'users.username as user',
                'persons.name')              
                ->where('incomes.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->orderBy('date','desc')
                ->paginate(10);
 
         
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
             'pagination' => [
                 'total'         =>  $incomes->total(),
                 'current_page'  =>  $incomes->currentPage(),
                 'per_page'      =>  $incomes->perPage(),
                 'last_page'     =>  $incomes->lastPage(),
                 'from'          =>  $incomes->firstItem(),
                 'to'            =>  $incomes->lastItem(),
            ],
             'incomes' => $incomes
 
        ];
    }
 
    public function store(Request $request){

         
         if(!$request->ajax()) 
             return redirect('/');
 
         try{
            DB::beginTransaction();
            // registramos a una persona

            # google -> php time zone, zona horaria actual de php
            $myTime = Carbon::now('America/Caracas');

            $income                 =   new Income();
            $income->provider_id    =   $request->provider_id;
            $income->user_id        =   \Auth::user()->id;  #id del usuario logeado que registro el ingreso
            $income->ticket_type    =   $request->ticket_type;
            $income->ticket_serie   =   $request->ticket_serie;
            $income->ticket_number  =   $request->ticket_number;
            $income->date           =   $myTime->toDateString();
            $income->tax            =   $request->tax;
            $income->total          =   $request->total;
            $income->status         =   'Registrado';
            $income->save(); 
            
            #Se almacenan los detalles del ingreso

            $details = $request->data;  #Array de detalles

            foreach ($details as $ep => $detail) {

                $incomeDetail               = new IncomeDetail();
                $incomeDetail->income_id    = $income->id; #hacemos referencia a el ingreso que acaba de insertarse.
                # recorremos el array y almacenamos la informacion del ingreso
                $incomeDetail->article_id   = $detail['article_id'];
                $incomeDetail->quantity     = $detail['quantity'];
                $incomeDetail->price        = $detail['price'];
                $incomeDetail->save();
            }

            //Enviamos la notificacion:
            $this->sendNotification();
            DB::commit();
         }
         catch(Exception $e){
            DB::rollBack();
         }
    }

    // Devuelve los datos de la cabecera de un ingreso (basicos)
    public function getHeader(Request $request){
        
        if(!$request->ajax()) 
            return redirect('/');
 
        $id     = $request->id;
        $income = Income::join('persons','incomes.provider_id','=','persons.id')
            ->join('users','incomes.user_id','=','users.id')
            ->select('*')
            ->where('incomes.id', '=', $id)
            ->take(1)->get();
   
        return ['income' => $income];    
    }
 
    // Devuelve los detalles de un ingreso
    public function getDetailsIncome(Request $request){
        
        if(!$request->ajax()) 
            return redirect('/');
 
        $id     = $request->id;
        $details = IncomeDetail::join('articles','income_details.article_id','=','articles.id')
        ->select('income_details.quantity','income_details.price', 'articles.name')
        ->where('income_details.income_id', '=', $id)
        ->get();
   
        return ['details' => $details]; 

    }

    #Los ingresos apra este caso no se van a poder actualizar o modificar
    #en almacen y compras, si el usuario que registro el ingreso se equivoco al hacerlo
    #debera anularlo y volverlo a registrar. (uso de triggers)
 
    # esta funcion anula un ingreso, no lo elimina
    public function changeStatus(Request $request){
 
        if(!$request->ajax()) 
            return redirect('/');
 
        $income = Income::findOrFail($request->id);
        $income->status = 'Anulado';
        $income->save(); 
    }

    // Permite generar la notificación una vez se almacena una compra / ingreso
    public function sendNotification(){
        
        // para que permita mostrar el total de compras y ventas solo de un dia especifico, el dia actual
        $current_date = date('Y-m-d');
        $num_incomes = DB::table('incomes')->whereDate('date',$current_date)->count();
        $num_sales = DB::table('sales')->whereDate('date',$current_date)->count();
    
        $arrayData = [
            'sales' => [
                'cantidad' => $num_sales,
                'msj'      => 'Ventas'
            ],
            'incomes' => [
                'cantidad' => $num_incomes,
                'msj'      => 'Ingresos'
            ]
        ];

        //Notificar a todos los usuarios con click en el sistema
        $allUsers = User::all();
        foreach ($allUsers as $user)
            User::findOrFail($user->id)->notify( new NotifyAdmin($arrayData));   
    }
    
}
