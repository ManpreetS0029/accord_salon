<?php

namespace App\Http\Controllers;

use App\ClientPayment;
use App\ClientPaymentUsed;
use App\ClosingDay;
use App\CustomClasses\HS_Common;
use App\CustomClasses\HS_Revenue;
use App\Expense;
use App\Product;
use App\Salary;
use App\Sale;
use App\StaffSalaryPaid;
use Illuminate\Http\Request;
define('SERVICE_TAX', 18.0);
define('PRODUCT_TAX', 18.0);
class RevenueReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //total sale
        $salesQuery = Sale::Query();
        $clientPaymentQuery = ClientPayment::Query();
        $expensesQuery = Expense::query();
        $salaryPaidQuery = StaffSalaryPaid::query();
        $paymentUsed = ClientPaymentUsed::query();
        $closingDayQuery = ClosingDay::query();



         if( $request->datefrom == '' &&  $request->dateto == '' )
        {
            $request->datefrom = date("d/m/Y");
            $request->dateto = date("d/m/Y");
        }

        $days = array();
         $closingBalance = null;
    if( $request->datefrom ==  $request->dateto )
    {
        $request->dateto = '';

        if( $request->datefrom ==  date("d/m/Y") )
        {
            $hsRevenue = new HS_Revenue();
            $hsRevenue->addOpeningBalanceForDate( date("Y-m-d") );

            $days = ClosingDay::whereDate("dates", "=", date("Y-m-d"))->first();
            //$dayData = ClosingDay::whereDate("dates", "=", date("Y-m-d"));



             //count($dayData)

        }

    }



        if( $request->datefrom != '' )
        {
              $request->datefrom = HS_Common::extractDateAsString($request->datefrom);
        }

        if( $request->dateto != '' )
        {
              $request->dateto = HS_Common::extractDateAsString($request->dateto);
        }

        if( $request->datefrom != '' &&  $request->dateto != '' )
        {

            $salesQuery->whereBetween('created_at', [ $request->datefrom, $request->dateto ]);
            $clientPaymentQuery->whereBetween( 'created_at', [ $request->datefrom, $request->dateto ] );
            $expensesQuery->whereBetween( 'expensedate', [ $request->datefrom, $request->dateto ] );

            $salaryPaidQuery->whereBetween( 'created_at', [ $request->datefrom, $request->dateto ] );


            $paymentUsed->whereBetween( 'created_at', [ $request->datefrom, $request->dateto ] );

            $closingDayQuery->whereBetween( 'dates', [ $request->datefrom, $request->dateto ] );




        }
        else if($request->datefrom != '')
        {
            $salesQuery->whereDate('created_at', '=', $request->datefrom );
            $clientPaymentQuery->whereDate('created_at', '=', $request->datefrom );
            $expensesQuery->whereDate('expensedate', '=', $request->datefrom );
            $salaryPaidQuery->whereDate('created_at', '=', $request->datefrom );
            $paymentUsed->whereDate('created_at', '=', $request->datefrom );
            $closingDayQuery->whereDate('dates', '=', $request->datefrom );

        }
        else if($request->dateto != '')
        {
            $salesQuery->whereDate('created_at', '=', $request->dateto );
            $clientPaymentQuery->whereDate('created_at', '=', $request->dateto );
            $expensesQuery->whereDate('expensedate', '=', $request->dateto );
            $salaryPaidQuery->whereDate('created_at', '=', $request->dateto );

            $paymentUsed->whereDate('created_at', '=', $request->dateto );
            $closingDayQuery->whereDate('dates', '=', $request->dateto );
        }

        $sales = $salesQuery->get();
        $clientPayments = $clientPaymentQuery->get();
        $expenses = $expensesQuery->get();
        $salariesPaid = $salaryPaidQuery->get();
        $advancesUsed = $paymentUsed->get();
        $closingRecords = $closingDayQuery->orderBy('dates', 'asc' )->get();

        $totalSale = 0;
        $totalCash = 0;
        $totalCard = 0;
        $totalCheque = 0;
        $totalOther = 0;
        $totalCollection = 0;
        $totalPendingAmount = 0;
        $openingDrawerBalance = 0;
        $closingDrawerBalance = 0;
        $balanceCash = 0;

        $totalAdvanceAdjusted = 0;
        $totalAdvanceRecipet = 0;

        //service
        $serviceSales = 0;
        $serviceDiscount = 0;
        $serviceTotalPaidPrice = 0;
        $serviceTaxableAmount = 0;
        $serviceTax = 0;
        $totalService = 0;

        //product
        $productSales = 0;
        $productDiscount = 0;
        $productTotalPaidPrice = 0;
        $productTaxableAmount = 0;
        $productTax = 0;

        $totalCommission = 0;

        $totalSalary = 0;
        $totalSalaryInCash = 0;
        $totalSalaryInCheque = 0;
        $totalSalaryByCard = 0;
        $totalSalaryOther = 0;

        $totalExpenses = 0;
        $totalExpensesCash = 0;
        $totalExpensesByCard = 0;
        $totalExpensesByCheques = 0;
        $totalExpensesOtherPaymentMethod = 0;
        //sales
        foreach ( $sales as $sale) {


            $totalPendingAmount += $sale->actualPendingAmount();

            $serviceDiscount += $sale->discountamount;
            $saleItems = $sale->saleItem;
            foreach ( $saleItems as $item )
            {

                if( $item->itemtype == 'service' || $item->itemtype == 'package' )
                {
                    $serviceSales += ($item->actualpriceperitem * $item->quantity );
                    $serviceDiscount += $item->discountamount;
                }
                else
                {

                    $product = Product::findOrFail( $item->itemid );
                    $taxPercent = $product->tax > 0 ? $product->tax : PRODUCT_TAX;

                    $calculatedPrice = ($item->actualpriceperitem * $item->quantity ) - $item->discountamount;

                    $productTaxableAmountInner =  ( $calculatedPrice / ((100 + $taxPercent ) * 0.01 ) );
                    $productTax += ($calculatedPrice - $productTaxableAmountInner);
                    $productTaxableAmount += $productTaxableAmountInner;

                    $productSales += ($item->actualpriceperitem * $item->quantity );
                    $productDiscount += $item->discountamount;


                }

            }

        }

        //paid payments
        foreach($clientPayments as $payments )
        {

            $totalAdvanceRecipet = max(0, $payments->getAdvancePayment());

            if( $payments->paymentmodeid == '1' )
            {
                $totalCash += $payments->amount;
            }
            else if( $payments->paymentmodeid == '2'  )
            {
                $totalCheque += $payments->amount;
            }
            else if( $payments->paymentmodeid == '3' || $payments->paymentmodeid == '4'  )
            {
                $totalCard += $payments->amount;
            }
            else if( $payments->paymentmodeid == '5'    )
            {
                $totalOther += $payments->amount;
            }

        }


        foreach ($expenses as $expense)
        {
            $totalExpenses += $expense->amount;
            if( $expense->paymentmodeid == '1' )
            {
                $totalExpensesCash +=  $expense->amount;
            }
            else if( $expense->paymentmodeid == '2' )
            {
                $totalExpensesByCheques +=  $expense->amount;
            }
            else if( $expense->paymentmodeid == '3' || $expense->paymentmodeid == '4' )
            {
                $totalExpensesByCard +=  $expense->amount;
            }
            else if( $expense->paymentmodeid == '5'   ) {
                $totalExpensesOtherPaymentMethod += $expense->amount;
            }
        }

        foreach ($salariesPaid as $paid )
        {
            $totalSalary += $paid->amount;
            if( $paid->paymentmodeid == '1' )
            {
                $totalSalaryInCash +=  $paid->amount;
            }
            else if( $paid->paymentmodeid == '2' )
            {
                $totalSalaryInCheque +=  $paid->amount;
            }
            else if( $paid->paymentmodeid == '3' || $paid->paymentmodeid == '4' )
            {
                $totalSalaryByCard +=  $paid->amount;
            }

            else if( $paid->paymentmodeid == '5'   )
            {
                $totalSalaryOther +=  $paid->amount;
            }
        }

         foreach ( $advancesUsed as $advance )
         {
             $clientPaymentdate = date("Y-m-d", strtotime( $advance->clientPayment->created_at));
             if( date("Y-m-d", strtotime($advance->created_at )) != $clientPaymentdate )
             {
                 $totalAdvanceAdjusted += $advance->amount;
             }
         }

         $totalOpeningBalance = 0;
         $totalClosingBalance = 0;
         $totalCashBalance = 0;
         $extra = 0;
         $k = 0;
         foreach( $closingRecords as $closing )
         {
             $totalOpeningBalance += $closing->openingbalance;
             $totalClosingBalance += $closing->closingbalance;
            if( ( $k + 1) == count($closingRecords) )
            {
                if( $closing->closingbalance == '' )
                {
                    $extra = $closing->openingbalance;
                }
            }

                $k++;
         }

        $totalClosingBalance = $totalCash - $totalExpensesCash - $totalSalaryInCash + $totalOpeningBalance;

        $totalCashBalance =  $totalClosingBalance -  $totalOpeningBalance ;

        if( $totalCashBalance < 0 )
        {
            $totalClosingBalance = $totalOpeningBalance + $totalCashBalance; // minus
        }


        $serviceTotalPaidPrice = $serviceSales - $serviceDiscount;
        $serviceTaxableAmount = ( $serviceTotalPaidPrice / ((100 + SERVICE_TAX) * 0.01 ) );
        $serviceTax = ($serviceTotalPaidPrice  - $serviceTaxableAmount );
        $productTotalPaidPrice =  $productSales - $productDiscount;
        $staffCommision = ($serviceTaxableAmount + $productTaxableAmount) * 0.10;


