<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 25/08/18
 * Time: 6:26 PM
 */
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
use App\Product;
use App\ProductStock;
use App\ProductIssue;
use App\Staff;


use \Illuminate\Support\Facades\DB;

class HS_Sales {

    public function deleteSaleWithId($id)
    {
        $sale = Sale::findOrFail($id);

        $allMainIds = array();
        foreach( $sale->saleItem as $item )
        {
            $allMainIds[] = $item->id;
            if( $item->itemtype == 'product' ) {
                $product = Product::findOrFail($item->itemid);
                $product->stockavailable += $item->quantity;
                $product->soldcount -= $item->quantity;
                $product->save();
            }

        }

        $saleitems = SaleItem::whereIn('parentid', $allMainIds)->get();

        foreach ($saleitems as $item){
            $allMainIds[] = $item->id;
        }


        //delete all items
        ServiceDoneStaff::whereIn('saleitemid', $allMainIds)->get();
        SaleItem::whereIn('id', $allMainIds)->delete();
        SaleItem::whereIn('parentid', $allMainIds)->delete();

        //check if payment is all assigned to this


       // ClientPaymentUsed::where('saleid', '=',  $sale->id)->delete();
        $paymentsUsed = ClientPaymentUsed::where('saleid', '=',  $sale->id)->get();

        foreach( $paymentsUsed as $payment1 )
        {
            $paymentMaster =  ClientPayment::where( 'id', '=', $payment1->clientpaymentid)->first();
            
            if( $paymentMaster  )
            {
                if( $payment1->amount >= $paymentMaster->amount )
                {
                    $paymentMaster->delete();
                }
                else
                {
                    $paymentMaster->amount = $paymentMaster->amount - $payment1->amount;
                    $paymentMaster->save();
                }
            }

        }

        ClientPaymentUsed::where('saleid', '=',  $sale->id)->delete();
         $sale->delete();

    }

    public static function getSaleItemTotalPrice($actualpriceperitem, $quantity,  $discounttype, $discountvalue) {
        $totalPrice =  $actualpriceperitem * $quantity ;

        $discountAmount = 0;
        if( $discounttype == 'percent' && $discountvalue > 0 )
        {
            $discountAmount = $totalPrice * $discountvalue / 100 ;
        }
        else if( $discounttype == 'absolute' && $discountvalue > 0 )
        {
            $discountAmount = $discountvalue;
        }

        return $totalPrice - $discountAmount;
    }
}
