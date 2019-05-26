<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Sale;
use App\SaleDetail;
use App\User;
use App\Notifications\NotifyAdmin;

class SalesController extends Controller
{
            
    public function index(Request $request){
         
        if(!$request->ajax())
            return redirect('/');
 
        # Variables para las busquedas
        $text_search     = $request->search;
        $search_criteria = $request->criteria;
 
        # Si hay un texto para buscar
        if($text_search == '')
            
            $sales = Sale::join('persons','sales.client_id','=','persons.id')
                ->join('users','sales.user_id','=','users.id')
                ->select('sales.id',
                'sales.ticket_type',
                'sales.ticket_serie',
                'sales.ticket_number',
                'sales.date',
                'sales.tax',
                'sales.total',
                'sales.status',
                'users.username as user',
                'persons.name')
                ->orderBy('sales.date','desc')
                ->paginate(10);

        else    
            $sales = Sale::join('persons','persons.id', '=','sales.provider_id' )
                ->join('users','sales.user_id','=','users.id')
                ->select('sales.id',
                'sales.ticket_type',
                'sales.ticket_serie',
                'sales.ticket_number',
                'sales.date',
                'sales.tax',
                'sales.total',
                'sales.status',
                'users.username as user',
                'persons.name')              
                ->where('sales.'.$search_criteria, 'like', '%'. $text_search . '%')
                ->orderBy('sales.date','desc')
                ->paginate(10);
 
         
        # retornamos un array con los metodos necesarios
        # para controlar la paginacion
        return [
             'pagination' => [
                 'total'         =>  $sales->total(),
                 'current_page'  =>  $sales->currentPage(),
                 'per_page'      =>  $sales->perPage(),
                 'last_page'     =>  $sales->lastPage(),
                 'from'          =>  $sales->firstItem(),
                 'to'            =>  $sales->lastItem(),
            ],
             'sales' => $sales
 
        ];
    }
 
    public function store(Request $request){
         
         if(!$request->ajax()) 
             return redirect('/');
 
         try{
            DB::beginTransaction();
            // registramos una venta

            # google -> php time zone, zona horaria actual de php
            $myTime = Carbon::now('America/Caracas');

            $sale                 =   new Sale();
            $sale->client_id      =   $request->client_id;
            $sale->user_id        =   \Auth::user()->id;  #id del usuario logeado que registro el ingreso
            $sale->ticket_type    =   $request->ticket_type;
            $sale->ticket_serie   =   $request->ticket_serie;
            $sale->ticket_number  =   $request->ticket_number;
            $sale->date           =   $myTime->toDateString();
            $sale->tax            =   $request->tax;
            $sale->total          =   $request->total;
            $sale->status         =   'Registrado';
            $sale->save(); 
            
            #Se almacenan los detalles del ingreso

            $details = $request->data;  #Array de detalles

            foreach ($details as $ep => $detail) {

                $saleDetail               = new SaleDetail();
                $saleDetail->sale_id      = $sale->id; #hacemos referencia a la venta que acaba de insertarse.
                # recorremos el array que viene de la vista y almacenamos la informacion de la venta 
                $saleDetail->quantity     = $detail['quantity'];
                $saleDetail->price        = $detail['price'];
                $saleDetail->discount     = $detail['discount'];
                $saleDetail->article_id   = $detail['article_id'];

                $saleDetail->save();
            }

            $this->sendNotification();
            DB::commit();
            return ['id' => $sale->id]; 
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
        $sale = Sale::join('persons','sales.client_id','=','persons.id')
            ->join('users','sales.user_id','=','users.id')
            ->select('*')
            ->where('sales.id', '=', $id)
            ->take(1)->get();
   
        return ['sale' => $sale];    
    }
 
    // Devuelve los detalles de un ingreso
    public function getDetailsSale(Request $request){
        
        if(!$request->ajax()) 
            return redirect('/');
 
        $id     = $request->id;
        $details = SaleDetail::join('articles','sale_details.article_id','=','articles.id')
        ->select('sale_details.quantity','sale_details.price', 'articles.name as article','sale_details.discount')
        ->where('sale_details.sale_id', '=', $id)
        ->get();
   
        return ['details' => $details]; 
    }

    #Las ventas para este caso no se van a poder actualizar o modificar
    #en almacen y compras, si el usuario que registro el ingreso se equivoco al hacerlo
    #debera anularlo y volverlo a registrar. (uso de triggers)
 
    # esta funcion anula un ingreso, no lo elimina
    public function changeStatus(Request $request){
 
        if(!$request->ajax()) 
            return redirect('/');
 
        $sale = Sale::findOrFail($request->id);
        $sale->status = 'Anulado';
        $sale->save(); 
    }

    public function generatePDF(Request $request, $id){

        //if(!$request->ajax())
        //    return redirect('/');

        // Información de una venta
        $sale = Sale::join('persons','persons.id', '=','sales.client_id' )
                ->join('users','sales.user_id','=','users.id')
                ->select('sales.id',
                'sales.ticket_type',
                'sales.ticket_serie',
                'sales.ticket_number',
                'sales.date',
                'sales.tax',
                'sales.total',
                'sales.status',
                'users.username as user',
                'persons.name',
                'persons.document_type',
                'persons.document_number',
                'persons.address',
                'persons.email',
                'persons.phone_number')            
                ->where('sales.id','=',$id)
                ->take(1)->get();
        
        
        //Informacion de los detalles de esa venta
        $details = SaleDetail::join('articles','sale_details.article_id','=','articles.id')
        ->select('sale_details.quantity','sale_details.price', 'articles.name as article_name','sale_details.discount')
        ->where('sale_details.sale_id', '=', $id)
        ->get();
        
        //$num_venta = Sale::select('ticket_number')->where('id',$id)->get();
   
        $pdf = \PDF::loadView('pdf.salespdf',['sale' => $sale ,'details' => $details]);
        // Vista salepdf en la carpeta pdf, se le envia el array.
        
        return $pdf->download('comprobante.pdf');
        //return ['sale' => $sale, 'id' => $id];
        
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