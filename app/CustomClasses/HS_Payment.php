<?php

namespace App\CustomClasses;

use App\Client;
use App\ClientPackage;
use App\ClientPackageItems;
use App\ClientPayment;
use App\ClientPaymentUsed;
use App\PaymentMode;
use App\Sale;
use App\SaleItem;
use App\SalePayment;
use App\ServiceDoneStaff;
use App\Services;

use \Illuminate\Support\Facades\DB;

class HS_Payment {

    
public function getValidAmount ( $actualPendingAmount, $amount  )
    {
    
         
        if( $actualPendingAmount < $amount  )
        {
            return $actualPendingAmount;
        }
        
        return $amount;
    }
    
    public function isAllPaymentPaying( $pendingAmount, $payingAmount )
    {
        if( $pendingAmount > $payingAmount )
        {
            return false;
        }
        
        return true;
    }
    
     /*
    @return 1 or 0
     *      */
    
    public function isPaymentDone( $paymentModeId )
    {
        if( $paymentModeId != '2' )
        {
            return '1';
        }
        
        return '0';
    }
    
     
    public function getIsPaid( $pendingAmount, $payingAmount, $paymentModeId )
    {
        $validRequireAmount = $this->getValidAmount($pendingAmount, $payingAmount);
        if( isAllPaymentPaying( $validRequireAmount, $payingAmount )  && $this->isPaymentDone($paymentModeId)  )
        {
            return '1';
        }
        
        return '0';
    }
    
      /*
     * return '' or date as 2018-09-25
     *      
     */
    
    public function extractDateAsString($dates)
    {
        if( $dates->chequedate != '' ) {
                        $dt = explode( '/', $dates );

                        return ( $dt[2].'-'.$dt[1].'-'.$dt[0] );
                    }
        return '';
    }

public function useClientAmountInSale( $clientId, $saleId )
{

        $client = Client::findOrFail($clientId);
        $sale = Sale::findOrFail($saleId);

     $advanceAmounts = $client->getListOfAdvancePayments();
     $salePendingAmount = $sale->actualPendingAmount();

    if( count($advanceAmounts) > 0 && $salePendingAmount > 0 )
    {

        foreach ( $advanceAmounts as $clientPayment )
        {
             $advance =  $clientPayment->getAdvancePayment();
            if( $advance > 0 && $salePendingAmount > 0)
            {

                $clientPaymentUsed = new ClientPaymentUsed();
                $clientPaymentUsed->amount = $advance < $salePendingAmount ? $advance : $salePendingAmount ;
                $clientPaymentUsed->saleid = $sale->id;
                $clientPaymentUsed->clientpaymentid = $clientPayment->id;
                $clientPaymentUsed->save();
                $salePendingAmount -= $clientPaymentUsed->amount;
            }
        }
    }

    return $salePendingAmount;
}
     
public   function addClientPaymentUsed( $clientPaymentId, $saleId, $amount, $createDate = ''  )
    {


            $sale = Sale::findOrFail($saleId);
            $clientPayment = ClientPayment::findOrFail($clientPaymentId);


        $clientPaymentAdvance = $clientPayment->getAdvancePayment();
        if( $clientPayment->id > 0 && $clientPaymentAdvance > 0 )
        {

            $clientPaymentUsed = new ClientPaymentUsed();


            if( $amount > $sale->actualPendingAmount()  )
            {
                $amount = $sale->actualPendingAmount();
            }

            $amount = $amount > $clientPaymentAdvance ? $clientPaymentAdvance : $amount;

            if( $amount > 0 ) {
                $clientPaymentUsed->clientpaymentid = $clientPayment->id;
                $clientPaymentUsed->saleid = $sale->id;
                $clientPaymentUsed->amount = $amount;

                if( $createDate == '' )
                {
                    $createDate = date("Y-m-d H:i:s");
                }
                $clientPaymentUsed->created_at = $createDate;
                $clientPaymentUsed->save();
                if( $amount == $sale->pendingAmount() )
                {
                    $sale->ispaid = '1';
                }
                else
                {
                    $sale->ispaid = '0';
                }
                $sale->save();
                return $clientPaymentUsed;
            }


        }

        return null;

    }


     public   function addNewClientPayment( $clientId, $paymentModeId, $amount, $bankname, $bankaccountno, $chequeno, $chequedate , $other , $createDate = '' )
    {
        if( $clientId > 0 )
        {
            

            if( $amount > 0 ) {

                $clientPayment = new ClientPayment();
                $clientPayment->clientid = $clientId;
                $clientPayment->paymentmodeid = $paymentModeId;
                $clientPayment->amount = $amount;
                //not cheques then this payment assume as done
                
                $clientPayment->ispaymentdone = $this->isPaymentDone( $paymentModeId );
                
                  //bank payment
                if( $paymentModeId == '2' )
                {
                    $clientPayment->bankname = $bankname;
                    $clientPayment->bankaccountno = $bankaccountno;
                    $clientPayment->chequeno = $chequeno;
                    
                    $clientPayment->chequedate = $this->extractDateAsString( $chequedate );
 
                }
                else if( $paymentModeId == '5' ) // other
                {
                    $clientPayment->other = $other;
                }

                if( $createDate == '' )
                {
                    $createDate = date("Y-m-d H:i:s");
                }
                $clientPayment->created_at = $createDate;
                $clientPayment->save();

                
                return $clientPayment;
            }

        }
        return null;
    }
  

}

?>