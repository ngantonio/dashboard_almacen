<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Article;
class Category extends Model
{
    //redundante pero, se puede indicar con que tabla va a trabajar
    #protected $table = 'categories';
    // se puede indicar que la tabla tendra otro id con:
    #protected $primaryKey = 'id';
    
    #Asignaciones en masa con fillable
    protected $fillable = ['name','description','active'];
    
    # Relacion uno a muchos
    function article(){
        return $this->hasMany(Article::class);
    }
}