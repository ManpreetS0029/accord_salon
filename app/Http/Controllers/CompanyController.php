<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;
use App\City;
use App\Company;
use App\CompanyContactPersons;

use Illuminate\Support\Facades\Validator;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $companies = Company::orderBy('companyname','asc')->paginate(50);
        return view('companylist', ['companies' => $companies]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $states = State::orderBy('name', 'asc')->get();
        $arrStates = array();
        $arrStates[null] = "Select";
         foreach( $states as $k => $item ) { 
             $arrStates[$item->id] = $item->name;
         }
        
         return view('companyadd', ['states' => $arrStates ]);
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
                    'companyname.required' => 'Company Name is required.',
            'name.required' => 'Contact person name is required.'

        ];
        //
                $validator =  Validator::make( $request->all(), [ 'companyname' => 'required', 'address' => 'required', 'state' => 'required', 'cityid' => 'required', 'name' => 'required', 'phone' => 'required' ], $messages );

        if ($validator->fails()) {
            return redirect( route('company.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {



            $company = new Company();
            $company->companyname = $request->companyname;
            $company->gstno = $request->gstno;
            $company->address = $request->address;
            $company->cityid = $request->cityid;
            
            
            $company->save();

            if( $company->id > 0 )
            {
                $contact = new CompanyContactPersons();
                $contact->companyid = $company->id;
                $contact->name = $request->name;
                $contact->designation = $request->designation;
                $contact->phone = $request->phone;
                $contact->save();
            }
            
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
        $company = Company::findOrFail($id);
        $states = State::orderBy('name', 'asc')->get();
        $arrStates = array();
        $arrStates[null] = "Select";
         foreach( $states as $k => $item ) { 
             $arrStates[$item->id] = $item->name;
         }
        

         
         return view('companyedit', ['company' => $company, 'states' => $arrStates]);
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
        $company = Company::findOrFail($id);
            $messages = [
                    'companyname.required' => 'Company Name is required.',
            'name.required' => 'Contact person name is required.'

        ];
        //
                $validator =  Validator::make( $request->all(), [ 'companyname' => 'required', 'address' => 'required', 'state' => 'required', 'cityid' => 'required', 'name' => 'required', 'phone' => 'required' ], $messages );

        if ($validator->fails()) {
            return redirect( route('company.edit', $id ) )
                ->withInput()
                ->withErrors($validator);
        }
        else {



            //            $company = new Company();
            $company->companyname = $request->companyname;
            $company->gstno = $request->gstno;
            $company->address = $request->address;
            $company->cityid = $request->cityid;
            
            
            $company->save();

            if( $company->id > 0 )
            {
                
                //  $contact = new CompanyContactPersons();
                $contact = $company->contactpersons[0];
                $contact->companyid = $company->id;
                $contact->name = $request->name;
                $contact->designation = $request->designation;
                $contact->phone = $request->phone;
                $contact->save();
            }
            
            $request->session()->flash("successmsg", "Successfully updated.");
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
    }
}
