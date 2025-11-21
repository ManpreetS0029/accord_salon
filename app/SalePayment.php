<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    //
    protected $table = 'salepayment';
    protected $fillable = ['salemasterid', 'paymentmodeid', 'other','bankname','bankaccountno','chequeno','chequedate','amount','ispaymentdone', 'paymentfailedreason' ];

    public function paymentmode()
    {
        return $this->hasOne('App\PaymentMode', 'id', 'paymentmodeid' );
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
            $vals = "Cheque";
            if(  ($mainPay = $this->paymentDetail ) )
            {
                $vals .= "<br />Bank: ".$mainPay->bankname;
                $vals .= "<br />Account No: ".$mainPay->bankaccountno;
                $vals .= "<br />Cheque No: ".$mainPay->chequeno;
                $vals .= "<br />Cheque Date: ".date("d/m,Y",$mainPay->chequedate);
            }

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
            $vals = "Other";
            if(  ($mainPay = $this->paymentDetail ) )
            {
                $vals .= ": ".$mainPay->other;
            }
        }

        return $vals;

    }


    function getPaymentPaidStatus()
    {
        $paymentMsg = "";

        if ($this->paymentmodeid == "2") {
            if ($this->ispaymentdone == "2") {
                $paymentMsg = "Payment Failed: ";
                if ( ($mainPay = $this->paymentDetail ) ) {
                    $paymentMsg .= $mainPay->paymentfailedreason;
                }
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


}




/*class SalePayment extends Model
{
    //
    protected $primaryKey = 'salepaymentid';
    protected $table = 'salepayment';
    




   public function paymentDetail()
   {
       return $this->hasOne('App\ClientPayment', 'id', 'clientpaymentid' );

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
            $vals = "Cheque";
           if(  ($mainPay = $this->paymentDetail ) )
           {
            $vals .= "<br />Bank: ".$mainPay->bankname;
             $vals .= "<br />Account No: ".$mainPay->bankaccountno;
            $vals .= "<br />Cheque No: ".$mainPay->chequeno;
             $vals .= "<br />Cheque Date: ".date("d/m,Y",$mainPay->chequedate);
           }
          
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
           $vals = "Other";
           if(  ($mainPay = $this->paymentDetail ) )
           {
               $vals .= ": ".$mainPay->other;
           }
       }
       
       return $vals;
       
   }

   
   function getPaymentPaidStatus()
 {
       $paymentMsg = "";

        if ($this->paymentmodeid == "2") {
            if ($this->ispaymentdone == "2") {
                $paymentMsg = "Payment Failed: ";
                if ( ($mainPay = $this->paymentDetail ) ) {
                    $paymentMsg .= $mainPay->paymentfailedreason;
                }
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

} */
