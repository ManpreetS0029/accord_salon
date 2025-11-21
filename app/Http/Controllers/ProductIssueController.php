<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


use App\Product;
use App\Staff;
use App\ProductIssue;
class ProductIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $issuedProduct = ProductIssue::paginate(50);

        return view( 'productissuelist', ['items' => $issuedProduct ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $products = Product::all()->where('stockavailable', '>', '0');

        $staff = Staff::orderBy('firstname', 'asc')->get();


        $productArr = array();
        $productArr[null] = 'Select Product';
        foreach ( $products as $prod )
        {
            $productArr[$prod->id] = $prod->name.' ( '.$prod->barcode. ' ) ';
        }

        $staffArr = array();
        foreach ( $staff as $stf )
        {
            $staffArr[$stf->id] = $stf->firstname.' '.$stf->lastname;
        }

        return view('productissueadd', ['products' => $productArr, 'staff' => $staffArr ]);
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
        $messages = [
            'productid.required_without' => 'Please select Product.',
            'barcode.required_without' => 'Selection of Product or Barcode is required.',
            'qnty.greator_then_zero' => 'Quantity should be greator than zero.'

        ];

        $validator =  Validator::make( $request->all(), [ 'barcode' => 'required_without:productid',
             'issuedate' => 'required|date_format:d/m/Y',
             'staffid' => 'required|greator_then_zero',
             'qnty' => 'required|greator_then_zero' ], $messages );

        if ($validator->fails()) {
            return redirect( route('productissue.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $productId = $request->productid;
            $productName = '';
            if( $request->input('barcode') != '' )
            {
                $product = Product::where('barcode', '=', $request->input('barcode') )->first();

            }
            else if( $request->input('productid') != ''  )
            {
                $product = Product::where('id', '=', $request->input('productid') )->first();

            }

            
            if( !$product )
            {
                return redirect( route('productissue.create') )->withInput()->withErrors( ['Product not found.'] );
            }

            $staff = Staff::where('id', '=', $request->input('staffid') )->first();

            $productId = $product->id;
            $productName = $product->name;


            $product->stockavailable = max(0,$product->stockavailable - $request->qnty);
            $product->save();
            

            $productIssue = new ProductIssue();
            $productIssue->barcode = $product->barcode;
            $productIssue->productid = $product->id;
            $productIssue->qnty = $request->qnty;
            $productIssue->productname = $product->name;
            $productIssue->staffid = $staff->id;
            $productIssue->staffname = $staff->firstname.' '.$staff->lastname;
            if( !empty($request->input('issuedate')) ) {
                $dt = explode('/', $request->issuedate);
                $productIssue->issuedate = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            }

            
            $productIssue->remarks =  $request->remarks;

            $productIssue->save();
           // return redirect( route('productissue.create') )->withInput()->withErrors( ['very bad error'] );

           // $sale = new Sale();
          //  $sale->clientid = $request->clientid;
            //$sale->
          //  $id = $this->calculateSale( $request);

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
        
        $products = Product::all()->where('stockavailable', '>', '0');

        $staff = Staff::orderBy('firstname', 'asc')->get();


        $productArr = array();
        $productArr[null] = 'Select Product';
        foreach ( $products as $prod )
        {
            $productArr[$prod->id] = $prod->name.' ( '.$prod->barcode. ' ) ';
        }

        $staffArr = array();
        foreach ( $staff as $stf )
        {
            $staffArr[$stf->id] = $stf->firstname.' '.$stf->lastname;
        }

        //        return view('productissueadd', ['products' => $productArr, 'staff' => $staffArr ]);
        
        $productIssue = ProductIssue::findOrFail( $id );
        return view( 'productissueedit', ['item' => $productIssue, 'products' => $productArr, 'staff' => $staffArr ]);
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
        
        //
        $productIssue = ProductIssue::findOrFail($id);
        //
        $messages = [
            'productid.required_without' => 'Please select Product.',
            'barcode.required_without' => 'Selection of Product or Barcode is required.',
            'qnty.greator_then_zero' => 'Quantity should be greator than zero.'

        ];

        $validator =  Validator::make( $request->all(), [ 'barcode' => 'required_without:productid',
             'issuedate' => 'required|date_format:d/m/Y',
             'staffid' => 'required|greator_then_zero',
             'qnty' => 'required|greator_then_zero' ], $messages );

        if ($validator->fails()) {
            return redirect( route('productissue.edit', $productIssue->id ) )
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $productId = $request->productid;
            $productName = '';
            if( $request->input('barcode') != '' )
            {
                $product = Product::where('barcode', '=', $request->input('barcode') )->first();

            }
            else if( $request->input('productid') != ''  )
            {
                $product = Product::where('id', '=', $request->input('productid') )->first();

            }

            
            if( !$product )
            {
                return redirect( route('productissue.create') )->withInput()->withErrors( ['Product not found.'] );
            }

            $staff = Staff::where('id', '=', $request->input('staffid') )->first();

            $productId = $product->id;
            $productName = $product->name;

            // that means they are changing the product so we have to reupdate the last added product
            $lastQnty = $productIssue->qnty;
            if( $productId != $productIssue->productid )
            {
                //last productId
                $lastProductId = $productIssue->productid;


                $productLast = Product::where('id', '=', $lastProductId  ) -> first();
                $productLast->stockavailable = $productLast->stockavailable + $lastQnty;
                $productLast->save();

                $lastQnty = 0;
            }
            

            
                 $product->stockavailable = max(0, $product->stockavailable + $lastQnty - $request->qnty);
                 $product->save();
           
            

            
            //$productIssue = new ProductIssue();
            $productIssue->barcode = $product->barcode;
            $productIssue->productid = $product->id;
            $productIssue->qnty = $request->qnty;
            $productIssue->productname = $product->name;
            $productIssue->staffid = $staff->id;
            $productIssue->staffname = $staff->firstname.' '.$staff->lastname;
            if( !empty($request->input('issuedate')) ) {
                $dt = explode('/', $request->issuedate);
                $productIssue->issuedate = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            }

            
            $productIssue->remarks =  $request->remarks;

            $productIssue->save();
           // return redirect( route('productissue.create') )->withInput()->withErrors( ['very bad error'] );

           // $sale = new Sale();
          //  $sale->clientid = $request->clientid;
            //$sale->
          //  $id = $this->calculateSale( $request);

            $request->session()->flash("successmsg", "Successfully Updated.");
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
        $productIssue = ProductIssue::findOrFail($id);

        $product = Product::where('id', '=', $productIssue->productid )->first();

        $product->stockavailable = $product->stockavailable + $productIssue->qnty;
        $product->save();
        
        $productIssue->delete();
        
        Session::flash('successmsg', 'Successfully deleted!');
        
        return redirect()->back();
    }
}
