<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;

class Article extends Model
{
    protected $fillable = ['code','name','sale_price','stock','description','active','category_id'];

    # Creando relacion de pertenencia, pertenece a una categoria ...
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
