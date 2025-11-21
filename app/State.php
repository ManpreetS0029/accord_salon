<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $table = 'state';
    function cities(){ 
        return $this->hasMany( 'App\City', 'state_id', 'id' );
    } 
}
