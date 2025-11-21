<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    
    //
    protected $table = 'sale';
    protected $fillable = ['clientid', 'totalprice', 'paidprice', 'discounttype', 'discountamount', 'ispaid'];

    public function saleItem()
    {
        return $this->hasMany('App\SaleItem', 'saleid' );
    }



    public function client()
    {
        return $this->hasOne( 'App\Client' , 'id',  'clientid' );
    }

    public function salepayments()
    {
       // return $this->hasMany('App\SalePayment',  'saleorpackageid', 'id')->where('ispackage', '!=','1')->orderBy('updated_at', 'desc');
        return $this->hasMany('App\ClientPaymentUsed',  'saleid', 'id')->orderBy('updated_at', 'desc');
    }


    /*public function salepaymentsfailed()
    {
        return $this->hasMany('App\SalePayment',  'salemasterid', 'id')->where('ispaymentdone','=','2')->orderBy('updated_at', 'desc');
    } */


    var $totalAmountPaid = null;
    var $amountUnderReview  = null;
    var $totalAmountPaidAsCash = null;
    var $totalUnderReviewFailed = null;

    public function getTotalFailedAmount()
    {
        if($this->totalUnderReviewFailed == null )
        {
            $this->initAmounts();
        }
        //return $this->totalUnderReviewFailed;
        return 0;
    }
    
    public function getTotalPaidAmount()
    {
        if( $this->totalAmountPaid == null )
        {
            $this->initAmounts();
        }

        return $this->totalAmountPaid;
    }


    public function getTotalAmountPaidAsCash()
    {
           if( $this->totalAmountPaidAsCash == null )
        {
            $this->initAmounts();
        }

        return $this->totalAmountPaidAsCash;
    }

   public function getAmountUnderReview()
    {
        if( $this->amountUnderReview == null )
        {
            $this->initAmounts();
        }

        //return $this->amountUnderReview;
        return 0;
    }

    public function pendingAmount()
    {
        return $this->paidprice - $this->getTotalPaidAmount();
    }

    
    public function actualPendingAmount()
    {
        return $this->paidprice - $this->getTotalPaidAmount() - $this->getAmountUnderReview();
    }

    public function canAddNewPayments()
    {
        if( $this->getTotalPaidAmount() + $this->getAmountUnderReview() < $this->paidprice  )
        {
            return true;
        }
        return false; 
    }

    public function initAmounts()
    {

        $this->totalAmountPaid = 0;
        $this->amountUnderReview = 0;
        $this->totalAmountPaidAsCash = 0;
        $this->totalUnderReviewFailed = 0;
        foreach( $this->salepayments as $salePayment ) {

            $paymentDetails = $salePayment->clientPayment;

            if($paymentDetails && $paymentDetails->ispaymentdone == '1')
            {
                if( $paymentDetails->paymentmodeid == '1' )
                {
                    $this->totalAmountPaidAsCash += $salePayment->amount;
                }
                $this->totalAmountPaid += $salePayment->amount;
            }
            //if cheque payment and under review
           /* else if( $salePayment->paymentmodeid == '2' && $salePayment->ispaymentdone == '0'  )
            {
                $this->amountUnderReview += $salePayment->amount;
            } */

           /* if( $salePayment->ispaymentdone == '2' )
            {
                $this->totalUnderReviewFailed += $salePayment->amount;
            } */

        }

    }


    /* public function initAmounts()
     {

         $this->totalAmountPaid = 0;
         $this->amountUnderReview = 0;
         $this->totalAmountPaidAsCash = 0;
         $this->totalUnderReviewFailed = 0;


         foreach( $this->salepayments as $payment ) {


             $salePayment = $payment->paymentDetail;

             if( $salePayment ) {
                     if( $salePayment->ispaymentdone == '1')
                     {
                         if( $salePayment->paymentmodeid == '1' )
                         {
                             $this->totalAmountPaidAsCash += $payment->amount;
                         }
                         $this->totalAmountPaid += $payment->amount;
                     }
                     //if cheque payment and under review
                     else if( $salePayment->paymentmodeid == '2' && $salePayment->ispaymentdone == '0'  )
                     {
                         $this->amountUnderReview += $payment->amount;
                     }

                     if( $salePayment->ispaymentdone == '2' )
                     {
                         $this->totalUnderReviewFailed += $payment->amount;
                     }
             }

         }

     } */

   /* public function getUnderReviewPayments()
    {
        $arr = array();
        /*foreach( $this->salepayments as $payment )
        {
            if( $payment->ispaymentdone == "0" && $payment->paymentmodeid == "2" )
            {
                $arr[] =  $payment;
            }
        }

        return $arr;
    } */
    
}
