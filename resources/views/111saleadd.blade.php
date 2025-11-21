<?php
$servicesArr = array();
$servicesJsonData = '';

$staffOptions = '<option value="">Select</option>';
    foreach ( $staffs as $staff )
        {
            $staffOptions .= '<option value="'.$staff->id.'">'.$staff->firstname.' '.$staff->lastname.'</option>';
        }

?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Sale
<div style="float: right">
			    <a  href="<?php echo route('sale.index'); ?>"><strong>List Sale</strong></a>
			</div>
        </div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => ('sale.store'))) }}
                            {{ csrf_field() }}

                            <div class="panel-body container-fluid">

				<ul class="nav nav-tabs">
				    <li class="<?php echo Request::old('clientname') == '' ? 'active' : ''; ?>" active"><a data-toggle="tab" href="#tab_existclient">Client Info</a></li>
				    <li class="<?php echo Request::old('clientname') != '' ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab_newclient">Add New Client</a></li>
				</ul>
				<div class="tab-content">
				    <div id="tab_existclient" class="tab-pane fade <?php  echo Request::old('clientname') == '' ? 'in active' : ''; ?>" >
					<div class="row row-lg" style="margin-top:20px; margin-bottom: 20px;">

					    <div class="col-lg-6">

						
						
						

						<div class="form-group row exist_client_info">


						    {{ Form::label('title', 'Select Client Or Walk-In') }} *
						    <br />
						    <div class="row">
							<div class="col-md-6">
                            {{ Form::select('clientid', $clients, '' , array('class' => 'form-control', 'data-plugin' => "select2") ) }}
							</div>
							<div class="col-md-6">
							    {{ Form::text('walkin_name', 'Walk-In' , array('class' => 'form-control') ) }}
							</div>

						    </div>                   </div>


					    </div>
					</div>
					
				    </div>
				    <div id="tab_newclient" class="tab-pane fade <?php  echo Request::old('clientname') != '' ? 'in active' : ''; ?>">


					<div class="row new_client_info" style="margin-top:20px; margin-bottom: 20px;">
					   
						
						<div class="row">
						    <div class="form-group col-md-4">
							{{ Form::label('clientname', 'Client Name') }} *
							{{ Form::text('clientname', '', array('class' => 'form-control') ) }}
						    </div>

						    
						    <div class="form-group col-md-3">

							{{ Form::label('dob', 'Date of Birth') }} * (dd/mm/yyyy)
							{{ Form::text('dob', '', array('class' => 'form-control', 'placeholder' => 'dd/mm/yyyy') ) }}
						    </div>
						    

						    <div class="form-group col-md-2">
							{{ Form::label('phone', 'Phone') }} *
							{{ Form::text('phone', '', array('class' => 'form-control') ) }}
						    </div>

						    <div class="form-group col-md-2">
							{{ Form::label('phone2', 'Phone 2') }}
							{{ Form::text('phone2', '', array('class' => 'form-control') ) }}
						    </div>


						</div>

						<div class="row"> 					
						    <div class="form-group col-md-4">
							{{ Form::label('email', 'Email Address') }}
							{{ Form::text('email', '', array('class' => 'form-control') ) }}
						    </div>
						</div>
						<div class="row"> 		
						    <div class="form-group col-md-4">
							{{ Form::label('address', 'Address') }}
							{{ Form::text('address', '', array('class' => 'form-control') ) }}
						    </div>
						    
						    <div class="form-group col-md-4">
							{{ Form::label('city', 'City') }}
							{{ Form::text('city', '', array('class' => 'form-control') ) }}
						    </div>

						    <div class="form-group col-md-4">
							{{ Form::label('state', 'State') }}
							{{ Form::text('state', '', array('class' => 'form-control') ) }}
						    </div>
						</div>
						<div class="row">
						    <div class="form-group col-md-3">
							{{ Form::label('zipcode', 'Zip Code') }}
							{{ Form::text('zipcode', '', array('class' => 'form-control') ) }}
						    </div>
						</div>

						<div style="clear:both;"></div>
					    
					</div>
					
				    </div>
				</div>
				
				
				
                             

				<div class="form-group row">
                                    <div class="row">
                                        <div class="col-md-3">
					    {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                            {{ Form::select('paymentmodeid', $paymentmodes, '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
					</div>

					<div class="col-md-3">
					    {{ Form::label('amount', 'Amount Paid') }} *<br />
                                            {{ Form::text('amount',  '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
					   
					    <p>(If full payment paid then leave it empty)</p>
					</div>

					
				    </div>
				</div>

				<div id="sale_other_payment_info" class="form-group row " style="<?php if(Request::old('paymentmodeid') != '5' ) {  echo 'display:none'; } ?>">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
					    {{ Form::text('other', '' , array('class' => 'form-control' ) ) }}
					</div>
				    </div>
				</div>

				<div id="sale_bank_payment_info" class="form-group row " style="<?php if(Request::old('paymentmodeid') != '2' ) {  echo 'display:none'; } ?>">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('bankname', 'Bank Name') }} 
					    {{ Form::text('bankname', '' , array('class' => 'form-control' ) ) }}
					</div>

					<div class="form-group col-md-3">
					    {{ Form::label('bankaccountno', 'Bank Account No') }} 
					    {{ Form::text('bankaccountno', '' , array('class' => 'form-control' ) ) }}
					</div>

					
					<div class="form-group col-md-3">
					    {{ Form::label('chequeno', 'Cheque No') }} 
					    {{ Form::text('chequeno', '' , array('class' => 'form-control' ) ) }}
					</div>
					<div class="form-group col-md-3">
					    {{ Form::label('chequedate', 'Cheque Date') }} 
					    {{ Form::text('chequedate', '' , array('class' => 'form-control' ) ) }}
					</div>
					
				    </div>
				</div>
				

				
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
                                                $servicesInside .= '{ "name" : "'.addslashes($service->service->name).'", "id" : "'.$service->service->id.'"},';
                                                //echo $service->service->name;
                                                }
                                                $servicesInside =  rtrim($servicesInside,',');
                                                $servicesInside .= ']';
                                                $servicesArr[] = array( "id" => $R->id, "name" => $R->title, "price" =>  $R->price, 'types' => 'package', "tax" => SERVICE_TAX  );
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
							$servicesJsonData .= '{ "id" : "'.$R->id.'", "name" : "'.addslashes($R->name).'", "price" : "'.$R->price.'", "types" : "service", "tax" : '.SERVICE_TAX.' },';
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

						    <div class="form-group col-md-12">
							
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
<!-- 								<ul id="autosearchproduct" class="searchlist" style="" >


								</ul>-->
							    </div>
                                                            
							    <div class="col-md-4">
								<label>&nbsp;</label><br />
								<button class="btn btn-primary" id="add_product_button" type="button">Add</button>
                                               </div>
                                           </div>


                                                </div>
                                                </div>
                                                <br /><br /><br />
                                            </div>
                                            <!-- =========================== ./Products Tab ============== -->

                                        </div>

                                    </div>

                                </div>

                                <div class="row">
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



                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Sub Total</th>
                                                <th align="left" colspan="3"><span id="subtotal">0.00</span></th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Type</th>
                                                <th align="left" colspan="3">
                                                <select class="global_discounttype form-control" name="global_discounttype"><option value="">Select</option><option value="percent">%</option><option value="absolute">Absolute</option></select>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Value</th>
                                                <th align="left" colspan="3">

                                                    <input type="text" id="global_discountvalue" name="global_discountvalue" value="<?php echo Request::old("global_discountvalue"); ?>" />
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Amount</th>
                                                <th align="left" colspan="3">

                                                    <span id="global_discount_amount">0</span>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Taxable Amount</th>
                                                <th align="left" colspan="3">

                                                    <span id="taxable_amount">0</span>
                                                </th>
                                            </tr>
                                            <tr style="display: none;">
                                                <th align="right" style="text-align: right;" colspan="7">Tax Percent</th>
                                                <th align="left" colspan="3">
                                                   <input type="text" id="taxpercent" name="taxpercent" value="<?php echo Request::old("taxpercent"); ?>" />
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Tax Amount</th>
                                                <th align="left" colspan="3">
                                                     <span id="taxamount">0</span>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Grand Total</th>
                                                <th align="left" colspan="3">
                                                    <span id="grandtotal">0.00</span>
                                                </th>
                                            </tr>
                                            </tfoot>
                                        </table>
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

                                        {{ Form::submit('Save',  array('class' => 'btn btn-primary'))  }}
                                    </div>
                                </div>

                            </div> {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var jsonDataServices = '[<?php echo $servicesJsonData; ?>]';

        var jsonObjForServices = JSON.parse(jsonDataServices);
        // alert(jsonObjForServices.length);
var servicesTaxPercent = <?php echo SERVICE_TAX; ?>;
        var rowsString = '<tr>';
        rowsString += '<td>{rowno}<input type="hidden" data-type="{itemtype}" class="sale_ids" name="ids[]" value="{ids}" /><input type="hidden" class="sale_itemtype" name="itemtypes[]" value="{itemtype}" /><input type="hidden" class="sale_taxes" name="taxes[]" value="{taxes}" /></td>';
        rowsString += '<td>{product_servicename}</td>';
        rowsString += '<td><input type="text" name="unitprice[]" value="{unitprice}" class="class_unitprice form-control"></td>';

        rowsString += '<td><input type="text" class="sale_qnty form-control" name="qnty[]" value="{qnty}"></td>';
        rowsString += '<td><select class="sale_discounttype form-control" name="discounttype[]"><option value="" >Select</option><option value="percent">%</option><option value="absolute">Absolute</option></select></td>';
        rowsString += '<td><input type="text" class="sale_discountvalue form-control" name="discountvalue[]" value="0"></td>';
        rowsString += '<td><span class="sale_discount_amount">0</span></td>';
        rowsString += '<td><span class="totalprice">{totalprice}</span></td>';
        rowsString += '<td>{staffstring}</td>';
        rowsString += '<td class="text-nowrap"><button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-close" aria-hidden="true"></i></button></td>';
        rowsString += '</tr>';


        var productArr = <?php  if(count($products) > 0 ) { echo $products; } else { echo '[]'; } ?>;

        var staffOptions = '<?php echo $staffOptions; ?>';

    </script>
@endsection

