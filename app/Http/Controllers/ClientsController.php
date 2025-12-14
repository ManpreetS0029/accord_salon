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
        // Get clients who have made exactly one purchase (non-repeating customers)
        // This includes clients with no sales at all, or clients with exactly one purchase
        
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

        // Date range filter
        $dateFrom = null;
        $dateTo = null;
        $dateCondition = '';
        
        if ($request->datefrom != "") {
            $dt = explode('/', $request->datefrom);
            if (count($dt) == 3) {
                $dateFrom = $dt[2] . "-" . $dt[1] . "-" . $dt[0];
            }
        }
        
        if ($request->dateto != "") {
            $dt = explode('/', $request->dateto);
            if (count($dt) == 3) {
                $dateTo = $dt[2] . "-" . $dt[1] . "-" . $dt[0] . " 23:59:59";
            }
        }

        // Build date condition for SQL queries (with alias S)
        $dateCondition = '';
        if ($dateFrom && $dateTo) {
            $dateCondition = " AND S.created_at >= '" . $dateFrom . "' AND S.created_at <= '" . $dateTo . "'";
        } elseif ($dateFrom) {
            $dateCondition = " AND S.created_at >= '" . $dateFrom . "'";
        } elseif ($dateTo) {
            $dateCondition = " AND S.created_at <= '" . $dateTo . "'";
        }

        // Build date condition for queries without alias
        $dateConditionNoAlias = '';
        if ($dateFrom && $dateTo) {
            $dateConditionNoAlias = " AND created_at >= '" . $dateFrom . "' AND created_at <= '" . $dateTo . "'";
        } elseif ($dateFrom) {
            $dateConditionNoAlias = " AND created_at >= '" . $dateFrom . "'";
        } elseif ($dateTo) {
            $dateConditionNoAlias = " AND created_at <= '" . $dateTo . "'";
        }

        // Get client IDs who have made exactly one purchase (within date range if specified)
        $singlePurchaseQuery = 'SELECT S.clientid, COUNT(*) as purchase_count
             FROM `sale` AS S 
             WHERE S.clientid > 0';
        
        if ($dateCondition) {
            $singlePurchaseQuery .= $dateCondition;
        }
        
        $singlePurchaseQuery .= ' GROUP BY S.clientid HAVING purchase_count = 1';
        
        $singlePurchaseClients = DB::select($singlePurchaseQuery);
        
        $singlePurchaseClientIds = [];
        foreach ($singlePurchaseClients as $client) {
            if ($client->clientid > 0) {
                $singlePurchaseClientIds[] = $client->clientid;
            }
        }

        // Get clients who have made any purchase (within date range if specified)
        $clientsWithSalesQuery = 'SELECT DISTINCT S.clientid 
             FROM `sale` AS S 
             WHERE S.clientid > 0';
        
        if ($dateCondition) {
            $clientsWithSalesQuery .= $dateCondition;
        }
        
        $clientsWithSales = DB::select($clientsWithSalesQuery);
        
        $clientsWithSalesIds = [];
        foreach ($clientsWithSales as $sale) {
            if ($sale->clientid > 0) {
                $clientsWithSalesIds[] = $sale->clientid;
            }
        }

        // If date range is specified, we need to consider:
        // 1. Clients with exactly one purchase in the date range AND that's their only purchase ever
        // 2. Clients with no purchases ever (they would be non-repeating regardless of date range)
        // If no date range is specified, use the original logic
        if ($dateCondition) {
            // With date range: Find clients whose ONLY purchase was in the date range
            // Get all clients who have made exactly one purchase ever (without date filter)
            $allSinglePurchaseClientsEver = DB::select(
                'SELECT S.clientid, COUNT(*) as purchase_count
                 FROM `sale` AS S 
                 WHERE S.clientid > 0 
                 GROUP BY S.clientid
                 HAVING purchase_count = 1'
            );
            
            $allSinglePurchaseClientsEverIds = [];
            foreach ($allSinglePurchaseClientsEver as $client) {
                if ($client->clientid > 0) {
                    $allSinglePurchaseClientsEverIds[] = $client->clientid;
                }
            }
            
            // Clients who made exactly one purchase in the date range AND that's their only purchase ever
            $nonRepeatingInRange = array_intersect($singlePurchaseClientIds, $allSinglePurchaseClientsEverIds);
            
            // Also include clients who have never made any purchase (they are always non-repeating)
            $allRealClients = Client::where('isrealclient', '=', '1')->pluck('id')->toArray();
            $allClientsWithSalesEver = DB::select('SELECT DISTINCT S.clientid FROM `sale` AS S WHERE S.clientid > 0');
            $allClientsWithSalesEverIds = [];
            foreach ($allClientsWithSalesEver as $sale) {
                if ($sale->clientid > 0) {
                    $allClientsWithSalesEverIds[] = $sale->clientid;
                }
            }
            $clientsWithNoPurchasesEver = array_diff($allRealClients, $allClientsWithSalesEverIds);
            
            $nonRepeatingIds = array_merge($nonRepeatingInRange, $clientsWithNoPurchasesEver);
        } else {
            // Without date range: original logic - clients with exactly one purchase ever OR clients with no purchases ever
            $allRealClients = Client::where('isrealclient', '=', '1')->pluck('id')->toArray();
            // Get all clients who have ever made a purchase (without date filter)
            $allClientsWithSales = DB::select('SELECT DISTINCT S.clientid FROM `sale` AS S WHERE S.clientid > 0');
            $allClientsWithSalesIds = [];
            foreach ($allClientsWithSales as $sale) {
                if ($sale->clientid > 0) {
                    $allClientsWithSalesIds[] = $sale->clientid;
                }
            }
            $clientsWithNoPurchasesEver = array_diff($allRealClients, $allClientsWithSalesIds);
            $nonRepeatingIds = array_merge($singlePurchaseClientIds, $clientsWithNoPurchasesEver);
        }

        if (count($nonRepeatingIds) > 0) {
            $query->whereIn('id', $nonRepeatingIds);
        } else {
            // If no non-repeating clients exist, return empty result
            $query->whereRaw('1 = 0');
        }

        $perPage = $request->get('per_page', 50);
        $clients = $query->orderBy('id', 'desc')->paginate($perPage);
        $clients->appends([
            'searchtext' => $request->searchtext, 
            'datefrom' => $request->datefrom,
            'dateto' => $request->dateto,
            'per_page' => $perPage
        ]);

        // Get last sale date for each client to display (within date range if specified)
        foreach ($clients as $client) {
            $lastSaleQuery = 'SELECT MAX(created_at) as last_sale_date 
                 FROM `sale` 
                 WHERE clientid = ' . $client->id . ' 
                 AND clientid > 0';
            
            if ($dateConditionNoAlias) {
                $lastSaleQuery .= $dateConditionNoAlias;
            }
            
            $lastSale = DB::selectOne($lastSaleQuery);
            $client->last_sale_date = $lastSale ? $lastSale->last_sale_date : null;
        }

        return view('nonrepeatingcustomers', ['clients' => $clients]);
    }


}
