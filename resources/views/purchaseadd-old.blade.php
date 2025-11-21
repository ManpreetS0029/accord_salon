@extends('layouts.app')
    
@section('content')
    <?php
    $selectProducts = '<select data-plugin="select2" name="purchase_products[]" class="purchase_products form-control">';
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
                    <div class="panel-heading">Add Purchase</div>

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
                                        {{ Form::label('downpayment', 'Down Payment') }} *
                                        {{ Form::text('downpayment', '', array('class' => 'form-control') ) }}
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ Form::label('paymentduedate', 'Payment Due Date') }} *
                                        {{ Form::text('paymentduedate', Request::old('paymentduedate') != '' ?  Request::old('paymentduedate') : date("d/m/Y"), array('class' => 'form-control') ) }}
                                    </div>
				</div>

				<div class="row">
				    <div class="col-md-12">
					<div class="row">
					    <h3>Invoice Details</h3>

					    <div class="table-responsive">
						<table class="sales_table table table-hover table-bordered">
						    <thead>
							<tr>
							    <th>#</th>
							    <th>
                                                                                                    <div style="width: 250px;">Product Name</div></th>
							    <th>
                                                                                                    <div style="width: 50px;">
                                                                                                    Price
                                                                                                    </div></th>
							    <th>
                                                                                                    <div style="width: 50px;">
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


                                                                                                    $pRows = max( count( Request::old('purchase_products') ), 5 );

$qntyArr = Request::old('purchase_qnty');
							$unitPriceArr = Request::old('purchase_unit_price');
							$productsSelect = Request::old('purchase_products');
							$purchaseDiscountVal = Request::old('purchase_discount_value');
							$purchaseDiscountType = Request::old('purchase_discount_type');

$purchaseProductTaxes = Request::old('purchase_product_taxes');
$purchaseTotalDiscount = 0;
$totalTax = 0;
$totalAmountWithOutDiscount = 0;
$taxableAmount = 0;
$grandTotal = 0;

for( $x= 0; $x < $pRows; $x++ ) {


    $productPrice = floatval($qntyArr[$x]) * floatval($unitPriceArr[$x]);
    $discountAmount = 0;
    $totalAmountWithOutDiscount += $productPrice;
    
    if( $purchaseDiscountType[$x] == 'percent' && $purchaseDiscountVal[$x] > 0 )
    {
        $discountAmount = $purchaseDiscountVal[$x] *  $productPrice  / 100;
    }
    else if( $purchaseDiscountType[$x] == 'absolute' && $purchaseDiscountVal[$x] > 0 )
    {
        $discountAmount = $purchaseDiscountVal[$x];
    }
    


    $totalPrice = $productPrice - $discountAmount;
     $purchaseTotalDiscount += $discountAmount;
    $tax = $purchaseProductTaxes[$x];
    $taxValues = max(0, ($tax * $totalPrice / 100) ) ; 
              $totalTax += $taxValues;
              $taxableAmount += $totalPrice;
              $grandTotal += $totalPrice + $taxValues; 

        
    ?>
							<tr>
        <td><?php echo $x+1; ?></td>
							    <td>
    <input type="hidden" class="purchase_product_taxes" name="purchase_product_taxes[]" value="<?php echo $tax; ?>" />
								<select style="width: 250px;" class="purchase_products form-control" name="purchase_products[]" data-plugin="select2">
								    <option value="">Select</option>
        <?php 
        foreach( $products as $p ){
?>
<option <?php if( $p->id == $productsSelect[$x] ) { echo 'selected' ; } ?> value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
			<?php } ?>					    
								</select>
							    </td>
							    <td>
								<input style="width: 50px;" class="purchase_unit_price" type="number" name="purchase_unit_price[]" id="purchase_unit_price" value="<?php echo floatval($unitPriceArr[$x]); ?>" />
							    </td>
							    <td>
								<input style="width: 50px;" type="number" name="purchase_qnty[]" class="purchase_qnty" value="<?php echo max(intval($qntyArr[$x]),1); ?>" class="form-control" />
							    </td>							   <td> 
							    <select name="purchase_discount_type[]" class="purchase_discount_type form-control">
								<option value="">Select</option>
								<option <?php if( $purchaseDiscountType[$x] == 'percent' ) { echo 'selected'; } ?> value="percent">%</option>
								<option value="absolute" <?php if( $purchaseDiscountType[$x] == 'absolute' ) { echo 'selected'; } ?> >Absolute</option>

							    </select>
							    </td>
							    <td>
								<input style="width: 100px;" type="number" name="purchase_discount_value[]" class="purchase_discount_value" value="<?php echo max(0,$purchaseDiscountVal[$x]); ?>" class="form-control" />
							    </td>
                                    <td><span class="purchase_discount_amount"><?php echo number_format($discountAmount,2); ?></span> </td>
                                    <td><span class="purchase_product_total_price"><?php echo $totalPrice; ?></span> </td>
							    <td>

<?php if( $x > 4) {
            ?>
<button type="button" class="deleteitempurchasebtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-close" aria-hidden="true"></i></button>
            <?php 
        }
?>
                                    </td>
							</tr>

							<?php } ?>
        
						    </tbody>
						    <tfoot>