/*

echo "<pre>";
echo 'Service Sales: '.$serviceSales;
echo "<br />";
echo 'Service Discount: '.$serviceDiscount;
echo "<br />";
echo 'Service Taxable Amount: '.$serviceTaxableAmount;
echo "<br />";
echo 'Service Tax: '.$serviceTax;
        echo "<br />";
        echo 'Service Amount: '.$serviceTotalPaidPrice;

        echo "<br />";
        echo "<br />";
echo "Prodct Sales: ".$productSales;
        echo "<br />";
echo "Product Discount: ".$productDiscount;
echo "<br />";
echo "Product Taxable Amount: ".$productTaxableAmount;
 echo "<br />";
echo "Product Tax: ".$productTax;
echo "<br />";
echo "Product Amount: ".$productTotalPaidPrice;
        echo "</pre>"; */

    $arrParam = array(
        'serviceSales' => $serviceSales,
        'serviceDiscount' => $serviceDiscount,
        'serviceTaxableAmount' => $serviceTaxableAmount,
        'serviceTax' => $serviceTax,
        'serviceTotalPaidPrice' => $serviceTotalPaidPrice,
        'productSales' => $productSales,
        'productDiscount' => $productDiscount,
        'productTaxableAmount' =>  $productTaxableAmount,
        'productTax' => $productTax,
        'productTotalPaidPrice' => $productTotalPaidPrice,
        'staffCommision' => $staffCommision,
        'grand_expenses' => $totalExpenses + $totalSalary,
        'totalExpenses' => $totalExpenses,
        'totalSalary' => $totalSalary,
        'grandExpensesCash' => $totalExpensesCash + $totalSalaryInCash,
        'grandExpensesCheque' => $totalExpensesByCheques + $totalSalaryInCheque,
        'grandExpensesCard' => $totalExpensesByCard + $totalSalaryByCard,
        'grandExpensesOther' => $totalExpensesOtherPaymentMethod + $totalSalaryOther,
        'grand_sale' => $productTotalPaidPrice + $serviceTotalPaidPrice,
        'grand_sale_pending_amount' => $totalPendingAmount,
        'grand_sale_cash' =>  $totalCash,
        'grand_sale_cheque' => $totalCheque,
        'grand_sale_card' => $totalCard,
        'grand_sale_other' => $totalOther,
        'grand_sale_collection' =>  $totalCash + $totalCard + $totalOther,
        'grand_sale_expenses' =>  $totalExpenses + $totalSalary,
        'totalAdvanceAdjusted' => $totalAdvanceAdjusted,
        'totalAdvanceRecipet' => $totalAdvanceRecipet,
        'totalOpeningBalance' => $totalOpeningBalance,
        'totalClosingBalance' => $totalClosingBalance ,
        'totalCashBalance' => $totalCashBalance,
        'days' => $days

    );

        return view('revenuereport', $arrParam);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if( $request->close_day == "close_day" )
        {
            $days = ClosingDay::whereDate("dates","=", date("Y-m-d") )->first();
            if(count($days) > 0 ) {
                $hsRevenue = new HS_Revenue();
                $totalClosingBal = ($hsRevenue->getCashForDate(date("Y-m-d"))) + $days->openingbalance;

                $days->closingbalance = $totalClosingBal;
                $days->isclosed = '1';
                $days->save();
            }
            $request->session()->flash("successmsg", "Successfully Closed.");
            return redirect()->back();
        }
        else if( $request->update_opening_balance == "update_opening_balance" )
        {
            $days = ClosingDay::whereDate("dates","=", date("Y-m-d") )->first();
            if( $days )
            {
                $days->openingbalance = $request->openingbalance;
                $days->save();

            }
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
