<?php
/**
 * Created by PhpStorm.
 * User: sonu
 * Date: 7/12/2018
 * Time: 6:09 PM
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
//ClientPayment
class ClientPayment extends Model
{
    //
    protected $table = 'clientpayment';

    public function client()
    {
        return $this->belongsTo('App\clients', 'clientid', 'id');
    }

    public function paymentmode()
    {
        return $this->hasOne('App\PaymentMode', 'id', 'paymentmodeid' );
    }

    public function clientPaymentUsedLists()
    {
        return $this->hasMany( 'App\ClientPaymentUsed', 'clientpaymentid', 'id' );
    }
    
    public function getPaymentModeDisplay()
   {
       $vals = "";
       if ( $this->paymentmodeid == "1" )
       {
           $vals = "Cash";
       }
       else if ( $this->paymentmodeid == "2" )
       {
           
           $vals = "Cheque: <br />Bank: ".$this->bankname;
           $vals .= "<br />Account No: ".$this->bankaccountno;
           $vals .= "<br />Cheque No: ".$this->chequeno;
           $vals .= "<br />Cheque Date: ".date("d/m/Y", strtotime($this->chequedate));
       }
       else if( $this->paymentmodeid == "3"  )
       {
           $vals = "Credit Card.";
       }
       else if( $this->paymentmodeid == "4"  )
       {
           $vals = "Debit Card.";
       }
       else if( $this->paymentmodeid == "5"  )
       {
           $vals = "Other: ".$this->other;
       }
       
       return $vals;
       
   }
   
   function getPaymentPaidStatus()
 {
       $paymentMsg = "";

        if ($this->paymentmodeid == "2") {
            if ($this->ispaymentdone == "2") {
                $paymentMsg = "Payment Failed: ";
                 
                    $paymentMsg .= $this->paymentfailedreason;
                
            } else  if ($this->ispaymentdone == "0") {
                
                $paymentMsg = "Pending";
            }
            else  if ($this->ispaymentdone == "1") {{
                
                $paymentMsg = "Paid";
            }
        }
    }
    else  if ($this->ispaymentdone == "1") 
    {
        $paymentMsg = "Paid";
    }
    return $paymentMsg;
 }

 public function getAdvancePayment()
 {
    $usedPaymentsList = $this->clientPaymentUsedLists;
    $amount = 0;
    if(  $this->ispaymentdone == '1'  ) {
        $amount = $this->amount;
        foreach ( $usedPaymentsList as $paymentList )
         {
             $amount -= $paymentList->amount;
         }
    }

    return $amount;
 }
   
   
}