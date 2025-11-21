<?php

namespace App\Http\Controllers;

use App\Category;
use App\Packages;
use App\PackagesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $packages = Packages::paginate(50);  //DB::table('packagemaster')->paginate(50);


        /*foreach ( $packages as $package)
       {
           //echo $package->packageservices;
          foreach ( $package->packageservices as $R )
           {


               echo $R->service;
           }
        }
        */
        // print_r($clients);
        return view('packagelist', [ 'packages' => $packages]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $categories = Category::all();
       /* foreach ( $category as $cat  ) {
            foreach( $cat->services as $R )
            {
                echo $R->name;
            }
        } */


        return view('packageadd', ['categories' =>  $categories ]);
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

        $messages = [
            'hdnaddedservice.required' => 'Please select atleast one service to create package',
            'price.greator_then_zero' => 'The price should be greator than zero.',
        ];

        $validator =  Validator::make( $request->all(), ['title' => 'required|max:255',  'price' => 'required|greator_then_zero', 'hdnaddedservice' => 'required'], $messages );

        if ($validator->fails()) {

            // $request->session()->flash("errors", $validator );
            return redirect(url('package/create'))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $package = new Packages();


            $input = $request->all();

            $package->title = $request->title;
            if( $request->description != '' ) {
                $package->description = $request->description;
            }
            else
            {
                $package->description = '';
            }

            $package->price = $request->price;

            $package->save();
            if( $package->id > 0 )
            {
                $serviceArr = array();
                if( is_array($request->hdnaddedservice) && count($request->hdnaddedservice) > 0 )
                {
                    foreach ($request->hdnaddedservice as $R)
                    {
                        if( $R != '' )
                        {
                            $packageService = new PackagesServices();
                            $packageService->serviceid = $R;
                            $packageService->packageid = $package->id;
                            $serviceArr[] = $packageService;
                        }
                    }
                }

               // PackagesServices::insert( $serviceArr );
                $package->packageservices()->saveMany($serviceArr);

            }
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }
        return view('packageadd');
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
        $package = Packages::findOrFail($id);
        //
        $categories = Category::all();
        /* foreach ( $category as $cat  ) {
             foreach( $cat->services as $R )
             {
                 echo $R->name;
             }
         } */

    return view('packageedit', ['categories' =>  $categories , 'package' => $package ]);


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
        $messages = [
            'hdnaddedservice.required' => 'Please select atleast one service to create package',
            'price.greator_then_zero' => 'The price should be greator than zero.',
        ];

        $validator =  Validator::make( $request->all(), ['title' => 'required|max:255',  'price' => 'required|greator_then_zero', 'hdnaddedservice' => 'required'], $messages );

        if ($validator->fails()) {

            // $request->session()->flash("errors", $validator );
            return redirect(route('package.edit', $id ))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $package = Packages::findOrFail($id);


            $input = $request->all();

            $package->title = $request->title;
            if( $request->description != '' ) {
                $package->description = $request->description;
            }
            else
            {
                $package->description = '';
            }

            $package->price = $request->price;

            $package->save();
            if( $package->id > 0 )
            {
                 $package->packageservices()->delete();

                 $serviceArr = array();
                if( is_array($request->hdnaddedservice) && count($request->hdnaddedservice) > 0 )
                {
                    foreach ($request->hdnaddedservice as $R)
                    {
                        if( $R != '' )
                        {
                            $packageService = new PackagesServices();
                            $packageService->serviceid = $R;
                            $packageService->packageid = $package->id;
                            $serviceArr[] = $packageService;
                        }
                    }
                }

                // PackagesServices::insert( $serviceArr );
                $package->packageservices()->saveMany($serviceArr);

            }
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }
        return view('packageedit');
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
        $package = Packages::findOrFail($id);
        $package->packageservices()->delete();
        $package->delete();

        Session::flash('successmsg', 'Successfully deleted!');
        return redirect()->route('package.index');
    }
}
