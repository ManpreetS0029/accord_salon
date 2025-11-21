<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Client;
use App\Packages;
use App\Sale;
use App\SaleItem;
use App\Services;
use App\PaymentMode;
use App\SalePayment;
use App\Staff;
use App\ServiceDoneStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

define('SERVICE_TAX', 18.0);
//simple comment
class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //$sales = Sale::with(['salepayments' => function ($query) { $query->where('ispaymentdone', '=', '2'); }] )->where('salepayments.ispaymentdone', '=', 2)->orderBy('created_at', 'desc')->paginate(50); //DB::table('')->paginate(50);
        //print_r($categories);
        $query = Sale::query();
        $clientArr = array();
        $search = false;
        if( '' != trim($request->input("clientname")) )
        {
             
            $clients = Client::where('clientname', 'like', $request->input("clientname").'%')->get();
            
            
            foreach ($clients as $client )
            {
                $clientArr[] = $client->id;
             
            }
            $search = true;
            
                if( count($clientArr) > 0 )
            {
             
                $query->where( function($query) use ($clientArr, $request ) { 

                    $query->orWhereIn('clientid', $clientArr )
                            ->orWhere( function($query) use ($request) { 
                               
                                $query->where(['walkin_name', 'like', $request->input("clientname").'%'] );
                                $query->where(['clientid', '=', '0' ]);
                            });
                });
                                
                
            }
            else 
            {
                $query->where('walkin_name', 'like', $request->input("clientname").'%');
                $query->where('clientid', '=', 0 );
            }
        
            
        }
        
        
        if( '' != trim($request->input("paymentstatus")) )
        {
            $query->where('ispaid', trim($request->input("paymentstatus")));
        }
         
        
        if( '' != trim($request->input("paymentmodeid"))   )
        {
            
            $search = true;
            $query->join('salepayment', function($join) use ($request) { 
             
             $join->on ('sale.id', '=', 'salepayment.salemasterid')
                      
                     ->where ('salepayment.paymentmodeid', '=', trim($request->input("paymentmodeid")) )
                     ->select(' salepayment.ispaymentdone ')
                     ;
             });
             
        }
        
        /*else if( '' != trim($request->input("paymentmodeid"))  )
        {
            $search = true;
            $query->join('salepayment', function($join) use ($request) { 
             
             $join->on ('sale.id', '=', 'salepayment.salemasterid')
                     
                     ->where ('salepayment.paymentmodeid', '=', trim($request->input("paymentmodeid")) )
                     ->select(' salepayment.ispaymentdone ')
                     ;
             });
             }*/
       /*  else if(   '' != trim($request->input("paymentstatus")) )
        {
             $search = true;
            $query->join('salepayment', function($join) use ($request) { 
             
             $join->on ('sale.id', '=', 'salepayment.salemasterid')
                     ->where('salepayment.ispaymentdone', '=', $request->input("paymentstatus") ) 
                     ->select(' salepayment.ispaymentdone ')
                     ;
             });
        } */
        
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
            $query->whereBetween('sale.created_at', [ $dateFrom, $dateTo ] );
        }
        else if( $dateFrom != ''  )
        {
            $query->whereDate('sale.created_at', '>=',  $dateFrom   );
        }
        else if( $dateTo != ''  )
        {
            $query->whereDate('sale.created_at', '<=',  $dateFrom   );
        }

        
        
        
        /* $sales = Sale::join('salepayment', function($join) { 
             
             $join->on ('sale.id', '=', 'salepayment.salemasterid')
                     ->where('salepayment.ispaymentdone', '=', 2 )
                     ->select(' salepayment.ispaymentdone ')
                     ;
         }  )->groupBy('sale.id')->orderBy('sale.created_at', 'desc')->paginate(50); //DB::table('')->paginate(50);*/
        $saleForCalculations = '';
        if($search == false )
        {
            $salesAll = Sale::groupBy('id')->orderBy('sale.created_at', 'desc')->get();
            $saleForCalculations = $salesAll;
            
            
         
            $sales = Sale::groupBy('id')->orderBy('sale.created_at', 'desc')->paginate(50);

            
            
        }
        else {
  
                  $sales = $query->groupBy('sale.id')->orderBy('sale.created_at', 'desc')->get();
                  $saleForCalculations = $sales;
                  
        }

        $totalSale = 0;
        $totalPaidAmount = 0;
        $totalCashPaid = 0;
        $totalPendingAmount = 0;
        $totalAmountUnderReview = 0;
        $totalAmountReviewFailed = 0;
        foreach ( $saleForCalculations as $sale ) {
            $totalSale += $sale->paidprice;

            $totalPaidAmount += $sale->getTotalPaidAmount();
            $totalCashPaid += $sale->getTotalAmountPaidAsCash();
            $totalAmountUnderReview += $sale->getAmountUnderReview();
            $totalPendingAmount += ($sale->pendingAmount() > 0 ?  $sale->pendingAmount() : 0 );

            $totalAmountReviewFailed += $sale->getTotalFailedAmount(); 
            
            
        }
        
         
        //echo $query->toSql();
         
         $paymentModes = PaymentMode::all();
        
        
        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select';
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        
        
        
        return view('salelist', ['sales' => $sales, 'paymentmodes' => $paymentModeArr, 'totalsale' => $totalSale, 'totalpaidamount' => $totalPaidAmount, 'totalcashpaid' => $totalCashPaid, 'totalpendingamount' => $totalPendingAmount, 'totalunderreviewamount' => $totalAmountUnderReview, 'totalfailedamount' => $totalAmountReviewFailed ]);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $packages = Packages::all();
        $categories = Category::all();
        $clients = Client::all();
        $paymentModes = PaymentMode::all();
        
        $clientArr = array();
        $clientArr[0] = 'Select';
        foreach ( $clients as $client)
        {
            $clientArr[$client->id] = $client->clientname.' ('.$client->phone.')';
        }
        $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        $products = Product::all()->where('stockavailable', '>', '0');

        $staff = Staff::orderBy('firstname', 'asc')->get();

        return view('saleadd', ['categories' =>  $categories, 'packages' => $packages   , 'products' => $products, 'clients' => $clientArr, 'staffs' => $staff, 'paymentmodes' => $paymentModeArr ] );
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
            'ids.required' => 'Please select package, service or product to make order.',
            'qnty.greator_then_zero' => 'Quantity should be greator than zero.'

        ];

        
        $validator =  Validator::make( $request->all(), [ 'clientid' => 'required', 'ids' => 'required|array', 'qnty' => 'required|array|greator_then_zero' ], $messages );

        
        
        if ($validator->fails()) {
            return redirect( route('sale.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $msg = array();
            if( '' != trim($request->clientname  ) )
            {
                if(  '' == trim($request->phone ) )
                {
                    $msg[] = "Client Phone is required.";
                }

                if(  '' == trim($request->dob ) )
                {
                    $msg[] = "Date of birth is required.";
                }
                
            }

            if( count($msg) > 0 )
            {
                return redirect( route('sale.create') )
                ->withInput()
                ->withErrors($msg);
            }
            
            $sale = new Sale();
            $sale->clientid = $request->clientid;
            //$sale->
            $id = $this->calculateSale( $request);

            //$request->session()->flash("successmsg", "Successfully Added.");
            //            echo  route('sale.edit', $id );
                        return redirect( route('sale.edit', $id ));
          //  return redirect('sale/'.$id.'/edit');
        }

    }

    // adding sale in database
    protected function calculateSale( $request)
    {

        $sale = new Sale();

        if( count($request->ids) > 0 )
        {
            if( trim($request->clientname) != ''  )
            {
                //add new client
                $client = new Client();
                $client->clientname = $request->clientname;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->state = $request->state;
                $client->zipcode = $request->zipcode;
                $client->phone = $request->phone;
                $client->phone2 = $request->phone2;
                $dt = explode('/',$request->dob);
                $client->dob =  $dt[2]."-".$dt[1]."-".$dt[0];
                $client->save();
                
                $sale->clientid = $client->id;

            }
            
            

            if( $sale->clientid == '' )
            {
                $sale->clientid = $request->clientid;
            }

            $sale->walkin_name = $request->walkin_name;

            
            if( $sale->clientid  > 0 )
            {
                $sale->walkin_name = '';
            }

            $sale->save();

            
            
            $arrItems = array();
            
            $globalDiscount = 0;
            $globalDiscountType = $request->global_discounttype;
            $globalDiscountValue = $request->global_discountvalue;

            
            
            $x = 0;
            $packagesIds = array();
            $productIds = array();
            $serviceIds = array();
            $servicesTotalAmount = 0;
            foreach( $request->ids as $R )
            {
                
                if( $request->itemtypes[$x] == 'package' )
                {
                    $packagesIds[] = $R;
                   
                }
                else if( $request->itemtypes[$x] == 'product' )
                {
                    $productIds[] = $R;
                }
                else if( $request->itemtypes[$x] == 'service' )
                {
                    $serviceIds[] = $R;
                }

                
                
                //qnty
                //itemtypes
                //discounttype
                //discountvalue

                $x++;
            }
            
            $totalAmount = 0;
            if( count($packagesIds) > 0 )
            {
                $packagesRows = Packages::whereIn('id' , $packagesIds )->get();

                
                // print_r($packagesRows);
                foreach( $packagesRows as $R )
                {
                    //echo $R->id.'==';
                    // print_r($request->ids);

                    $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'package'  )
                        {

                        //print_r($R);


                        $packageServices = $R->packageservices;
                            
                        //$price = $R->price;
                        // getting dynamic price from user
                        $price = $request->unitprice[$keys];

                        //change package price temporary to calculate discount
                        $R->price = $price;
                        
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;

                        $servicesTotalAmount += $calculatedPrice;
                            $itemP = new SaleItem();
                            $itemP->itemid = $R->id;
                            $itemP->saleid = $sale->id;
                            $itemP->itemtype = 'package';
                            $itemP->actualpriceperitem = $price;  //$R->price;
                            $itemP->quantity = $qnty;
                            $itemP->discountvalue = $discountValue;
                            $itemP->discounttype = $discountType;
                            $itemP->discountamount = $discountAmount;
                            $itemP->title = $R->title;
                            $itemP->description = $R->description;
                            $itemP->parentid = 0;
                            $itemP->taxpercent = SERVICE_TAX;
                            //$arrItems[] = $item;
                            $itemP->save();
                            
                            
                        foreach( $packageServices as $packageService)
                        {
                             
                            $item = new SaleItem();
                            $item->itemid = $packageService->service->id;
                            $item->saleid = 0;
                            $item->itemtype = 'service';
                            $item->actualpriceperitem = $packageService->service->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $packageService->service->name;
                            $item->description = $packageService->service->description;
                            $item->parentid = $itemP->id;
                            $item->taxpercent = SERVICE_TAX;
                            //$arrItems[] = $item;
                            //$staffDropDown = 'staffs_'.$R->id.'_'.$packageService->service->id;
                            //$item->staffid = $request->{$staffDropDown};
                            $item->save();
                            //ServiceDoneStaff
                            $dropDownValues = $request->{'staffs_'.$R->id.'_'.$packageService->service->id}  ;


                            //$dropDownAmount = $request->{'amount_staffs_'.$R->id.'_'.$packageService->service->id} ;

                            //if no amount value filled then divide equal
                            //if few values filled then total -
                            
                            
                            $arrDropDownValues = array();
                            for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();
                                }
                                
                            }


                            
                         }

                        }
                    }
                }
                
            }

            if( count($serviceIds) > 0 )
            {
                //print_r($request->ids);
                //print_r($serviceIds);
                $servicesRows = Services::whereIn('id' , $serviceIds )->get();

                //count($servicesRows);
                foreach( $servicesRows as $R )
                {

                        $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'service'  )
                        {


                            //$price = $R->price;

                            // getting dynamic price entered by user
                        $price = $request->unitprice[$keys];
                        //temprary change service price to calculate discount
                        $R->price = $price;
                        
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;
                        $servicesTotalAmount += $calculatedPrice;
                        
                        $item = new SaleItem;
                            $item->itemid = $R->id;
                            $item->saleid = $sale->id;
                            $item->itemtype = 'service';
                            $item->actualpriceperitem = $price; //$R->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $R->name;
                            $item->description = $R->description;
                            $item->parentid = 0;
                            $item->taxpercent = SERVICE_TAX;
                           // $arrItems[] = $item;
                            $staffDropDown = 'staffs_services_'.$R->id;
                            //$item->staffid = $request->{$staffDropDown};

                            $item->save();
                            $dropDownValues = $request->{$staffDropDown};
                              for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                      
                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();
                                }
                                
                               }
                        }
                    }
                }
            }

            $productsTotalPrice = 0;
            $tax = 0;
            if( count($productIds) > 0 )
            {
                $productRows = Product::whereIn('id' , $productIds )->get();

                foreach( $productRows as $R )
                {
                    $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'product'  )
                        {

                            //$price = $R->price;

                            //getting dynamic price entered by user
                            $price = $request->unitprice[$keys];
                            //temporary change product price to calculate discount
                            $R->price = $price;
                            
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;
                        $productsTotalPrice += $calculatedPrice;

                        $tax += ( $calculatedPrice - ( $calculatedPrice / ((100 + $R->tax) * 0.01 ) ) );
                        
                        
                            $item = new SaleItem();
                            $item->itemid = $R->id;
                            $item->saleid = $sale->id;
                            $item->barcode = $R->barcode;
                            $item->itemtype = 'product';
                            $item->actualpriceperitem = $price; //$R->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $R->name;
                            $item->description = $R->description;
                            $item->parentid = 0;
                            $item->taxpercent = $R->tax;
                            $staffDropDown = 'staffs_product_'.$R->id;
                            //$item->staffid = $request->{$staffDropDown};

                            //$arrItems[] = $item;
                            $item->save();

                            $dropDownValues = $request->{$staffDropDown};
                              for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                      
                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();
                                }
                                
                               }
                            
                            $product = Product::find($R->id);
                            $product->stockavailable = $product->stockavailable - $qnty;
                            $product->soldcount = $product->soldcount + $qnty;
                            $product->save();
                        }
                    }


                }

            }


            if( $globalDiscountType == 'percent'  )
            {
                $globalDiscount =  ($totalAmount - $productsTotalPrice) * $globalDiscountValue / 100;
            }
            else
            {
                $globalDiscount = $globalDiscountValue;
            }

            
            $grandAmount = $totalAmount - $globalDiscount;

            $servicesTotalAmount = $servicesTotalAmount - $globalDiscount;
            $tax += ($servicesTotalAmount -  ($servicesTotalAmount / ( (100 + SERVICE_TAX  ) * 0.01 ) )); 
            /*if( $request->taxpercent > 0 )
            {
                $tax = $taxableAmount *  $request->taxpercent / 100;
            } */

            $grandTotal = $grandAmount;
            
            $sale->totalprice = $grandTotal;
            $sale->paidprice = $grandTotal;
            $sale->discounttype = $globalDiscountType;
            $sale->discountvalue = $globalDiscountValue;
            $sale->discountamount = $globalDiscount;
            $sale->ispaid = "1";
            $sale->taxamount = $tax;
            $sale->taxpercent = 0; //$request->taxpercent;

            $sale->totalamountpaidyet = 0;
            // only add amount if amount field i empty or >0
            if( ($request->amount == '' || $request->amount > 0 )  && $sale->totalamountpaidyet < $grandTotal ) {

                
            $salePayment = new SalePayment();
            $salePayment->salemasterid = $sale->id;
            $salePayment->paymentmodeid = $request->paymentmodeid;

            if( trim($request->amount) == '' )
            {
                $salePayment->amount = $grandTotal;
            }
            else
            {
                $salePayment->amount = min( $grandTotal, $request->amount );
            }

            if( $request->paymentmodeid != '2' ) {
                $sale->totalamountpaidyet += $salePayment->amount;
            }
            
            //fulle payment paid also not by cheque so 100% proofed to pay
            if( $sale->totalamountpaidyet == $grandTotal && $request->paymentmodeid != '2' )
            {
                $sale->ispaid = '1';
            }
            else
            {
                $sale->ispaid = '0';
            }
                
            //not cheques then this payment assume as done
            if ( $request->paymentmodeid != '2' )
            {
                $salePayment->ispaymentdone = 1;
            }
            else
            {
                $salePayment->ispaymentdone = 0;
            }
            
            //bank payment
            if( $request->paymentmodeid == '2' )
            {
                $salePayment->bankname = $request->bankname;
                $salePayment->bankaccountno = $request->bankaccountno;
                $salePayment->chequeno = $request->chequeno;
                if( $salePayment->checkdate != '' ) {
                    $salePayment->chequedate = $request->chequedate;
                }
                
            }
            else if( $request->paymentmodeid == '5' ) // other
            {
                $salePayment->other = $request->other;
            }
            $salePayment->save();
            }

            $sale->save();
            
            // $sale->saleItem()->saveMany($arrItems);
            
            return $sale->id;
        }
    }

    protected function calculateSaleForEdit( $request, $id )
    {


        $sale = Sale::findOrFail($id);
        $oldProductIds = array();
        $oldServiceIds = array();
        $oldPackageIds = array();
        $allMainIds = array();

        
        foreach( $sale->saleItem as $item )
       {
           
           $allMainIds[] = $item->id;
           if( $item->itemtype == "service" && $item->saleid > 0 )
           {
               $oldServiceIds[] = $item->itemid;
               
           }
           else if ( $item->itemtype == "product" )
           {
               $oldProductIds[] = $item->itemid;
               $product = Product::find( $item->itemid );
               $product->stockavailable = $product->stockavailable + $item->quantity;
               $product->soldcount =      $product->soldcount - $item->quantity;
               $product->save();
           }
           else if ( $item->itemtype == "package" )
           {
               $oldPackageIds[] = $item->itemid;           
           }
           
       }

        


       //  print_r($allMainIds);
       // delete all sale items

        // comment by hardeep new
        if( count($allMainIds) > 0 ) {

           //not delete those items which are not available

           $saleItems =  SaleItem::whereIn('parentid', $allMainIds)->get();
           $arrIds = array();
           foreach( $saleItems as $item ) { 
               $arrIds[] = $item->id;
           }
           if( count($arrIds) > 0 )
           {
               ServiceDoneStaff::whereIn('saleitemid', $arrIds )->delete();
           }

           
           ServiceDoneStaff::whereIn('saleitemid', $allMainIds )->delete();
           SaleItem::whereIn('id', $allMainIds)->delete();
           SaleItem::whereIn('parentid', $allMainIds)->delete();
            
            
            
            
            //  DB::table('saleitems')->whereIn('id', $allMainIds)->delete(); 
           //           $sale->saleItem()->where->delete();
           //sleep(1);
       } 
       //$sale->delete();


        if( count($request->ids) > 0 )
        {
            //$sale = new Sale();
            $sale->clientid = $request->clientid;
            $sale->save();
            
            $arrItems = array();
            
            $globalDiscount = 0;
            $globalDiscountType = $request->global_discounttype;
            $globalDiscountValue = $request->global_discountvalue;

            
            
            $x = 0;
            $packagesIds = array();
            $productIds = array();
            $serviceIds = array();
            $servicesTotalAmount = 0;
            foreach( $request->ids as $R )
            {
                
                if( $request->itemtypes[$x] == 'package' )
                {
                    $packagesIds[] = $R;
                   
                }
                else if( $request->itemtypes[$x] == 'product' )
                {
                    $productIds[] = $R;
                }
                else if( $request->itemtypes[$x] == 'service' )
                {
                    $serviceIds[] = $R;
                }

                
                
                //qnty
                //itemtypes
                //discounttype
                //discountvalue

                $x++;
            }
            $servicesTotalAmount = 0;            
            $totalAmount = 0;
            if( count($packagesIds) > 0 )
            {

                
                $packagesRows = Packages::whereIn('id' , $packagesIds )->get();

                
                // print_r($packagesRows);
                foreach( $packagesRows as $R )
                {
                    //echo $R->id.'==';
                    // print_r($request->ids);

                    $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'package'  )
                        {

                            

                        //print_r($R);


                         $packageServices = $R->packageservices;

                          
                         //$price = $R->price;
                         //Using price entered by user
                         $price = $request->unitprice[$keys];

                         //temporary update package price to calculate discount
                         $R->price = $price;
                        
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;
                        $servicesTotalAmount += $calculatedPrice;
                            $itemP = new SaleItem();
                            $itemP->itemid = $R->id;
                            $itemP->saleid = $sale->id;
                            $itemP->itemtype = 'package';
                            $itemP->actualpriceperitem = $price; //$R->price;
                            $itemP->quantity = $qnty;
                            $itemP->discountvalue = $discountValue;
                            $itemP->discounttype = $discountType;
                            $itemP->discountamount = $discountAmount;
                            $itemP->title = $R->title;
                            $itemP->description = $R->description;
                            $itemP->parentid = 0;
                            $itemP->taxpercent = SERVICE_TAX;
                            //$arrItems[] = $item;
                            $itemP->save();
                            
                            
                        foreach( $packageServices as $packageService)
                        {
                             
                            $item = new SaleItem();
                            $item->itemid = $packageService->service->id;
                            $item->saleid = 0;
                            $item->itemtype = 'service';
                            $item->actualpriceperitem = $packageService->service->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $packageService->service->name;
                            $item->description = $packageService->service->description;
                            $item->parentid = $itemP->id;
                            $item->taxpercent = SERVICE_TAX;
                            //$arrItems[] = $item;

                            $staffDropDown = 'staffs_'.$R->id.'_'.$packageService->service->id;
                            //  $item->staffid =  $request->{$staffDropDown};

                            

                            $item->save();

                             $dropDownValues = $request->{$staffDropDown};
                            for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();
                                }
                                
                            }


                            
                         }

                        }
                    }
                }
                
            }

            if( count($serviceIds) > 0 )
            {
                //print_r($request->ids);
                //print_r($serviceIds);
                $servicesRows = Services::whereIn('id' , $serviceIds )->get();

                //count($servicesRows);
                foreach( $servicesRows as $R )
                {

                                                                $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'service'  )
                        {

                  
                    

                            //$price = $R->price;
                            // using price entered by user
                            $price = $request->unitprice[$keys];

                            // temporary update service price to calculate discount
                            $R->price = $price;
                            
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;
                        $servicesTotalAmount += $calculatedPrice;
                        $item = new SaleItem;
                            $item->itemid = $R->id;
                            $item->saleid = $sale->id;
                            $item->itemtype = 'service';
                            $item->actualpriceperitem = $price; // $R->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $R->name;
                            $item->description = $R->description;
                            $item->parentid = 0;
                            $item->taxpercent = SERVICE_TAX;
                            // $arrItems[] = $item;

                            $staffDropDown = 'staffs_services_'.$R->id;
                            //$item->staffid = $request->{$staffDropDown};

                            $item->save();

                            $dropDownValues = $request->{$staffDropDown};
                              for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                      
                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();

                                    
                                }
                                
                               }
                        }
                    }
                }
            }

            $productsTotalPrice = 0;
            $tax = 0;

            
            if( count($productIds) > 0 )
            {
                $productRows = Product::whereIn('id' , $productIds )->get();

                foreach( $productRows as $R )
                {

                                                                                    $keys = 0;
                    for ( $keys = 0; $keys < count($request->ids);  $keys++ ) { 

                        if( $request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'product'  )
                        {

                            



                            //$price = $R->price;

                            $price  = $request->unitprice[$keys];
                            //temprary update product price to calculate correct discount
                            $R->price = $price;
                        $qnty = $request->qnty[$keys];
                        $discountType = $request->discounttype[$keys];
                        $discountValue = $request->discountvalue[$keys];
                        $discountAmount = $qnty * $R->getDiscountAmount( $discountType, $discountValue );

                        $calculatedPrice = $price * $qnty - $discountAmount;

                        $totalAmount += $calculatedPrice;
                        $productsTotalPrice += $calculatedPrice;

                        $tax += ( $calculatedPrice - ( $calculatedPrice / ((100 + $R->tax) * 0.01 ) ) );
                        
                            $item = new SaleItem();
                            $item->itemid = $R->id;
                            $item->saleid = $sale->id;
                            $item->barcode = $R->barcode;
                            $item->itemtype = 'product';
                            $item->actualpriceperitem = $price; //$R->price;
                            $item->quantity = $qnty;
                            $item->discounttype = $discountType;
                            $item->discountvalue = $discountValue;
                            $item->discountamount = $discountAmount;
                            $item->title = $R->name;
                            $item->description = $R->description;
                            $item->parentid = 0;
                            $item->taxpercent = $R->tax;
                            //$arrItems[] = $item;
                            $staffDropDown = 'staffs_product_'.$R->id;
                            //$item->staffid = $request->{$staffDropDown};

                            $item->save();

                            
                            $dropDownValues = $request->{$staffDropDown};
                              for( $x = 0; $x < count($dropDownValues); $x++  ) { 

                      
                                if( trim($dropDownValues[$x]) != ''  )
                                {

                                    $serviceDone = new ServiceDoneStaff();
                                    $serviceDone->staffid = $dropDownValues[$x];
                                    $serviceDone->saleitemid = $item->id;
                                    //$arrDropDownValues[] = $dropDownValues[$x];
                                    $serviceDone->amount = 0;
                                    $serviceDone->save();
                                }
                                
                               }

                              
                            $product = Product::find($R->id);
                            $product->stockavailable = $product->stockavailable - $qnty;
                            $product->soldcount = $product->soldcount + $qnty;
                            $product->save();
                        }
                    }
                }

            }


            if( $globalDiscountType == 'percent'  )
            {
                $globalDiscount =  ( $totalAmount - $productsTotalPrice )  * $globalDiscountValue / 100;
            }
            else
            {
                $globalDiscount = $globalDiscountValue;
            }

            //$taxableAmount = $totalAmount - $globalDiscount;

            $grandAmount = $totalAmount - $globalDiscount;

            $servicesTotalAmount = $servicesTotalAmount - $globalDiscount;
            $tax += ($servicesTotalAmount -  ($servicesTotalAmount / ( (100 + SERVICE_TAX  ) * 0.01 ) )); 
            
            /*if( $request->taxpercent > 0 )
            {
                $tax = $taxableAmount *  $request->taxpercent / 100;
            } */

            $sale->clientid = $request->clientid ;
            if( $request->clientid  > 0 )
            {
                $sale->walkin_name = '';
            }
            else 
            {
                $sale->walkin_name = $request->walkin_name == '' ? 'Walk-In' : $request->walkin_name ;
            }
            
            $grandTotal = $grandAmount;  //$taxableAmount + $tax;
            $sale->totalprice = $grandTotal;
            $sale->paidprice = $grandTotal;
            $sale->discounttype = $globalDiscountType;
            $sale->discountvalue = $globalDiscountValue;
            $sale->discountamount = $globalDiscount;
            //       $sale->ispaid = "1";
            $sale->taxamount = $tax;
            $sale->taxpercent = 0; //$request->taxpercent;



                        
            $sale->save();
           // $sale->saleItem()->saveMany($arrItems);
            
            return $sale->id;
        }
    }


        public function addpayment($id)
        {
            
            $sale = Sale::findOrFail($id);

           $paymentModes = PaymentMode::all();

                $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }
        

 
            return view('saleaddpayment', ['sale' => $sale, 'paymentmodes' => $paymentModeArr ] );

            
        }

         public function storepayment( Request $request, $id )
            {
                
            $sale = Sale::findOrFail($id);
            
           
            //if amount is empty
            if( $request->amount == '' || $request->amount <= 0 )
            {
                return redirect( route('sale.addpayment', $sale->id) )
                ->withInput()
                   ->withErrors(array('Amount should be greator than zero.'));

            }
            
            //if already added full amount entries and some could be under reviews or full payment paid
            
            
            if( $sale->pendingAmount() <= $sale->getAmountUnderReview() )
            {
                
                return redirect( route('sale.addpayment', $sale->id) )
                ->withInput()
                   ->withErrors(array('We can not add as other payments are under review for this sale.'));
            
                
            }
            else if( ($sale->pendingAmount() - $sale->getAmountUnderReview()) < $request->amount )
            {
                 return redirect( route('sale.addpayment', $sale->id) )
                ->withInput()
                   ->withErrors(array('Pending amount is less than the amount you entered. There could be payment under review. You should add amount of: '.($sale->pendingAmount() - $sale->getAmountUnderReview())));
            }
            
           
            
            
            if( $request->amount > 0 ) {
                
                                
            $salePayment = new SalePayment();
            $salePayment->salemasterid = $sale->id;
            $salePayment->paymentmodeid = $request->paymentmodeid;

            $salePayment->amount = $request->amount;
            if( $request->amount > $sale->pendingAmount()  )
            {
                $salePayment->amount = $sale->pendingAmount();
            }




            if( $sale->paymentmodeid != '2' ) {
                $sale->totalamountpaidyet += $salePayment->amount;
            }
            //fulle payment paid also not by cheque so 100% proofed to pay
            if( $sale->totalamountpaidyet >= $sale->paidprice && $request->paymentmodeid != '2' )
            {
                $sale->ispaid = '1';
            }
            else
            {
                $sale->ispaid = '0';
            }
                
            //not cheques then this payment assume as done
            if ( $request->paymentmodeid != '2' )
            {
                $salePayment->ispaymentdone = 1;
            }
            else
            {
                $salePayment->ispaymentdone = 0;
            }
            
            //bank payment
            if( $request->paymentmodeid == '2' )
            {
                $salePayment->bankname = $request->bankname;
                $salePayment->bankaccountno = $request->bankaccountno;
                $salePayment->chequeno = $request->chequeno;
                if( $request->chequedate != '' ) {
                    $dt = explode( '/', $request->chequedate );
                    
                    $salePayment->chequedate = $dt[2].'-'.$dt[1].'-'.$dt[0];
                }
                
            }
            else if( $request->paymentmodeid == '5' ) // other
            {
                $salePayment->other = $request->other;
            }
            $salePayment->save();
            $sale->save();

                    $request->session()->flash("successmsg", "Successfully Updated.");
                    return redirect()->back();
            }
            else
            {
                return redirect( route('sale.addpayment', $sale->id) )
                ->withInput()
                   ->withErrors(array('Looks like there is no pending amount for this sale.'));
            }
            
     }


    
    public function editpayment($saleId, $paymentId )
    {
        //echo $saleId.' == '.$paymentId;
        $sale = Sale::findOrFail($saleId);

                $salePaymentFound = false;
        foreach( $sale->salepayments as  $item ) {

            if( $item->id == $paymentId )
            {
                $salePaymentFound = true;
                break;
            }
            
        }

        if( $salePaymentFound == false )
        {
            die("Invalid Access");
        }
        
        $payment = SalePayment::findOrFail($paymentId);

        
           $paymentModes = PaymentMode::all();

                $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }
        

 


            
            return view('saleeditpayment', [ 'sale' => $sale, 'payment' => $payment, 'paymentmodes' => $paymentModeArr  ]);
    }

    public function updatepayment(Request $request, $saleId, $paymentId )
    {

        if ($request->amount <= 0) {
            return redirect(route('sale.editpayment', [$saleId, $paymentId]))
                            ->withInput()
                            ->withErrors(array('Amount should be greator than zero.'));
        }

        $sale = Sale::findOrFail($saleId);
        $salePaymentFound = false;
        $totalPaymentPaid = 0;
        $totalPaymentUnderReview = 0;

        foreach ($sale->salepayments as $item) {

            if ($item->id == $paymentId) {
                $salePaymentFound = true;
            } else {
                if ($item->ispaymentdone == '1') {
                    $totalPaymentPaid += $item->amount;
                } else if ($item->ispaymentdone == '0') {
                    $totalPaymentUnderReview += $item->amount;
                }
            }
        }


        if ($salePaymentFound == false) {
            die("Invalid Access");
        }


        $paymentUnderSale = $totalPaymentPaid + $totalPaymentUnderReview;


        if ($paymentUnderSale >= $sale->paidprice) {

            return redirect(route('sale.editpayment', [$saleId, $paymentId]))
                            ->withInput()
                            ->withErrors(array('We can not update as we have entries available respect to total sale amount. Please check all entries first.'));
        }


        $payment = SalePayment::findOrFail($paymentId);

        $oldAmount = $payment->amount;
        $newAmount = $request->amount;
        $oldStatus = $payment->ispaymentdone;
        $newStatus = $request->ispaymentdone;


        if ($paymentUnderSale + $newAmount >= $sale->paidprice) {
            $newAmount = $sale->paidprice - ($paymentUnderSale );
        }

        

        //if current payment mark ads paid
        if ($newStatus == '1') {

            if ($newAmount + $paymentUnderSale >= $sale->paidprice) {
                $sale->ispaid = '1';
            } else {
                $sale->ispaid = '0';
            }

            $sale->totalamountpaidyet = $totalPaymentPaid + $newAmount;
        } else {
            $sale->totalamountpaidyet = $totalPaymentPaid;
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

        $sale->save();
        $payment->save();

        $request->session()->flash("successmsg", "Successfully Updated.");
        return redirect()->back();
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
                $sale = Sale::findOrFail($id);

                $packages = Packages::all();
                $categories = Category::all();
                $clients = Client::all();
                $paymentModes = PaymentMode::all();


                

                $clientArr = array();
                $clientArr[0] = 'Select';
                foreach ( $clients as $client)
                {
                    $clientArr[$client->id] = $client->clientname.' ('.$client->phone.')';
                }

                $paymentModeArr = array();
                foreach ($paymentModes as $pMode ) {
                    $paymentModeArr[$pMode->id] = $pMode->name;
                }
        
                $staff = Staff::orderBy('firstname', 'asc')->get();

                $products = Product::all()->where('stockavailable', '>', '0');

                return view('saleedit', ['sale' => $sale , 'categories' =>  $categories, 'packages' => $packages   , 'products' => $products, 'clients' => $clientArr, 'staffs' => $staff, 'paymentmodes' => $paymentModeArr ] );
        
        
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
                //get all product id that was already added
                //check post product ids against already added
                //if ids matched then check last and new qnty, if changed then increase or decrease stock value for that product
                // if product id not found in post data but has in old , then delete that product and increase qnty
                // if product id is in post data but not found in old, then add that product in order and reduce stock

                $messages = [
                    'ids.required' => 'Please select package, service or product to make order.',
                    'qnty.greator_then_zero' => 'Quantity should be greator than zero.'

                ];

                $validator =  Validator::make( $request->all(), [ 'clientid' => 'required', 'ids' => 'required|array', 'qnty' => 'required|array|greator_then_zero' ], $messages );

                if ($validator->fails()) {
                    return redirect( route('sale.create') )
                        ->withInput()
                        ->withErrors($validator);
                }
                else {


                    if( $this->calculateSaleForEdit( $request, $id ) )
                    {
                        $request->session()->flash("successmsg", "Successfully Updated.");
                    }
        
        
                    return redirect()->back();

                }
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

                
                //delete all items
                 SaleItem::whereIn('id', $allMainIds)->delete();
                 SaleItem::whereIn('parentid', $allMainIds)->delete();

                 
                 SalePayment::where('salemasterid', '=', $sale->id)->delete();

                 $sale->delete(); 

                    session()->flash("successmsg", "Successfully Deleted.");
                    return redirect()->back();
                 

                 
            }

            public function paymenthistory($id)
            {
                $sale = Sale::findOrFail($id);
                return view('salepaymenthistory', ['sale' => $sale]);
            }
}