<tr>
							<td colspan="9" align="left"><button type="button" class="btn btn-primary add_more_product">+ Add More</button> </td>
       </tr>

<tr>
       <td colspan="8" align="right">
       Total Amount
       
       </td>
       <td>
       <span id="purchase_total_amount_without_discount"><?php echo $totalAmountWithOutDiscount; ?></span>
       </td>
       
       </tr>



       <tr>
       <td colspan="8" align="right">
       Total Discount
       
       </td>
       <td>
       <span id="purchase_total_discount"><?php echo $purchaseTotalDiscount; ?></span>
       </td>

       <tr>
       <td colspan="8" align="right">
       Taxable Amount
       
       </td>
       <td>
       <span id="purchase_taxable_amount"><?php echo $taxableAmount; ?></span>
       </td>
       
       </tr>

       </tr>
              <tr>
       <td colspan="8" align="right">
       Total Tax
       
       </td>
       <td>
       <span id="purchase_total_tax"><?php echo $totalTax; ?></span>
       </td>
       
       </tr>
  <tr>
       <td colspan="8" align="right">
       Grand Total
       
       </td>
       <td>
       <span id="purchase_grand_total"><?php echo $grandTotal; ?></span>
       </td>
       
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
var markNo = <?php echo $pRows; ?>;
var strPurchaseRow = '<tr>';
strPurchaseRow += '<td>{mark}</td><td><input type="hidden" class="purchase_product_taxes" name="purchase_product_taxes" /><?php echo $selectProducts; ?></td>';
strPurchaseRow += '<td><input style="width: 50px;" class="purchase_unit_price" type="number" name="purchase_unit_price[]" id="purchase_unit_price" value="0"></td>';
strPurchaseRow += '<td><input style="width: 50px;" type="number" name="purchase_qnty[]" class="purchase_qnty" value="1"></td>';
strPurchaseRow += '<td><select name="purchase_discount_type[]" class="purchase_discount_type form-control"><option value="">Select</option><option value="percent">%</option><option value="absolute">Absolute</option></select></td>';
strPurchaseRow += '<td><input style="width: 100px;" type="number" name="purchase_discount_value[]" class="purchase_discount_value" value="0"></td>';
strPurchaseRow += '<td><span class="purchase_discount_amount">0.00</span></td>';
strPurchaseRow += '<td><span class="purchase_product_total_price">0</span></td>';
strPurchaseRow += '<td><button type="button" class="deleteitempurchasebtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-close" aria-hidden="true"></i></button></td>';
strPurchaseRow += '</tr>';
</script>
@endsection
