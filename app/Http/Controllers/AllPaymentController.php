<?php

namespace App\Http\Controllers;


use App\ClientPayment;
use App\ClientPaymentUsed;
use App\CustomClasses\HS_Payment;
use App\SalePayment;
use Illuminate\Http\Request;
use App\PaymentMode;
use App\Sale;
use App\ClientPackage;
use App\Client;
class AllPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $sale = array();
        $clientPackage = array();
        $client = array();
        $paramGet = array();
        $isSale = false;
        if( $request->saleid != '' )
        {
            $sale = Sale::findOrFail($request->saleid);
            $paramGet['name'] =  'saleid';
            $paramGet['value'] =  $request->saleid;
            $isSale = true;
         
        }
        else if ( $request->packageid != '' )
        {
            $clientPackage = ClientPackage::findOrFail($request->packageid);
            $paramGet['name'] =  'packageid';
            $paramGet['value'] =  $request->packageid;
        }
        else if ( $request->clientid != '' )
        {
            $client = Client::findOrFail( $request->clientid);
            $paramGet['name'] =  'clientid';
            $paramGet['value'] =  $request->clientid;
        }
        else 
        {
            exit("Invalid access");
        }

        $paymentModes = PaymentMode::all();
 
        $paymentModeArr = array();
        foreach ($paymentModes as $pMode ) {
            if( $isSale == true  &&  $pMode->id == '2' )
            {
                continue;
            }
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

         
        
        return view('allpaidpayment', ['sale' => $sale, 'clientPackage' => $clientPackage, 'paymentmodes' => $paymentModeArr , 'client' => $client, 'arrParam' => $paramGet ] );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       /* $saleId = 0;
        $packageId = 0;
        $isPackage = 0;
        $clientId = 0;
        $redirectPar = array();
        $arrAdvancePayments = array();
       
       
        if(  $request->saleid != '' )
        {
            $saleId = $request->saleid;
            $isPackage = '0';
            $sale = Sale::findOrFail($saleId);
            $clientId = $sale->clientid;
            
            $redirectPar['saleid'] = $saleId;
             
            if( $sale->clientid > 0 ) {
                $arrAdvancePayments = $sale->client->getAdvancePaymentLists();
                

            }
        }
        else if(  $request->packageid != '' )
        {
               $packageId = $request->packageid;
                $isPackage = '1';
                $package = ClientPackage::findOrFail($packageId);
                $clientId = $package->clientid;
                $redirectPar['packageid'] = $packageId;
                $arrAdvancePayments = $package->client->getAdvancePaymentLists();
        }
        else  if(  $request->clientid != '' )
        {
            $saleId = '0';
            $packageId = '0';
            $isPackage = '0';
            $clientId = $request->clientid;
            $redirectPar['clientid'] = $clientId;
            $client = Client::findOrFail($clientId);
            $arrAdvancePayments = $client->getAdvancePaymentLists();
        }
        
        if( $request->iframe  )
        {
            $redirectPar['iframe'] = $request->iframe ;
        }

        if( $request->useadvanceamount == 'useadvanceamount' ) {

            $advanceError = false;


            if( count($arrAdvancePayments) > 0 )
            {
                $pendingAmount = 0;
                if( $saleId > 0 )
                {
                     $pendingAmount = $sale->actualPendingAmount();
                     
                }
                else if ( $packageId > 0 )
                {
                    $pendingAmount = $package->actualPendingAmount();
                }

                if ($pendingAmount > 0) {
                    
                     
                    foreach ($arrAdvancePayments as  $advanceObj) {
                        
                         
                        if( $advanceObj->canuse_amount > 0 && $pendingAmount > 0 ){
                        $req = new \stdClass();
                         
                        $req->paymentmodeid = $advanceObj->paymentmodeid;
                        $req->ispaymentdone = $advanceObj->ispaymentdone; 
                        $req->amount = $advanceObj->canuse_amount;
                        $pendingAmount = $pendingAmount - $advanceObj->canuse_amount;
                        
                        if( $saleId > 0 ){
                        
                             
                        $paymentId = $this->addNewSalePaymentForSale( $sale, $advanceObj->id , $req );
                        }
                        else if ( $packageId > 0 )
                        {
                            $paymentId = $this->addNewSalePaymentForClientPackage( $package, $advanceObj->id , $req );
                        }
                        
                        }
                        
                    }
                }

            }

        } */
        

      /*  if(  $request->addpayment == "addpayment" )
        {
            
            //if amount is empty
            if( ($request->amount == '' || $request->amount <= 0 )  )
            {

                return redirect( route('allpayment.create', $redirectPar ) )
                    ->withInput()
                    ->withErrors(array('Amount should be greator than zero.'));

            }

            if( $saleId > 0  )
            {

                if ($sale->pendingAmount() <= $sale->getAmountUnderReview()) {

                    return redirect(route('allpayment.create', $redirectPar))
                    ->withInput()
                    ->withErrors(array('We can not add as other payments are under review for this sale.'));


            } else if (($sale->pendingAmount() - $sale->getAmountUnderReview()) < $request->amount) {
                return redirect(route('allpayment.create', $redirectPar))
                    ->withInput()
                    ->withErrors(array('Pending amount is less than the amount you entered. There could be payment under review. You should add amount of: ' . ($sale->pendingAmount() - $sale->getAmountUnderReview())));
            }
            
            if( !$sale->client )
            {
                $client = new Client();
                $client->id = 0;
            }
            else 
            {
                $client =  $sale->client;
            }

                // client payment added now add sale payment
                $clientPaymentId = $this->addNewPaymentClient( $sale->client, $request );
                if( $clientPaymentId > 0 )
                {
                    // add payment in sale now
                    //TODO: add payment in sale
                    
                    $salePaymentId =  $this->addNewSalePaymentForSale($sale, $clientPaymentId, $request);
                    
                }
                
                
            }
            else if ( $packageId > 0  )
            {
                
                
                if ($package->actualPendingAmount() <= 0 ) {
                    
                    return redirect(route('allpayment.create', $redirectPar))
                    ->withInput()
                    ->withErrors(array('We can not add as other payments are under review for this sale.'));
                    
                    
                } else if ( $package->actualPendingAmount() < $request->amount) {
                    return redirect(route('allpayment.create', $redirectPar))
                    ->withInput()
                    ->withErrors(array('Pending amount is less than the amount you entered. There could be payment under review. You should add amount of: ' . ($package->actualPendingAmount())));
                }
                
                
                // client payment added now add sale payment
                $clientPaymentId = $this->addNewPaymentClient( $package->client, $request );
                if( $clientPaymentId > 0 )
                {
                    // add payment in sale now
                    //TODO: add payment in sale
                    
                    $salePaymentId =  $this->addNewSalePaymentForClientPackage($package, $clientPaymentId, $request);
                    
                }
                
            }
            else */ 
        
        $redirectPar = array();
        if( $request->clientid != '' )
        {
            $redirectPar['clientid'] =  $request->clientid;
        }
        else if ( $request->saleid != '')
        {
            $redirectPar['saleid'] =  $request->saleid;
        }

        
             if( ($request->amount == '' || $request->amount <= 0 ) && $request->useadvanceamount != 'useadvanceamount'  )
            {

                return redirect( route('allpayment.create', $redirectPar ) )
                    ->withInput()
                    ->withErrors(array('Amount should be greator than zero.'));

            }

            $hsPayment = new HS_Payment();
        //if adding payment for client
             if( $request->clientid > 0  )
            {
                $client = Client::findOrFail($request->clientid);

                //use client advance amounts to adjust in sales amount
                if( $request->useadvanceamount == 'useadvanceamount' )
                {
                    $sales = $client->getPendingAmountSalesList();
                    if( count($sales) > 0 )
                    {
                        foreach( $sales as $sl ) {
                            $hsPayment->useClientAmountInSale($client->id, $sl->id);
                        }
                    }


                }
                //if new payment added in client
                else if($request->addpayment == "addpayment") {


                    if( $request->paymentdate == '' )
                    {
                        $createDate = date("Y-m-d H:i:s");
                    }
                    else
                    {
                        $createDate = $this->getDate( $request->paymentdate ).' '.date("H:i:s" );
                    }
                    //$clientPaymentId = $this->addNewPaymentClient($client, $request);
                    $clientPayment = $hsPayment->addNewClientPayment($client->id, $request->paymentmodeid, $request->amount, $request->bankname, $request->bankaccountno, $request->chequeno, $request->chequedate, $request->other, $createDate);

                    //if use client advance payment used by the time of adding new payment
                    if( $request->use_amount_in_sales == '1' &&  $request->paymentmodeid != '2' && $clientPayment != null )
                    {
                        $sales = $client->getPendingAmountSalesList();
                        if( count($sales) > 0 )
                        {
                            foreach( $sales as $sl ) {
                                $hsPayment->useClientAmountInSale($client->id, $sl->id);
                            }
                        }
                    }

                }

            }
            // if payment managing for sales
            else if( $request->saleid > 0 )
            {
                $sale = Sale::findOrFail($request->saleid);
                //if use advance amount from client payment
                if( $request->useadvanceamount == 'useadvanceamount' )
                {
                    $hsPayment->useClientAmountInSale( $sale->clientid, $sale->id );
                }
                //new payment adding
                else if( $request->addpayment == "addpayment" )
                {
                    //new payment add
                    $salePendingAmount = $sale->actualPendingAmount();
                    if( $salePendingAmount <= 0 || $salePendingAmount < $request->amount )
                    {
                        return redirect( route('allpayment.create', $redirectPar ) )
                            ->withInput()
                            ->withErrors(array('Something wrong with the payment. Please check if new amount is greator than the Sale pending amount.'));
                    }
                    else // add new payment
                    {

                        if( $request->paymentdate == '' )
                        {
                            $createDate = date("Y-m-d H:i:s");
                        }
                        else
                        {
                            $createDate = $this->getDate( $request->paymentdate ).' '.date("H:i:s" );
                        }
                        $clientPayment = $hsPayment->addNewClientPayment( $sale->clientid, $request->paymentmodeid, $request->amount, '', '','','', $request->other, $createDate );
                        if( $clientPayment != null  )
                        {
                            $hsPayment->addClientPaymentUsed( $clientPayment->id, $sale->id, $request->amount, $createDate );
                        }

                    }
                }

            }
            

            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
              

    }


    protected function getDate($dates)
    {
        if( $dates == "" )
            return "";

        $dt = explode( '/', $dates );

        return $dt[2].'-'.$dt[1].'-'.$dt[0];
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
        $clientPayment = ClientPayment::findOrFail($id);
        
        $salePayments = SalePayment::where( 'clientpaymentid', '=', $clientPayment->id  )->get();
        
        foreach( $salePayments as $payment )
        {
             $payment->ispaymentdone = $request->payment_mode_status_add_select;
             $payment->save();
             
        }
        
        $clientPayment->ispaymentdone = $request->payment_mode_status_add_select;
        
        if( $clientPayment->ispaymentdone == "2" )
        {
            $clientPayment->paymentfailedreason = $request->payment_failed_reason_textarea;
        }
        else 
        {
            $clientPayment->paymentfailedreason = '';
        }
        
        $clientPayment->save();
        
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        
        /*if( $request->type == 'salepayment' )
        {
            $salePayment = SalePayment::where('salepaymentid', '=', $id )->first();
           
            $mainPayment = ClientPayment::findOrFail( $salePayment->clientpaymentid );
            
            if( $mainPayment->amount <= $salePayment->amount )
            {
                
                $mainPayment->delete();
            }
            else 
            {
                $mainPayment->amount = $mainPayment->amount - $salePayment->amount;
                $mainPayment->save();
            }
            
             SalePayment::where('salepaymentid', '=', $id )->delete();  
            
        }
        else if( $request->type == 'packagepayment' )
        {
            
             $salePayment = SalePayment::where('salepaymentid', '=', $id )->first();
           
             
              
             $mainPayment = ClientPayment::findOrFail( $salePayment->clientpaymentid );
            
            if( $mainPayment->amount <= $salePayment->amount )
            {
                
                $mainPayment->delete();
            }
             else 
            {
                $mainPayment->amount = $mainPayment->amount - $salePayment->amount;
                $mainPayment->save();
            }  
             SalePayment::where('salepaymentid', '=', $id )->delete();  
            
        }
         else */
        if( $request->type == 'salepayment' )
        {

            $mainPayment = ClientPaymentUsed::findOrFail( $id );

            if( $request->delete_client_payment_too == '1' )
            {
                $clientPayment = $mainPayment->clientPayment;

                // client full payment is assigned to this sale payment so delete record
                if( $clientPayment->amount <= $mainPayment->amount )
                {
                    $clientPayment->delete();
                }
                else // client payment is divided into multiple sales so only deduct amount
                {
                    $clientPayment->amount = $clientPayment->amount - $mainPayment->amount;
                    $clientPayment->save();
                }
            }
            // SalePayment::whereIn( "clientpaymentid", $id )->delete();

            $mainPayment->delete();

        }
        else if( $request->type == 'clientpayment' )
        {
            
            $mainPayment = ClientPayment::findOrFail( $id );

            $salePayments = $mainPayment->clientPaymentUsedLists;
            foreach ( $salePayments as $payment )
            {
                $payment->delete();
            }
            // SalePayment::whereIn( "clientpaymentid", $id )->delete();
           
            $mainPayment->delete();
            
        }
        
        
        session()->flash("successmsg", "Successfully Deleted.");
                    return redirect()->back();
    }
}
