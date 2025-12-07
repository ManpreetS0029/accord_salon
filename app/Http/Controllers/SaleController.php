<?php

namespace App\Http\Controllers;

use App\Category;
use App\ClientPackage;
use App\ClientPayment;
use App\CustomClasses\HS_Common;
use App\CustomClasses\HS_Payment;
use App\CustomClasses\HS_Sales;
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
use Illuminate\Support\Facades\Auth;

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

        //$sales = Sale::with(['salepayments' => function ($query) { $query->where('ispaymentdone', '=', '2'); }] )->where('salepayments.ispaymentdone', '=', 2)->orderBy('created_at', 'desc')->paginate(50); //DB::table('')->paginate(50);
        //print_r($categories);

        $query = Sale::query();
        $clientArr = array();
        $search = false;
        if ('' != trim($request->input("clientname"))) {

            $clients = Client::where('clientname', 'like', $request->input("clientname") . '%')->get();


            foreach ($clients as $client) {
                $clientArr[] = $client->id;
            }
            $search = true;

            if (count($clientArr) > 0) {

                $query->where(function ($query) use ($clientArr, $request) {

                    $query->orWhereIn('clientid', $clientArr)
                        ->orWhere(function ($query) use ($request) {

                            $query->where('walkin_name', 'like', $request->input("clientname") . '%');
                            $query->where('clientid', '=', '0');
                        })->orWhere('id', '=', $request->input("clientname") . '%');;
                });
            } else {
                $query->where(function ($query) use ($request) {

                    $query->orWhere(function ($query) use ($request) {

                        $query->where('walkin_name', 'like', $request->input("clientname") . '%');
                        $query->where('clientid', '=', '0');
                    })->orWhere('id', '=', $request->input("clientname") . '%');
                });
            }
        }


        if ('' != trim($request->input("paymentstatus"))) {
            $query->where('ispaid', trim($request->input("paymentstatus")));
        }


        if ('' != trim($request->input("paymentmodeid"))) {

            $search = true;
            $query->join('salepayment', function ($join) use ($request) {

                $join->on('sale.id', '=', 'salepayment.salemasterid')

                    ->where('salepayment.paymentmodeid', '=', trim($request->input("paymentmodeid")))
                    ->select(' salepayment.ispaymentdone ')
                ;
            });
        }

        $dateFrom = '';
        $dateTo = '';
        $dateFromExcel = '';
        $dateToExcel = '';
        if ('' != trim($request->input('datefrom'))) {
            $search = true;
            $dateArr = explode('/', $request->input('datefrom'));
            $dateFrom = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 00:00:00';
            $dateFromExcel = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
            /*if( '' == trim($request->input('dateto')) )
            {
                $dateTo = date("Y-m-d");
            } */
        }

        if ('' != trim($request->input('dateto'))) {
            $search = true;
            $dateArr = explode('/', $request->input('dateto'));
            $dateTo = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 23:59:59';
            $dateToExcel = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
            /*if( '' == trim($request->input('datefrom')) )
            {
                $dateTo = $dateFrom;
            }*/
        }

        if ($dateFrom == '' && $dateTo == '' && $request->clientname == "") {
            $search = true;
            $dateFrom = date("Y-m-d") . " 00:00:00";
            $dateTo = date("Y-m-d") . " 23:59:59";
        }

        if ($dateFrom != '' && $dateTo != '') {
            $query->whereBetween('sale.created_at', [$dateFrom, $dateTo]);
        } else if ($dateFrom != '') {
            $query->whereDate('sale.created_at', '>=',  $dateFrom);
        } else if ($dateTo != '') {
            $query->whereDate('sale.created_at', '<=',  $dateFrom);
        }

        /* $sales = Sale::join('salepayment', function($join) {

             $join->on ('sale.id', '=', 'salepayment.salemasterid')
                     ->where('salepayment.ispaymentdone', '=', 2 )
                     ->select(' salepayment.ispaymentdone ')
                     ;
         }  )->groupBy('sale.id')->orderBy('sale.created_at', 'desc')->paginate(50); //DB::table('')->paginate(50);*/

        $saleForCalculations = '';

        if ($search == false) {
            $salesAll = Sale::groupBy('id')->orderBy('sale.created_at', 'desc')->get();
            $saleForCalculations = $salesAll;

            $perPage = $request->get('per_page', 50);
            $sales = Sale::groupBy('id')->orderBy('sale.created_at', 'desc')->paginate($perPage);
            $sales->appends(['datefrom' => $request->datefrom, 'dateto' => $request->dateto, 'paymentmode' => $request->paymentmode, 'per_page' => $perPage]);
        } else {

            $sales = $query->groupBy('sale.id')->orderBy('sale.created_at', 'desc')->get();
            $saleForCalculations = $sales;
        }

        $totalSale = 0;
        $totalPaidAmount = 0;
        $totalCashPaid = 0;
        $totalPendingAmount = 0;
        $totalAmountUnderReview = 0;
        $totalAmountReviewFailed = 0;
        $saleIds = [];
        $clientIds = [];

        /** --------------------- */
        foreach ($saleForCalculations as $sale) {
            $saleIds[] = $sale->id;
            $clientIds[] = $sale->clientid;
        }

        $salePayments = [];

        if (!empty($saleIds)) {
            $querySelectPayments = " SELECT CP.paymentmodeid, CP.ispaymentdone, CPU.amount, CPU.saleid  
                        FROM clientpaymentused AS CPU INNER JOIN clientpayment AS CP 
                ON CP.id = CPU.clientpaymentid WHERE  CPU.saleid IN (" . implode(",", $saleIds) . ") ";

            $allPayments = DB::select($querySelectPayments);

            foreach ($allPayments as $salePayment) {

                if ($salePayment->ispaymentdone == '1') {
                    if ($salePayment->paymentmodeid == '1') {
                        $totalCashPaid += $salePayment->amount;
                    }

                    $totalPaidAmount += $salePayment->amount;
                    if (empty($salePayments[$salePayment->saleid])) {
                        $salePayments[$salePayment->saleid] = $salePayment->amount;
                    } else {
                        $salePayments[$salePayment->saleid] += $salePayment->amount;
                    }
                }
            }
        }
        $allClients = [];
        if (!empty($clientIds)) {
            $queryGetClients = "SELECT id, clientname FROM client WHERE id IN ( "
                . implode(",", $clientIds) . " ) ";
            $allClients = collect(DB::select($queryGetClients))->keyBy('id');
        }

        foreach ($saleForCalculations as $sale) {
            $totalSale += $sale->paidprice;

            /*
            $totalPaidAmount += $sale->getTotalPaidAmount();
            $totalCashPaid += $sale->getTotalAmountPaidAsCash();
            $totalAmountUnderReview += $sale->getAmountUnderReview();
            $totalPendingAmount += ($sale->pendingAmount() > 0 ?  $sale->pendingAmount() : 0 );

            $totalAmountReviewFailed += $sale->getTotalFailedAmount(); */
        }


        //echo $query->toSql();

        $paymentModes = PaymentMode::all();


        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select';
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }


        if ($request->exportdata == '1') {
            $salesForExcelSheet = $query->groupBy('sale.id')->orderBy('sale.created_at', 'desc')->get();
            $arrayData = array();
            $arrayData[] = array("Sale ID",  "Client Name", "Amount", "Amount Paid", "Amount Under Review", "Amt. Pending", "Sale Date");
            foreach ($salesForExcelSheet as $item) {
                $saleId = 'ACCD' . $item->id;
                $paidPrice = $item->paidprice;
                $amountPaid = number_format($item->getTotalPaidAmount());
                $amountReview =    number_format($item->getAmountUnderReview(), 2);
                $amountPending = number_format($item->pendingAmount());
                $saleDate = date("d/m/Y h:i:s A", strtotime($item->created_at));
                $clientName = '';
                if ($item->client) {
                    $clientName = $item->client->clientname;
                } else {
                    $clientName = $item->walkin_name;
                }


                $arrayData[] = array($saleId, $clientName, $paidPrice, $amountPaid, $amountReview, $amountPending, $saleDate);
            }

            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("Total Sale", "Total Paid", "Total Cash", "Total Pending", "Under Review", "Failed", "");
            $arrayData[] = array($totalSale, $totalPaidAmount, $totalCashPaid, $totalPendingAmount, $totalAmountUnderReview, $totalAmountReviewFailed, "");
            HS_Common::array_to_csv_download($arrayData, "sale_" . $dateFromExcel . "_TO_" . $dateToExcel . ".csv", ",");

            exit(0);
        }


        $user = Auth::user();

        return view('salelist', [
            'sales' => $sales,
            'salePayments' =>  $salePayments,
            'allClients' => $allClients,
            'paymentmodes' => $paymentModeArr,
            'totalsale' => $totalSale,
            'totalpaidamount' => $totalPaidAmount,
            'totalcashpaid' => $totalCashPaid,
            'totalpendingamount' => $totalPendingAmount,
            'totalunderreviewamount' => $totalAmountUnderReview,
            'totalfailedamount' => $totalAmountReviewFailed,
            'user' => $user
        ]);
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
        foreach ($clients as $client) {
            $clientArr[$client->id] = $client->clientname . ' (' . $client->phone . ')';
        }
        $paymentModeArr = array();
        foreach ($paymentModes as $pMode) {
            if ($pMode->id == '2') {
                continue;
            }
            $paymentModeArr[$pMode->id] = $pMode->name;
        }

        $products = Product::all()->where('stockavailable', '>', '0');

        $staff = Staff::orderBy('firstname', 'asc')->where('activestatus', '=', '1')->get();


        /****************** test ****************************/

        // $package = ClientPackage::findOrFail(1);

        /* $myObj = new \stdClass();

            $myObj->statuses = array( '0','0','0' );
          $myObj->ids = array( '1', '1' , '1');
          $myObj->itemtypes = array("service", "package", "product");
           $myObj->unitprice = array('50', "500", "50");
           $myObj->qnty = array('10', "1", "100");
            $myObj->discounttype = array('percent', 'absolute', "");
            $myObj->discountvalue = array('5', '200',"0");
        $myObj->staffs1_1_1_6 = array('1');
        $myObj->staffs1_1_3_7 = array('1');
        $myObj->staffs0_services_1 = array('1');
        $myObj->staffs2_product_1 = array('1');
            $myObj->taxes = array('0', '' ,18);
            $myObj->global_discounttype = "percent";
            $myObj->global_discountvalue = "10";
            $myObj->clientname = "";
            $myObj->clientid = 1;
            $myObj->walkin_name = "";

            $myObj->package_name = "1";
            $myObj->amount = array("0"); */


        // echo $this->calculateSale($myObj);


        /*****************************************/




        return view('saleadd', ['categories' =>  $categories, 'packages' => $packages, 'products' => $products, 'clients' => $clientArr, 'staffs' => $staff, 'paymentmodes' => $paymentModeArr]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    protected function isValidCashPackage($package, $request, $excludeSaleIds = array())
    {
        $leftMoney = $package->packageLeftMoney($excludeSaleIds);
        $arr = array();
        $arr['iserror'] = 0;
        $arr['msg'] = "";
        if ($leftMoney <= 0) {
            $arr['iserror'] = 1;
            $arr['msg'] = "No Amount left in this package.";
            return $arr;
        }

        $amt = $this->getGrandAmount($request);

        if (($leftMoney - $amt) < 0) {
            $arr['iserror'] = 1;
            $arr['msg'] = number_format(abs($leftMoney - $amt), 2) . " Amount is exceeding than package left amount.";
            return $arr;
        }

        return $arr;
    }

    protected function getGrandAmount($request)
    {

        $products = $this->getItemsFromRequest($request, "product");
        $subpackages = $this->getItemsFromRequest($request, "package");
        $services = $this->getItemsFromRequest($request, "service");

        $totalAmountServices = $this->getTotalAmountSubPackages($subpackages);
        $totalAmountServices += $this->getTotalPriceServices($services);

        $totalAmountProducts = $this->getTotalPriceProducts($products);

        $globalDiscount  = 0;
        if ($request->global_discounttype == 'percent') {
            $globalDiscount =  $totalAmountServices * $request->global_discountvalue / 100;
        } else {
            $globalDiscount = $request->global_discountvalue;
        }


        $grandAmount = $totalAmountServices + $totalAmountProducts - $globalDiscount;

        return $grandAmount;
    }


    protected function isValidCompositePackage($package, $request, $excludeSaleIds = array())
    {

        $arr = array();
        $arr['iserror'] = 0;
        $arr['msg'] = "";
        $leftItems = $package->packageLeftItems($excludeSaleIds);



        $products = $this->getItemsFromRequest($request, "product");

        if (count($products) > 0) {
            $arr['iserror'] = 1;
            $arr['msg'] = "Products are not allowed in package.";
            return $arr;
        }

        $subpackages = $this->getItemsFromRequest($request, "package");
        if (count($subpackages) > 0) {
            $arr['iserror'] = 1;
            $arr['msg'] = "Packages are not allowed in this package.";
            return $arr;
        }

        $services = $this->getItemsFromRequest($request, "service");


        if (count($leftItems) <= 0) {
            $arr['iserror'] = 1;
            $arr['msg'] = "No item left in the package.";
            return $arr;
        }

        // print_r( $services);

        if (count($services) > 0) {
            foreach ($services as $service) {
                //   print_r($services);
                if (!isset($leftItems[$service["id"]])) {
                    //continue;
                    $arr['iserror'] = 1;
                    $arr['msg'] = "Items mismatched.";
                    return $arr;
                }

                $leftItems[$service["id"]] -= $service["qnty"];


                if ($leftItems[$service["id"]] < 0) {
                    $arr['iserror'] = 1;
                    $arr['msg'] = "Items mismatched.";
                    return $arr;
                }
            }
        }

        return $arr;
    }



    protected function getItemsFromRequest($request, $itemtype = "")
    {
        $x = 0;
        $items = array();
        foreach ($request->ids as $R) {

            //package, product, service
            if ($request->itemtypes[$x] == $itemtype) {
                $items[] = array("id" => $R, "type" => $itemtype, 'unitprice' => $request->unitprice[$x], 'qnty' => $request->qnty[$x], 'discounttype' => $request->discounttype[$x], 'discountvalue' => $request->discountvalue[$x], 'taxes' => $request->taxes[$x]);
            }

            $x++;
        }

        return $items;
    }


    protected function getTotalAmountSubPackages($packages)
    {

        $totalAmount = 0;
        if (count($packages) > 0) {

            foreach ($packages as $R) {

                $obj = new Services();
                $obj->price = $R["unitprice"];
                $obj->qnty = $R["qnty"];
                $obj->discounttype = $R["discounttype"];
                $obj->discount = $R["discountvalue"];

                // getting dynamic price entered by user
                $price = $R["unitprice"];
                //temprary change service price to calculate discount
                $qnty = $R["qnty"];

                $discountType = $R["discounttype"];
                $discountValue = $R["discountvalue"];
                $discountAmount = $qnty * $obj->getDiscountAmount($discountType, $discountValue);

                $calculatedPrice = $price * $qnty - $discountAmount;

                $totalAmount += $calculatedPrice;
            }
        }

        return $totalAmount;
    }

    protected function getTotalPriceServices($services)
    {
        $totalAmount = 0;
        if (count($services) > 0) {

            // parse all service ids to add record for each

            //$request->ids[$keys] == $R->id &&
            foreach ($services as $R) {

                $obj = new Services();
                $obj->price = $R["unitprice"];
                $obj->qnty = $R["qnty"];
                $obj->discounttype = $R["discounttype"];
                $obj->discount = $R["discountvalue"];

                // getting dynamic price entered by user
                $price = $R["unitprice"];
                //temprary change service price to calculate discount
                $qnty = $R["qnty"];

                $discountType = $R["discounttype"];
                $discountValue = $R["discountvalue"];
                $discountAmount = $qnty * $obj->getDiscountAmount($discountType, $discountValue);

                $calculatedPrice = $price * $qnty - $discountAmount;

                $totalAmount += $calculatedPrice;
            }
        }

        return $totalAmount;
    }


    protected function getTotalPriceProducts($products)
    {
        $totalAmount = 0;
        if (count($products) > 0) {


            foreach ($products as $R) {


                $obj = new Services();
                $obj->price = $R["unitprice"];
                $obj->qnty = $R["qnty"];
                $obj->discounttype = $R["discounttype"];
                $obj->discount = $R["discountvalue"];

                // getting dynamic price entered by user
                $price = $R["unitprice"];
                //temprary change service price to calculate discount

                $qnty = $R["qnty"];

                //$price = $R->price;

                $discountType = $R["discounttype"];
                $discountValue = $R["discountvalue"];
                $discountAmount = $qnty * $obj->getDiscountAmount($discountType, $discountValue);

                $calculatedPrice = $price * $qnty - $discountAmount;

                $totalAmount += $calculatedPrice;
            }
        }

        return $totalAmount;
    }


    public function store(Request $request)
    {
        //
        $messages = [
            'ids.required' => 'Please select package, service or product to make order.',
            'qnty.greator_then_zero' => 'Quantity should be greator than zero.'

        ];


        $validator =  Validator::make($request->all(), ['clientid' => 'required', 'ids' => 'required|array', 'qnty' => 'required|array|greator_then_zero'], $messages);



        if ($validator->fails()) {
            return redirect(route('sale.create'))
                ->withInput()
                ->withErrors($validator);
        } else {

            $msg = array();
            if ('' != trim($request->clientname)) {
                if ('' == trim($request->phone)) {
                    $msg[] = "Client Phone is required.";
                }

                if ('' == trim($request->dob)) {
                    $msg[] = "Date of birth is required.";
                }
            } else if ($request->package_name > 0 && $request->clientid > 0) //
            {

                // validate package
                $package = ClientPackage::findOrFail($request->package_name);
                if ($package->isPackageCompleted() == true) {
                    $msg[] = "This package is not valid to add.";
                } else { //now compare package items or money

                    //composite package
                    $arr = array();

                    if ($package->packagetype == "1") // cash
                    {
                        $arr = $this->isValidCashPackage($package, $request);
                    } else // composite package
                    {

                        $arr = $this->isValidCompositePackage($package, $request);
                    }

                    if ('1' == $arr['iserror']) {
                        $msg[] = $arr['msg'];
                    }
                }
            }

            if (count($msg) > 0) {
                // print_r($msg);
                //die("error");
                return redirect(route('sale.create'))
                    ->withInput()
                    ->withErrors($msg);
            }

            //  $sale = new Sale();
            //  $sale->clientid = $request->clientid;
            //$sale->
            $id = $this->calculateSale($request);

            //$request->session()->flash("successmsg", "Successfully Added.");
            //            echo  route('sale.edit', $id );
            return redirect(route('sale.edit', $id));
            //  return redirect('sale/'.$id.'/edit');
        }
    }

    // adding sale in database
    protected function calculateSale($request)
    {

        $sale = new Sale();
        //$sale->ispackage = intval($request->ispackage);

        if (count($request->ids) > 0) {

            //if new client selected
            if (trim($request->clientname) != '') {
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
                $dt = explode('/', $request->dob);
                $client->dob =  $dt[2] . "-" . $dt[1] . "-" . $dt[0];
                $client->isrealclient = '1';
                $client->save();

                $sale->clientid = $client->id;
            } else if (trim($request->clientid) != '' &&  $request->clientid > 0) {

                $sale->clientid = $request->clientid;
            } else // add new temp client 
            {
                //add new client
                $client = new Client();
                $client->clientname = $request->walkin_name;
                $client->isrealclient = '0';
                $client->save();

                $sale->walkin_name = $request->walkin_name;
                $sale->clientid = $client->id;
            }


            if ($sale->clientid > 0 && $request->package_name > 0) {
                $sale->packageid = $request->package_name;
            }


            $sale->created_at =  $this->getDate($request->saledate);
            $sale->updated_at = $this->getDate($request->saledate);
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

            foreach ($request->ids as $R) {

                if ($request->itemtypes[$x] == 'package') {
                    $packagesIds[] = $R;
                } else if ($request->itemtypes[$x] == 'product') {
                    $productIds[] = $R;
                } else if ($request->itemtypes[$x] == 'service') {
                    $serviceIds[] = $R;
                }



                //qnty
                //itemtypes
                //discounttype
                //discountvalue

                $x++;
            }

            $totalAmount = 0;
            // add packages in db
            if (count($packagesIds) > 0) {
                $packagesRows = Packages::whereIn('id', $packagesIds)->get();

                for ($keys = 0; $keys < count($request->ids); $keys++) {
                    if ($request->itemtypes[$keys] == 'package') {
                        //$request->ids[$keys] == $R->id
                        foreach ($packagesRows as $R) {
                            if ($request->ids[$keys] == $R->id) {
                                $packageServices = $R->packageservices;

                                //$price = $R->price;
                                // getting dynamic price from user
                                $price = $request->unitprice[$keys];

                                //change package price temporary to calculate discount
                                $R->price = $price;

                                $qnty = $request->qnty[$keys];
                                $discountType = $request->discounttype[$keys];
                                $discountValue = $request->discountvalue[$keys];
                                $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

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
                                $itemP->status = $request->statuses[$keys];

                                //$arrItems[] = $item;
                                $itemP->save();


                                foreach ($packageServices as $packageService) {
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

                                    $dropDownValues = (array) $request->{'staffs' . $keys . '_' . $R->id . '_' . $packageService->service->id . '_' . $packageService->id};


                                    //$dropDownAmount = $request->{'amount_staffs_'.$R->id.'_'.$packageService->service->id} ;

                                    //if no amount value filled then divide equal
                                    //if few values filled then total -



                                    $arrDropDownValues = array();



                                    for ($x = 0; $x < count($dropDownValues); $x++) {
                                        if (trim($dropDownValues[$x]) != '') {
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
            }

            // add services in db
            if (count($serviceIds) > 0) {
                //print_r($request->ids);
                //print_r($serviceIds);
                $servicesRows = Services::whereIn('id', $serviceIds)->get();

                // parse all service ids to add record for each
                $keys = 0;
                for ($keys = 0; $keys < count($request->ids); $keys++) {
                    if ($request->itemtypes[$keys] == 'service') {

                        //$request->ids[$keys] == $R->id &&
                        foreach ($servicesRows as $R) {

                            if ($request->ids[$keys] == $R->id) {
                                //$price = $R->price;

                                // getting dynamic price entered by user
                                $price = $request->unitprice[$keys];
                                //temprary change service price to calculate discount
                                $R->price = $price;

                                $qnty = $request->qnty[$keys];
                                $discountType = $request->discounttype[$keys];
                                $discountValue = $request->discountvalue[$keys];
                                $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

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
                                $item->status = $request->statuses[$keys];
                                // $arrItems[] = $item;

                                $staffDropDown = 'staffs' . $keys . '_services_' . $R->id;
                                //$item->staffid = $request->{$staffDropDown};

                                $item->save();
                                $dropDownValues = (array) $request->{$staffDropDown};
                                for ($x = 0; $x < count($dropDownValues); $x++) {


                                    if (trim($dropDownValues[$x]) != '') {

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

            $productsTotalPrice = 0;
            $tax = 0;
            if (count($productIds) > 0) {
                $productRows = Product::whereIn('id', $productIds)->get();

                foreach ($productRows as $R) {
                    $keys = 0;
                    for ($keys = 0; $keys < count($request->ids); $keys++) {

                        if ($request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'product') {

                            //$price = $R->price;

                            //getting dynamic price entered by user
                            $price = $request->unitprice[$keys];
                            //temporary change product price to calculate discount
                            $R->price = $price;

                            $qnty = $request->qnty[$keys];
                            $discountType = $request->discounttype[$keys];
                            $discountValue = $request->discountvalue[$keys];
                            $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

                            $calculatedPrice = $price * $qnty - $discountAmount;

                            $totalAmount += $calculatedPrice;
                            $productsTotalPrice += $calculatedPrice;

                            $tax += ($calculatedPrice - ($calculatedPrice / ((100 + $R->tax) * 0.01)));


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
                            $item->status = $request->statuses[$keys];

                            $staffDropDown = 'staffs' . $keys . '_product_' . $R->id;
                            //$item->staffid = $request->{$staffDropDown};

                            //$arrItems[] = $item;
                            $item->save();

                            $dropDownValues = (array) $request->{$staffDropDown};
                            for ($x = 0; $x < count($dropDownValues); $x++) {


                                if (trim($dropDownValues[$x]) != '') {

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


            if ($globalDiscountType == 'percent') {
                $globalDiscount =  ($totalAmount - $productsTotalPrice) * $globalDiscountValue / 100;
            } else {
                $globalDiscount = $globalDiscountValue;
            }


            $grandAmount = $totalAmount - $globalDiscount;

            $servicesTotalAmount = $servicesTotalAmount - $globalDiscount;
            $tax += ($servicesTotalAmount -  ($servicesTotalAmount / ((100 + SERVICE_TAX) * 0.01)));
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

            $sale->created_at =  $this->getDate($request->saledate) . ' ' . date("H:i:s");
            $sale->updated_at = $this->getDate($request->saledate) . ' ' . date("H:i:s");
            $sale->save();

            $paymentCounter = 0;
            $amountForPayment = $grandTotal;
            $clientPackage = null;
            if ($sale->clientid > 0 && $sale->packageid > 0) {
                $clientPackage = ClientPackage::findOrFail($sale->packageid);
            }

            $hsPayment = new HS_Payment();
            if ($request->use_advance_amount == '1' && $amountForPayment > 0) {
                //$amount

                $pendingPayment = $hsPayment->useClientAmountInSale($sale->clientid, $sale->id);
                $amountForPayment = $pendingPayment;
            }

            foreach ($request->amount as $amount) {
                if ($amount > 0 &&  $request->paymentmodeid[$paymentCounter] != "") {


                    $clientPayment = $hsPayment->addNewClientPayment($sale->clientid, $request->paymentmodeid[$paymentCounter], $amount, $request->bankname[$paymentCounter], $request->bankaccountno[$paymentCounter], $request->chequeno[$paymentCounter], $request->chequedate[$paymentCounter], $request->other[$paymentCounter], $this->getDate($request->saledate) . ' ' . date("H:i:s"));
                    //($sale, $request->paymentmodeid[$paymentCounter] )

                    if ($clientPayment != null) {
                        $paymentUsed = $hsPayment->addClientPaymentUsed($clientPayment->id, $sale->id, $amount, $this->getDate($request->saledate) . ' ' . date("H:i:s"));
                    }

                    /*$req = new \stdClass();
                    $req->amount = $amount;
                    $req->paymentmodeid = $request->paymentmodeid[$paymentCounter];
                    $req->bankname = $request->bankname[$paymentCounter];
                    $req->bankaccountno = $request->bankaccountno[$paymentCounter];
                    $req->chequeno = $request->chequeno[$paymentCounter];
                    $req->chequedate = $request->chequedate[$paymentCounter];
                    $req->other = $request->other[$paymentCounter]; */
                }

                $paymentCounter++;
            }

            // $sale->saleItem()->saveMany($arrItems);

            return $sale->id;
        }
    }



    /*
    function addNewSalePaymentForClientPackage($clientPackage, $clientPaymentId,  $request)
    {
        
        $salePayment = new SalePayment();
        $salePayment->saleorpackageid = $clientPackage->id;
        $salePayment->paymentmodeid = $request->paymentmodeid;
        $salePayment->clientpaymentid = $clientPaymentId;
        $salePayment->amount = $request->amount;
        $salePayment->ispackage = "1";
        if( $request->amount > $clientPackage->actualPendingAmount()  )
        {
            $salePayment->amount = $clientPackage->actualPendingAmount();
        }
        
        if( $salePayment->amount == $clientPackage->pendingAmount() && $request->paymentmodeid != '2' )
        {
            $clientPackage->ispaymentdone = '1';
        }
        else
        {
            $clientPackage->ispaymentdone = '0';
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
        
        
        $salePayment->save();
        $clientPackage->save();
        
        return $salePayment->id;
        
    }
*/
    protected function getDate($dates)
    {
        if ($dates == "")
            return "";

        $dt = explode('/', $dates);

        return $dt[2] . '-' . $dt[1] . '-' . $dt[0];
    }




    /*function addNewPaymentClient( $client, $request )
    {
        if( $client )
        {
            
            
            if( $request->amount > 0 ) {
                
                $clientPayment = new ClientPayment();
                $clientPayment->clientid = $client->id;
                $clientPayment->paymentmodeid = $request->paymentmodeid;
                $clientPayment->amount = $request->amount;
                //not cheques then this payment assume as done
                if ( $request->paymentmodeid != '2' )
                {
                    $clientPayment->ispaymentdone = 1;
                }
                else
                {
                    $clientPayment->ispaymentdone = 0;
                }
                
                //bank payment
                if( $request->paymentmodeid == '2' )
                {
                    $clientPayment->bankname = $request->bankname;
                    $clientPayment->bankaccountno = $request->bankaccountno;
                    $clientPayment->chequeno = $request->chequeno;
                    if( $request->chequedate != '' ) {
                        $dt = explode( '/', $request->chequedate );
                        
                        $clientPayment->chequedate = $dt[2].'-'.$dt[1].'-'.$dt[0];
                    }
                    
                }
                else if( $request->paymentmodeid == '5' ) // other
                {
                    $clientPayment->other = $request->other;
                }
                $clientPayment->save();
                
                return $clientPayment->id;
            }
            
        }
        return '0';
    }
     */


    protected function calculateSaleForEdit($request, $id)
    {


        $sale = Sale::findOrFail($id);
        //$sale->ispackage = intval($request->ispackage);
        $oldClientId = $sale->clientid;
        $oldPackageId = $sale->packageid;

        $oldProductIds = array();
        $oldServiceIds = array();
        $oldPackageIds = array();
        $allMainIds = array();
        $deleteRecordIds = array();

        foreach ($sale->saleItem as $item) {

            $allMainIds[] = $item->id;
            $shouldDelete = false;
            if (!in_array($item->id, $request->org_ids)) {
                $deleteRecordIds[] = $item->id;
                $shouldDelete = true;
            }

            if ($item->itemtype == "service" && $item->saleid > 0) {
                $oldServiceIds[] = $item->itemid;
            } else if ($item->itemtype == "product") {
                $oldProductIds[] = $item->itemid;
                //if product item will delete from sale then increase the product stock again 
                if ($shouldDelete == true) {
                    $product = Product::find($item->itemid);
                    $product->stockavailable = $product->stockavailable + $item->quantity;
                    $product->soldcount =      $product->soldcount - $item->quantity;
                    $product->save();
                }
            } else if ($item->itemtype == "package") {
                $oldPackageIds[] = $item->itemid;
            }
        }





        //delete only those records which are deleted by user

        if (count($deleteRecordIds) > 0) {

            //not delete those items which are not available

            $saleItems =  SaleItem::whereIn('parentid', $deleteRecordIds)->get();
            $arrIds = array();
            foreach ($saleItems as $item) {
                $arrIds[] = $item->id;
            }
            if (count($arrIds) > 0) {
                ServiceDoneStaff::whereIn('saleitemid', $arrIds)->delete();
            }


            ServiceDoneStaff::whereIn('saleitemid', $deleteRecordIds)->delete();
            SaleItem::whereIn('id', $deleteRecordIds)->delete();
            SaleItem::whereIn('parentid', $deleteRecordIds)->delete();
        }




        if (count($request->ids) > 0) {
            //$sale = new Sale();
            $sale->clientid = $request->clientid;

            $sale->updated_at = date("Y-m-d H:i:s");
            $sale->save();

            $arrItems = array();

            $globalDiscount = 0;
            $globalDiscountType = $request->global_discounttype;
            $globalDiscountValue = $request->global_discountvalue;



            $x = -1;
            $packagesIds = array();
            $productIds = array();
            $serviceIds = array();
            $servicesTotalAmount = 0;

            foreach ($request->ids as $R) {
                //skip those records which are already in db
                $x++;
                if (isset($request->org_ids[$x]) && $request->org_ids[$x] != '') {
                    continue;
                }

                if ($request->itemtypes[$x] == 'package') {
                    $packagesIds[] = $R;
                } else if ($request->itemtypes[$x] == 'product') {
                    $productIds[] = $R;
                } else if ($request->itemtypes[$x] == 'service') {
                    $serviceIds[] = $R;
                }
            }
            $servicesTotalAmount = 0;
            $totalAmount = 0;


            //TODO: Start here on morning
            //Tasks: delete/update members done by staff for packages exist
            //Update Services and Delete/Update services done by
            //Update product and Delete/Update Done by
            //I think for product done by staff we should use old date that lastly added
            // add is complete in front of each record except products
            /******************** update already exist records ********************/
            $saleItems =  SaleItem::whereIn('id', $request->org_ids)->get();
            $packagesRows = Packages::whereIn('id', $packagesIds)->get();
            $servicesRows = Services::whereIn('id', $serviceIds)->get();
            $productRows = Product::whereIn('id', $productIds)->get();
            $productsTotalPrice = 0;
            $deleteDoneBy = array();
            $tax = 0;


            for ($keys = 0; $keys < count($request->ids); $keys++) {

                //updates
                if (isset($request->org_ids[$keys]) && $request->org_ids[$keys] != '') {

                    foreach ($saleItems as $itemP) {
                        if ($itemP->id ==  $request->org_ids[$keys]) {
                            //update here

                            $price = $request->unitprice[$keys];

                            //temporary update package price to calculate discount

                            $oldQnty = $itemP->quantity;



                            $qnty = $request->qnty[$keys];
                            $discountType = $request->discounttype[$keys];
                            $discountValue = $request->discountvalue[$keys];
                            $discountAmount = $qnty * $this->getDiscountAmount($price, $discountType, $discountValue);

                            $calculatedPrice = $price * $qnty - $discountAmount;

                            $totalAmount += $calculatedPrice;


                            $itemP->actualpriceperitem = $price; //$R->price;
                            $itemP->quantity = $qnty;
                            $itemP->discountvalue = $discountValue;
                            $itemP->discounttype = $discountType;
                            $itemP->discountamount = $discountAmount;
                            $itemP->parentid = 0;
                            $itemP->status = $request->statuses[$keys];
                            //$itemP->taxpercent = SERVICE_TAX;

                            $tax += ($calculatedPrice - ($calculatedPrice / ((100 + $itemP->taxpercent) * 0.01)));


                            //$arrItems[] = $item;
                            $itemP->save();


                            if ($itemP->itemtype == "package") {



                                //update/delete done by

                                foreach ($itemP->packageItems as $packageItem) {
                                    foreach ($packageItem->doneByStaffMembers as $staffMain) {
                                        if ($request->{"already_staffs_" . $staffMain->id} != "") {

                                            //update this

                                            $staffMain->staffid = $request->{"already_staffs_" . $staffMain->id};
                                            $staffMain->save();
                                        } else {
                                            $deleteDoneBy[] = $staffMain->id;
                                        }
                                    }
                                }

                                // add new staff done for exist items
                                foreach ($itemP->packageItems as $packageItem) {

                                    $dropDownValues = (array) $request->{'staffs' . $keys . '_' . $packageItem->id};

                                    for ($x = 0; $x < count($dropDownValues); $x++) {
                                        if (trim($dropDownValues[$x]) != '') {
                                            $serviceDone = new ServiceDoneStaff();
                                            $serviceDone->staffid = $dropDownValues[$x];
                                            $serviceDone->saleitemid = $packageItem->id;
                                            //$arrDropDownValues[] = $dropDownValues[$x];
                                            $serviceDone->amount = 0;
                                            $serviceDone->save();
                                        }
                                    }
                                }
                            }
                            // if it is a service
                            elseif ($itemP->itemtype == "service") {





                                foreach ($itemP->doneByStaffMembers as $staffMain) {
                                    if ($request->{"already_staffs_services_" . $staffMain->id} != "") {

                                        //update this

                                        $staffMain->staffid = $request->{"already_staffs_services_" . $staffMain->id};
                                        $staffMain->save();
                                    } else {
                                        $deleteDoneBy[] = $staffMain->id;
                                    }
                                }



                                $dropDownValues = (array) $request->{"staffs" . $keys . "_services_" . $itemP->id};

                                for ($x = 0; $x < count($dropDownValues); $x++) {
                                    if (trim($dropDownValues[$x]) != '') {
                                        $serviceDone = new ServiceDoneStaff();
                                        $serviceDone->staffid = $dropDownValues[$x];
                                        $serviceDone->saleitemid = $itemP->id;
                                        //$arrDropDownValues[] = $dropDownValues[$x];
                                        $serviceDone->amount = 0;
                                        $serviceDone->save();
                                    }
                                }
                            } else { // is a product
                                //staffs_product_1

                                $productsTotalPrice += $calculatedPrice;





                                if ($oldQnty != $qnty) {

                                    //update product stock
                                    $product = Product::find($itemP->itemid);
                                    $product->stockavailable = ($product->stockavailable - $qnty + $oldQnty);
                                    $product->soldcount = $product->soldcount + $qnty - $oldQnty;
                                    $product->save();
                                }

                                foreach ($itemP->doneByStaffMembers as $staffMain) {
                                    if ($request->{"already_staffs_product_" . $staffMain->id} != "") {

                                        //update this

                                        $staffMain->staffid = $request->{"already_staffs_product_" . $staffMain->id};
                                        $staffMain->save();
                                    } else {
                                        $deleteDoneBy[] = $staffMain->id;
                                    }
                                }



                                // new Added staff done
                                $staffDropDown = 'staffs' . $keys . '_product_' . $itemP->id;
                                //$item->staffid = $request->{$staffDropDown};

                                $dropDownValues = (array) $request->{$staffDropDown};
                                for ($x = 0; $x < count($dropDownValues); $x++) {
                                    if (trim($dropDownValues[$x]) != '') {
                                        $serviceDone = new ServiceDoneStaff();
                                        $serviceDone->staffid = $dropDownValues[$x];
                                        //foreach ($itemP->doneByStaffMembers as $staffMain) {
                                        $serviceDone->saleitemid = $itemP->id;
                                        //$arrDropDownValues[] = $dropDownValues[$x];
                                        $serviceDone->amount = 0;
                                        $serviceDone->save();
                                        //}
                                    }
                                }
                            }
                        }
                    }

                    // delete done by here
                } else {  // add new records
                    //add  new here


                    if (count($packagesIds) > 0) {


                        // print_r($packagesRows);
                        foreach ($packagesRows as $R) {
                            //echo $R->id.'==';
                            // print_r($request->ids);


                            if ($request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'package') {
                                $packageServices = $R->packageservices;


                                //$price = $R->price;
                                //Using price entered by user
                                $price = $request->unitprice[$keys];

                                //temporary update package price to calculate discount
                                $R->price = $price;

                                $qnty = $request->qnty[$keys];
                                $discountType = $request->discounttype[$keys];
                                $discountValue = $request->discountvalue[$keys];
                                $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

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
                                $itemP->status = $request->statuses[$keys];
                                //$arrItems[] = $item;
                                $itemP->save();


                                foreach ($packageServices as $packageService) {
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

                                    //  $staffDropDown = 'staffs_'.$R->id.'_'.$packageService->service->id;

                                    $dropDownValues = (array) $request->{'staffs' . $keys . '_' . $R->id . '_' . $packageService->service->id . '_' . $packageService->id};
                                    //  $item->staffid =  $request->{$staffDropDown};



                                    $item->save();

                                    // $dropDownValues = $request->{$staffDropDown};

                                    for ($x = 0; $x < count($dropDownValues); $x++) {
                                        if (trim($dropDownValues[$x]) != '') {
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

                    if (count($serviceIds) > 0) {
                        //print_r($request->ids);
                        //print_r($serviceIds);


                        //count($servicesRows);
                        foreach ($servicesRows as $R) {


                            if ($request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'service') {




                                //$price = $R->price;
                                // using price entered by user
                                $price = $request->unitprice[$keys];

                                // temporary update service price to calculate discount
                                $R->price = $price;

                                $qnty = $request->qnty[$keys];
                                $discountType = $request->discounttype[$keys];
                                $discountValue = $request->discountvalue[$keys];
                                $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

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
                                $item->status = $request->statuses[$keys];
                                // $arrItems[] = $item;

                                $staffDropDown = 'staffs' . $keys . '_services_' . $R->id;
                                //$item->staffid = $request->{$staffDropDown};

                                $item->save();

                                $dropDownValues = (array) $request->{$staffDropDown};
                                for ($x = 0; $x < count($dropDownValues); $x++) {
                                    if (trim($dropDownValues[$x]) != '') {
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




                    if (count($productIds) > 0) {



                        foreach ($productRows as $R) {


                            if ($request->ids[$keys] == $R->id && $request->itemtypes[$keys] == 'product') {
                                $price  = $request->unitprice[$keys];
                                //temprary update product price to calculate correct discount
                                $R->price = $price;
                                $qnty = $request->qnty[$keys];
                                $discountType = $request->discounttype[$keys];
                                $discountValue = $request->discountvalue[$keys];
                                $discountAmount = $qnty * $R->getDiscountAmount($discountType, $discountValue);

                                $calculatedPrice = $price * $qnty - $discountAmount;

                                $totalAmount += $calculatedPrice;
                                $productsTotalPrice += $calculatedPrice;

                                $tax += ($calculatedPrice - ($calculatedPrice / ((100 + $R->tax) * 0.01)));

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
                                $item->status = $request->statuses[$keys];
                                //$arrItems[] = $item;
                                $staffDropDown = 'staffs' . $keys . '_product_' . $R->id;
                                //$item->staffid = $request->{$staffDropDown};

                                $item->save();


                                $dropDownValues = (array) $request->{$staffDropDown};
                                for ($x = 0; $x < count($dropDownValues); $x++) {
                                    if (trim($dropDownValues[$x]) != '') {
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
            }

            if (is_array($deleteDoneBy) && count($deleteDoneBy) > 0) {
                ServiceDoneStaff::whereIn('id', $deleteDoneBy)->delete();
            }


            if ($globalDiscountType == 'percent') {
                $globalDiscount =  ($totalAmount - $productsTotalPrice)  * $globalDiscountValue / 100;
            } else {
                $globalDiscount = $globalDiscountValue;
            }

            //$taxableAmount = $totalAmount - $globalDiscount;

            $grandAmount = $totalAmount - $globalDiscount;



            $servicesTotalAmount = $servicesTotalAmount - $globalDiscount;

            $tax += ($servicesTotalAmount -  ($servicesTotalAmount / ((100 + SERVICE_TAX) * 0.01)));

            /*if( $request->taxpercent > 0 )
            {
                $tax = $taxableAmount *  $request->taxpercent / 100;
            } */
            $clientChanged = false;

            if ($oldClientId != $request->clientid) {
                $clientChanged = true;
            }

            $sale->clientid = $request->clientid;
            if ($request->clientid  > 0) {
                $sale->walkin_name = '';
            } else {
                $clientNew = new Client();
                $clientNew->clientname = $request->walkin_name == '' ? 'Walk-In' : $request->walkin_name;
                $clientNew->isrealclient = "0";

                $clientNew->save();
                //$sale->walkin_name = $request->walkin_name == '' ? 'Walk-In' : $request->walkin_name ;
                $sale->clientid = $clientNew->id;
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


            if ($request->package_name > 0) {
                $sale->packageid = $request->package_name;
            } else {
                $sale->packageid = 0;
            }

            if ($clientChanged == true) {
                //adjust payments
                $salePayment = $sale->salepayments;
                $arrDeleteSalePayment = array();
                foreach ($salePayment as $payment) {
                    $clientPayment = $payment->clientPayment;
                    //below means only one whole record for this payment
                    //so we can update it
                    if ($clientPayment->amount == $payment->amount) {
                        $clientPayment->clientid = $sale->clientid;
                        $clientPayment->save();
                    } else //delete sale payment
                    {
                        $arrDeleteSalePayment[] = $payment;
                    }
                }


                if (count($arrDeleteSalePayment) > 0) {
                    foreach ($arrDeleteSalePayment as $payment) {
                        $payment->delete();
                    }
                }
            }


            /*  if( ($oldPackageId == '' || $oldPackageId <= 0 ) &&  $sale->packageid  > 0 )
            {
                foreach( $sale->salepayments as $salePayment )
                {
                    if( $salePayment->paymentDetail )
                    {
                        $clientPay = $salePayment->paymentDetail;
                        $clientPay->clientid = $sale->clientid;
                        $clientPay->save();
                    }
                    $salePayment->saleorpackageid = $sale->packageid;
                    $salePayment->ispackage = "1";
                    $salePayment->save();
                }
            
            }
            
            if( $oldPackageId > 0 && $sale->packageid <= 0 )
            {
                $package = ClientPackage::findOrFail($oldPackageId);
                foreach( $package->salepayments as $salePayment )
                {
                    if( $salePayment->paymentDetail )
                    {
                        $clientPay = $salePayment->paymentDetail;
                        $clientPay->clientid = $sale->clientid;
                        $clientPay->save();
                    }
                    $salePayment->saleorpackageid = $sale->id;
                    $salePayment->ispackage = "0";
                    $salePayment->save();
                }
            } */




            //  $clientPayments = SalePayment::where("id", "=", $sale->id )->where("ispackage", "=", '0' )->get();

            /*foreach ( $clientPayments as $payment )
                {
                    //$payment->saleid = 0;

                    $payment->clientid = $sale->clientid;
                    $payment->save();
                }   */



            $sale->save();
            // $sale->saleItem()->saveMany($arrItems);

            return $sale->id;
        }
    }


    public function getDiscountAmount($price, $discountType, $discountValue)
    {

        $discountAmount = 0;
        if ($discountType != '' &&  $discountValue > 0) {
            if ($discountType == 'percent') {
                $discountAmount = $price * $discountValue / 100;
            } else {
                $discountAmount = $discountValue;
            }
        }

        return $discountAmount;
    }

    public function addpayment($id)
    {

        $sale = Sale::findOrFail($id);

        $paymentModes = PaymentMode::all();

        $paymentModeArr = array();
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }



        return view('saleaddpayment', ['sale' => $sale, 'paymentmodes' => $paymentModeArr]);
    }

    public function storepayment(Request $request, $id)
    {

        $sale = Sale::findOrFail($id);


        //if amount is empty
        if ($request->amount == '' || $request->amount <= 0) {
            return redirect(route('sale.addpayment', $sale->id))
                ->withInput()
                ->withErrors(array('Amount should be greator than zero.'));
        }

        //if already added full amount entries and some could be under reviews or full payment paid


        if ($sale->pendingAmount() <= $sale->getAmountUnderReview()) {

            return redirect(route('sale.addpayment', $sale->id))
                ->withInput()
                ->withErrors(array('We can not add as other payments are under review for this sale.'));
        } else if (($sale->pendingAmount() - $sale->getAmountUnderReview()) < $request->amount) {
            return redirect(route('sale.addpayment', $sale->id))
                ->withInput()
                ->withErrors(array('Pending amount is less than the amount you entered. There could be payment under review. You should add amount of: ' . ($sale->pendingAmount() - $sale->getAmountUnderReview())));
        }




        if ($request->amount > 0) {


            $salePayment = new SalePayment();
            $salePayment->salemasterid = $sale->id;
            $salePayment->paymentmodeid = $request->paymentmodeid;

            $salePayment->amount = $request->amount;
            if ($request->amount > $sale->pendingAmount()) {
                $salePayment->amount = $sale->pendingAmount();
            }




            if ($sale->paymentmodeid != '2') {
                $sale->totalamountpaidyet += $salePayment->amount;
            }
            //fulle payment paid also not by cheque so 100% proofed to pay
            if ($sale->totalamountpaidyet >= $sale->paidprice && $request->paymentmodeid != '2') {
                $sale->ispaid = '1';
            } else {
                $sale->ispaid = '0';
            }

            //not cheques then this payment assume as done
            if ($request->paymentmodeid != '2') {
                $salePayment->ispaymentdone = 1;
            } else {
                $salePayment->ispaymentdone = 0;
            }

            //bank payment
            if ($request->paymentmodeid == '2') {
                $salePayment->bankname = $request->bankname;
                $salePayment->bankaccountno = $request->bankaccountno;
                $salePayment->chequeno = $request->chequeno;
                if ($request->chequedate != '') {
                    $dt = explode('/', $request->chequedate);

                    $salePayment->chequedate = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
                }
            } else if ($request->paymentmodeid == '5') // other
            {
                $salePayment->other = $request->other;
            }
            $salePayment->save();
            $sale->save();

            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
        } else {
            return redirect(route('sale.addpayment', $sale->id))
                ->withInput()
                ->withErrors(array('Looks like there is no pending amount for this sale.'));
        }
    }



    public function editpayment($saleId, $paymentId)
    {
        //echo $saleId.' == '.$paymentId;
        $sale = Sale::findOrFail($saleId);

        $salePaymentFound = false;
        foreach ($sale->salepayments as  $item) {

            if ($item->id == $paymentId) {
                $salePaymentFound = true;
                break;
            }
        }

        if ($salePaymentFound == false) {
            die("Invalid Access");
        }

        $payment = SalePayment::findOrFail($paymentId);


        $paymentModes = PaymentMode::all();

        $paymentModeArr = array();
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }






        return view('saleeditpayment', ['sale' => $sale, 'payment' => $payment, 'paymentmodes' => $paymentModeArr]);
    }

    public function updatepayment(Request $request, $saleId, $paymentId)
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
            $newAmount = $sale->paidprice - ($paymentUnderSale);
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
        if ($request->paymentmodeid != '5') {
            $payment->other = '';
        }

        if ($request->paymentmodeid == '2') {

            $payment->bankname = $request->bankname;
            $payment->bankaccountno = $request->bankaccountno;
            $payment->chequeno = $request->chequeno;

            if ($request->chequedate != '') {
                $dt =  explode('/', $request->chequedate);
                $payment->chequedate = $dt[2] . '-' . $dt[1] . '-' . $dt[0];
            } else {
                $payment->chequedate = null;
            }
        } else {
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
        $sale = Sale::findOrFail($id);

        $packages = Packages::all();
        $categories = Category::all();
        $clients = Client::all();
        $paymentModes = PaymentMode::all();

        $clientArr = array();
        $clientArr[0] = 'Select';
        foreach ($clients as $client) {
            if ($client->isrealclient == "1" || $client->id ==  $sale->clientid)
                $clientArr[$client->id] = $client->clientname . ' (' . $client->phone . ')';
        }

        $paymentModeArr = array();
        foreach ($paymentModes as $pMode) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }


        $staff = Staff::orderBy('firstname', 'asc')->where('activestatus', '=', '1')->get();

        $products = Product::all()->where('stockavailable', '>', '0');

        $user = Auth::user();

        return view('saleedit', ['sale' => $sale, 'categories' =>  $categories, 'packages' => $packages, 'products' => $products, 'clients' => $clientArr, 'staffs' => $staff, 'paymentmodes' => $paymentModeArr, 'user' => $user]);
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

        $validator =  Validator::make($request->all(), ['clientid' => 'required', 'ids' => 'required|array', 'qnty' => 'required|array|greator_then_zero'], $messages);

        $msg = array();
        if ($validator->fails()) {
            return redirect(route('sale.edit'))
                ->withInput()
                ->withErrors($validator);
        } else {



            if ($request->package_name > 0 && $request->clientid > 0) //
            {

                // validate package
                $package = ClientPackage::findOrFail($request->package_name);


                if ($package->isPackageCompleted() == true) {
                    $msg[] = "This package is not valid to add.";
                } else { //now compare package items or money

                    //composite package
                    $arr = array();
                    if ($package->packagetype == "1") // cash
                    {
                        $arr = $this->isValidCashPackage($package, $request, array($id));
                    } else // composite package
                    {

                        $arr = $this->isValidCompositePackage($package, $request, array($id));
                    }

                    if ('1' == $arr['iserror']) {
                        $msg[] = $arr['msg'];
                    }
                }
            }

            if (count($msg) > 0) {
                // print_r($msg);
                //die("error");
                return redirect(route('sale.edit'))
                    ->withInput()
                    ->withErrors($msg);
            }


            if ($this->calculateSaleForEdit($request, $id)) {
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
        $hsSales = new HS_Sales();
        $hsSales->deleteSaleWithId($id);
        session()->flash("successmsg", "Successfully Deleted.");
        return redirect()->back();
    }

    public function paymenthistory($id)
    {
        $sale = Sale::findOrFail($id);
        return view('salepaymenthistory', ['sale' => $sale]);
    }
}
