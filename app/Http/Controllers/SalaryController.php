<?php

namespace App\Http\Controllers;

use App\CustomClasses\HS_Common;
use App\Staff;
use App\CustomClasses\HS_Staff;
use App\StaffSalaryPaid;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        $months = array(null => 'Select', '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
        $year = date("Y");

        $paymentModes = HS_Common::getPaymentModeDropDownVals();

        $salaryPaidQuery = StaffSalaryPaid::Query();

        $staff = Staff::all();
        $staffSelectBox = array();
        $staffSelectBox[null] = 'Select Staff Member';
        foreach ($staff as $item) {
            $staffSelectBox[$item->id] = $item->getName();
        }


        if ($request->salary_month != '') {
            $salaryPaidQuery->where('months', '=', intval($request->salary_month));
        }

        if ($request->salary_year != '') {
            $salaryPaidQuery->where('years', '=', intval($request->salary_year));
        }

        if ($request->searchtext != '') {
            $salaryPaidQuery->where('staffid', '=', intval($request->searchtext));
        }

        $rs = $salaryPaidQuery->get();
        return view("salarypaidlist", ['salarypaidlist' => $rs, 'months' => $months, 'years' => $year, 'paymentmodes' => $paymentModes, 'staff' => $staffSelectBox]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $staffList = Staff::all();
        $staffArr = array();
        $staffArr[null] = 'Select';
        foreach ($staffList as $staff) {
            $staffArr[$staff->id] = $staff->firstname . ' ' . $staff->lastname . ' ( ' . $staff->mobile . ')';
        }

        $months = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
        $year = date("Y");
        $staffId = 0;



        //$staffMember->month = '08';
        // $staffMember->year = '2018';

        //echo $hsStaff->calculateMonthSalary(  $staffId,'08', '2018' );
        //  $data = $staffMember->getAttendanceForMonth;
        //print_r($data);

        $paymentModes = HS_Common::getPaymentModeDropDownVals();

        return view("staffsalaryadd", ['staff' => $staffArr, 'months' => $months, 'year' => date("Y"), 'paymentmodes' => $paymentModes]);
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
        //
        $msg = array();
        if ($request->staffname == '') {
            $msg[] = 'Staff is reuired.';
        }
        if ($request->salary_month == '') {
            $msg[] = 'Month is reuired.';
        }

        if ($request->salary_year == '') {
            $msg[] = 'Year is reuired.';
        }

        if ($request->paymentmodeid == '') {
            $msg[] = 'Payment Mode is reuired.';
        }



        if ($request->salary_amount <= 0) {
            $msg[] = 'Amount is reuired.';
        }

        if (count($msg) > 0) {
            return redirect(route('salary.create'))
                ->withInput()
                ->withErrors($msg);
        }

        //time to add salary
        $salaryPaid = new StaffSalaryPaid();
        $salaryPaid->staffid = $request->staffname;
        $salaryPaid->amount = $request->salary_amount;
        $salaryPaid->months = $request->salary_month;
        $salaryPaid->years = $request->salary_year;
        $salaryPaid->remarks = $request->remarks;
        $salaryPaid->paymentmodeid = $request->paymentmodeid;
        $salaryPaid->save();
        $request->session()->flash("successmsg", "Successfully Saved.");
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
        $staffList = Staff::all();
        $staffArr = array();
        $staffArr[null] = 'Select';
        foreach ($staffList as $staff) {
            $staffArr[$staff->id] = $staff->firstname . ' ' . $staff->lastname . ' ( ' . $staff->mobile . ')';
        }

        $months = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
        $year = date("Y");

        $salaryPaidDetails = StaffSalaryPaid::findOrFail($id);

        $paymentModes = HS_Common::getPaymentModeDropDownVals();

        return view("staffsalaryedit", ['staff' => $staffArr, 'months' => $months, 'year' => date("Y"), 'paymentmodes' => $paymentModes, 'salaryPaidDetails' => $salaryPaidDetails]);
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

        $msg = array();
        if ($request->staffname == '') {
            $msg[] = 'Staff is reuired.';
        }
        if ($request->salary_month == '') {
            $msg[] = 'Month is reuired.';
        }

        if ($request->salary_year == '') {
            $msg[] = 'Year is reuired.';
        }

        if ($request->paymentmodeid == '') {
            $msg[] = 'Payment Mode is reuired.';
        }



        if ($request->salary_amount <= 0) {
            $msg[] = 'Amount is reuired.';
        }

        if (count($msg) > 0) {
            return redirect(route(['salary.edit', $id]))
                ->withInput()
                ->withErrors($msg);
        }

        $salaryPaid = StaffSalaryPaid::findOrFail($id);
        $salaryPaid->amount = $request->salary_amount;
        $salaryPaid->months = $request->salary_month;
        $salaryPaid->years = $request->salary_year;
        $salaryPaid->paymentmodeid = $request->paymentmodeid;
        $salaryPaid->remarks = $request->remarks;
        $salaryPaid->save();
        // $request->session()->flash("successmsg", "Successfully Updated.");
        //   return redirect()->back();

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

        $salaryPaid = StaffSalaryPaid::findOrFail($id);
        $salaryPaid->delete();

        session()->flash("successmsg", "Successfully Deleted.");
        return redirect()->back();
    }
}
