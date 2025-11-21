<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    //
       protected $table = 'purchasepayment';
    protected $fillable = ['purchasemasterid', 'paymentmodeid', 'other','bankname','bankaccountno','chequeno','chequedate','amount','ispaymentdone', 'paymentfailedreason' ];

    public function paymentmode()
    {
        return $this->hasOne('App\PaymentMode', 'id', 'paymentmodeid' );
    }
}
