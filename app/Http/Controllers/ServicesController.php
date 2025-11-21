<?php

namespace App\Http\Controllers;

use App\Category;
use App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $services = Services::paginate(50);


         return view('serviceslist', ['services' => $services ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::orderBy('name','asc')->get();
        $arrCats = array();
        $arrCats[''] =  'Select';
        foreach ($categories as $cat)
        {
            $arrCats[$cat->id] =  $cat->name;
        }
        return view('serviceadd',['categories' => $arrCats]);
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
            'servicecategoriesid.required' => 'The category field is required.',
            'price.greator_then_zero' => 'The price should be greator than zero.',
            'discount.required_if_selected_grator_than_zero' => 'Discount should be greator than zero.'
        ];
        $validator =  Validator::make( $request->all(), ['servicecategoriesid' => 'required', 'name' => 'required', 'price' => 'required|greator_then_zero', 'discount' => 'required_if_selected_grator_than_zero:discounttype' ], $messages );
//,   'discount' => 'required_if:discounttype,1'
        /*$validator->sometimes('price', 'required', function ($input) {

            return $input->price > 0;
        }); */


        if ($validator->fails()) {

            // $request->session()->flash("errors", $validator );
            return redirect(route('services.create'))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $service = new Services();


            $input = $request->all();


            $service->fill($input);

            if ($request->discounttype == '' )
            {
                $service->discount = 0;
            }


            if( $request->discounttype != '' &&  $request->discount > 0 )
            {
                $service->isdiscountable = '1';
            }
            else
            {
                $service->isdiscountable = '0';
            }

            $service->save();
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
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
        $service = Services::findOrFail($id);
        $categories = Category::orderBy('name','asc')->get();
        $arrCats = array();
        $arrCats[''] =  'Select';
        foreach ($categories as $cat)
        {
            $arrCats[$cat->id] =  $cat->name;
        }


        return view('serviceedit', ['service' =>  $service , 'categories' => $arrCats ]);
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
            'servicecategoriesid.required' => 'The category field is required.',
            'price.greator_then_zero' => 'The price should be greator than zero.',
            'discount.required_if_selected_grator_than_zero' => 'Discount should be greator than zero.'
        ];
        $validator =  Validator::make( $request->all(), ['servicecategoriesid' => 'required', 'name' => 'required', 'price' => 'required|greator_then_zero', 'discount' => 'required_if_selected_grator_than_zero:discounttype' ], $messages );
//,   'discount' => 'required_if:discounttype,1'
        /*$validator->sometimes('price', 'required', function ($input) {

            return $input->price > 0;
        }); */


        if ($validator->fails()) {

            // $request->session()->flash("errors", $validator );
            return redirect(route('services.edit', $id ))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $service = Services::findOrFail($id);


            $input = $request->all();


            $service->fill($input);

            if ($request->discounttype == '' )
            {
                $service->discount = 0;
            }


            if( $request->discounttype != '' &&  $request->discount > 0 )
            {
                $service->isdiscountable = '1';
            }
            else
            {
                $service->isdiscountable = '0';
            }

            $service->save();
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Added.");
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
    // Find the record
    $service = Services::findOrFail($id);

    // Delete the record
    $service->delete();

    // Redirect or return a response
    return redirect()->route('services.index')
                     ->with('success', 'Service deleted successfully.');
}

}
