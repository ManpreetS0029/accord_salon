<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $table = 'expenses';
    protected $fillable = ['expensemasterid', 'amount', 'remarks', 'paymentmodeid', 'expensedate' ];

    public function expensemaster()
    {
        return $this->belongsTo( 'App\ExpenseMaster', 'expensemasterid', 'id' );
    }

    public function paymentmode()
    {
        return $this->hasOne('App\PaymentMode','paymentmodeid', 'id' );
    }
    
}
