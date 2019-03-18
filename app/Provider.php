<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Person;

class Provider extends Model
{
    //
    protected $table = 'providers';
    protected $fillable = ['id','contact_name','contact_phone'];
    public $timestamps = false;

    public function person(){
        return $this->belongsTo(Person::class);
    }
}
