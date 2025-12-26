<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientPayment;
use App\ClientPaymentUsed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class ClientsController extends Controller
{
    /*
 * Verb    Path                        Action  Route Name
GET     /users                      index   users.index
GET     /users/create               create  users.create
POST    /users                      store   users.store
GET     /users/{user}               show    users.show
GET     /users/{user}/edit          edit    users.edit
PUT     /users/{user}               update  users.update
DELETE  /users/{user}               destroy users.destroy
 */
    //
    //get
    public function index(Request $request) {


        $query = Client::query();
        $query->where('isrealclient', '=', '1');


        if( $request->searchtext != "" )
        {
            $searchText = $request->searchtext;
                   $query->where( function($query) use ( $searchText, $request ) { 
                       $query->orWhere('clientname', 'like', "%".$searchText."%" )
                            ->orWhere( 'phone', 'like', '%'.$searchText.'%' )
                                                                        ->orWhere( 'phone2', 'like', '%'.$searchText.'%' );
                                                                        
                });
        }

        if ((int)$request->lastvisit === 1) {

            $oldSales = DB::select(
                'SELECT S.clientid FROM `sale` AS S
WHERE (S.walkin_name != "Walk-In" OR S.walkin_name IS NULL) AND S.created_at >= (CURDATE() - INTERVAL 3 MONTH) GROUP BY S.clientid'
            );
            $clientIds = [];
            foreach ($oldSales as $oldSale) {
                $clientIds[] = $oldSale->clientid;
            }

            $query->whereNotIn('id', $clientIds);
        }

        
        $perPage = $request->get('per_page', 50);
        $clients = $query->paginate($perPage);
        $clients->appends(['lastvisit' => $request->lastvisit, 'searchtext' => $request->searchtext, 'per_page' => $perPage]);
        //$clients =  DB::table('client')->paginate(50);


       // print_r($clients);
        return view('clientlist', [ 'clients' => $clients]);
    }

    //get edit for us
    public function show($id) {



    }

    public function edit($id)
    {
        $client = Client::find($id);


        if(!$client) {
            // echo "pppp";
            abort(404,"Not Found");
        }
        //return redirect(url('category'))
        // ->withErrors(['error1' => 'Not found']);



        return view('clientedit', ['client' => $client] );
    }

    public function update( Request $request, $id)
    {

        $validator =  Validator::make( $request->all(), ['clientname' => 'required|max:255', 'dob' => 'required|date_format:d/m/Y', 'phone' => ['required', Rule::unique('client')->ignore($id)] ] );

        if ($validator->fails()) {

            // $request->session()->flash("errors", $validator );
            return redirect(route('clients.edit', $id))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $client = Client::findOrFail($id);

            $input = $request->all();

            $client->fill($input);

            $dt = explode('/',$request->dob);
            $client->dob =  $dt[2]."-".$dt[1]."-".$dt[0];
            $client->save();
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Updated.");
            return redirect()->back();
        }

    }

    //get create form
    function create()
    {


        return view('clientadd');
    }

    //save
    public function store(Request $request) {

        $validator =  Validator::make( $request->all(), ['clientname' => 'required|max:255', 'dob' => 'required|date_format:d/m/Y', 'phone' => 'required|unique:client,phone'] );

        if ($validator->fails()) {

           // $request->session()->flash("errors", $validator );
            return redirect(route('clients.create'))
                ->withInput()
                ->withErrors($validator);
        }
        else {

            $client = new Client();


            $input = $request->all();


            $client->fill($input);

            $dt = explode('/',$request->dob);
            $client->dob =  $dt[2]."-".$dt[1]."-".$dt[0];
            $client->save();
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }
        return view('clientadd');

    }

    public function destroy( $id )
    {
        Client::destroy($id);

        //return redirect(url('category'));

        //session()->flash("successmsg", "Successfully Deleted.");
        Session::flash('successmsg', 'Successfully deleted!');
        return redirect()->route('clients.index');

    }

    public function listfirsthundred (Request $request){

        $query = Client::query();
        $query->where('isrealclient', '=', '1');
        $clients = $query->orderBy('id', 'Asc')->paginate(100);
        //$clients =  DB::table('client')->paginate(50);

        // print_r($clients);
        return view('clientlist100', [ 'clients' => $clients]);
    }

    public function paymentlist( Request $request, $id)
    {

    	if( $request->input('delid') != ''  )
	    {
	    	ClientPaymentUsed::where('clientpaymentid', '=', $request->input('delid') )->delete();
	    	ClientPayment::where('id', '=', $request->input('delid') )->delete();
	    }

    	$query = ClientPayment::query();
    	 $query->where('clientid', '=', $id);
    	$perPage = $request->get('per_page', 100);
	    $paymentList = $query->orderBy('id', 'desc' )->paginate($perPage);
	    $paymentList->appends(['per_page' => $perPage]);

	    return view('clientpaymentlist', ['paymentlist' => $paymentList, 'clientid' => $id]);
    }

    public function nonRepeatingCustomers(Request $request)
    {
        $query = Client::query();
        $query->where('isrealclient', '=', '1');

        // Search filter
        if ($request->searchtext != "") {
            $searchText = $request->searchtext;
            $query->where(function($query) use ($searchText) {
                $query->orWhere('clientname', 'like', "%".$searchText."%")
                    ->orWhere('phone', 'like', '%'.$searchText.'%')
                    ->orWhere('phone2', 'like', '%'.$searchText.'%');
            });
        }

        // Get all real clients
        $allRealClients = Client::where('isrealclient', '=', '1')->pluck('id')->toArray();
        
        // Get all clients who have ever made a purchase
        $allClientsWithSales = DB::select('SELECT DISTINCT S.clientid FROM `sale` AS S WHERE S.clientid > 0');
        $allClientsWithSalesIds = [];
        foreach ($allClientsWithSales as $sale) {
            if ($sale->clientid > 0) {
                $allClientsWithSalesIds[] = $sale->clientid;
            }
        }
        
        // Clients who have never made a purchase
        $clientsWithNoPurchases = array_diff($allRealClients, $allClientsWithSalesIds);
        
        // Get purchase counts for all clients
        $purchaseCounts = DB::select('
            SELECT S.clientid, COUNT(*) as purchase_count
            FROM `sale` AS S 
            WHERE S.clientid > 0 
            GROUP BY S.clientid
        ');
        
        $clientPurchaseCountMap = [];
        foreach ($purchaseCounts as $pc) {
            if ($pc->clientid > 0) {
                $clientPurchaseCountMap[$pc->clientid] = $pc->purchase_count;
            }
        }
        
        // Get clients with exactly one purchase
        $singlePurchaseClientIds = [];
        foreach ($clientPurchaseCountMap as $clientId => $count) {
            if ($count == 1) {
                $singlePurchaseClientIds[] = $clientId;
            }
        }
        
        // Get clients with exactly two purchases
        $twoPurchaseClientIds = [];
        foreach ($clientPurchaseCountMap as $clientId => $count) {
            if ($count == 2) {
                $twoPurchaseClientIds[] = $clientId;
            }
        }
        
        // Get regular clients (visiting at least once in 2 months)
        // This means clients who have made at least one purchase in the last 2 months
        $regularClientsQuery = DB::select('
            SELECT DISTINCT S.clientid 
            FROM `sale` AS S 
            WHERE S.clientid > 0 
            AND S.created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH)
        ');
        $regularClientIds = [];
        foreach ($regularClientsQuery as $rc) {
            if ($rc->clientid > 0) {
                $regularClientIds[] = $rc->clientid;
            }
        }
        
        // Apply filter based on request
        $filter = $request->get('filter', '');
        $filteredClientIds = [];
        
        if ($filter === 'never') {
            // Clients who have never made a purchase
            $filteredClientIds = $clientsWithNoPurchases;
        } elseif ($filter === 'once') {
            // Clients who have made exactly one purchase
            $filteredClientIds = $singlePurchaseClientIds;
        } elseif ($filter === 'two_times') {
            // Clients who have made exactly two purchases
            $filteredClientIds = $twoPurchaseClientIds;
        } elseif ($filter === 'regular') {
            // Clients visiting at least once in 2 months
            $filteredClientIds = $regularClientIds;
        } else {
            // Default (All): non-repeating customers = never + once
            $filteredClientIds = array_merge($clientsWithNoPurchases, $singlePurchaseClientIds);
        }

        if (count($filteredClientIds) > 0) {
            $query->whereIn('id', $filteredClientIds);
        } else {
            // If no clients match the filter, return empty result
            $query->whereRaw('1 = 0');
        }

        $perPage = $request->get('per_page', 50);
        $clients = $query->orderBy('id', 'desc')->paginate($perPage);
        $clients->appends([
            'searchtext' => $request->searchtext, 
            'filter' => $request->filter,
            'per_page' => $perPage
        ]);

        // Get last sale date for each client to display
        foreach ($clients as $client) {
            $lastSale = DB::selectOne('
                SELECT MAX(created_at) as last_sale_date 
                FROM `sale` 
                WHERE clientid = ' . (int)$client->id . ' 
                AND clientid > 0
            ');
            
            $client->last_sale_date = $lastSale ? $lastSale->last_sale_date : null;
        }

        return view('nonrepeatingcustomers', ['clients' => $clients]);
    }


}
