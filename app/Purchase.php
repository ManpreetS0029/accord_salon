<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $table = 'purchasemaster';
    protected $fillable = ['companyid','invoiceno','billdate','deliverydate', 'downpayment', 'paymentduedate', 'totallineamount', 'totaltaxamount', 'discountamount', 'grandtotal'];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'companyid' );
    }
    
    public function getItems()
    {
        return $this->hasMany( 'App\PurchaseItem', 'purchasemasterid', 'id' );
    }

    public function payments()
    {
        return $this->hasMany('App\PurchasePayment',  'purchasemasterid', 'id')->orderBy('updated_at', 'desc');
    }


    public function getTotalTax()
    {
         $tax = 0;
        $items = $this->getItems();
        foreach( $items as $item ) { 
            $tax += $item->getTaxAmount();
        }

        return $tax;
    }

    public function getGrandTotal()
    {
        $grandTotal = 0;
        $items = $this->getItems();
        foreach( $items as $item ) { 
            $grandTotal += $item->grandTotal();
        }

        return $grandTotal;
    }

    public function getLineTotal()
    {
         $total = 0;
        $items = $this->getItems();
        foreach( $items as $item ) { 
            $total += $item->getTotalPriceWithoutDiscount();
        }
        return $total;
    }

    
    
    public function getTotalDiscount()
    {
        $total = 0;
        $items = $this->getItems();
        foreach( $items as $item ) { 
            $total += $item-> getDiscountAmount();
        }
        return $total+$this->discountamount;
    }

    
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
        return $this->totalUnderReviewFailed;
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

        return $this->amountUnderReview;
    }

    public function pendingAmount()
    {
        return $this->grandtotal - $this->getTotalPaidAmount();
    }

    public function canAddNewPayments()
    {
        if( $this->getTotalPaidAmount() + $this->getAmountUnderReview() < $this->grandtotal  )
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
        foreach( $this->payments as $salePayment ) { 

            
					if( $salePayment->ispaymentdone == '1')
					{
                        if( $salePayment->paymentmodeid == '1' )
                        {
                            $this->totalAmountPaidAsCash += $salePayment->amount;
                        }
                        $this->totalAmountPaid += $salePayment->amount;
					}
					//if cheque payment and under review
					else if( $salePayment->paymentmodeid == '2' && $salePayment->ispaymentdone == '0'  )
					{
                        $this->amountUnderReview += $salePayment->amount;
					}

                    if( $salePayment->ispaymentdone == '2' )
                    {
                        $this->totalUnderReviewFailed += $salePayment->amount;
                    }
					
        }
    
    }

    

    
}
