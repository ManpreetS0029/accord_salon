<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 31/08/18
 * Time: 11:12 PM
 */

namespace App\CustomClasses;
use App\ClientPayment;
use App\ClosingDay;
use App\ExpenseMaster;
use App\Expense;
use App\StaffSalaryPaid;

class HS_Revenue
{

    //date would be Y-m-d
    function getCashForDate( $dates )
    {
        $clientPaymentQuery = ClientPayment::Query();
        $clientPaymentQuery->whereDate('created_at', '=', $dates );
        $clientPayments = $clientPaymentQuery->get();

        $totalCash = 0;
        foreach($clientPayments as $payments )
        {

            if( $payments->paymentmodeid == '1' )
            {
                $totalCash += $payments->amount;
            }

        }

        $expensesQuery = Expense::query();
        $salaryPaidQuery = StaffSalaryPaid::query();

        $expensesQuery->whereDate('expensedate', '=', $dates );
        $salaryPaidQuery->whereDate('created_at', '=', $dates );

        $expenses = $expensesQuery->get();
        $salariesPaid = $salaryPaidQuery->get();

        $totalExpensesCash = 0;
        foreach ($expenses as $expense)
        {
            if( $expense->paymentmodeid == '1' )
            {
                $totalExpensesCash +=  $expense->amount;
            }

        }

        $totalSalaryInCash = 0;
        foreach ($salariesPaid as $paid )
        {

            if( $paid->paymentmodeid == '1' )
            {
                $totalSalaryInCash +=  $paid->amount;
            }
            
        }


        $todayCashCollected = ($totalCash -  $totalSalaryInCash - $totalExpensesCash ) ;

        return $todayCashCollected;
    }

    function getClosingBalanceForDate( $dates )
    {
        if( false !== $this->isOpeningBalanceAddedForDate($dates) )
        {
            //all fine
            $closingBalance = $this->isClosingBalanceAddedForDate($dates);
            if( false !==  $closingBalance )
            {
                return $closingBalance;
            }
        }

         return false;

    }


    function addOpeningBalanceForDate( $dates, $shouldUpdateClosingBalance = '0' )
    {
        $data = \App\ClosingDay::whereDate("dates", '=', $dates )->first();

        if( $data && is_array($data) && count($data) > 0 )
        {

            if( $data->isclosed == '1' )
            {
                //all done nothing need to do
            }
            else if( $shouldUpdateClosingBalance == '1' ) {
                {
                    $dayCash = $this->getCashForDate($dates);
                    $openingBalance = doubleval($data->openingbalance);
                    $closingBal = $openingBalance + $dayCash;
                    //save to db
                    $currentData = \App\ClosingDay::whereDate("dates", '=', $dates)->first();

                    $currentData->closingbalance = $closingBal;
                    $currentData->isclosed = 1;
                    $currentData->save();
                }
            }
        }
        else
        {

            //check previous day
            $yesterDay = date('Y-m-d', strtotime('-1 day', strtotime(date("Y-m-d"))));
            $yesterDayData = \App\ClosingDay::whereDate("dates", '=', $yesterDay)->first();
            if( $yesterDayData && is_array($yesterDayData) && count($yesterDayData) > 0 )
            {

                $newObj = new \App\ClosingDay();
                $newObj->dates = $dates;

                if( $yesterDayData->isclosed != '1' )
                {
                    //close yesterday first
                    $yesterDayData->closingbalance = $this->getCashForDate($yesterDay) + $yesterDayData->openingbalance;
                    $yesterDayData->isclosed = '1';
                    $yesterDayData->save();
                }

                $newObj->openingbalance = $yesterDayData->closingbalance;
                if( $shouldUpdateClosingBalance == '1' )
                {
                    $newObj->closingbalance =  $this->getCashForDate($dates) + $newObj->openingbalance;
                }

                $newObj->save();


            }
            else
            {
                // nothing need to do, user will add manually
            }

        }

    }

    function isOpeningBalanceAddedForDate($dates)
    {
        $data = \App\ClosingDay::whereDate("dates", '=', $dates )->first();

           if( $data && is_array($data) && count($data ) > 0 )
           {
               return doubleval($data->openingbalance);
           }
        return false;
    }

    function isClosed()
    {

    }
    function isClosingBalanceAddedForDate($dates)
    {
        $data = \App\ClosingDay::whereDate("dates", '=', $dates )->first();

        if( count($data ) > 0 && $data->closingbalance != '' )
        {
            return doubleval($data->closingbalance);
        }
        return false;
    }
}
