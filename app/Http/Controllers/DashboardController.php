<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // al registrar una ruta no se necesita especificar ningun metodo en la ruta
    public function __invoke(Request $request){

        // año actual
        $current_year = date('Y'); 
        // obtener el mes y el año de la fecha
        //obtener el total de $ de ingresos 

        //Con whereYear, filtro que solo se muestren los meses
        //y los totales del año definido en current_year

        //agrupamos por el mes y año, apra poder usar SUM 

        //Si estamos en el mes de mayo, nos mostrara solo
        //los registros desde enero hasta mayo
        $incomes = DB::table('incomes as i')
        ->select(DB::raw('MONTH(i.date) as month'),
                 DB::raw('YEAR(i.date) as year'),
                 DB::raw('SUM(i.total) as total'))
        ->whereYear('i.date', $current_year)
        ->groupBy(DB::raw('MONTH(i.date)'),DB::raw('YEAR(i.date)'))
        ->get();

        //Lo mismo para las ventas
        $sales = DB::table('sales as s')
        ->select(DB::raw('MONTH(s.date) as month'),
                 DB::raw('YEAR(s.date) as year'),
                 DB::raw('SUM(s.total) as total'))
        ->whereYear('s.date', $current_year)
        ->groupBy(DB::raw('MONTH(s.date)'),DB::raw('YEAR(s.date)'))
        ->get();

        return ['incomes' => $incomes, 'sales' => $sales, 'current_year' => $current_year];
    }
}
