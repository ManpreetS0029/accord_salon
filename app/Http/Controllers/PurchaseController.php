<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Company;
use App\Product;
use App\Purchase;
use App\PurchaseItem;
use App\ProductStock;
use App\PaymentMode;
use App\PurchasePayment;
class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        $query = Purchase::query();
        
        //$purchases = Purchase::orderBy('billdate','desc')->paginate(50);
        
        $search = false;
        if( '' != trim($request->input("companyid")) )
        {
            $search = true;
             $query->where('companyid', trim($request->input("companyid")));
        }

        if( '' != trim($request->input("paymentstatus")) )
        {
            $query->where('ispaid', trim($request->input("paymentstatus")));
        }

        if( '' != trim($request->input("paymentmodeid"))   )
        {
            
            $search = true;
            $query->join('purchasepayment', function($join) use ($request) { 
             
             $join->on ('purchasemaster.id', '=', 'purchasepayment.purchasemasterid')
                      
                     ->where ('purchasepayment.paymentmodeid', '=', trim($request->input("paymentmodeid")) )
                     ->select(' purchasepayment.ispaymentdone ')
                     ;
             });
             
        }
        

        $dateFrom = '';
        $dateTo = '';
        if( '' != trim($request->input('datefrom')))
        {
            $search = true;
            $dateArr = explode('/',$request->input('datefrom') );
            $dateFrom = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0].' 00:00:00';
            
            /*if( '' == trim($request->input('dateto')) )
            {
                $dateTo = date("Y-m-d");
            } */
            
        }
        
         if( '' != trim($request->input('dateto')))
        {
             $search = true;
            $dateArr = explode('/',$request->input('dateto') );
            $dateTo = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0].' 23:59:59';
            /*if( '' == trim($request->input('datefrom')) )
            {
                $dateTo = $dateFrom;
            }*/
            
        }

         if( $dateFrom == '' && $dateTo == '' )
        {
             $search = true;
            $dateFrom = date("Y-m-d")." 00:00:00";
            $dateTo = date("Y-m-d")." 23:59:59";
        }
         
        if( $dateFrom != '' && $dateTo != '' )
        {
            $query->whereBetween('purchasemaster.created_at', [ $dateFrom, $dateTo ] );
        }
        else if( $dateFrom != ''  )
        {
            $query->whereDate('purchasemaster.created_at', '>=',  $dateFrom   );
        }
        else if( $dateTo != ''  )
        {
            $query->whereDate('purchasemaster.created_at', '<=',  $dateFrom   );
        }

        
        $purchaseForCalculations = '';
        if($search == false )
        {
            $purchaseAll = Purchase::groupBy('id')->orderBy('purchasemaster.created_at', 'desc')->get();
            $purchaseForCalculations = $purchaseAll;
            
            
         
            $purchases = Purchase::groupBy('purchasemaster.id')->orderBy('purchasemaster.created_at', 'desc')->paginate(50);

            
            
        }
        else {
  
                  $purchases = $query->groupBy('purchasemaster.id')->orderBy('purchasemaster.created_at', 'desc')->get();
                  $purchaseForCalculations = $purchases;
                  
        }

        
        $totalSale = 0;
        $totalPaidAmount = 0;
        $totalCashPaid = 0;
        $totalPendingAmount = 0;
        $totalAmountUnderReview = 0;
        $totalAmountReviewFailed = 0;
        foreach ( $purchaseForCalculations as $sale ) {
            $totalSale += $sale->grandtotal;

            $totalPaidAmount += $sale->getTotalPaidAmount();
            $totalCashPaid += $sale->getTotalAmountPaidAsCash();
            $totalAmountUnderReview += $sale->getAmountUnderReview();
            $totalPendingAmount = $sale->pendingAmount();

            $totalAmountReviewFailed += $sale->getTotalFailedAmount(); 
            
            
        }
        
         
        //echo $query->toSql();
         
         $paymentModes = PaymentMode::all();
        
        
        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select';
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        

        $companies = Company::all();

        $arrComp = array();
        $arrComp[null] = "Select";
        

        foreach  ( $companies as $comp) {
            $arrComp[$comp->id] = $comp->companyname;
        }
        
        return view('purchaselist', ['purchases' => $purchases, 'paymentmodes' => $paymentModeArr, 'totalsale' => $totalSale, 'totalpaidamount' => $totalPaidAmount, 'totalcashpaid' => $totalCashPaid, 'totalpendingamount' => $totalPendingAmount, 'totalunderreviewamount' => $totalAmountUnderReview, 'totalfailedamount' => $totalAmountReviewFailed, 'companies' => $arrComp ]);

        
        
        //return view('purchaselist', ['purchases' => $purchases]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $companies = Company::orderBy('companyname','asc')->get();
        $arrComp = array();
        $arrComp[null] = 'Select';
        foreach ($companies as $value) {
            $arrComp[$value->id] = $value->companyname;
        }

        $products = Product::orderBy('name', 'asc')->get();
                $paymentModes = PaymentMode::all();
                        $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

                
            return view('purchaseadd', ['companies' => $arrComp, 'products' => $products, 'paymentmodes' => $paymentModeArr ]);
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
           $messages = [
               'ids.required' => 'Please select products.',

               //'amount.greator_then_zero' => 'Amount should be greator than zero.'

        ];
        //
           $validator =  Validator::make( $request->all(), [ 'companyid' => 'required', 'invoiceno' => 'required', 'billdate' => 'required|date_format:d/m/Y', 'deliverydate' => 'required|date_format:d/m/Y', 'paymentduedate' => 'required|date_format:d/m/Y', 'ids' => 'required|array' ], $messages );

        if ($validator->fails()) {
            return redirect( route('purchase.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {


            // check all products qnty and price if zero then show error
            $x = 0;
            $messages = array();
            $error = true;
            $itemMultipleCheck = array();
            foreach  ($request->ids as $p ) {

                 if( $p > 0 && in_array( $p,  $itemMultipleCheck ))
                {
                    $messages = array('Products are selected multiple times.');
                }


                 if($p > 0)
                {
                    $error = false;
                    $itemMultipleCheck[] = $p;
                }
                                 
                 if( $p > 0 && ( $request->unitprice[$x] <= 0 || $request->qnty[$x] <= 0 ) )
                {
                    $messages = array('Selected products price and quantity should be greator than zero.');
                }
                $x++;

            }

            if( $error == true )
            {
                $messages = array('Please select atleast one product to add purchase.');
            }
            
            if( count($messages) > 0 )
            {
                return redirect( route('purchase.create') )
                ->withInput()
                ->withErrors($messages);
            }

            //add Here
            //first update product price
            $x = 0;
            $productsArr = array();
            $totalLineAmount = 0;
            $totalDiscountAmount = 0;
            $totalTaxAmount = 0;
            $grandTotal = 0;
            $arrItems = array();
                        foreach  ($request->ids as $p ) {

                            if($p > 0)
                            {
                                $product = Product::findOrFail($p);
                                if( $product->purchaseprice != $request->unitprice[$x] )
                                {
                                    //update
                                    $product->purchaseprice = $request->unitprice[$x];
                                }
                                $product->save();
                                $item = new PurchaseItem();
                                $item->itemid = $product->id;
                                $item->qnty = $request->qnty[$x];
                                $item->purchaseprice = $product->purchaseprice;
                                $item->discounttype = $request->discounttype[$x];
                                $item->discountvalue = $request->discountvalue[$x];
                                $item->discountamount = $item->getDiscountAmount();
                                $item->taxtype = $product->tax;
                                $item->taxamount = $item->getTaxAmount();
                                $arrItems[] = $item;
                                
                            }
                            $x++;
                        }

                        $purchaseMaster = new Purchase();
                        $purchaseMaster->companyid = $request->companyid;
                        $purchaseMaster->invoiceno = $request->invoiceno;
                        $billDate = explode('/',$request->billdate);
                        $purchaseMaster->billdate = $billDate[2].'-'.$billDate[1].'-'.$billDate[0];

                        $deliveryDate = explode('/',$request->deliverydate);
                        $purchaseMaster->deliverydate = $deliveryDate[2].'-'.$deliveryDate[1].'-'.$deliveryDate[0];

                        $paymentduedate = explode('/',$request->paymentduedate);
                        $purchaseMaster->paymentduedate = $paymentduedate[2].'-'.$paymentduedate[1].'-'.$paymentduedate[0];

                        $paidAmount = $request->amount;
                        
                        
                        $taxableAmount = 0;
                        foreach( $arrItems as $item ) { 
                            $totalLineAmount += $item->getTotalPriceWithoutDiscount();
                            $totalTaxAmount += $item->getTaxAmount();
                            $totalDiscountAmount += $item->getDiscountAmount();
                            $grandTotal += $item->grandTotal();
                            $taxableAmount += $item->getTaxableAmount();
                        }

                        $globalDiscount = 0;
                        if( $request->global_discounttype == 'percent' && $request->global_discountvalue > 0 )
                        {
                            $globalDiscount = $taxableAmount * $request->global_discountvalue / 100;
                        }
                        else if( $request->global_discounttype == 'absolute' && $request->global_discountvalue > 0 ){
                            $globalDiscount = $request->global_discountvalue;
                        }

                        $grandTotal = $grandTotal - $globalDiscount;
                        
                        if( $paidAmount == '' )
                        {
                            $paidAmount = $grandTotal;
                        }
                        else {
                            $paidAmount = $request->amount;
                        }

                        if( $paidAmount > $grandTotal )
                        {
                            $paidAmount = $grandTotal;
                        }

                        
                        if( $request->paymentmodeid != '2' && $paidAmount >= $grandTotal )
                        {
                            $purchaseMaster->ispaid = '1';
                        }
                        else
                        {
                            $purchaseMaster->ispaid = '0';
                        }

                        
                        
                            //$purchaseMaster->downpayment = $paidAmount;
                        
                        $purchaseMaster->totallineamount = $totalLineAmount;
                        
                        
                        $purchaseMaster->totaltaxamount = $totalTaxAmount;

                        $purchaseMaster->discounttype = $request->global_discounttype;
                        $purchaseMaster->discountvalue = $request->global_discountvalue;
                        
                        $purchaseMaster->discountamount= $globalDiscount;

                        $purchaseMaster->grandtotal = $grandTotal;

                        

                        $purchaseMaster->save();

                        foreach( $arrItems as  $item ) { 

                            $product = Product::where('id', $item->itemid)->first();
                            $product->stockavailable += $item->qnty;
                            $product->save();

                            $stock = new ProductStock();
                            $stock->productid =  $item->itemid;
                            $stock->quantity = $item->qnty;
                            $stock->purchaseprice = $item->purchaseprice;
                            $stock->purchaseorderid = $purchaseMaster->id;
                            $stock->save();
                            
                            $item->purchasemasterid = $purchaseMaster->id;
                            $item->save();
                        }

                        // payments
                        if( $paidAmount > 0 )
                        {

                            $payment = new PurchasePayment();
                            $payment->purchasemasterid = $purchaseMaster->id;
                            $payment->paymentmodeid = $request->paymentmodeid;
                            if( $request->paymentmodeid == '5' )
                            {
                                $payment->other = $request->other;
                            }
                            if( $request->paymentmodeid == '2' )
                            {
                                $payment->bankname = $request->bankname;
                                $payment->bankaccountno = $request->bankaccountno;
                                $payment->chequeno = $request->chequeno;
                                if( $request->chequedate != '' ) {
                                    $dt = explode( '/', $request->chequedate );
                    
                                    $payment->chequedate = $dt[2].'-'.$dt[1].'-'.$dt[0];
                                }

                                
                            }
                            $payment->amount = $paidAmount;
                            //means payment is not b cheques and full payment paid
                            if( $payment->paymentmodeid != '2' )
                            {
                                $payment->ispaymentdone = '1';
                            
  
                            }
                            else
                            {
                                $payment->ispaymentdone = '0';
                            }
                            $payment->save();
        }
                        
                        
                        //                        $purchaseMaster->getItems()->saveMany($arrItems);
                        //$totallineamount
                        
                        //$purchase = new Purchase();
                        //$purchase = 

            
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
            //  return redirect('sale/'.$id.'/edit');
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
        $purchase = Purchase::findOrFail($id);
        $companies = Company::orderBy('companyname','asc')->get();
        $arrComp = array();
        $arrComp[null] = 'Select';
        foreach ($companies as $value) {
            $arrComp[$value->id] = $value->companyname;
        }

        
        $products = Product::orderBy('name', 'asc')->get();
        return view("purchaseedit", ['purchase' => $purchase, 'companies' => $arrComp, 'products' => $products  ]);

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

        $purchase = Purchase::findOrFail($id);
           $messages = [
               'ids.required' => 'Please select products.',

               //'amount.greator_then_zero' => 'Amount should be greator than zero.'

        ];
        //
           $validator =  Validator::make( $request->all(), [ 'companyid' => 'required', 'invoiceno' => 'required', 'billdate' => 'required|date_format:d/m/Y', 'deliverydate' => 'required|date_format:d/m/Y', 'paymentduedate' => 'required|date_format:d/m/Y', 'ids' => 'required|array' ], $messages );

        if ($validator->fails()) {
            return redirect( route('purchase.edit', $id ) )
                ->withInput()
                ->withErrors($validator);
        }
        else {


            // check all products qnty and price if zero then show error
            $x = 0;
            $messages = array();
            $error = true;
            $itemMultipleCheck = array();
            foreach  ($request->ids as $p ) {

                if($p > 0)
                {
                    $error = false;
                }
                 if( in_array( $p,  $itemMultipleCheck ))
                {
                    $messages = array('Products are selected multiple times.');
                }
                 $itemMultipleCheck[] = $p;
                 
                if( $p > 0 && ( $request->unitprice[$x] <= 0 || $request->qnty[$x] <= 0 ) )
                {
                    $messages = array('Selected products price and quantity should be greator than zero.');
                }
                $x++;
            }

            if( $error == true )
            {
                $messages = array('Please select atleast one product to add purchase.');
            }
            
            if( count($messages) > 0 )
            {
                return redirect( route('purchase.create') )
                ->withInput()
                ->withErrors($messages);
            }

            //add Here
            //first update product price
            $x = 0;
            $productsArr = array();
            $totalLineAmount = 0;
            $totalDiscountAmount = 0;
            $totalTaxAmount = 0;
            $grandTotal = 0;
            $arrItems = array();

            $savedItems = $purchase->getItems;
            $itemsWeNeed = array();



            // delete all items
            //  PurchaseItem::whereIn('purchasemasterid', $purchase->id)->delete();
            /*foreach( $savedItems as $item )
            {
                echo $item->itemid;
                }
            exit(0); */
            // delete stock
            ProductStock::where( 'purchaseorderid', $purchase->id )->delete();
                
                        foreach  ($request->ids as $p ) {

                            if($p > 0)
                            {
                                $product = Product::findOrFail($p);
                                $productSave = false;
                                if( $product->purchaseprice != $request->unitprice[$x] )
                                {
                                    //update
                                    $productSave = true;
                                    $product->purchaseprice = $request->unitprice[$x];
                                }
                                //$product->save();
                                $continues = true;
                                foreach( $savedItems as  $itemN ) {
                                    // means item is there qlready so compare qnty
                                    if( $itemN->itemid == $p )
                                    {
                                        $continues = false;
                                        if( $itemN->qnty != $request->qnty[$x] )
                                        {
                                            // that means we need to update product stock qnty
                                            $diffQnty = $request->qnty[$x] - $itemN->qnty;
                                            $product = Product::where('id', $itemN->itemid)->first();
                                            $product->stockavailable += $diffQnty;
                                            $productSave = true;
                                        }
                                                                                
                                            //$item->itemid = $product->id;
                                            $itemN->qnty = $request->qnty[$x];
                                            $itemN->purchaseprice =  $product->purchaseprice;
                                            $itemN->discounttype = $request->discounttype[$x];
                                            $itemN->discountvalue = $request->discountvalue[$x];
                                            $itemN->discountamount = $itemN->getDiscountAmount();
                                            $itemN->taxtype = $product->tax;
                                            $itemN->taxamount = $itemN->getTaxAmount();
                                            
                                            
                                            $stock = new ProductStock();
                                            $stock->productid =  $itemN->itemid;
                                            $stock->quantity = $itemN->qnty;
                                            $stock->purchaseprice = $itemN->purchaseprice;
                                            $stock->purchaseorderid = $purchase->id;
                                            $stock->save();

                                            $itemN->save();   
                                            $arrItems[] = $itemN;
                                            $itemsWeNeed[] = $itemN; 
                                        
                                    }
                                }

                                if( $continues == true ) {

                                    $item = new PurchaseItem();
                                    $item->itemid = $product->id;
                                    $item->qnty = $request->qnty[$x];
                                    $item->purchaseprice =  $product->purchaseprice;
                                    $item->discounttype = $request->discounttype[$x];
                                    $item->discountvalue = $request->discountvalue[$x];
                                    $item->discountamount = $item->getDiscountAmount();
                                    $item->taxtype = $product->tax;
                                    $item->taxamount = $item->getTaxAmount();
                                    $item->purchasemasterid = $purchase->id;
                                    $item->save();

                                    $arrItems[] = $item;

                                    $stock = new ProductStock();
                                            $stock->productid =  $item->itemid;
                                            $stock->quantity = $item->qnty;
                                            $stock->purchaseprice = $item->purchaseprice;
                                            $stock->purchaseorderid = $purchase->id;
                                            $stock->save();

                                            $productSave = true;

                                            $product->stockavailable =  $product->stockavailable + $item->qnty;

                                }

                                if( $productSave == true ) {

                                    $product->save();
                                }
                            }
                            $x++;
                        }

                        
                        $purchaseMaster = $purchase;
                        $purchaseMaster->companyid = $request->companyid;
                        $purchaseMaster->invoiceno = $request->invoiceno;
                        $billDate = explode('/',$request->billdate);
                        $purchaseMaster->billdate = $billDate[2].'-'.$billDate[1].'-'.$billDate[0];

                        $deliveryDate = explode('/',$request->deliverydate);
                        $purchaseMaster->deliverydate = $deliveryDate[2].'-'.$deliveryDate[1].'-'.$deliveryDate[0];

                        $paymentduedate = explode('/',$request->paymentduedate);
                        $purchaseMaster->paymentduedate = $paymentduedate[2].'-'.$paymentduedate[1].'-'.$paymentduedate[0];

                        //                        $purchaseMaster->downpayment = $request->downpayment;
                      
                        foreach( $arrItems as $item ) { 
                            $totalLineAmount += $item->getTotalPriceWithoutDiscount();
                            $totalTaxAmount += $item->getTaxAmount();
                            $totalDiscountAmount += $item->getDiscountAmount();
                            $grandTotal += $item->grandTotal();
                        }
                        
                        $purchaseMaster->totallineamount = $totalLineAmount;
                        $purchaseMaster->totaltaxamount = $totalTaxAmount;
                        $purchaseMaster->discountamount= $totalDiscountAmount;
                        $purchaseMaster->grandtotal = $grandTotal;
                        $purchaseMaster->save();

                        // delete old items that are deleted from current
                        $deleteItemIds = array();
                         foreach( $savedItems as  $item ) { 

                             $deleteItem = true;
                             foreach( $itemsWeNeed as $k => $itemInner ) { 
                                 if( $itemInner->id == $item->id )
                                 {
                                     $deleteItem = false;
                                 }
                             }
                             
                             if( $deleteItem == true )
                             {
                                 $product = Product::where('id', $item->itemid)->first();
                                 $product->stockavailable = max( $product->stockavailable - $item->qnty, 0  );
                                 $product->save();

                                 $deleteItemIds[] = $item->id;
                             }

                         }

                         if( count($deleteItemIds) > 0 )
                         {
                             PurchaseItem::whereIn( 'id', $deleteItemIds)->delete();
                         }
                        
                        /*                        foreach( $arrItems as  $item ) { 

                            $item->purchasemasterid = $purchaseMaster->id;
                            $item->save();
                        } */

                        //                        $purchaseMaster->getItems()->saveMany($arrItems);
                        //$totallineamount
                        
                        //$purchase = new Purchase();
                        //$purchase = 

            
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
            //  return redirect('sale/'.$id.'/edit');
        }

    }
        
    


    public function paymenthistory($id)
            {
                $purchase = Purchase::findOrFail($id);
                return view('purchasepaymenthistory', ['purchase' => $purchase]);
            }

    public function addpayment($id)
        {
            
            $purchase = Purchase::findOrFail($id);

           $paymentModes = PaymentMode::all();

                $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }
        

 
            return view('purchaseaddpayment', ['purchase' => $purchase, 'paymentmodes' => $paymentModeArr ] );

            
        }

         public function storepayment( Request $request, $id )
            {
                
            $purchase = Purchase::findOrFail($id);
            
           
            //if amount is empty
            if( $request->amount == '' || $request->amount <= 0 )
            {
                return redirect( route('purchase.addpayment', $purchase->id) )
                ->withInput()
                   ->withErrors(array('Amount should be greator than zero.'));

            }
            
            //if already added full amount entries and some could be under reviews or full payment paid
            
            
            if( $purchase->pendingAmount() <= $purchase->getAmountUnderReview() )
            {
                
                return redirect( route('purchase.addpayment', $purchase->id) )
                ->withInput()
                   ->withErrors(array('We can not add as other payments are under review for this purchase.'));
            
                
            }
            else if( ($purchase->pendingAmount() - $purchase->getAmountUnderReview()) < $request->amount )
            {
                 return redirect( route('purchase.addpayment', $purchase->id) )
                ->withInput()
                   ->withErrors(array('Pending amount is less than the amount you entered. There could be payment under review. You should add amount of: '.($purchase->pendingAmount() - $purchase->getAmountUnderReview())));
            }
            
           
            
            
            if( $request->amount > 0 ) {
                
                                
            $payment = new PurchasePayment();
            $payment->purchasemasterid = $purchase->id;
            $payment->paymentmodeid = $request->paymentmodeid;

            $payment->amount = $request->amount;
            if( $request->amount > $purchase->pendingAmount()  )
            {
                $payment->amount = $purchase->pendingAmount();
            }




            if( $purchase->paymentmodeid != '2' ) {
                $purchase->totalamountpaidyet += $payment->amount;
            }
            //fulle payment paid also not by cheque so 100% proofed to pay
            if( $purchase->totalamountpaidyet >= $purchase->grandtotal && $request->paymentmodeid != '2' )
            {
                $purchase->ispaid = '1';
            }
            else
            {
                $purchase->ispaid = '0';
            }
                
            //not cheques then this payment assume as done
            if ( $request->paymentmodeid != '2' )
            {
                $payment->ispaymentdone = 1;
            }
            else
            {
                $payment->ispaymentdone = 0;
            }
            
            //bank payment
            if( $request->paymentmodeid == '2' )
            {
                $payment->bankname = $request->bankname;
                $payment->bankaccountno = $request->bankaccountno;
                $payment->chequeno = $request->chequeno;
                if( $request->chequedate != '' ) {
                    $dt = explode( '/', $request->chequedate );
                    
                    $payment->chequedate = $dt[2].'-'.$dt[1].'-'.$dt[0];
                }
                
            }
            else if( $request->paymentmodeid == '5' ) // other
            {
                $payment->other = $request->other;
            }
            $payment->save();
            $purchase->save();

                    $request->session()->flash("successmsg", "Successfully Updated.");
                    return redirect()->back();
            }
            else
            {
                return redirect( route('purchase.addpayment', $purchase->id) )
                ->withInput()
                   ->withErrors(array('Looks like there is no pending amount for this purchase.'));
            }
            
     }

        public function editpayment($purchaseId, $paymentId )
    {
        //echo $saleId.' == '.$paymentId;
        $purchase = Purchase::findOrFail($purchaseId);

                $paymentFound = false;
        foreach( $purchase->payments as  $item ) {

            if( $item->id == $paymentId )
            {
                $paymentFound = true;
                break;
            }
            
        }

        if( $paymentFound == false )
        {
            die("Invalid Access");
        }
        
        $payment = PurchasePayment::findOrFail($paymentId);

        
           $paymentModes = PaymentMode::all();

                $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }
    

        
            return view('purchaseeditpayment', [ 'purchase' => $purchase, 'payment' => $payment, 'paymentmodes' => $paymentModeArr  ]);
    }

    public function updatepayment(Request $request, $purchaseId, $paymentId )
    {

        if ($request->amount <= 0) {
            return redirect(route('purchase.editpayment', [$purchaseId, $paymentId]))
                            ->withInput()
                            ->withErrors(array('Amount should be greator than zero.'));
        }

        $purchase = Purchase::findOrFail($purchaseId);
        $paymentFound = false;
        $totalPaymentPaid = 0;
        $totalPaymentUnderReview = 0;

        foreach ($purchase->payments as $item) {

            if ($item->id == $paymentId) {
                $paymentFound = true;
            } else {
                if ($item->ispaymentdone == '1') {
                    $totalPaymentPaid += $item->amount;
                } else if ($item->ispaymentdone == '0') {
                    $totalPaymentUnderReview += $item->amount;
                }
            }
        }


        if ($paymentFound == false) {
            die("Invalid Access");
        }


        $paymentUnderSale = $totalPaymentPaid + $totalPaymentUnderReview;


        if ($paymentUnderSale >= $purchase->grandtotal) {

            return redirect(route('purchase.editpayment', [$puchaseId, $paymentId]))
                            ->withInput()
                            ->withErrors(array('We can not update as we have entries available respect to total purchase amount. Please check all entries first.'));
        }


        $payment = PurchasePayment::findOrFail($paymentId);

        $oldAmount = $payment->amount;
        $newAmount = $request->amount;
        $oldStatus = $payment->ispaymentdone;
        $newStatus = $request->ispaymentdone;


        if ($paymentUnderSale + $newAmount >= $purchase->grandtotal) {
            $newAmount = $sale->paidprice - ($paymentUnderSale );
        }

        

        //if current payment mark ads paid
        if ($newStatus == '1') {

            if ($newAmount + $paymentUnderSale >= $purchase->grandtotal) {
                $purchase->ispaid = '1';
            } else {
                $purchase->ispaid = '0';
            }

             $purchase->totalamountpaidyet = $totalPaymentPaid + $newAmount;
        } else {
            $purchase->totalamountpaidyet = $totalPaymentPaid;
        }

        $payment->paymentmodeid = $request->paymentmodeid;
        if( $request->paymentmodeid != '5' )
        {
            $payment->other = '';
        }
        
        if ( $request->paymentmodeid == '2' )
        {
            
            $payment->bankname = $request->bankname;
            $payment->bankaccountno = $request->bankaccountno;
            $payment->chequeno = $request->chequeno;

            if( $request->chequedate != '' )
            {
                $dt =  explode('/', $request->chequedate);
                $payment->chequedate = $dt[2].'-'.$dt[1].'-'.$dt[0];
            }
            else
            {
                $payment->chequedate = null;
            }

            
        }
        else
        {
            $payment->bankname = '';
            $payment->bankaccountno = '';
            $payment->chequeno = '';
            $payment->chequedate = null;
        }
        
        $payment->amount = $newAmount;
        $payment->ispaymentdone = $newStatus;
        $payment->paymentfailedreason = $request->paymentfailedreason;

        $purchase->save();
        $payment->save();

        $request->session()->flash("successmsg", "Successfully Updated.");
        return redirect()->back();
    }

            public function destroy($id)
            {
                
                //
                
                $purchase = Purchase::findOrFail($id);
                $allMainIds = array();
                
                foreach( $purchase->getItems as $item )
                {
                    
                    //   $allMainIds[] = $item->id;
                    // if( $item->itemtype == 'product' ) {
                        $product = Product::findOrFail($item->itemid);
                        $product->stockavailable -=  $item->qnty;

                        if( $product->stockavailable < 0 )
                        {
                            $product->stockavailable = 0;
                        }
                        //  $product->stockavailable =   max(0, $product->stockavailable);
                        //$product->soldcount -= $item->quantity;
                        $product->save();
                        //}
                    
                }

                
                
                //delete all items
                //PurchaseItem::whereIn('id', $allMainIds)->delete();
                PurchaseItem::where('purchasemasterid', '=', $id )->delete();

                 
                PurchasePayment::where('purchasemasterid', '=', $purchase->id)->delete();

                ProductStock::where( 'purchaseorderid', '=', $purchase->id )->delete();
                
                 $purchase->delete(); 

                                     session()->flash("successmsg", "Successfully Deleted.");
                 return redirect()->back();
                 

                 
            }


    
    
}
