<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductStock;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //bynames=&stockmode=&search=Search
        $query = Product::query();

        if (trim($request->bynames) != "") {
            $query->where(function ($query) use ($request) {

                $query->orWhere('hsncode', 'like',  $request->bynames . '%')
                    ->orWhere('barcode', 'like',   $request->bynames . '%')
                    ->orWhere('name', 'like',   $request->bynames . '%');
            });
        }

        if (trim($request->stockmode) == '0') {

            $query->where('stockavailable', '<=', 0);
        } else if (trim($request->stockmode) == '1') {

            $query->where('stockavailable', '>', 0);
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 50);
        $products = $query->paginate($perPage); //Product::paginate(50);
        $products->appends(['searchtext' => $request->searchtext, 'per_page' => $perPage]);


        return view("productlist", ['products' => $products]);
    }

    public function getproductbyid($id)
    {
        $product = Product
            ::where('id', '=', $id)->get()->first();
        echo $product;
        die(0);
    }
    public function searchproduct($searchText)
    {

        // Product::all()->where( "productcode", "like", "%".$searchText."%", );

        $products = Product
            ::where('stockavailable', '>', '0')
            ->where(function ($query) use ($searchText) {
                $query->where('productcode', 'like', "%" . $searchText . "%")
                    ->orWhere('barcode', 'like', "%" . $searchText . "%")
                    ->orWhere('name', 'like', "%" . $searchText . "%");
            })->get();

        if (count($products) > 0) {
            if ($products[0]->tax == "") {
                $products[0]->tax = 0.0;
            }
        }

        echo $products;
        die(0);
    }
    public function getproduct($searchText)
    {

        // Product::all()->where( "productcode", "like", "%".$searchText."%", );
        $products = Product::where('stockavailable', '>', '0')
            ->where('barcode', 'like', "%" . $searchText . "%")
            ->get()->first();

        echo $products;
        die(0);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view("productadd");
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
        $validator =  Validator::make($request->all(), ['productcode' => 'required|unique:products,productcode', 'barcode' => 'required|unique:products,barcode', 'hsncode' => 'required', 'name' => 'required|max:255', 'price' => 'required', 'startstock' => 'required']);

        if ($validator->fails()) {
            return redirect(route('product.create'))
                ->withInput()
                ->withErrors($validator);
        } else {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->purchaseprice = $request->purchaseprice;
            $product->startstock = $request->startstock;
            $product->status = 1;
            $product->stockavailable =   $request->startstock;
            $product->soldcount = 0;
            $product->productcode = $request->productcode;
            $product->barcode = $request->barcode;
            $product->hsncode = $request->hsncode;
            $product->tax = $request->tax;
            $product->save();
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
        $product = Product::findOrFail($id);

        return view("productedit", ["product" => $product]);
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
    
        $product = Product::findOrFail($id);
    
        $messages = [

            'addstock.greator_then_zero' => 'The Add Stock should be greator than zero.',
        ];

        if (isset($request->updatestock)) {
            //$validator =  Validator::make( $request->all(), ['addstock' => 'required|greator_then_zero'], $messages );
            $validator =  Validator::make($request->all(), ['addstock' => 'required'], $messages);


            if ($validator->fails()) {
                return redirect(route('product.edit', $product->id))
                    ->withInput()
                    ->withErrors($validator);
            } else {
                $product->stockavailable =  $product->stockavailable + $request->addstock;
            }

            $product->save();

            $productStock = new ProductStock();

            $productStock->productid = $product->id;

            $productStock->quantity = $request->addstock;

            $productStock->save();
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
        } else {
            $validator =  Validator::make($request->all(), ['productcode' => ['required', Rule::unique('products')->ignore($id)], 'hsncode' => 'required',  'barcode' => ['required', Rule::unique('products')->ignore($id)], 'name' => 'required|max:255', 'price' => 'required', 'purchaseprice' => 'required']);

            if ($validator->fails()) {
                return redirect(route('product.edit', $product->id))
                    ->withInput()
                    ->withErrors($validator);
            } else {
                //$product = new Product();
                $product->name = $request->name;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->purchaseprice = $request->purchaseprice;

                //$product->startstock = $request->startstock;
                $product->status = 1;
                $product->productcode = $request->productcode;
                $product->barcode = $request->barcode;
                $product->tax = $request->tax;

                $product->hsncode = $request->hsncode;
                // $product->stockavailable =  $product->stockavailable // $request->startstock;

                //$product->soldcount = 0;
                $product->save();
                $request->session()->flash("successmsg", "Successfully Updated.");
                return redirect()->back();
            }
        }

        /*
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
            }*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->back();
    }
}
