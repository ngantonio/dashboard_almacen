<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class saleDetail extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'sale_id',
        'article_id',
        'quantity',
        'price',
        'discount'
    ];
    // Corregimos el standard de laravel de que el nombre del modelo
    // debe ser la palabra en singular de la tabla en la base de datos
    // la tabla deberia llamarse "sale_details"

    protected $table = 'sale_details';
    
}