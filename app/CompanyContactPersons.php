<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyContactPersons extends Model
{
    //
    public $timestamps = false;
    protected $table = 'companycontactpersons';
    protected $fillable = ['companyid','name','designation', 'phone'];

    public function company( )
    {
        return $this->belongsTo( 'App\Company', 'companyid', 'id' );
    }
    
}
