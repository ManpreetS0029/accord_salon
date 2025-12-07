<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExpenseMaster;
use Illuminate\Support\Facades\Validator;
use App\Expense;
use App\PaymentMode;
class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request  )
    {
	    $totalAmount = 0;
    	if( $request->input('search') == 'Search' )
	    {

		    $query = Expense::query();

		    $dateFrom = '';
		    $dateTo = '';
		    if( '' != trim($request->input('expensedatefrom')))
		    {

			    $search = true;
			    $dateArr = explode('/',$request->input('expensedatefrom') );
			    $dateFrom = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0].' 00:00:00';

			    $dateFromExcel = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
			    $dateFound = true;
			    /*if( '' == trim($request->input('dateto')) )
				{
					$dateTo = date("Y-m-d");
				} */

		    }

		    if( '' != trim($request->input('expensedateto')))
		    {
			    $dateFound = true;
			    $search = true;
			    $dateArr = explode('/',$request->input('expensedateto') );
			    $dateTo = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0].' 23:59:59';
			    $dateToExcel = $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
			    /*if( '' == trim($request->input('datefrom')) )
				{
					$dateTo = $dateFrom;
				}*/

		    }



		    if( $dateFrom != '' && $dateTo != '' )
		    {

			    $query->whereBetween('expensedate', [ $dateFrom, $dateTo ] );
		    }
		    else if( $dateFrom != ''  )
		    {
			    $query->whereDate('expensedate', '>=',  $dateFrom   );
		    }
		    else if( $dateTo != ''  )
		    {
			    $query->whereDate('expensedate', '<=',  $dateFrom   );
		    }

		    $cats = '';
		    if( $request->input('expensecatsearch') != '' )
		    {
			    $cats = '1';
			    $query->where('expensemasterid', '=',  $request->input('expensecatsearch')   );
		    }


	    $perPage = $request->get('per_page', 50);
	    if( $dateFrom == '' && $dateTo == '' && $cats == '')
	    {
		    $expenses = Expense::orderBy('id','desc')->paginate($perPage);



	    }
	    else {
		    $expenses = $query->orderBy( 'id', 'desc' )->paginate( $perPage );
			    $expensesAll = $query->orderBy('id','desc')->get();

			    //print_r($expensesAll);
			    $totalAmount = 0;
			    foreach( $expensesAll as $exp )
			    {
				    $totalAmount += $exp->amount;
			    }



		    }


    }
    else
    {
	    $perPage = $request->get('per_page', 50);
	    $expenses = Expense::orderBy('id','desc')->paginate($perPage);



    }
    
    $expenses->appends(['datefrom' => $request->datefrom, 'dateto' => $request->dateto, 'expensecatsearch' => $request->expensecatsearch, 'per_page' => $request->get('per_page', 50)]);

	    $expenseMaster = ExpenseMaster::orderBy( 'name','asc')->get();

	    $expenseMasterArr = array();
	    $expenseMasterArr[null] = 'Select';
	    foreach ( $expenseMaster as $e)
	    {
		    $expenseMasterArr[$e->id] = $e->name;
	    }

	    $paymentModeArr = array();
	    $paymentModes = PaymentMode::get();
	    foreach( $paymentModes as $modes ) {
		    $paymentModeArr[$modes->id] = $modes->name;
	    }



        return view('expenselist', ['expenses' => $expenses, 'expensemaster' => $expenseMasterArr, 'paymentmodes' => $paymentModeArr, 'totalamount' => $totalAmount ]);
    }

    public function addexpenses(Request $request)
    {
	    $messages = [
		    'expensemasterid.required' => 'Please select Expense Type.',
		    'amount.greator_then_zero' => 'Amount should be greator than zero.'

	    ];
	    //
	    $validator =  Validator::make( $request->all(), [ 'expensemasterid' => 'required', 'amount' => 'required|greator_then_zero' ], $messages );

	    if ($validator->fails()) {
		    return redirect( route('expense.index') )
			    ->withInput()
			    ->withErrors($validator);
	    }
	    else {


		    $expense                  = new Expense();
		    $expense->expensemasterid = $request->expensemasterid;
		    $expense->amount          = $request->amount;
		    $expense->remarks         = $request->remarks;
		    $expense->paymentmodeid   = $request->paymentmodeid;
		    $dtArr                    = explode( '/', $request->expensedate );
		    $expense->expensedate     = $dtArr[2] . "-" . $dtArr[1] . "-" . $dtArr[0];

		    //$sale->

		    $expense->save();
		    $request->session()->flash( "successmsg", "Successfully Added." );

		    return redirect()->back();
		    //  return redirect('sale/'.$id.'/edit');
	    }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $expenseMaster = ExpenseMaster::orderBy( 'name','asc')->get();

        $expenseMasterArr = array();
        //$expenseMasterArr[0] = 'Select';
        foreach ( $expenseMaster as $e)
        {
            $expenseMasterArr[$e->id] = $e->name;
        }

        $paymentModeArr = array();
        $paymentModes = PaymentMode::get();
        foreach( $paymentModes as $modes ) { 
            $paymentModeArr[$modes->id] = $modes->name;
        }
        
        return view( 'expenseadd', ['expensemaster' => $expenseMasterArr, 'paymentmodes' => $paymentModeArr ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

                $messages = [
            'expensemasterid.required' => 'Please select Expense Type.',
            'amount.greator_then_zero' => 'Amount should be greator than zero.'

        ];
        //
                $validator =  Validator::make( $request->all(), [ 'expensemasterid' => 'required', 'amount' => 'required|greator_then_zero' ], $messages );

        if ($validator->fails()) {
            return redirect( route('expense.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {



            $expense = new Expense();
            $expense->expensemasterid = $request->expensemasterid;
            $expense->amount = $request->amount;
            $expense->remarks = $request->remarks;
            $expense->paymentmodeid = $request->paymentmodeid;
            $dtArr = explode('/', $request->expensedate );
            $expense->expensedate = $dtArr[2]."-".$dtArr[1]."-".$dtArr[0];
            
            //$sale->

            $expense->save();
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
        $expenseMaster = ExpenseMaster::orderBy( 'name','asc')->get();

        $expenseMasterArr = array();
        //$expenseMasterArr[0] = 'Select';
        foreach ( $expenseMaster as $e)
        {
            $expenseMasterArr[$e->id] = $e->name;
        }

        
        $expense = Expense::findOrFail($id);

        $paymentModeArr = array();
        $paymentModes = PaymentMode::get();
        foreach( $paymentModes as $modes ) {
            $paymentModeArr[$modes->id] = $modes->name;
        }

              return view( 'expenseedit', ['expense' => $expense, 'expensemaster' => $expenseMasterArr, 'paymentmodes' => $paymentModeArr ]);
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
        $expense = Expense::findOrFail($id);
        
                $messages = [
            'expensemasterid.required' => 'Please select Expense Type.',
            'amount.greator_then_zero' => 'Amount should be greator than zero.'

        ];
        //
                $validator =  Validator::make( $request->all(), [ 'expensemasterid' => 'required', 'amount' => 'required|greator_then_zero' ], $messages );

        if ($validator->fails()) {
            return redirect( route('expense.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {




            $expense->paymentmodeid = $request->paymentmodeid;
            $dtArr = explode('/', $request->expensedate );
            $expense->expensedate = $dtArr[2]."-".$dtArr[1]."-".$dtArr[0];

            //            $expense = new Expense();
            $expense->expensemasterid = $request->expensemasterid;
            $expense->amount = $request->amount;
            $expense->remarks = $request->remarks;

            //$sale->

            $expense->save();
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
            //  return redirect('sale/'.$id.'/edit');
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
        $expense = Expense::findOrFail($id);
        $expense->delete();
        session()->flash("successmsg", "Successfully Deleted.");
            return redirect()->back();
        
    }
}
