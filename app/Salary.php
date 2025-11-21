<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    //
    protected $table = 'staffsalaries';
    // protected $fillable =  ['staffid',''];

    public function staff()
    {
        return $this->hasOne( 'App\Staff', 'id', 'staffid');
    }


}
