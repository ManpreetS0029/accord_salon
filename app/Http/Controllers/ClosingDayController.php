<?php

namespace App\Http\Controllers;

use App\ClosingDay;
use App\CustomClasses\HS_Revenue;
use Illuminate\Http\Request;

class ClosingDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $list = ClosingDay::orderBy("dates", "Desc")->paginate(100);

        return view( 'closingdaylist',[ 'lists' => $list ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $day = ClosingDay::whereDate("dates","=", date("Y-m-d") )->first();


        return view( 'addclosingday', [ 'days' => $day]  );

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
        $dates = date("Y-m-d");
        $day = ClosingDay::whereDate("dates","=", $dates )->first();

        $hsRevenue = new HS_Revenue();
        if( $request->add_opening_balance == "add_opening_balance" || $request->update_opening_balance == "update_opening_balance" )
        {
            //add new

            if( is_array($day)  && count($day) > 0  )
            {

                if( $day->isclosed != '1')
                {

                    $day->openingbalance = $request->openingbalance;
                    $day->save();
                }

            }
            else
            {


                $day = new ClosingDay();
                $day->dates = $dates;
                $day->openingbalance = $request->openingbalance;
                $day->save();
            }


        }
        else if( $request->regenrate_closing_bal_save == "regenrate_closing_bal_save" )
        {
            if ( is_array($day) && count($day) > 0 )
            {
                if( $day->isclosed != '1' )
                {
                    $totalCash = $hsRevenue->getCashForDate($dates);
                    $closingBal = $totalCash + $day->openingbalance;
                    $day->closingbalance = $closingBal;
                    $day->isclosed = "1";
                    $day->save();
                }
            }
        }

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
