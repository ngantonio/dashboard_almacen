<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Provider;

class Person extends Model
{
    //generaba un erro si no especificaba la tabla
    protected $table = 'persons';
    protected $fillable = ['name','document_type','document_number','address','phone_number','email'];

    person provider(){
        //un solo proveedor
        return $this->hasOne(Porvider::class);
    }
}
