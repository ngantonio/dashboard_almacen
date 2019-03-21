<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Provider;
use App\User;

class Person extends Model
{
    //generaba un erro si no especificaba la tabla
    protected $table = 'persons';
    protected $fillable = ['name','document_type','document_number','address','phone_number','email'];

    public function provider(){
        //un solo proveedor
        return $this->hasOne(Provider::class);
    }

    public function user(){
        return $this->hasOne(User::class);
    }

}
