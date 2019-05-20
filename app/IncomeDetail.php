<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncomeDetail extends Model
{
    protected $table = 'income_details'; #Porque la tabla no es el plural exacto del modelo
    protected $fillable = [
        'income_id',
        'article_id',
        'quantity',
        'price',
    ];
    public $timestamps = false;
}
