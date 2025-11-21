<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 24/08/18
 * Time: 10:18 PM
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientPaymentUsed extends Model
{
    //

    protected $table = 'clientpaymentused';

    public function clientPayment()
    {

       return $this->belongsTo('App\ClientPayment', 'clientpaymentid', 'id' );

    }

    public function sale()
    {
        return $this->belongsTo( 'App\Sale', 'saleid', 'id' );
    }

}