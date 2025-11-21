<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    //
    protected $table = 'purchaseitems';
    protected $fillable = ['purchasemasterid', 'itemid', 'qnty', 'purchaseprice', 'discounttype', 'discountvalue', 'discountamount', 'taxtype', 'taxamount' ];

    public function purchaseMaster()
    {
        return $this->belongsTo( 'App\Purchase','purchasemasterid', 'id' );
    }

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'itemid' );
    }
    
    public function getDiscountAmount()
    {
        if( $this->discounttype == 'percent' && $this->discountvalue > 0  )
        {
            return   $this->qnty * $this->purchaseprice * $this->discountvalue / 100;
        }
        else if( $this->discounttype == 'absolute' && $this->discountvalue > 0  )
        {
            return    $this->discountvalue ;
        }

        return 0;
        
    }

    public function getTotalPriceWithoutDiscount()
    {
        return $this->qnty * $this->purchaseprice;
    }

    public function getAmount()
    {
        return $this->getTotalPriceWithoutDiscount() - $this->getDiscountAmount();
    }
    public function getTaxAmount()
    {
        if( $this->taxtype > 0 )
        {
            return $this->getAmount() * $this->taxtype / 100;
        }

        return 0;
    }
    public function getTaxableAmount()
    {
        return $this->getAmount();
    }

    public function grandTotal()
    {
        return $this->getAmount() + $this->getTaxAmount();
    }
}
