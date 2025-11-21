<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ClientPackagePayment extends Model
{
    //
    protected $table = 'tblclientpackagepayment';

    public function package()
    {
        return $this->belongsTo('App\ClientPackage', 'packageid', 'id');
    }

    public function paymentmode()
    {
        return $this->hasOne('App\PaymentMode', 'id', 'paymentmodeid');
    }

}