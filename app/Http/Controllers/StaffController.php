<?php

namespace App\Http\Controllers;

use App\ClientPackage;
use App\CustomClasses\HS_Common;
use App\CustomClasses\HS_Sales;
use App\Staff;
use App\Salary;
use App\Attendance;
use App\ServiceDoneStaff;
use App\SaleItem;
use App\Sale;
use App\StaffSalaryIncrement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use stdClass;


class StaffController extends Controller
{

    /* function __construct()
     {
        // echo Route::getCurrentRoute()->getActionName();
     }*/

    public function updateattendance(Request $request)
    {


        $attendance = '';
        if ($request->present != '') {
            $attendance = 'Present';
        } else if ($request->halfday != '') {
            $attendance = 'Half Day';
        } else if ($request->absent != '') {
            $attendance = 'Absent';
        } else if ($request->holiday != '') {
            $attendance = 'Holiday';
        }

        if ($attendance != '') {
            if ($request->attendance_date != '') {
                $attendanceDate = HS_Common::extractDateAsString($request->attendance_date);
            } else {
                $attendanceDate = date("Y-m-d");
            }

            if ($request->group_save_attendance != '') {

                $arrStaffIds = explode(',', $request->group_save_attendance);

                $attenDanceExist = Attendance::whereDate("attandance_date", "=", $attendanceDate)->whereIn('staffid', $arrStaffIds)->get();

                foreach ($arrStaffIds as $id) {
                    if ($id > 0) {
                        $attendanceObj = null;
                        foreach ($attenDanceExist as $attendanceRecord) {
                            if ($attendanceRecord->staffid == $id) {
                                $attendanceObj = $attendanceRecord;
                                break;
                            }
                        }
                        if ($attendanceObj == null) {
                            $attendanceObj = new Attendance();
                        }
                        $attendanceObj->staffid = $id;
                        $attendanceObj->attandence = $attendance;
                        $attendanceObj->attandance_date = $attendanceDate;
                        $attendanceObj->save();

                    }
                }
            }

        }
        $request->session()->flash("successmsg", "Successfully Updated.");
        return redirect()->back();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        //id=1&act=attn&atype=Half%20Day
        if ($request->act == "attn" && $request->id > 0 && $request->atype != "") {
            $attenDance = Attendance::whereDate("attandance_date", "=", date("Y-m-d"))->where('staffid', '=', $request->id)->first();


            if (!$attenDance) {
                $attenDance = new Attendance();
            }

            $attenDance->staffid = $request->id;
            $attenDance->attandence = $request->atype;
            $attenDance->attandance_date = date("Y-m-d");
            $attenDance->save();
        }

        $staff = Staff::paginate(50);


        return view("stafflist", ['staffmembers' => $staff]);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('staffadd');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //update salary info

        //


        $rules = ['firstname' => 'required', 'lastname' => 'required', 'gender' => 'required', 'mobile' => 'required', 'designation' => 'required', 'salary' => 'required', 'commission' => 'required', 'fromdate' => 'required|date_format:d/m/Y'
        ];

        if (!empty($request->input('dob'))) {
            $rules['dob'] = 'date_format:d/m/Y';
        }

        if (!empty($request->input('hiringdate'))) {
            $rules['hiringdate'] = 'date_format:d/m/Y';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect(route('staff.create'))
                ->withInput()
                ->withErrors($validator);
        } else {
            $staff = new Staff();
            $staff->firstname = $request->firstname;
            $staff->lastname = $request->lastname;

            if (!empty($request->input('dob'))) {
                $dt = explode('/', $request->dob);
                $staff->dob = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            }

            if (!empty($request->input('hiringdate'))) {
                $dt = explode('/', $request->hiringdate);
                $staff->hiringdate = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            }


            $staff->gender = $request->gender;
            $staff->address = $request->address;
            $staff->email = $request->email;
            $staff->mobile = $request->mobile;
            $staff->phone = $request->phone;
            $staff->designation = $request->designation;

            $staff->idprooftype = $request->idprooftype;
            $staff->idproofvalue = $request->idproofvalue;

            $staff->save();

            $id = $staff->id;
            $salary = new StaffSalaryIncrement();
            $salary->staffid = $id;
            $salary->salary = $request->salary;
            $salary->commission = $request->commission;
            $dtFrom = explode('/', $request->fromdate);
            $salary->fromdate = $dtFrom[2] . '-' . $dtFrom[1] . '-01';
            $salary->save();

            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $staffMember = Staff::findOrFail($id);

        //, 'months' => $months 
        $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');


        return view('staffdetails', ['staffmember' => $staffMember, 'months' => $months]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $staffMember = Staff::findOrFail($id);


        return view('staffedit', ['staffmember' => $staffMember]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //
        $staff = Staff::findOrFail($id);

        if ('1' == $request->input('update_salary')) {
            $rules = ['salary' => 'required', 'commission' => 'required', 'fromdate' => 'required|date_format:d/m/Y'];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect(route('staff.edit', $staff->id))
                    ->withInput()
                    ->withErrors($validator);
            } else {

                //check if selected month salary is already there so update it otherwise insert new
                $allSalaries = $staff->salaryIncrementInfo;
                $fromDate = explode('/', $request->fromdate);
                $day = '1';
                $months = $fromDate[1];
                $years = $fromDate[2];
                $found = false;

                foreach ($allSalaries as $salary) {

                    $addedMonth = date("m", strtotime($salary->fromdate));
                    $addedYear = date('Y', strtotime($salary->fromdate));
                    //that means this given months salary is already added so need to update it
                    if ($addedMonth == $months && $addedYear == $years) {
                        $found = true;
                        $salary->salary = $request->salary;
                        $salary->commission = $request->commission;
                        $salary->save();
                        break;
                    }

                }
                StaffSalaryIncrement::whereDate('fromdate', '>', $years . '-' . $months . '-01')->delete();

                if ($found == true) {

                } else {
                    $staff = Staff::findOrFail($id);

                    //$latestSalary = $staff->getLatestSalary();
                    //print_r($latestSalary);
                    if ($months == 1) {
                        $addMonth = 12;
                        $addYear = $years - 1;
                    } else {
                        $addMonth = $months - 1;
                        $addYear = $years;
                    }

                    /*if( $latestSalary ) {
                     $latestSalary->todate = $addYear.'-'.$addMonth.'-01';
                    $latestSalary->save();
                    } */
                    //add new Salary
                    $salary = new StaffSalaryIncrement();
                    $salary->staffid = $staff->id;
                    $salary->salary = $request->salary;
                    $salary->commission = $request->commission;
                    $salary->fromdate = $years . '-' . $months . '-01';
                    $salary->save();
                }

                $request->session()->flash("successmsg", "Successfully Updated.");
                return redirect()->back();

            }
            //$staff->save();

        } else {

            $rules = ['firstname' => 'required', 'lastname' => 'required', 'gender' => 'required', 'mobile' => 'required', 'designation' => 'required', 'activestatus' => 'required'
            ];

            if (!empty($request->input('dob'))) {
                $rules['dob'] = 'date_format:d/m/Y';
            }

            if (!empty($request->input('hiringdate'))) {
                $rules['hiringdate'] = 'date_format:d/m/Y';
            }


            $staff->firstname = $request->firstname;
            $staff->lastname = $request->lastname;

            if (!empty($request->input('dob'))) {
                $dt = explode('/', $request->dob);
                $staff->dob = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            } else {
                $staff->dob = null;
            }

            if (!empty($request->input('hiringdate'))) {
                $dt = explode('/', $request->hiringdate);
                $staff->hiringdate = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            } else {
                $staff->hiringdate = null;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect(route('staff.edit', $staff->id))
                    ->withInput()
                    ->withErrors($validator);
            } else {


                $staff->gender = $request->gender;
                $staff->address = $request->address;
                $staff->email = $request->email;
                $staff->mobile = $request->mobile;
                $staff->phone = $request->phone;
                $staff->designation = $request->designation;

                $staff->idprooftype = $request->idprooftype;
                $staff->idproofvalue = $request->idproofvalue;
                $staff->activestatus = $request->activestatus;
                $staff->save();
                $request->session()->flash("successmsg", "Successfully Updated.");
                return redirect()->back();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Staff Sale Details
     */

    public function saledetails(Request $request)
    {
        $id = $request->id;
        $staff = Staff::findOrFail($id);

        $query = Sale::query();

        $dateFromExcel = '';
        $dateToExcel = '';
        $dateFound = false;
        $dateFrom = '';
        $dateTo = '';
        if ('' != trim($request->input('datefrom'))) {
            $search = true;
            $dateArr = explode('/', $request->input('datefrom'));
            $dateFrom = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 00:00:00';

            $dateFromExcel = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
            $dateFound = true;
            /*if( '' == trim($request->input('dateto')) )
            {
                $dateTo = date("Y-m-d");
            } */

        }

        if ('' != trim($request->input('dateto'))) {
            $dateFound = true;
            $search = true;
            $dateArr = explode('/', $request->input('dateto'));
            $dateTo = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 23:59:59';
            $dateToExcel = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0];
            /*if( '' == trim($request->input('datefrom')) )
            {
                $dateTo = $dateFrom;
            }*/

        }

        if ($dateFound == false) {
            $dateFrom = date("Y-m-d") . ' 00:00:00';
            $dateTo = date("Y-m-d") . ' 23:59:59';

            $dateFromExcel = date("Y-m-d");
            $dateToExcel = date("Y-m-d");

        }

        /* if( $dateFrom == '' && $dateTo == '' )
        {
             $search = true;
            $dateFrom = date("Y-m-d")." 00:00:00";
            $dateTo = date("Y-m-d")." 23:59:59";
        } */

        if ($dateFrom != '' && $dateTo != '') {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } else if ($dateFrom != '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        } else if ($dateTo != '') {
            $query->whereDate('created_at', '<=', $dateFrom);
        }
        $sales = $query->orderBy('created_at', 'desc')->get();


        $allDoneByStaffMember = array();
        $incentive = 0;

        $x = 0;
        $totalSaleDoneByStaff = 0;
        $currentMemberProductSale = 0;
        $currentMemberSale = 0;
        $arrayData = array();
        $arrayData[] = array("Sale ID", "Item Name", "Sale Date", "Total Price", "Staff Done Amount");

        foreach ($sales as $sale) {

            $items = $sale->saleItem;

            //$isClientPackage = $sale->ispackage;

            $clientPackageId = $sale->packageid;
            $ratios = 1;

            if ($clientPackageId > 0) {
                $package = ClientPackage::find($clientPackageId);

                if ($package->packagetype == '1') {

                    $actPrice = $package->actualprice;
                    $giftPrice = $package->giftedprice;

                    $ratios = $actPrice / $giftPrice;
                }

            }


            foreach ($items as $item) {
                $totalDoneStaff = 0;
                $userCount = 0;

                if ($item->itemtype == "package") {
                    $packageItems = $item->packageItems;
                    foreach ($packageItems as $packageItem) {

                        //$itemDoneByMembers = $packageItem->doneByStaffMembers;
                        $arr = $this->getDoneCountForStaffMember($packageItem, $id);
                        if (is_array($arr)) {
                            $totalDoneStaff += $arr['totalmembers'];
                            $userCount += $arr['usercount'];

                        }
                        $saleItemIds[] = $packageItem->id;
                    }
                } else {
                    $arr = $this->getDoneCountForStaffMember($item, $id);
                    if (is_array($arr)) {
                        $totalDoneStaff += $arr['totalmembers'];
                        $userCount += $arr['usercount'];
                    }


                }

                if ($userCount > 0 && $totalDoneStaff > 0) {
                    //echo $ratios.'==';
                    $userDoneAmount = ($item->getTotalPrice() * $ratios) / $totalDoneStaff * $userCount;
                    if ($item->itemtype == "product") {
                        $currentMemberProductSale += $userDoneAmount;
                    } else {

                        $currentMemberSale += $userDoneAmount;
                    }


                    $allDoneByStaffMember[$x]['saleid'] = $sale->id;
                    $allDoneByStaffMember[$x]['date'] = $sale->created_at;
                    $allDoneByStaffMember[$x]['itemtype'] = $item->itemtype;
                    $allDoneByStaffMember[$x]['userdoneamount'] = $userDoneAmount;
                    $allDoneByStaffMember[$x]['totalamount'] = $item->getTotalPrice();
                    $allDoneByStaffMember[$x]['itemid'] = $item->id;
                    $allDoneByStaffMember[$x]['itemtitle'] = $item->title;


                    $arrayData[] = array($sale->id, $item->title, $sale->created_at, $item->getTotalPrice(), $userDoneAmount);

                    $x++;
                    //if( $item->itemtype != "product" )
                    {
                        $totalSaleDoneByStaff += $userDoneAmount;
                    }

                }

            }
        }

        if ($totalSaleDoneByStaff > 0) {
            $incentive = $totalSaleDoneByStaff * 0.05;
        }


        if ($request->exportdata == '1') {

            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("", "", "", "", "", "", "");
            $arrayData[] = array("Total Amount", "Incentive Amount", "Total Product Sale Amount", "Total Services Amount");
            $arrayData[] = array(number_format($totalSaleDoneByStaff, 2), number_format($incentive, 2), number_format($currentMemberProductSale, 2), number_format($currentMemberSale, 2));
            HS_Common::array_to_csv_download($arrayData, "sale_" . $staff->firstname . '_' . $staff->lastname . '-' . $dateFromExcel . "_TO_" . $dateToExcel . ".csv", ",");
            exit(0);

        }

        return view('staffdonetasklist', ['staffsale' => $allDoneByStaffMember, 'totalamount' => $totalSaleDoneByStaff, 'staff' => $staff, 'incentive' => $incentive, 'totalproductsale' => $currentMemberProductSale, 'totalservicessale' => $currentMemberSale]);


        //   print_r($allDoneByStaffMember);
        /*if( count($saleItemIds) > 0  )
        {
            $query =  ServiceDoneStaff::query();
            $query->where("staffid","=", $id);
            $query->whereIn("saleitemid", $saleItemIds );
            $doneItems = $query->get();

            //        print_r($doneItems);
            $itemIds = array();
        
            foreach ( $doneItems as $item ) {
                $itemIds[] = $item->saleitemid;
                //print_r($item->saleitem->title);
                //echo "<br />";
            }

            if( count($itemIds) > 0 )
            {
                $queryItemDoneCounts = DB::table('saleservicedoneby')
                                     ->select(DB::raw('count(id) as countsdone, saleitemid '))
                                     ->whereIn('saleitemid',  $itemIds )
                                     ->groupBy('saleitemid')
                                     ->get();

                print_r($queryItemDoneCounts);
            }
        
        } */

    }

    protected function isUserExistInDonBy($doneByMembers, $userId)
    {
        foreach ($doneByMembers as $item) {
            if ($item->staffid == $userId) {
                return true;
            }
        }

        return false;

    }

    protected function getDoneCountForStaffMember($packageItem, $userId, $isPackageItem = false)
    {
        $itemDoneByMembers = $packageItem->doneByStaffMembers;

        if ($this->isUserExistInDonBy($itemDoneByMembers, $userId)) {
            $userCount = 0;
            foreach ($itemDoneByMembers as $doneBy) {
                if ($doneBy->staffid == $userId) {
                    $userCount++;
                }

            }
            return ['totalmembers' => count($itemDoneByMembers), 'usercount' => $userCount];
        } else if ($packageItem->saleid == 0) // means package item so members should count
        {
            return ['totalmembers' => count($itemDoneByMembers), 'usercount' => 0];
        }


        return false;
    }


    public function salegraph(Request $request)
    {
        $staffMembers = Staff::where('activestatus', '=', '1')->get();
        $dateFrom = '';
        $dateTo = '';
        $search = false;
        $queryAppendString = '';
        $queryAppendParams = [];
        if ('' != trim($request->input('datefrom'))) {
            $search = true;
            $dateArr = explode('/', $request->input('datefrom'));
            $dateFrom = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 00:00:00';
        }

        if ('' != trim($request->input('dateto'))) {
            $search = true;
            $dateArr = explode('/', $request->input('dateto'));
            $dateTo = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 23:59:59';
        }


        if ($dateFrom != '' && $dateTo != '') {
            $queryAppendString .= ' DATE(SL.created_at) BETWEEN ? AND ? ';
            $queryAppendParams[] =  $dateFrom;
            $queryAppendParams[] =  $dateTo;
        } elseif ($dateFrom != '') {
            $queryAppendString .= ' SL.created_at >= ? ';
            $queryAppendParams[] =  $dateFrom;
        } elseif ($dateTo != '') {
            $queryAppendString .= ' SL.created_at <= ? ';
            $queryAppendParams[] =  $dateTo;
        }

        $queryMain = 'SELECT SL.*, SLI.id as saleItemId, SLI.itemtype as saleItemItemType,
                    SLI.actualpriceperitem as saleItemActualpriceperitem,
                    SLI.quantity as saleItemQuantity, 
                    SLI.discounttype as saleItemDiscounttype,
                    SLI.discountvalue as saleItemDiscountvalue,
                    SLI.parentid as saleItemParentid
                FROM sale AS SL 
                INNER JOIN  saleitems AS SLI ON  SLI.saleid = SL.id';


        if ($dateFrom == '' && $dateTo == '') {
            if ('' != trim($request->input('month'))) {
                $search = true;
                $queryAppendString = ' MONTH(SL.created_at) = ? AND  YEAR(SL.created_at) = ? ';
                $queryAppendParams[] = $request->input('month');
                if ('' != trim($request->input('years'))) {
                    $queryAppendParams[] = trim($request->input('years'));
                 } else {
                    $queryAppendParams[] = date('Y');
                }

            } elseif ('' != trim($request->input('years'))) {
                $search = true;
                $queryAppendString = ' YEAR(SL.created_at) = ? ';
                $queryAppendParams[] = $request->input('years');
            }

            if ('' == trim($request->input('years')) && '' == trim($request->input('month'))) {
                $search = true;
                $queryAppendString = ' DATE(SL.created_at) = ? ';
                $queryAppendParams[] = date("Y-m-d");
            }
        }

        if ($search == false) {
            $search = true;
            $dateFrom = date("Y-m-d") . " 00:00:00";
            $dateTo = date("Y-m-d") . " 23:59:59";

            $queryAppendString .= ' SL.created_at BETWEEN ? AND ? ';
            $queryAppendParams[] =  $dateFrom;
            $queryAppendParams[] =  $dateTo;
        }

        $results = DB::select($queryMain . ($queryAppendString != '' ? ' WHERE ' : '')
            . ' ' . $queryAppendString, $queryAppendParams);
        $arrMainSales = [];
        $arrSaleItems = [];
        $packageList = [];
        $itemPackages = [];
        $normalItemsIds = [];

        foreach ($results as $row) {
            $mainSaleClass = new stdClass();
            $mainSaleClass->id = $row->id;
            $mainSaleClass->clientid = $row->clientid;
            $mainSaleClass->packageid = $row->packageid;
            $arrMainSales[$row->id] = $mainSaleClass;

            if (($row->saleItemItemType === 'package' || $row->saleItemParentid <= 0) && $row->saleItemId > 0) {
                $itemClass = new stdClass();
                $itemClass->id = $row->saleItemId;
                $itemClass->saleid = $row->id;
                $itemClass->itemtype = $row->saleItemItemType;
                $itemClass->actualpriceperitem = $row->saleItemActualpriceperitem;
                $itemClass->quantity = $row->saleItemQuantity;
                $itemClass->discounttype = $row->saleItemDiscounttype;
                $itemClass->discountvalue = $row->saleItemDiscountvalue;
                $itemClass->parentid = $row->saleItemParentid;
                $arrSaleItems[$row->id][] = $itemClass;
            }

            if ($row->saleItemId > 0) {
                if ($row->saleItemItemType === 'package') {
                    $itemPackages[] = $row->saleItemId;
                } elseif ($row->saleItemParentid <= 0){
                    $normalItemsIds[] = $row->saleItemId;
                }
            }

            if ($row->packageid > 0) {
                $packageList[] = $row->packageid;
            }
        }

        if (!empty($packageList)) {
           $clientPackages = collect(DB::select("SELECT actualprice, giftedprice FROM tblclientpackage WHERE id IN (" .
                implode(",", $packageList). ") "))->keyBy('id');
        }

        $allPackageItems = [];
        if (!empty($itemPackages)) {
            $packageItems = DB::select("select * from saleitems WHERE parentid IN (" . implode(",", $itemPackages) . ") ");
            foreach ($packageItems as $packageItem) {
                $allPackageItems[$packageItem->parentid][] = $packageItem;
                $normalItemsIds[] = $packageItem->id;
            }
        }


        $itemsDoneBy = [];

        if (!empty($normalItemsIds)) {
            foreach (array_chunk($normalItemsIds, 5000) as $chunkNormalIds) {
                $itemsDone = DB::select("SELECT saleitemid, staffid FROM saleservicedoneby 
                WHERE saleitemid IN (" . implode(",", $chunkNormalIds) . ") ");
                foreach ($itemsDone as $item) {
                    $itemsDoneBy[$item->saleitemid][] = $item;
                }
            }
        }

        $totalSaleByStaff = 0;
        $totalIncentiveByStaff = 0;
        $totalProductSale = 0;
        foreach ($staffMembers as $staff) {
            $id = $staff->id;
            $currentMemberSale = 0;
            $currentMemberIncentive = 0;
            $currentMemberProductSale = 0;

            foreach ($arrMainSales as $saleId => $row ) {
                $ratios = 1;
                if ($row->packageid > 0 && !empty($clientPackages[$row->packageid])) {
                    $package = $clientPackages[$row->packageid];
                    if ($package->packagetype == '1') {
                        $actPrice = $package->actualprice;
                        $giftedPrice = $package->giftedprice;
                        $ratios = $actPrice / $giftedPrice;
                    }
                }

                $items = $arrSaleItems[$saleId] ?? [];

                if (empty($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    $totalDoneStaff = 0;
                    $userCount = 0;
                    $productSale = 0;

                    if ($item->itemtype == "package") {
                        $packageItems = $allPackageItems[$item->id];
                        foreach ($packageItems as $packageItem) {
                            $packageItem->doneByStaffMembers = $itemsDoneBy[$packageItem->id] ?? [];
                            $arr = $this->getDoneCountForStaffMember($packageItem, $id);
                            if (is_array($arr)) {
                                $totalDoneStaff += $arr['totalmembers'];
                                $userCount += $arr['usercount'];
                            }
                            $saleItemIds[] = $packageItem->id;
                        }
                    } else {
                        $item->doneByStaffMembers = $itemsDoneBy[$item->id] ?? [];
                        $arr = $this->getDoneCountForStaffMember($item, $id);
                        if (is_array($arr)) {
                            $totalDoneStaff += $arr['totalmembers'];
                            $userCount += $arr['usercount'];
                        }
                    }

                    if ($userCount > 0 && $totalDoneStaff > 0) {
                        $totalPrice = HS_Sales::getSaleItemTotalPrice(
                            $item->actualpriceperitem,
                            $item->quantity,
                            $item->discounttype ?? '',
                            $item->discountvalue
                        );

                        $userDoneAmount = ($totalPrice * $ratios) / $totalDoneStaff * $userCount;
                        if ($item->itemtype == "product") {
                            $currentMemberProductSale += $userDoneAmount;
                        } else {
                            $currentMemberSale += $userDoneAmount;
                        }
                    }
                }
            }

            $staff->totalMemberSale = $currentMemberSale;
            $staff->totalMemberProductSale = $currentMemberProductSale;
            $totalSaleByStaff += $currentMemberSale;
            $totalProductSale += $currentMemberProductSale;
            if ($currentMemberSale > 0) {
                //$staff->product
                $staff->totalIncentive = $currentMemberSale * 0.05;
                $totalIncentiveByStaff += $staff->totalIncentive;
            }
        }

        return view(
            'staffsalegraph',
            ['staff' => $staffMembers, 'incentive' => $totalIncentiveByStaff, 'totalamount' => $totalSaleByStaff,
                'totalProductSale' => $totalProductSale
            ]
        );
    }

    public function salegraph_old(Request $request)
    {
        //$q->whereDay('created_at', '=', date('d'));
        //$q->whereMonth('created_at', '=', date('m'));
        //$q->whereYear('created_at', '=', date('Y'));


        $staffMembers = Staff::where('activestatus', '=', '1')->get();

        $query = Sale::query();

        $dateFrom = '';
        $dateTo = '';
        $search = false;
        if ('' != trim($request->input('datefrom'))) {

            $search = true;
            $dateArr = explode('/', $request->input('datefrom'));
            $dateFrom = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 00:00:00';

        }

        if ('' != trim($request->input('dateto'))) {
            $search = true;
            $dateArr = explode('/', $request->input('dateto'));
            $dateTo = $dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0] . ' 23:59:59';

        }

        if ($dateFrom != '' && $dateTo != '') {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } else if ($dateFrom != '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        } else if ($dateTo != '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }


        if ($dateFrom == '' && $dateTo == '') {
            if ('' != trim($request->input('month'))) {
                $search = true;
                $query->whereMonth('created_at', '=', $request->input('month'));
            }

            if ('' != trim($request->input('years'))) {
                $search = true;
                $query->whereYear('created_at', '=', $request->input('years'));
            }

            if ('' == trim($request->input('years')) && '' == trim($request->input('month'))) {
                $search = true;
                $query->whereDate('created_at', '=', date("Y-m-d"));
            }

        }

        if ($search == false) {
            $search = true;
            $dateFrom = date("Y-m-d") . " 00:00:00";
            $dateTo = date("Y-m-d") . " 23:59:59";

            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        $sales = $query->get();


        $totalSaleByStaff = 0;
        $totalIncentiveByStaff = 0;

        foreach ($staffMembers as $staff) {

            $id = $staff->id;
            $currentMemberSale = 0;
            $currentMemberIncentive = 0;
            $currentMemberProductSale = 0;
            foreach ($sales as $sale) {
                $items = $sale->saleItem;
                // $isClientPackage = $sale->ispackage;
                $clientPackageId = $sale->packageid;
                $ratios = 1;
                if ($clientPackageId > 0) {
                    $package = ClientPackage::find($clientPackageId);
                    if ($package) {

                        if ($package->packagetype == '1') {
                            $actPrice = $package->actualprice;
                            $giftedPrice = $package->giftedprice;

                            $ratios = $actPrice / $giftedPrice;

                        }

                    }
                }
                foreach ($items as $item) {
                    $totalDoneStaff = 0;
                    $userCount = 0;
                    $productSale = 0;

                    if ($item->itemtype == "package") {
                        $packageItems = $item->packageItems;
                        foreach ($packageItems as $packageItem) {
                            //$itemDoneByMembers = $packageItem->doneByStaffMembers;
                            $arr = $this->getDoneCountForStaffMember($packageItem, $id);
                            if (is_array($arr)) {
                                $totalDoneStaff += $arr['totalmembers'];
                                $userCount += $arr['usercount'];

                            }
                            $saleItemIds[] = $packageItem->id;
                        }
                    } else {
                        $arr = $this->getDoneCountForStaffMember($item, $id);
                        if (is_array($arr)) {

                            $totalDoneStaff += $arr['totalmembers'];
                            $userCount += $arr['usercount'];
                        }


                    }


                    if ($userCount > 0 && $totalDoneStaff > 0) {

                        $userDoneAmount = ($item->getTotalPrice() * $ratios) / $totalDoneStaff * $userCount;


                        if ($item->itemtype == "product") {
                            $currentMemberProductSale += $userDoneAmount;
                        } else {
                            $currentMemberSale += $userDoneAmount;
                        }

                    }


                }

            }

            $staff->totalMemberSale = $currentMemberSale;
            $staff->totalMemberProductSale = $currentMemberProductSale;
            $totalSaleByStaff += $currentMemberSale;
            if ($currentMemberSale > 0) {
                //$staff->product
                $staff->totalIncentive = $currentMemberSale * 0.05;
                $totalIncentiveByStaff += $staff->totalIncentive;
            }

        }


        /*foreach( $staffMembers as $member ) {
            echo $member->firstname.' '.$member->lastname.' == '.$member->totalMemberSale.' == '.$member->totalIncentive;
            echo "<br />";
        }*/

        return view('staffsalegraph', ['staff' => $staffMembers, 'incentive' => $totalIncentiveByStaff, 'totalamount' => $totalSaleByStaff]);


    }

}
