<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $table = 'client';
    protected $hidden = ['password'];
    protected $fillable = ['clientname','description', 'dob','email', 'address', 'city', 'state', 'zipcode', 'phone', 'phone2', 'status'];


    public function advancepayments()
    {
        return $this->hasMany('App\ClientPayment', "clientid", "id" )->orderBy("id","asc");
    }

    public function sales()
    {
        return $this->hasMany('App\Sale', "clientid", "id" );
    }



    public function packages()
    {
        return $this->hasMany('App\ClientPackage', "clientid", "id" );
    }

    public function unCompletedPackages( $includePackageId = '' )
    {
        $packages = array();
            foreach ( $this->packages as $package )
            {
                if( $package->isPackageCompleted() == false || ( $includePackageId != '' &&  $includePackageId == $package->id )   )
                {
                    $packages[] = $package;
                }
            }

            return $packages;
    }

    public function getTotalAdvanceAmount()
    {
        $amount = 0;
        $payments = $this->advancepayments;
        foreach ( $payments as $payment )
        {
            $amount += $payment->getAdvancePayment();
        }

        return max(0,$amount);
    }

    public function getListOfAdvancePayments()
    {
        $payments = $this->advancepayments;
        $arr = array();
        foreach ( $payments as $payment )
        {
             if( $payment->getAdvancePayment() > 0 )
             {
                 $arr[] = $payment;
             }
        }
        return $arr;
    }

    public function getSalesPendingAmount()
    {
        $sales = $this->sales;
        $amount = 0;
        foreach ( $sales as $sale )
        {
            $amount += $sale->actualPendingAmount();
        }
        return $amount;
    }

    public function getPendingAmountSalesList()
    {
        $sales = $this->sales;
        $arr = array();
        foreach ( $sales as $sale )
        {
            if( $sale->actualPendingAmount() > 0 )
            {
                $arr[] = $sale;
            }
        }

        return $arr;
    }

    public function clientPendingPaymentRegardingPackages()
    {
        $payments = $this->advancepayments;
        $totalPayment = 0;
        foreach ( $payments as $payment )
        {
            $totalPayment += $payment->amount;
        }

        $sales = $this->sales;
        $totalSaleCost = 0;
        foreach ( $sales as $sale  )
        {
            if( $sale->packageid == '' || $sale->packageid <= 0 )
            {
                $totalSaleCost += $sale->paidprice;
            }
        }

        $packages = $this->packages;
        $packagesTotalAmount = 0;
        foreach ( $packages as $package )
        {
            $packagesTotalAmount +=  $package->actualprice;
        }

        $totalRealPendingAmount =   ($totalSaleCost + $packagesTotalAmount) - $totalPayment ;

        return $totalRealPendingAmount;
    }
}
