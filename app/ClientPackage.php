<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientPackage extends Model
{
    //
    protected $table = 'tblclientpackage';
    var $totalAmountPaid = null;
    var $amountUnderReview  = null;
    var $totalAmountPaidAsCash = null;
    var $totalUnderReviewFailed = null;

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'clientid');
    }

    public function packageItems(){
        return $this->hasMany('App\ClientPackageItems', "packageid", "id");
    }

    public function packageSales()
    {
        return $this->hasMany('App\Sale', "packageid", "id");
    }


    public function packageLeftMoney( $excludeSaleIds = array() )
    {
        $usedAmount = 0;
        if( $this->packagetype == "1" ) // composite package
        {
            foreach ( $this->packageSales as $sale )
            {
                if( count($excludeSaleIds) > 0 && in_array($sale->id, $excludeSaleIds )  )
                {
                    continue;
                }
                $usedAmount += $sale->paidprice;
            }
        }

        return $this->giftedprice - $usedAmount;
    }


    public function packageLeftItems($excludeSaleIds = array() )
    {
        if( $this->packagetype == "2" ) // composite package
        {
            // calculate items left to be done
            $itemsArr = array();
            foreach( $this->packageItems as $item )
            {
                 if(isset($itemsArr[$item->itemid]))
                 {
                     $itemsArr[$item->itemid] += $item->quantity;
                 }
                 else
                 {
                     $itemsArr[$item->itemid] = $item->quantity;
                 }

            }

            foreach ( $this->packageSales as $sale )
            {
                if ( count($excludeSaleIds) > 0 && in_array($sale->id, $excludeSaleIds ))
                {
                    continue;
                }
                 foreach( $sale->saleItem as $saleItem )
                 {

                      if( isset( $itemsArr[$saleItem->itemid] ))
                      {
                          $itemsArr[$saleItem->itemid] -= $saleItem->quantity;
                      }

                 }
            }


            return $itemsArr;

        }
    }

    public function isPackageCompleted()
    {
        if( $this->packagetype == "2" )
        {
            $arr = $this->packageLeftItems();

            foreach( $arr as $k => $R )
            {
                if( $R > 0 )
                {
                    return false;
                }
            }

            return true;
        }
        else // cash gift
        {
            if( $this->packageLeftMoney() > 0 )
            {
                return false;
            }

            return true;

        }
    }



    /*public function packagepayments()
    {
        return $this->hasMany('App\ClientPayment',  'saleid', 'id')->where('ispackage', '=','1')->orderBy('updated_at', 'desc');
    }*/

    function getSalesTotalPaidPrice()
    {
        $sales = $this->packageSales;
        $amount = 0;
        foreach ( $sales as $sale )
        {
            $amount += $sale->paidprice;
        }
        return $amount;

    }

    function usedItemsList()
    {
        $itemsArr = array();

        foreach ( $this->packageSales as $sale )
        {

            foreach( $sale->saleItem as $saleItem )
            {

                if( isset( $itemsArr[$saleItem->itemid] ))
                {
                    $itemsArr[$saleItem->itemid] += $saleItem->quantity;
                }
                else
                {
                    $itemsArr[$saleItem->itemid] = $saleItem->quantity;
                }
            }
        }
        return $itemsArr;
    }

}
