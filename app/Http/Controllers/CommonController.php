<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientPackage;
use App\CustomClasses\HS_Common;
use App\CustomClasses\HS_Staff;
use App\Packages;
use App\SaleItem;
use App\Services;
use App\ClientPayment;
use App\StaffSalaryIncrement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\City;
use App\Product;
class CommonController extends Controller
{
    //

    function assignstafftoosale(  $id   )
    {

        //echo $id;
        //echo Input::get("type");

        if( Input::get("type") == "pack" )
        {

            $package = Packages::findOrFail( $id );
           // print_r($package->packageservices);
            foreach ( $package->packageservices  as $service )
            {
                echo $service->service->name;
            }

        }
       // else if( Input::get("type") == "pack" )



    }

    function getproductwithid($pid)
    {
        $product = Product::findOrFail($pid);
        if( $product->tax == "" )
        {
            $product->tax = 0;
        }
        echo $product;
        die(0);
    }
    function getcities(Request $request)
    {
        //$input = Input::all();
        $stateId = $request->input('stateid');
        $cityId = '';
        if( $request->has('cityid') ) {
            $cityId = $request->input('cityid');
        }
        $cities = City::where('state_id','=', $stateId )->orderBy('city_name', 'asc')->get();
        $data = '';
        //$data = '<select name="cityid" id="cityid" class="form-control" data-plugin="select2" >';
        $data .= '<option value="">Select</option>';
        foreach ($cities as $city ) {
            $selected = '  ';
            if( $cityId == $city->id )
            {
                $selected = ' selected ';
            }
            $data .= '<option '.$selected.' value="'.$city->id.'">'.$city->city_name.'</option>';
        }
        //$data .= '</select>';

        echo $data;
        die(0);
    }

    function getclientadvance($clientId)
    {

        $totalPayment = 0;
        $totalAmountIncludedNotClearedYet = 0;
        $arrRes = array();
        $arrRes['advance_amount'] = 0;
        $arrRes['sale_pending_amount'] = 0;

        if($clientId > 0 )
        {
            $client = Client::findOrFail($clientId);

            $arrRes['advance_amount'] = sprintf( "%0.2f", $client->getTotalAdvanceAmount() );
            $arrRes['sale_pending_amount'] = sprintf( "%0.2f", $client->getSalesPendingAmount(), 2 );
            $arrRes['in_100_list'] = '0';

            $query = Client::query();

            $clients = $query->orderBy('id', 'Asc')->paginate(100);
            //$clients =  DB::table('client')->paginate(50);

            foreach ( $clients as $clientRow )
            {
                if( $clientRow->id  ==  $client->id )
                {
                    $arrRes['in_100_list'] = '1';
                    break;
                }
            }


            echo json_encode($arrRes);

            exit(0);
        }

    }

    function getClientUncompletedPackages( $clientId)
    {
        if( $clientId > 0 )
        {
            $client = Client::findOrFail($clientId);


            $arrRes = $client->unCompletedPackages( $_GET['selected'] );
            
           // print_r($arrRes);
            echo json_encode($arrRes);

            exit(0);
        }
    }

    function packageLeftItemsOrMoney( $packageId )
    {
        if( $packageId > 0 )
        {
            $package = ClientPackage::findOrFail($packageId);
            if( $package->packagetype == "2" )
            {
                $arr = $package->packageLeftItems();
                $arrIds = array();
                foreach ( $arr as $k => $R )
                {
                    $arrIds[] = $k;
                }

                $service = Services::whereIn( 'id', $arrIds  )->get();
                foreach ( $service as $ser )
                {
                    $ser->cat = $ser->category;
                }
                $arrRes['services'] = $service;
                $arrRes['qnty'] = $arr;
                //print_r($arrRes);
                echo json_encode($arrRes);

            }
            else {
                echo json_encode(array("leftmoney" => number_format($package->packageLeftMoney(),2)));
            }

        }
        exit(0);
    }

    public function deleteSaleItem( $id )
    {
        $item = SaleItem::find($id);
        $item->delete();
    }
   /* protected function getSalePaidAmount( $sale,  )
    {
        $payments = $sale->salepayments;
        foreach ( $payments as $payment )
        {
            $paymentDetails = $payment->paymentDetail;

        }
    } */

   function getsalarydetails( Request $request )
   {
       $staffId = $request->staffid;
       $months = $request->months;
       $years = $request->years;

       $hsStaff = new HS_Staff();
       $arr = $hsStaff->getSalaryAllDetails($staffId, $months, $years );

       $newArr = array();
       $newArr['result'] = isset($arr['results'][$years.'_'.$months]) ? $arr['results'][$years.'_'.$months] : '' ;
       $newArr['total_balance'] =  isset($arr['total_balance']) ? $arr['total_balance'] : 0;
      echo json_encode($newArr);
      exit(0);
   }

    public function updatesalary(Request $request)
    {
        $msg = array();
        $msg['error'] = '0';
        $msg['msg'] = '';
      //$msg['id'] = implode('|', $request);

           if( $request->id == "" )
           {
               $msg['error'] = "1";
               $msg['msg'] .= "Technical Problem to update.\n";
           }

        if( $request->salary == "" || !is_numeric($request->salary) )
        {
            $msg['error'] = "1";
            $msg['msg'] .= "Invalid salary amount.\n";
        }

        if( $request->commission == "" || !is_numeric($request->commission) )
        {
            $msg['error'] = "1";
            $msg['msg'] .= "Invalid commission amount.\n";
        }

        if( $request->fromdate == ""  )
        {
            $msg['error'] = "1";
            $msg['msg'] .= "Invalid Date.\n";
        }

        if( $msg['msg'] == '' )
        {
        $salaryId = $request->id;
        $salary = StaffSalaryIncrement::findOrFail($salaryId);
        $salary->salary = $request->salary;
        $salary->commission = $request->commission;
        $salary->fromdate = HS_Common::extractDateAsString($request->fromdate);
        $salary->save();
        }

       echo json_encode($msg);

        exit(0);

    }
}
