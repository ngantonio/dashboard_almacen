<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sale extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'client_id',
        'user_id',
        'ticket_type',
        'ticket_serie',
        'ticket_number',
        'date',
        'tax',
        'total',
        'status'
    ];

    
}
