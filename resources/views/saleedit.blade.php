<?php
$servicesArr = array();
$servicesJsonData = '';
$staffOptions = '<option value="">Select</option>';
$totalDiscount = 0;
$printItems = '';
foreach ( $staffs as $staff )
{
    $staffOptions .= '<option value="'.$staff->id.'">'.$staff->firstname.' '.$staff->lastname.'</option>';
}

$printData = '<div><h2 class="companys">Accord Salon</h2></div>';
$printData .= '<div class="customer_name">Customer Name: '.($sale->client ? $sale->client->clientname : ( $sale->walkin_name != '' ? $sale->walkin_name : 'Walk-In' )).'</div>';

$printData .= '<div class="datetime"><div class="dates">'.date("d/m/Y").'</div><div class="times">'.date("h:i A").'</div></div>';

$printData .= '<table id="items_details"><thead>';

    $printData .= '<tr><th>Item Name</th><th>Qnty</th><th>Disc.</th><th>Amount</th></tr>';
    $printData .= '</thead>';
    $printData .= '<tbody>';
?>
@extends('layouts.app')

@section('content')
    <style>
        .pendingClass{ background-color: #ffcdd2; }
        .underprocessClass{ background-color: #f0f4c3; }
        .completedClass{ background-color: #b2dfdb; }
        .completedClass td, .pendingClass td, .underprocessClass td { border: 1px solid #aaa !important; }
        
        .modal-dialog { width: 100%; height: 100% }
    .modal-content { width: 100%; height: 100%; }
    .modal-body { width: 100%; height: 70%; }
    iframe { border: 0px; }
            
     @media screen {
        #display_screen { display: block; }
      #print_screen { display: none; } 

     }
     @media print {
     
     .customer_name { margin-bottom: 10px; }
     #items_details { width: 100%;  }
     #items_details tfoot { margin-top: 30px; line-height: 25px; }
     #items_details thead  { border-top: 1px dashed #000; line-height:40px; border-bottom: 1px dashed #000; }
     #items_details tbody { border-bottom: 1px dashed #000;  }
     #items_details tbody td{ padding-top:5px; padding-bottom: 5px; }
     .companys { text-align: center; width: 100%; padding:0; margin: 0; margin-bottom:15px; border-bottom: 1px solid #000; color: #000; font-size: 1.3em; padding-bottom: 15px; }
     #display_screen { display: none; }
     #print_screen { display: block; width: 216px; margin: 0 auto;   padding-top: 40px; padding-bottom: 60px; }
     #datetime{ float: left; width: 100px; }
     .dates{ float: left; width: 100px; text-align: left; }
     .times{ float: right; width: 100px; text-align: right; }
     
     }
    </style>
    
    <div id="display_screen" class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
         <div class="panel-heading">Update Sale <?php if( $sale->actualPendingAmount() > 0 ) { echo 'Pending Amount: '.$sale->actualPendingAmount(); } else { echo '(Fully Paid)'; } ?> <div style="float:right">
<a href="<?php echo route('sale.index'); ?>">List Sales</a> | 
         
<button style="margin-top: -7px;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Manage Payments
</button>
             </div></div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
{{ Form::open(array( 'method' => 'PUT', 'route' => [ 'sale.update', $sale->id ] )) }}
                            {{ csrf_field() }}

                            <div class="panel-body container-fluid">

                                <!--
                                    <div class="row">
                                            <div class="col-md-6">
                                                <label>
                                                    <input class="checkbox-inline" style="vertical-align: 1px;" type="checkbox" value="1" <?php if( $sale->ispackage == "1" ) { echo "checked"; } ?> name="ispackage" id=""> Custom Package
                                                </label>
                                            </div>
                                        </div>
                                        <br /> -->

                                <div class="row row-lg">

                                    <div class="col-lg-12">


                                        <div class="form-group row">

                                            {{ Form::label('title', 'Select Client') }} Or Walk-In * <br />
                                            <div class="row">
                                                <div class="col-md-4">

                                            {{ Form::select('clientid', $clients, $sale->client ? $sale->client->id : '0' , array('class' => 'form-control', 'data-plugin' => "select2", 'id' => 'client_dropdown') ) }}
                                        </div>

                                        <div class="col-md-4">
                                            {{ Form::text('walkin_name', $sale->client ?  $sale->walkin_name : 'Walk-In' , array('class' => 'form-control') ) }}
                                        </div>

                                                <div class="col-md-4">

                                                    <div style="color: #f00;"   id="clientadvanceinfo"></div>
                                                </div>
                                                
                                                 


                                            </div>

					</div>
                                    </div>


                                    <div class="row">
                                        <div id="package_dropdown_container" class="col-md-4">

                                        </div>
                                        <div id="package_dropdown_pending_list" class="col-md-8">

                                        </div>

                                    </div>
					
                                </div>

				<br />
                                    <div class="row" style="border: 1px solid #ccc;">
                                        <div class="col-md-4">
                                            {{ Form::label('saledate', 'Sale Date') }}
                                            {{ Form::text('saledate', date("d/m/Y", strtotime($sale->created_at)), array('class' => 'form-control dates_nodefault') ) }}
                                            <br />
                                        </div>

                                    </div>
                                    <br />
                                <div class="row" style="border: 1px solid #ccc;">
                                    <div class="col-md-12">
                                        <h3>+ Add Services/Products</h3>
                                        <br />


                                        <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" href="#tab_package">Packages</a></li>
                                            <li class=""><a data-toggle="tab" href="#tab_services">Services</a></li>
                                            <li class=""><a data-toggle="tab" id="hs_product_tab" href="#tab_products">Products</a></li>
                                        </ul>
                                        <div class="tab-content">

                                            <!-- ===================== Package Tab =============== -->
                                            <div id="tab_package" class="tab-pane fade in active">
                                                <?php

                                                foreach( $packages as $R )
                                                {
                                                echo '<button data-type="package" type="button" id="'.$R->id.'" class="addbtnssale btn btn-primary"> + '. $R->title." (".$R->price.")".'</button>';

                                                    $servicesInside = ', "services" : [ ';
                                                    foreach ( $R->packageservices  as $service )
                                                    {
                                                        $servicesInside .= '{ "name" : "'.addslashes($service->service->name).'", "id" : "'.$service->service->id.'", "packageserviceid" : "'.$service->id.'"  },';
                                                        //echo $service->service->name;
                                                    }
                                                    $servicesInside =  rtrim($servicesInside,',');
                                                    $servicesInside .= ']';

                                                    $servicesArr[] = array( "id" => $R->id, "name" => $R->title, "price" =>  $R->price, 'types' => 'package' , "tax" => SERVICE_TAX );
                                                    $servicesJsonData .= '{ "id" : "'.$R->id.'", "name" : "'.addslashes($R->title).'", "price" : "'.$R->price.'", "types" : "package" '.$servicesInside.', "tax" : '.SERVICE_TAX.' },';
} ?>
                                            </div>

                                            <!-- ======================= ./ Package Tab =============== -->

                                            <!-- ================= Services Tab ================-->
                                            <div id="tab_services" class="tab-pane fade">
<br /><br />
                                        <ul class="nav nav-tabs">
                                            <?php
                                            $x = 0;


                                            foreach ( $categories as $cat  ) { ?>

                                                <li class="<?php echo $x == 0 ? 'active' : ''; ?> "><a data-toggle="tab" href="#tab_<?php echo $cat->id;?>"><?php echo $cat->name; ?></a></li>
                                                <?php
                                                /*foreach( $cat->services as $R )
                                                {
                                                    echo $R->name;

                                                }*/
                                                $x++;
                                            }

                                            ?>


                                        </ul>

                                        <div class="tab-content">

                                            <?php
                                                $x = 0;

                                            foreach ( $categories as $cat  ) { ?>
                                                <div id="tab_<?php echo $cat->id;?>" class="tab-pane fade in <?php echo $x == 0 ? 'active' : ''; ?>">
                                                    <!--<h3><?php echo $cat->name; ?></h3>-->



                                            <?php
                                            foreach( $cat->services as $R )
                                            {
                                                echo '<button type="button" data-type="service" id="'.$R->id.'" class="addbtnssale btn btn-primary"> + '. $R->name." (".$R->price.")".'</button>';

                                                $servicesArr[] = array( "id" => $R->id, "name" => $R->name, "price" =>  $R->price, 'types' => 'service', "tax" => SERVICE_TAX );
                                                $servicesJsonData .= '{ "id" : "'.$R->id.'", "name" : "'.addslashes($R->name).'", "price" : "'.$R->price.'", "types" : "service" , "tax" : '.SERVICE_TAX.' },';
                                            }

                                            $x++;
                                            ?>
                                                </div>
                                            <?php
                                            }
                                                $servicesJsonData =  rtrim($servicesJsonData,",");

?>

                                        </div>
                                    </div>

                                            <!--============================= ./Services Tab =========================== -->

                                            <!-- =========================== Products Tab =============== -->
                                    <div id="tab_products" class="tab-pane fade" >
                                        <br /><br /><br />

					<div class="row">

					    <div class="col-md-4">
						<label>Barcode Scanning</label>
						<input type="text" class="form-control" id="productbarcode"  name="productbarcode" value="" >
					    </div>
					    
					    <div class="col-md-4">
						<label>Manual Select</label>

					    	<select style="width:300px;" data-plugin="select2" class="form-control" id="manual_productbarcode"  name="manual_productbarcode">
						    <option value="">Select</option>
						    <?php  foreach( $products as $item ) { 
						    ?>
						    <option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
						    <?php
						    
						    } ?>
						    
						</select>
					    </div>
                                            
					    <div class="col-md-4">
						<label>&nbsp;</label><br />
						<button class="btn btn-primary" id="add_product_button" type="button">Add</button>
                                            </div>
                                        </div>
                                                <br /><br /><br />
                                            </div>
                                            <!-- =========================== ./Products Tab ============== -->

                                        </div>

                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                    <h3>Invoice Details</h3>

                                    <div class="table-responsive">
                                        <table class="sales_table table table-hover table-bordered">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Package/Service/Product Name</th>
                                                <th>Unit Price</th>
                                                <th>Qnty.</th>
                                                <th>Discount Type</th>
                                                <th>Discount Value</th>
                                                <th>Discount Amount</th>
                                                <th>Total Price</th>
                                                <th>By Staff</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="invoicetable">

						<?php
						if( isset($sale) )
						{
						$k = 1;
						$subTotal = 0;

						if( isset($sale->saleItem)  && count($sale->saleItem) > 0  )
						{
                            $itemsLoop = -1;
						foreach( $sale->saleItem as $item )
						{

                            $itemsLoop++;

						$printItems .= '{ "id" : "'.$item->id.'",  "name" : "'.addslashes($item->title).'",  "totalprice" : "'.$item->getTotalPrice().'", "qnty" : "'.$item->quantity.'", "unitprice" : "'.$item->actualpriceperitem.'", "printamount" : "'. ( $item->actualpriceperitem * $item->quantity ).'"  },';
						$byStaff = '';
						if( $item->itemtype == 'package' )
						{
						//print_r($item->packageItems);

						foreach( $item->packageItems as $packageItem )
						{
						

                        // print_r( $packageItem->doneByStaffMembers );

                        $byStaff .= '<div class="service_staff_box" data-name="staffs'.$itemsLoop.'_'.$packageItem->id.'">'.$packageItem->title.'<button type="button" class="addstaff">Add Staff</button>';
                        
                        foreach( $packageItem->doneByStaffMembers as $staffMain ) { 
$staffOptionsIn = '<option value="">Select</option>';
                            //echo $staffMain->staffid;
                        
						
                        foreach ( $staffs as $staff )
						{
						$selected = '';
                        
						if( $staff->id == $staffMain->staffid )
						{
                              $selected = ' selected ';
						}
						$staffOptionsIn .= '<option '.$selected.' value="'.$staff->id.'">'.$staff->firstname.' '.$staff->lastname.'</option>';
                        
						}
                        $byStaff .= '<div class="service_done_assign"><select class="form-control" name="already_staffs_'.$staffMain->id.'">'.$staffOptionsIn.'</select><div class="row"><div class="col-md-4"><button type="button" class="remove_service_done"><i class="fa fa-remove"></i></button></div></div></div>';
                        
                    

						}
                        $byStaff .= '</div>';
                        }
						}
						else if ( $item->itemtype == 'service' )
						{
                           


                        $byStaff .= '<div class="service_staff_box" data-name="staffs'.$itemsLoop.'_services_'.$item->id.'"><button type="button" class="addstaff">Add Staff</button>';
                        foreach( $item->doneByStaffMembers as $staffMain ) { 

                            						$staffOptionsIn = '<option value="">Select</option>';
                            // echo $staffMain->staffid;
                        foreach ( $staffs as $staff )
						{
                            $selected = '';

                            if( $staff->id == $staffMain->staffid )
                            {
                                $selected = ' selected ';
                            }
                            $staffOptionsIn .= '<option '.$selected.' value="'.$staff->id.'">'.$staff->firstname.' '.$staff->lastname.'</option>';
						}
                            
                            
                            
                        $byStaff .= '<div class="service_done_assign"><select class="form-control" name="already_staffs_services_'.$staffMain->id.'">'.$staffOptionsIn.'</select><div class="row"><div class="col-md-4"><button type="button" class="remove_service_done"><i class="fa fa-remove"></i></button></div></div></div>';
                        }
                            $byStaff .= '</div>';
                        //         print_r( $item->doneByStaffMembers );
						
                            //$byStaff = '<div>'.$item->title.': <select class="form-control" name="staffs_services_'.$item->itemid.'">'.$staffOptionsIn.'</select></div>';

						}
						else if ( $item->itemtype == 'product' )
						{
                         
						
						$selected = '';

                        $byStaff .= '<div class="service_staff_box" data-name="staffs'.$itemsLoop.'_product_'.$item->id.'"><button type="button" class="addstaff">Add Staff</button>';
                        foreach( $item->doneByStaffMembers as $staffMain ) { 
                        $staffOptionsIn = '<option value="">Select</option>';    
                        foreach ( $staffs as $staff )
						{
                            $selected = ' ';
						if( $staff->id == $staffMain->staffid )
						{
						$selected = ' selected ';
						}
						$staffOptionsIn .= '<option '.$selected.' value="'.$staff->id.'">'.$staff->firstname.' '.$staff->lastname.'</option>';
						}
                                                $byStaff .= '<div class="service_done_assign"><select class="form-control" name="already_staffs_product_'.$staffMain->id.'">'.$staffOptionsIn.'</select><div class="row"><div class="col-md-4"><button type="button" class="remove_service_done"><i class="fa fa-remove"></i></button></div></div></div>';

                                                
                                                //$byStaff = '<div>'.$item->title.': <select class="form-control" name="staffs_product_'.$item->itemid.'">'.$staffOptionsIn.'</select></div>';
                        }
                         $byStaff .= '</div>';
                        
						}



						$subTotal += $item->getTotalPrice();
                        $totalDiscount += $item->discountamount;
						$printData .= '<tr >';
						$printData .= '<td>'.$item->title.'</td>';
						$printData .= '<td>'.$item->quantity.'</td>';
						$printData .= '<td>'.$item->discountamount.'</td>';
						$printData .= '<td>'.number_format($item->getTotalPrice(),2).'</td>';
                        $printData .= '</tr>';

                        $selectBoxStatus = '<select class="form-control" name="statuses[]">';

                        $selectBoxStatus .=  ' <option value="Pending" '.($item->status == "Pending" ? "selected" : "" ).' >Pending </option>';

                        $selectBoxStatus .=  '<option value="Under Process" '.($item->status == "Under Process" ? "selected" : "" ).' >Under Process</option>';

                        $selectBoxStatus .=  '<option value="Completed" '.($item->status == "Completed" ? "selected" : "" ).' >Completed</option>';
                         $selectBoxStatus .= '</select>';
						
						$rowsString = '<tr class="'.( $item->status == "Pending" ? "pendingClass" : "" ).' '. ( $item->status == "Under Process" ? "underprocessClass" : "" ).' '. ( $item->status == "Completed" ? "completedClass" : "" ).'">';
						$rowsString .= '<td>'.$k.'<input type="hidden" class="org_ids" name="org_ids[]" value="'.$item->id.'" /><input type="hidden" data-type="'.$item->itemtype.'" class="sale_ids" name="ids[]" value="'.$item->itemid.'" /><input type="hidden" class="sale_itemtype" name="itemtypes[]" value="'.$item->itemtype.'" /><input type="hidden" class="sale_taxes" name="taxes[]" value="'.$item->taxpercent.'" /></td>';
						$rowsString .= '<td>'.$item->title.'('.$item->barcode.')<br /><br>'.$selectBoxStatus.'</td>';
						$rowsString .= '<td><input type="text" name="unitprice[]" value="'.$item->actualpriceperitem.'" class="class_unitprice form-control"></td>';

						$k += 1;
						$discountTypeAbsoluteselectd = '';
						$discountTypePercentSelected = '';
						if( $item->discounttype == 'absolute' )
						{
						$discountTypeAbsoluteselectd = ' selected ';
						}
						else if ( $item->discounttype == 'percent' )
						{
						$discountTypePercentSelected = ' selected ';
						}


						
						
						$rowsString .= '<td><input type="text" class="sale_qnty form-control" name="qnty[]" value="'.$item->quantity.'"></td>';
						$rowsString .= '<td><select class="sale_discounttype form-control" name="discounttype[]"><option value="" >Select</option><option value="percent" '.$discountTypePercentSelected.' >%</option><option value="absolute" '.$discountTypeAbsoluteselectd.' >Absolute</option></select></td>';
						$rowsString .= '<td><input type="text" class="sale_discountvalue form-control" name="discountvalue[]" value="'.$item->discountvalue.'"></td>';
						$rowsString .= '<td><span class="sale_discount_amount">'.$item->discountamount.'</span></td>';
						$rowsString .= '<td><span class="totalprice">'.($item->getTotalPrice()).'</span></td>';

						$rowsString .= '<td>'.$byStaff.'</td>';

						$rowsString .= '<td class="text-nowrap"><button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete"  ><i class="fa fa-close" aria-hidden="true"></i></button></td>';
						$rowsString .= '</tr>'; 

						echo $rowsString;
						
						}
						
						}
						}
						?>


                                                <!--<tr>
                                                     <td>1</td>
                                                     <td>Pack1</td>
                                                     <td>
                                                     25
                                                     </td>
                                                     <td>
                                                     <input type="text" name="qnty[]" value="1">
                                                     </td>


                                                     <td>
                                                     <select>
                                                     <option value="">Select</option>
                                                     <option value="percent">%</option>
                                                     <option value="absolute">Absolute</option>

                                                     </select>
                                                     </td>
                                                     <td>
                                                     <input type="text" name="discountvalue[]" value="0">
                                                     </td>
                                                     <td>
                                                     <input type="text" name="discountamount[]" value="0">
                                                     </td>


                                                     <td>
                                                     100
                                                     </td>
                                                     <td class="text-nowrap">

                                                     <button type="button" class="btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm('Are you sure?')">
                                                     <i class="fa fa-close" aria-hidden="true"></i>
                                                     </button>
                                                     </td>
						     </tr> -->

						<?php $printData .= '</tbody><tfoot>'; ?>

<?php  $totalDiscount += $sale->discountamount; ?>
						<?php $printData .= '<tr><td>&nbsp;</td></tr>'; ?>
						<?php $printData .=  '<tr><td colspan="3" >Sub Total</td><td>'.$subTotal.'</td></tr>'; ?>
						<?php $printData .=  '<tr><td colspan="3" >Discount</td><td>'.number_format($sale->discountamount,2).'</td></tr>'; ?>
						<?php $printData .=  '<tr><td colspan="3" >Tax</td><td>'.$sale->taxamount.'</td></tr>'; ?>

                        <?php $printData .=  '<tr><td colspan="3" ><b>Grand Total</b></td><td>'.number_format($sale->paidprice, 2).'</td></tr>'; ?>
						<?php $printData .= '</tfoot>'; ?>
	
						
    </tbody>
    <tfoot>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Sub Total</th>
                        <th align="left" colspan="3"><span id="subtotal"><?php echo number_format($subTotal, 2);  ?></span></th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Type</th>
                                                <th align="left" colspan="3">
						    
						    <select class="global_discounttype form-control" name="global_discounttype">
							<option value="">Select</option>
							<option <?php echo $sale->discounttype == 'percent' ? 'selected' : ''; ?> value="percent">%</option>
							<option  <?php echo $sale->discounttype == 'absolute' ? 'selected' : ''; ?> value="absolute">Absolute</option>
						    </select>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Value</th>
                                                <th align="left" colspan="3">

                                                    <input class="form-control" type="text" id="global_discountvalue" name="global_discountvalue" value="<?php echo $sale->discountvalue; ?>" />
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Amount</th>
                                                <th align="left" colspan="3">

                                                    <span id="global_discount_amount"><?php echo $sale->discountamount; ?></span>
                                                </th>
                                            </tr>

                                            <tr >
                                                <th align="right" style="text-align: right;" colspan="7">Taxable Amount</th>
                                                <th align="left" colspan="3">

                                                    <span id="taxable_amount"><?php echo $subTotal - $sale->discountamount - $sale->taxamount;  ?></span>
                                                </th>
                                            </tr>
                                            <tr style="display: none;">
                                                <th align="right" style="text-align: right;" colspan="7">Tax Percent</th>
                                                <th align="left" colspan="3">
                                                    <input class="form-control" type="text" id="taxpercent" name="taxpercent" value="<?php echo $sale->taxpercent; ?>" />
                                                </th>
                                            </tr>

                                            <tr >
                                                <th align="right" style="text-align: right;" colspan="7">Tax Amount</th>
                                                <th align="left" colspan="3">
                                                    <span id="taxamount"><?php echo $sale->taxamount; ?></span>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Grand Total</th>
                                                <th align="left" colspan="3">
                                                    <span id="grandtotal"><?php echo $sale->paidprice ; ?></span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Amount Received</th>
                                                <th align="left" colspan="3">
                                                    <span id=""><input id="receivedamount" type="text" value> </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Balance Amount</th>
                                                <th align="left" colspan="3">
                                                    <span id="balanceamount"></span>
                                                </th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                </div>
                                <?php
                                //print_r(  Request::old('hdnaddedservice') );
                                //  $arrVals = Request::old('hdnaddedservice');
                                ?>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>
<?php if( $user->role == 'Super Admin' ) { ?>
                                        {{ Form::submit('Save',  array('class' => 'btn btn-primary', 'id' => 'btn_save_sale'))  }}
<?php } ?>
                                        <button id="printbtn"  type="button" class="btn btn-primary">Print</button>

                                    </div>
                                </div>

                            </div> {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br /><br />
    <div id="print_screen">
	<?php $printData .= '</table>'; ?>
	<?php echo $printData; ?>
    </div>
    
    <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <iframe style="width: 100%; height: 100%;" src="<?php echo route('allpayment.create',['saleid' =>  $sale->id , 'iframe' => '1' ]); ?>"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         
      </div>
    </div>
  </div>
</div>
    
    
<script>
    var jsonDataServices = '[<?php echo $servicesJsonData; ?>]';

    var jsonForPrintItems = '[<?php echo rtrim($printItems,','); ?>]';
    var subTotal = "<?php echo $subTotal + floatval($totalDiscount) - floatval($sale->discountamount);  ?>";
    var totalDiscount = "<?php echo $totalDiscount; ?>";
    var totalTax = "<?php echo $sale->taxamount;  ?>";
    var paidPrice = "<?php echo $sale->paidprice; ?>";
var servicesTaxPercent = <?php echo SERVICE_TAX; ?>;
    var jsonObjForServices = JSON.parse(jsonDataServices);

    // alert(jsonObjForServices.length);

    var rowsString = '<tr>';
    rowsString += '<td>{rowno}<input type="hidden" data-type="{itemtype}" class="sale_ids" name="ids[]" value="{ids}" /><input type="hidden" class="sale_itemtype" name="itemtypes[]" value="{itemtype}" /><input type="hidden" class="sale_taxes" name="taxes[]" value="{taxes}" /></td>';
    rowsString += '<td>{product_servicename}<br /><br /><select class="form-control" name="statuses[]"><option value="Pending">Pending</option><option value="Under Process">Under Process</option><option value="Completed">Completed</option></select></td>';
    rowsString += '<td><input type="text" name="unitprice[]" value="{unitprice}" class="class_unitprice form-control"></td>';

    rowsString += '<td><input type="text" class="sale_qnty form-control" name="qnty[]" value="{qnty}"></td>';
    rowsString += '<td><select class="sale_discounttype form-control" name="discounttype[]"><option value="" >Select</option><option value="percent">%</option><option value="absolute">Absolute</option></select></td>';
    rowsString += '<td><input type="text" class="sale_discountvalue form-control" name="discountvalue[]" value="0"></td>';
    rowsString += '<td><span class="sale_discount_amount">0</span></td>';
    rowsString += '<td><span class="totalprice">{totalprice}</span></td>';
    rowsString += '<td>{staffstring}</td>';
    rowsString += '<td class="text-nowrap"><button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete"  ><i class="fa fa-close" aria-hidden="true"></i></button></td>';
  rowsString += '</tr>';

  var productArr = <?php  if(count($products) > 0 ) { echo $products; } else { echo '[]'; } ?>;
  var staffOptions = '<?php echo $staffOptions; ?>';
  var selectedPackage =  '<?php echo $sale->packageid; ?>';
</script>

@endsection