@extends('layouts.app')
    
@section('content')
    <?php
    $selectProducts = '<select data-plugin="select2" id="purchase_manual_product"  name="" class=" form-control">';
$selectProducts .= '<option value="">Select</option>';
     foreach( $products as $p ){
         $selectProducts .= '<option value="'.$p->id.'">'.htmlspecialchars($p->name, ENT_QUOTES).'</option>';
     }
$selectProducts .=  '</select>';
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Purchase <div style="float: right">
			    <a  href="<?php echo route('purchase.index'); ?>"><strong>+ List Purchases</strong></a>
			</div></div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'purchase.store')) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-12">

                                <div class="row">
                                        <div class="form-group col-md-3">
                                            {{ Form::label('companyid', 'Select Company') }} *
                                                <br />
                                                {{ Form::select('companyid', $companies , '', array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group col-md-3">
                                            {{ Form::label('invoiceno', 'Invoice No.') }} *
                                            {{ Form::text('invoiceno', '', array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group col-md-3">
                                            {{ Form::label('billdate', 'Bill Date') }} *
                                            {{ Form::text('billdate', Request::old('billdate') != '' ? Request::old('billdate') : date("d/m/Y")  , array('class' => 'form-control') ) }}
                                        </div>
                                        <div class="form-group col-md-3">

                                                                                                              {{ Form::label('deliverydate', 'Delivery Date') }} *
                                            {{ Form::text('deliverydate', Request::old('deliverydate') != '' ? Request::old('deliverydate') : date("d/m/Y")  , array('class' => 'form-control') ) }}
</div>
				</div>
				<div class="row">

				    

                                    <div class="form-group col-md-3">
                                        {{ Form::label('paymentduedate', 'Payment Due Date') }} *
                                        {{ Form::text('paymentduedate', Request::old('paymentduedate') != '' ?  Request::old('paymentduedate') : date("d/m/Y"), array('class' => 'form-control') ) }}
                                    </div>
				</div>

				<div class="form-group row">
				    <div class="col-md-12">
				    <div class="row">
                                        <div class="col-md-3">
					    {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                            {{ Form::select('paymentmodeid', $paymentmodes, '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
					</div>

					<div class="col-md-3">
					    {{ Form::label('amount', 'Down Payment') }} *<br />
                                            {{ Form::text('amount',  '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
					    
					    <p>(If full payment paid then leave it empty)</p>
					</div>

					
				    </div>
				</div>
				</div>

				<div id="sale_other_payment_info" class="form-group row " style="<?php if(Request::old('paymentmodeid') != '5' ) {  echo 'display:none'; } ?>">
				    <div class="col-md-12">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
					    {{ Form::text('other', '' , array('class' => 'form-control' ) ) }}
					</div>
				    </div>
				    </div>
				</div>
				<div id="sale_bank_payment_info" class="form-group row " style="<?php if(Request::old('paymentmodeid') != '2' ) {  echo 'display:none'; } ?>">
				    <div class="col-md-12">
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
				</div>

				
				<div class="row">
				    <div class="col-md-12">
					<div class="row">



                                                                                                    <div class="row">

							    <div class="col-md-4">
								<label>Barcode Scanning</label>
								<input type="text" class="form-control" id="purchase_productbarcode"  name="purchase_productbarcode" value="" >
							    </div>
							    
							    <div class="col-md-4">
								<label>Manual Select</label>
                                                                                                    
<?php echo $selectProducts; ?>


								</ul>							    
							    </div>
                                                            
							    <div class="col-md-4">
								<label>&nbsp;</label><br />
								<button class="btn btn-primary" id="add_product_button" type="button">Add</button>
                                               </div>
                                           </div>

                                                                                                    					    <h3>Invoice Details</h3>
					    <div class="table-responsive">
						<table class="sales_table table table-hover table-bordered">
						    <thead>
							<tr>
							    <th>#</th>
							    <th>
                                                                                                    <div style="width: 250px;">Product Name</div></th>
							    <th>
                                                                                                    <div style="width: 100px;">
                                                                                                    Price
                                                                                                    </div></th>
							    <th>
                                                                                                    <div style="width: 80px;">
                                                                                                    Qnty.
</div>
                                                                                                    </th>
							    <th>
  <div style="width: 100px;">                                                                                                  Discount Type</div>
                                                                                                    </th>
							    <th>
  <div style="width: 100px;">                                                                       
                                                                                                    Discount Value
</div>
                                                                                                    </th>
							    <th>
<div style="width: 130px;">
                                                                                                    Discount Amount
</div>
                                                                                                    </th>
							    <th>
<div style="width: 120px;">
                                                                                                    Total Price
</div>
                                                                                                    </th>
							    <th>
<div style="width: 100px;">
                                                                                                    Action
</div>
                                                                                                    </th>





                                                                       </tr>
						    </thead>
						    <tbody id="purchase_invoicetable">
							<?php

							$arrs = Request::old('ids');
							
							if( $arrs && count($arrs) > 0 )
							{
							$arrUnitPrices = Request::old('unitprice');
							$arrNames =  Request::old('product_names');
							$arrQnty =  Request::old('qnty');
							$arrDiscountType =  Request::old('discounttype');
							$arrDiscountValue =  Request::old('discountvalue');

						  

                            for( $k = 0; $k < count($arrs); $k ++ ) { 
							?>
							<tr><td>1
							    <input type="hidden" class="product_names" name="product_names[]" value="<?php echo $arrNames[$k]; ?>">
							    <input type="hidden" class="purchase_ids" name="ids[]" value="<?php echo $arrs[$k]; ?>">
                                <input type="hidden" class="purchase_product_taxes" name="purchase_product_taxes[]" value="<?php echo Request::old('purchase_product_taxes')[$k]; ?>" />
							</td>
							<td><?php echo $arrNames[$k]; ?></td>
							<td>
							    <input type="number" name="unitprice[]" class="purchase_unit_price form-control" value="<?php echo $arrUnitPrices[$k]; ?>">
							</td>
							<td>
							    <input type="text" class="purchase_qnty form-control" name="qnty[]" value="<?php echo $arrQnty[$k]; ?>">
							</td>
							<td>
							    <select class="purchase_discounttype form-control" name="discounttype[]">
								<option value="">Select</option>
								<option value="percent" <?php echo $arrDiscountType[$k] == 'percent' ? 'selected' : ''; ?>>%</option>
								<option value="absolute" <?php echo $arrDiscountType[$k] == 'absolute' ? 'selected' : ''; ?>>Absolute</option>
							    </select>
							</td>
							<td>
							    <input type="text" class="purchase_discountvalue form-control" name="discountvalue[]" value="<?php echo $arrDiscountValue[$k]; ?>"></td>
							<td>
							    <span class="purchase_discount_amount">0</span></td><td><span class="purchase_product_total_price">0</span></td><td class="text-nowrap">
								<button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-close" aria-hidden="true"></i></button></td></tr>
                                 <?php
                                 
}
							}
							?>



        
						    </tbody>
						    <tfoot>



<tr>
    <th colspan="7" align="right" style="text-align: right;">
       Total Amount
       
       </th>
       <th colspan="2">
       <span id="purchase_total_amount_without_discount"></span>
       </th>
       
       </tr>


                                                                                                                                                <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Type</th>
                                                <th align="left" colspan="2">
                                                <select class="global_discounttype form-control" name="global_discounttype">
                                <option value="">Select</option>
                                <option value="percent" <?php echo Request::old('global_discounttype') == 'percent' ? 'selected' : ''; ?>>%</option>
                                <option value="absolute" <?php echo Request::old('global_discounttype') == 'absolute' ? 'selected' : ''; ?> >Absolute</option>
                                </select>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Value</th>
                                                <th align="left" colspan="2">

                                                    <input type="text" id="global_discountvalue" name="global_discountvalue" value="<?php echo Request::old("global_discountvalue"); ?>" />
                                                </th>
                                            </tr>
                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Amount</th>
                                                <th align="left" colspan="2">

                                                    <span id="global_discount_amount">0</span>
                                                </th>
                                            </tr>


					    <tr>

                                                
						<th colspan="7" align="right" style="text-align: right;">
						    Total Discount
						    
						</th>
						<th colspan="2">
						    <span id="purchase_total_discount"></span>
						</th>

						<tr>
						    <th colspan="7" align="right" style="text-align: right;">
							Taxable Amount
							
						    </th>
						    <th  colspan="2">
							<span id="purchase_taxable_amount"></span>
						    </th>
						    
						</tr>

					    </tr>
					    <tr>
						<th colspan="7" align="right" style="text-align: right;">
						    Total Tax
						    
						</th>
						<th  colspan="2">
						    <span id="purchase_total_tax"></span>
						</th>
						
					    </tr>
					    <tr>
						<th colspan="7" align="right" style="text-align: right;">
       Grand Total
       
       </th>
       <th  colspan="2" >
       <span id="purchase_grand_total"></span>
       </th>
       
       </tr>
       
       </tfoot>
						</table>
				    </div>
				</div>



                                    </div>
                                </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>




        // alert(jsonObjForServices.length);

        var strPurchaseRow = '<tr>';
        strPurchaseRow += '<td>{rowno}<input type="hidden" class="product_names" name="product_names[]" value="{product_names}" /><input type="hidden"  class="purchase_ids" name="ids[]" value="{ids}" /><input type="hidden" class="purchase_product_taxes" name="purchase_product_taxes[]" value="{purchase_product_taxes}" /></td>';
        strPurchaseRow += '<td>{productname}</td>';
        strPurchaseRow += '<td><input type="number" name="unitprice[]" class="purchase_unit_price form-control" value="{unitprice}"></span></td>';

        strPurchaseRow += '<td><input type="text" class="purchase_qnty form-control" name="qnty[]" value="{qnty}"></td>';
        strPurchaseRow += '<td><select class="purchase_discounttype form-control" name="discounttype[]"><option value="" >Select</option><option value="percent">%</option><option value="absolute">Absolute</option></select></td>';
        strPurchaseRow += '<td><input type="text" class="purchase_discountvalue form-control" name="discountvalue[]" value="0"></td>';
        strPurchaseRow += '<td><span class="purchase_discount_amount">0</span></td>';
        strPurchaseRow += '<td><span class="purchase_product_total_price">{totalprice}</span></td>';

        strPurchaseRow += '<td class="text-nowrap"><button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-close" aria-hidden="true"></i></button></td>';
        strPurchaseRow += '</tr>';


        var productArr = <?php  if(count($products) > 0 ) { echo $products; } else { echo '[]'; } ?>;

var shouldCaluculate = false;
<?php if($arrs && count($arrs) > 0 ) {
?>
shouldCaluculate = true;
<?php 
}
?>
</script>
@endsection
