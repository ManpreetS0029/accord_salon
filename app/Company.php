<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CompanyContactPersons;
class Company extends Model
{
    //
    protected $table = 'company';
    protected $fillable = ['companyname','gstno','address','cityid'];

    public function contactpersons()
    {
        return $this->hasMany('App\CompanyContactPersons', 'companyid', 'id');
    }

    public function city()
    {
        return $this->hasOne( 'App\City', 'id', 'cityid' );
    }
}
