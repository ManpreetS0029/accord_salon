<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackagesServices extends Model
{
    protected $table = 'packageservices';
    //
    protected $fillable = ['serviceid','packageid'];

    public $timestamps = false;

    public function service()
    {
        return $this->hasOne('App\Services', 'id', 'serviceid');
    }
    
}
