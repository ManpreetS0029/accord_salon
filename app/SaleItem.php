<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    //
    protected $table = 'saleitems';
    protected $fillable = ['saleid','itemid','itemtype','actualpriceperitem', 'quantity', 'discounttype', 'discountamount', 'title', 'barcode', 'description', 'parentid', 'taxpercent'];
    public function Sale()
    {
        $this->belongsTo('App\Sale', 'id', 'saleid' );
    }

    public function product()
    {
        return $this->hasOne( 'App\Product', 'id', 'itemid' ) -> where ( 'itemtype', '=', 'product' );
    }

    public function staff()
    {
        return $this->hasOne( 'App\Staff', 'id', 'staffid' );
    }

    public function service()
    {
        return $this->hasOne( 'App\Services', 'id', 'itemid' ) -> where ( 'itemtype', '=', 'service' );
        
    }
    public function package()
    {
        return $this->hasOne( 'App\Packages', 'id', 'itemid' ) -> where ( 'itemtype', '=', 'package' );
    }

    
    public function getTotalPrice()
    {
        $totalPrice =  $this->actualpriceperitem * $this->quantity ;

        $discountAmount = 0;
        if( $this->discounttype == 'percent' && $this->discountvalue > 0 )
        {
            $discountAmount = $totalPrice * $this->discountvalue / 100 ;
        }
        else if( $this->discounttype == 'absolute' && $this->discountvalue > 0 )
        {
            $discountAmount = $this->discountvalue;
        }

        return $totalPrice - $discountAmount;
    }

    public function packageItems()
    {
        return $this->hasMany( 'App\SaleItem','parentid', 'id' );
    }

    public function doneByStaffMembers()
    {
        return $this->hasMany('App\ServiceDoneStaff',  'saleitemid', 'id' );
    }
}
