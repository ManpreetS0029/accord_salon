<?php

namespace App\Http\Controllers;

use App\Attendance;
use Illuminate\Http\Request;
use App\Staff;
use App\CustomClasses\HS_Common;
class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $staffList = Staff::all();
        $staffArr = array();
        $staffArr[null] = 'Select';
        foreach ( $staffList as $staff )
        {
            $staffArr[$staff->id] = $staff->firstname.' '.$staff->lastname.' ( '.$staff->mobile.')';
        }

        $months = array( null => 'Select', '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' );
        $year = date("Y");

        $attendanceListQuery = Attendance::Query();

        if( $request->exists('attendance_month') )
        {
            if( $request->attendance_month != ''  ) {
                $monthSelected = $request->attendance_month;
                $attendanceListQuery->whereMonth('attandance_date', '=', $monthSelected);
            }
        }
        else
        {
            $attendanceListQuery->whereMonth('attandance_date', '=', date("m") );
        }

        //year
        if( $request->exists('attendance_year') )
        {
            if( $request->attendance_year != ''  ) {
                $yearSelected = $request->attendance_year;
                $attendanceListQuery->whereYear('attandance_date', '=', $yearSelected);
            }
        }
        else
        {
            $attendanceListQuery->whereYear('attandance_date', '=', date("Y") );
        }

        if( $request->searchtext != '' )
        {
            $attendanceListQuery->where('staffid', '=', $request->searchtext );
        }


        $attendanceList = $attendanceListQuery->get();

        return view("attendancelist", ['staff' => $staffArr , 'months' => $months, 'year' => date("Y"), 'attendanceList' => $attendanceList   ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $staffList = Staff::all();
        $staffArr = array();
        $staffArr[null] = 'Select';
        foreach ( $staffList as $staff )
        {
            $staffArr[$staff->id] = $staff->firstname.' '.$staff->lastname.' ( '.$staff->mobile.')';
        }



        $attendanceTypes = array( '1' => 'Present', '2' => 'Half Day', '4' => 'Absent', '5' => 'Holiday'  );



        return view("attendanceadd", ['staff' => $staffArr, 'attendanceTypes' => $attendanceTypes  ]);

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
        $msg = array();
        if( trim($request->staffname) == ''  )
        {
            $msg[] = 'Staff is required.';
        }

        if( trim($request->attendance_date) == ''  )
        {
            $msg[] = 'Attendance date is required.';
        }
        if( trim($request->attendance) == ''  )
        {
            $msg[] = 'Attendance is required.';
        }
        if( count($msg) > 0 )
        {
            return redirect( route('attendance.create') )
                ->withInput()
                ->withErrors($msg);
        }

        $attendanceDate = HS_Common::extractDateAsString( $request->attendance_date );

        $res = Attendance::whereDate('attandance_date', '=', $attendanceDate )->where('staffid', '=',$request->staffname )->first();

        if( $res && $res->staffid > 0 ) {
            return redirect(route('attendance.create'))
                ->withInput()
                ->withErrors(array('Attendance already exists for selected date.'));
        }
        $attendanceObj = new Attendance();
        $attendanceObj->attandence = $request->attendance;
        $attendanceObj->staffid =  $request->staffname;
        $attendanceObj->attandance_date =  $attendanceDate;
        $attendanceObj->save();
        $request->session()->flash("successmsg", "Successfully Added.");
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
        $attendance = Attendance::findOrFail($id);
        if( $request->present == "present" )
        {
            $attendance->attandence = "Present";
        }
        else if( $request->absent == "absent" )
        {
            $attendance->attandence = "Absent";
        }
        else if( $request->halfday == "halfday" )
        {
            $attendance->attandence = "Half Day";
        }
        else if( $request->holiday == "holiday" )
        {
            $attendance->attandence = "Holiday";
        }
        $attendance->save();
        session()->flash("successmsg", "Successfully Updated.");
        return redirect()->back();
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

        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        session()->flash("successmsg", "Successfully Deleted.");
        return redirect()->back();
    }
}
