<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientPackage;
use App\ClientPackageItems;
use App\ClientPayment;
use App\ClientPaymentUsed;
use App\PaymentMode;
use App\Sale;
use App\SaleItem;
use App\ServiceDoneStaff;
use App\Services;
use App\CustomClasses\HS_Payment;
use App\CustomClasses\HS_Sales;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClientsPackageController extends Controller
{
    /*
     * Verb    Path                        Action  Route Name
    GET     /users                      index   users.index
    GET     /users/create               create  users.create
    POST    /users                      store   users.store
    GET     /users/{user}               show    users.show
    GET     /users/{user}/edit          edit    users.edit
    PUT     /users/{user}               update  users.update
    DELETE  /users/{user}               destroy users.destroy
     */
    //
    
     
    
    
    
    //get
    public function index(Request $request)
    {

        $query = ClientPackage::query();

        $clientQuery = Client::query();

        if ($request->searchtextclientname != "") {

            $searchtextclientname = $request->searchtextclientname;

            $clientQuery->where('clientname', 'like', "%" . $searchtextclientname . "%");

            //print_r($clients);
            // die();

        }


        if ($request->searchtextclientphone) {
            $searchPhone = $request->searchtextclientphone;
            $clients = $clientQuery->where(function ($query) use ($searchPhone) {

                $query->Where('phone', 'like', "%" . $searchPhone . "%")
                    ->orWhere('phone2', 'like', "%" . $searchPhone . "%");
            });
        }

        if ($request->searchtextclientname != "" || $request->searchtextclientphone != '') {

            $clients = $clientQuery->get();
            $arr = array();
            foreach ($clients as $client) {
                $arr[] = $client->id;
            }

            $query->where(function ($query) use ( $request, $arr) {
                $query->WhereIn('clientid', $arr);
                    
                //     ->orWhere( 'phone', 'like', $request->input("searchtext").'%' )
                //     ->orWhere( 'phone2', 'like', $request->input("searchtext").'%' );

            });
        }

        if ($request->searchtext != "") {

            $searchtext = $request->searchtext;

            $query->where(function ($query) use ($searchtext, $request) {
                $query->Where('id', '=', $searchtext);
                 

            });

            //print_r($clients);
            // die();

        }

        //     $query = ClientPackage::query();

        $clientpackages = $query->orderBy('id', 'DESC')->paginate(50);
        
        return view('clientpackagelist', ['clientpackages' => $clientpackages]);
        // return view('clientlist', [ 'clients' => $clients]);
    }

    //get edit for us
    public function show($id)
    {

    }

    public function edit($id)
    {
        

        $package = ClientPackage::findOrFail($id);

        $duration = $this->durationmonths();

        $clients = Client::all();
        $paymentModes = PaymentMode::all();
        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select';
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        $clientArr = array();
        $clientArr[0] = 'Select';
        foreach ($clients as $client) {
            $clientArr[$client->id] = $client->clientname . ' (' . $client->phone . ')';
        }

        $services = Services::all();

        $arrItem = array();
        $arrItem[null] = "Select";
        foreach ($services as $service) {
            $arrItem[$service->id] = $service->category->name . " - " . $service->name . " - ( " . $service->price . " )";
        }

        $pendingAmountFromClient = $package->client->clientPendingPaymentRegardingPackages();

        return view('clientpackageedit', ['clients' => $clientArr, 'duration' => $duration, 'paymentmodes' => $paymentModeArr, 'services' => $arrItem, 'services2' => $services, 'package' => $package, 'pendingAmountFromClient' => $pendingAmountFromClient ]);

    }

    public function update(Request $request, $id)
    {

        //delete sales
        if( $request->delete_sale == "delete_sale" )
        {
            $saleId = $request->saleid;
            $hsSales = new HS_Sales();
            $hsSales->deleteSaleWithId($saleId);
            $request->session()->flash("successmsg", "Successfully Deleted.");
            return redirect()->back();

        }


        $clientPackage = ClientPackage::findOrFail($id);
        // echo "<pre>"; print_r($request->packagetype);  die();

        $messages = [

            //  'packagename.required' => 'Please type a New Package Name.',
            //  'clientid.greator_then_zero' => 'Please select Client.',
            //  'packagetype.greator_then_zero' => 'Please select Package Type.',
            'startedate.required' => 'Please select Start Date.',
            'duration.required' => 'Please select Duration.',
            'actualprice.required' => 'Please fill the Actual Price.',

        ];

        $validator = Validator::make($request->all(),
            [
                //  'packagename' => 'required|max:255',
                //  'clientid' => 'required|greator_then_zero',
                //  'packagetype' => 'required|greator_then_zero',
                'startedate' => 'required|date_format:d/m/Y',
                'duration' => 'required',
                'actualprice' => 'required',
            ], $messages
        );

        if ($validator->fails()) {
            //  die("testing");
            //  $request->session()->flash("errors", $validator );
            return redirect(route('clientspackage.edit', $id))

                ->withErrors($validator);

        }

        $msg = array();

        if ($clientPackage->packagetype == '1') {
            if (trim($request->giftedprice) == "") {
                $msg[] = "Gifted Price is required";
            }

            if (($request->giftedprice) <= ($request->actualprice)) {
                $msg[] = "Gifted Price should Greater than Actual Price";
            }
        }

        if (count($msg) > 0) {

            // print_r($msg);
            // die("error");
            return redirect(route('clientspackage.edit', $id))

                ->withErrors($msg);
        }

        // $validator =  Validator::make( $request->all(), ['clientname' => 'required|max:255', 'dob' => 'required|date_format:d/m/Y', 'phone' => 'required|unique:client,phone'] );

        //if ($validator->fails()  ) {

        else {

            //$client = Client::findOrFail($id );

            //get advance payments ids
            //$arrAdvanceList = $client->getAdvancePaymentLists();

            // $clientPackage->clientid = $request->clientid;
            //$clientPackage->packagetype = $request->packagetype;
            //$clientPackage->packagename = $request->packagename;

            $clientPackage->actualprice = $request->actualprice;

            if ($clientPackage->packagetype == "1") {

                $clientPackage->giftedprice = $request->giftedprice;
                //validate gifted price
                if( $clientPackage->getSalesTotalPaidPrice() > $request->giftedprice )
                {
                    return redirect(route('clientspackage.edit', $id))

                        ->withErrors(array("Sale amount is already above Gifted Price. Please delete Sale first to update gifted price."));
                }

            } else {
                $clientPackage->giftedprice = 0;
                //validate changed quantity
                $usedItemsArr = $clientPackage->usedItemsList();

                $x = 0;
                $itemsAdded = array();
                foreach ($request->item as $item) {
                    if ($request->itemquantity[$x] > 0) {
                        if( isset($itemsAdded[$item]))
                        {
                            $itemsAdded[$item] += $request->itemquantity[$x];
                        }
                        else {
                            $itemsAdded[$item] = $request->itemquantity[$x];
                        }
                    }
                    $x++;
                }
                $msgValidate = array();
                if( count($itemsAdded) <= 0 )
                {
                    $msgValidate[] = "Please add atleast one service to update package.";

                }
                else // compare items used and item added quantity
                {
                    if( count($usedItemsArr) > 0  )
                    {
                        foreach ( $usedItemsArr as  $itemUsedId => $usedQnty )
                        {
                            if( !isset( $itemsAdded[$itemUsedId] ) || $itemsAdded[$itemUsedId] < $usedQnty )
                            {
                                $msgValidate[] = "Package can not updated as there is difference between Services already done and Services available in Package. Please check carefully.";
                            }
                        }
                    }

                }


                if( count($msgValidate) > 0 )
                {
                    return redirect(route('clientspackage.edit', $id))

                        ->withErrors($msgValidate);
                }



            }

            if ($request->startedate != '') {
                $dt = explode('/', $request->startedate);

                $clientPackage->startedate = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
            }

            $clientPackage->duration = $request->duration;

            // is full payment done

            $clientPackage->save();

            // $deletedRows = App\Flight::where('active', 0)->delete();

            if ($clientPackage->packagetype == "2") {
                ClientPackageItems::where('packageid', '=', $clientPackage->id)->delete();
            }

            if ($clientPackage->id > 0) {
                //save package item details if package is composite
                if ($clientPackage->packagetype == "2") {

                    foreach ($itemsAdded as  $item => $qnty ) {

                            $itemObj = new ClientPackageItems();
                            $itemObj->packageid = $clientPackage->id;
                            $itemObj->itemid = $item;
                            $itemObj->quantity = $qnty;
                            $itemObj->save();
                    }

                }

            }
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
        }
        // $category->name = $request->name;
        //$category->description = $request->description;

    }

    //get create form
    public function create()
    {
        $duration = $this->durationmonths();

        $clients = Client::all();
        $paymentModes = PaymentMode::all();
        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select';
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        $clientArr = array();
        $clientArr[0] = 'Select';
        foreach ($clients as $client) {
            $clientArr[$client->id] = $client->clientname . ' (' . $client->phone . ')';
        }

        $services = Services::all();

        $arrItem = array();
        $arrItem[null] = "Select";
        foreach ($services as $service) {
            $arrItem[$service->id] = $service->category->name . " - " . $service->name . " - ( " . $service->price . " )";
        }

        return view('clientpackageadd', ['clients' => $clientArr, 'duration' => $duration, 'paymentmodes' => $paymentModeArr, 'services' => $arrItem, 'services2' => $services]);

    }

    //save
    public function store(Request $request)
    {

        $messages = [
            'packagename.required' => 'Please type a New Package Name.',
            'clientid.greator_then_zero' => 'Please select Client.',
            'packagetype.greator_then_zero' => 'Please select Package Type.',
            'startedate.required' => 'Please select Starte Date.',

        ];

        $validator = Validator::make($request->all(),
            ['packagename' => 'required|max:255',
                'clientid' => 'required|greator_then_zero',
                'packagetype' => 'required|greator_then_zero',
                'startedate' => 'required|date_format:d/m/Y',
                'duration' => 'required',
                'actualprice' => 'required',
            ], $messages
        );

        $msg = array();

        if ($validator->fails()) {
            //  $request->session()->flash("errors", $validator );
            return redirect(route('clientspackage.create'))
                ->withInput()
                ->withErrors($validator);

        }

        $msg = array();
        if ($request->packagetype == '1') {
            if ($request->giftedprice == "") {
                $msg[] = "Gifted Price is required";
            }

            if ($request->giftedprice <= $request->actualprice) {
                $msg[] = "Gifted Price should Greater than Actual Price";
            }
        }

        if (count($msg) > 0) {
            // print_r($msg);
            // die("error");
            return redirect(route('clientspackage.create'))
                ->withInput()
                ->withErrors($msg);
        } else {

            $client = Client::findOrFail($request->clientid);
            //get advance payments ids
           // $arrAdvanceList = $client->getAdvancePaymentLists();

            $clientPackage = new ClientPackage();
            $clientPackage->clientid = $request->clientid;
            $clientPackage->packagetype = $request->packagetype;
            $clientPackage->packagename = $request->packagename;

            $clientPackage->actualprice = $request->actualprice;

            if ($clientPackage->packagetype == "1") {
                $clientPackage->giftedprice = $request->giftedprice;
            } else {
                $clientPackage->giftedprice = 0;
            }

            if ($request->startedate != '') {
                $dt = explode('/', $request->startedate);

                $clientPackage->startedate = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
            }
            $clientPackage->duration = $request->duration;

            // is full payment done
          
            $paymentPaid = 0;
            
            if ($request->paymentmodeid != "2") {
                $paymentPaid += $request->paidamount;
            }

            if ($request->paymentmodeid2 != "2") {
                $paymentPaid += $request->paidamount2;
            }

            if ($paymentPaid >= $request->actualprice) {
                $clientPackage->ispaymentdone = "1";
            } 

            $clientPackage->save();

            if ($clientPackage->id > 0) {
                //save package item details if package is composite
                if ($clientPackage->packagetype == "2") {

                    $arrItems = array();
                    $x = 0;
                    foreach ($request->item as $item) {
                        if ($request->itemquantity[$x] > 0) {
                            if (isset($arrItems[$item])) {
                                $arrItems[$item] += $request->itemquantity[$x];

                                 }
                                 else
                                 {
                                     $arrItems[$item] = $request->itemquantity[$x];
                                 }
                        }
                        $x++;
                    }

                    if( count($arrItems) > 0 )
                    {
                        foreach ( $arrItems as $itemId => $qnty )
                        {
                            $itemObj = new ClientPackageItems();
                            $itemObj->packageid = $clientPackage->id;
                            $itemObj->itemid = $itemId;
                            $itemObj->quantity = $qnty;
                             $itemObj->save();
                        }
                    }
                   /* $x = 0;
                    foreach ($request->item as $item) {
                        if ($request->itemquantity[$x] > 0) {
                            $itemObj = new ClientPackageItems();
                            $itemObj->packageid = $clientPackage->id;
                            $itemObj->itemid = $item;
                            $itemObj->quantity = $request->itemquantity[$x];

                            $itemObj->save();
                        }
                        $x++;
                    } */

                }

                //save package payments

                //check if any advance payment there
                //first use any old advance price
                // foreach ($arrAdvanceList as $advancePayment )
                //  {

                //  }
                
                $hsPayment = new HS_Payment();


                $packageCost = $request->actualprice;
                if ($request->paymentmodeid != "" && $request->paidamount > 0) {

                    //save client payment1
                    $clientPayment = $hsPayment->addNewClientPayment($client->id, $request->paymentmodeid, $request->paidamount, $request->bankname, $request->bankaccountno, $request->chequeno, $request->chequedate, $request->other );


                }


                if ($request->paymentmodeid2 != "" && $request->paidamount2 > 0) {

                    $clientPayment2 = $hsPayment->addNewClientPayment( $client->id, $request->paymentmodeid2, $request->paidamount2, $request->bankname2, $request->bankaccountno2, $request->chequeno2, $request->chequedate2, $request->other2  );


                }

            }

            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }
        // $category->name = $request->name;
        //$category->description = $request->description;

        // return view('clientadd');

    }

    protected function getDate($dates)
    {
        if ($dates == "") {
            return "";
        }

        $dt = explode('/', $dates);

        return $dt[2] . '-' . $dt[1] . '-' . $dt[0];
    }

    
    
    public function destroy($id)
    {
        //  $clientpackage = ClientPackage::findOrFail($id);

        $sales = Sale::where('packageid', '=', $id)->get();

        $allMainIds = array();
        foreach ($sales as $sale) {
            foreach ($sale->saleItem as $item) {
                $allMainIds[] = $item->id;
                if ($item->itemtype == 'product') {
                    $product = Product::findOrFail($item->itemid);
                    $product->stockavailable += $item->quantity;
                    $product->soldcount -= $item->quantity;
                    $product->save();
                }

            }

        }

        $saleitems = SaleItem::whereIn('parentid', $allMainIds)->get();

        foreach ($saleitems as $item) {
            $allMainIds[] = $item->id;
        }

        //delete all items
	    //ClientPaymentUsed::where()
       // ClientPayment::where('ispackage', '=', 1)->where('saleid', '=', $id)->delete();

        ServiceDoneStaff::whereIn('saleitemid', $allMainIds)->delete();

        SaleItem::whereIn('id', $allMainIds)->delete();

        SaleItem::whereIn('parentid', $allMainIds)->delete();

        Sale::where('packageid', '=', $id)->delete();

        ClientPackage::where('id', '=', $id)->delete();

        session()->flash("successmsg", "Successfully Deleted.");
        return redirect()->back();

    }

    public function durationmonths()
    {

        $zx = array();

        $zx[null] = 'Select';
        $zx[99] = 'Life Time';

        for ($x = 1; $x <= 48; $x++) {

            $zx[$x] = $x . " Months";

            if ($x >= 3 && $x < 6) {
                $x = 5;
            }

            if ($x >= 6 && $x < 9) {
                $x = 8;
            }

            if ($x >= 9 && $x < 12) {
                $x = 11;
            }

            if ($x >= 12 && $x < 18) {
                $x = 17;
            }

            if ($x >= 18 && $x < 24) {
                $x = 23;
            }

            if ($x >= 24 && $x < 36) {
                $x = 35;
            }

            if ($x >= 36 && $x < 48) {
                $x = 47;
            }
        }

        return $zx;
    }

}
