<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    //
    protected $table = 'roles';
    protected $fillable = ['id','rolename','description','active'];
    public $timestamps = false;

    // Un rol puede tener varios usuarios, la relacion es hasMany
    public function users(){
        return $this->hasMany(User::class);
    }
}
