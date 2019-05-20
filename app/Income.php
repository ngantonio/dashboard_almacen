<?php

namespace App;
use App\User;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'provider_id',
        'user_id',
        'ticket_type',
        'ticket_serie',
        'ticket_number',
        'date',
        'tax',
        'total',
        'status'
    ];
    public $timestamps = false;

    #Usuario del sistema que registra un ingreso
    public function user(){
        return $this->belongsTo(User::class);
    }

    #Proveedor que ha abastecido el almacen, ha generado el ingreso
    public function provider(){
        return $this->belongsTo('App\Provider');
    } 
}
