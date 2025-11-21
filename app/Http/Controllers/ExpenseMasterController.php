<?php

namespace App\Http\Controllers;

use App\ExpenseMaster;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class ExpenseMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $expenses = ExpenseMaster::orderBy('name', 'asc') -> paginate(100);

        return view( 'expensemasterlist',['expenses' => $expenses] );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('expensemasteradd');
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

        $validator =  Validator::make( $request->all(), [ 'name' => 'required' ] );

        if ($validator->fails()) {
            return redirect( route('expensemaster.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {



            $expenseMaster = new ExpenseMaster();
            $expenseMaster->name = $request->name;
            //$sale->
            $expenseMaster->description = $request->description;
            $expenseMaster->save();
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
        $expense = ExpenseMaster::findOrFail($id);
        return view('expensemasteredit',['expense' => $expense ]);
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
        $expenseMaster = ExpenseMaster::findOrFail($id);
        
        $validator =  Validator::make( $request->all(), [ 'name' => 'required' ] );

        if ($validator->fails()) {
            return redirect( route('expensemaster.edit') )
                ->withInput()
                ->withErrors($validator);
        }
        else {



            //            $expenseMaster = new ExpenseMaster();
            $expenseMaster->name = $request->name;
            //$sale->
            $expenseMaster->description = $request->description;
            $expenseMaster->save();
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
    }
}
