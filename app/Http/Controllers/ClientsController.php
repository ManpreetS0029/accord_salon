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


}
