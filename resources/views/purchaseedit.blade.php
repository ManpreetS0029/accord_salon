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
                    <div class="panel-heading"> Update Purchase
<div style="float:right"><a href="<?php echo route('purchase.index'); ?>">List Purchases</a> | <a href="<?php echo URL::to('/'); ?>/purchase/<?php echo $purchase->id; ?>/paymenthistory">View Payment History <?php if( ( $purchase->getTotalPaidAmount() + $purchase->getAmountUnderReview() ) < $purchase->grandtotal) { echo ' | <a href="'.route('purchase.addpayment',$purchase->id).'">Add Payment</a>'; } ?></a></div>
                </div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
{{ Form::open(array( 'method' => 'PUT', 'route' => ['purchase.update', $purchase->id])) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-12">

                                <div class="row">
                                        <div class="form-group col-md-3">
                                            {{ Form::label('companyid', 'Select Company') }} *
                                                <br />
                                                {{ Form::select('companyid', $companies , $purchase->companyid, array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group col-md-3">
                                            {{ Form::label('invoiceno', 'Invoice No.') }} *
                                            {{ Form::text('invoiceno', $purchase->invoiceno , array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group col-md-3">
                                            {{ Form::label('billdate', 'Bill Date') }} *
                                            {{ Form::text('billdate', Request::old('billdate') != '' ? Request::old('billdate') : date("d/m/Y", strtotime($purchase->billdate))  , array('class' => 'form-control') ) }}
                                        </div>
                                        <div class="form-group col-md-3">

                                                                                                              {{ Form::label('deliverydate', 'Delivery Date') }} *
                                            {{ Form::text('deliverydate', Request::old('deliverydate') != '' ? Request::old('deliverydate') : date("d/m/Y", strtotime($purchase->deliverydate))  , array('class' => 'form-control') ) }}
</div>
				</div>
				<div class="row">

				    
                                   <div class="form-group col-md-3">
                                        {{ Form::label('paymentduedate', 'Payment Due Date') }} *
                                        {{ Form::text('paymentduedate', Request::old('paymentduedate') != '' ?  Request::old('paymentduedate') : date("d/m/Y", strtotime($purchase->paymentduedate)), array('class' => 'form-control') ) }}
                                    </div>
				</div>


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
                                                                                                    <div style="width: 100px;">
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

                                                                                                    $postSet = false ;
         if( is_array(Request::old('ids') ) && count( Request::old('ids') ) )
                                                                                                    {
                                                                                                        $postSet = true;
                                                                                                        $pRows =  count( Request::old('ids') );

                                                                                                    }
         else
         {
             $pRows = count($purchase->getItems);
         }
//$pRows = max( count( Request::old('purchase_products') ),  );
$qntyArr = array();
$unitPriceArr = array();
$productsSelect = array();
$purchaseDiscountVal = array();
$purchaseDiscountType = array();
$purchaseProductTaxes = array();
$productArr = array();
$productNames = array();
$globalDiscountType = '';
$globalDiscountValue = 0;
if( $postSet == true )
{
    $qntyArr = Request::old('qnty') ;
    $unitPriceArr = Request::old('unitprice');
    $productsSelect = Request::old('ids');
    $purchaseDiscountVal = Request::old('discountvalue');
    $purchaseDiscountType = Request::old('discounttype');
    $productNames = Request::old('product_names');
    $purchaseProductTaxes = Request::old('purchase_product_taxes');
    $globalDiscountType = Request::old('global_discounttype');
    $globalDiscountValue = Request::old('global_discountvalue');
}
else
{
    $globalDiscountType = $purchase->discounttype;
    $globalDiscountValue = $purchase->discountvalue;
    $items = $purchase->getItems;
    foreach( $items as $k => $item ) { 
        $qntyArr[] = $item->qnty;
        $unitPriceArr[] = $item->purchaseprice;
        $productsSelect[] = $item->itemid;
        $purchaseDiscountVal[] = $item->discountvalue;
        $purchaseDiscountType[] = $item->discounttype;
        $purchaseProductTaxes[] = $item->taxtype;
        
        
        $productNames[] = '('.$item->product->barcode.') '. $item->product->name;
    }
}

$purchaseTotalDiscount = 0;
$totalTax = 0;
$totalAmountWithOutDiscount = 0;
$taxableAmount = 0;
$grandTotal = 0;

for( $x= 0; $x < $pRows; $x++ ) {

        $tax = $purchaseProductTaxes[$x];

    /*  $productPrice = floatval($qntyArr[$x]) * floatval($unitPriceArr[$x]);
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

    $taxValues = max(0, ($tax * $totalPrice / 100) ) ; 
              $totalTax += $taxValues;
              $taxableAmount += $totalPrice;
              $grandTotal += $totalPrice + $taxValues; 
*/
        
    ?>
							<tr>
        <td><?php echo $x+1; ?>
    <input type="hidden" class="purchase_product_taxes" name="purchase_product_taxes[]" value="<?php echo $tax; ?>" />
     <input type="hidden" class="product_names" name="product_names[]" value="<?php echo htmlspecialchars($productNames[$x], ENT_QUOTES); ?>">
              <input type="hidden" class="purchase_ids" name="ids[]" value="<?php echo $productsSelect[$x]; ?>">
              
              </td>
							    <td>

              
             
<?php echo $productNames[$x]; ?>
</td>
							    <td>
								<input style="width: 100px;" class="purchase_unit_price form-control" type="number" name="unitprice[]" id="purchase_unit_price" value="<?php echo floatval($unitPriceArr[$x]); ?>" />
							    </td>
							    <td>
								<input style="width: 100px;" type="number" name="qnty[]" class="purchase_qnty form-control" value="<?php echo max(intval($qntyArr[$x]),1); ?>" class="form-control" />
							    </td>							   <td> 
							    <select name="discounttype[]" class="purchase_discounttype form-control">
								<option value="">Select</option>
								<option <?php if( $purchaseDiscountType[$x] == 'percent' ) { echo 'selected'; } ?> value="percent">%</option>
								<option value="absolute" <?php if( $purchaseDiscountType[$x] == 'absolute' ) { echo 'selected'; } ?> >Absolute</option>

							    </select>
							    </td>
							    <td>
								<input style="width: 100px;" type="number" name="discountvalue[]" class="purchase_discountvalue form-control" value="<?php echo max(0,$purchaseDiscountVal[$x]); ?>" class="form-control" />
							    </td>
                                    <td><span class="purchase_discount_amount"></span> </td>
                                    <td><span class="purchase_product_total_price"></span> </td>
							    <td>


								<button type="button" class="deleteitembtn btn btn-sm btn-icon btn-flat btn-default" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm('Are you sure?')"><i class="fa fa-close" aria-hidden="true"></i></button>

                                    </td>
							</tr>

							<?php } ?>
        
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
                                <option value="percent" <?php echo $globalDiscountType == 'percent' ? 'selected' : ''; ?>>%</option>
                                <option value="absolute" <?php echo $globalDiscountType == 'absolute' ? 'selected' : ''; ?> >Absolute</option>
                                </select>
                                                </th>
                                            </tr>

                                            <tr>
                                                <th align="right" style="text-align: right;" colspan="7">Discount Value</th>
                                                <th align="left" colspan="2">

                                                    <input type="text" id="global_discountvalue" name="global_discountvalue" value="<?php echo $globalDiscountValue; ?>" />
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

                                        <button type="submit" class="btn btn-primary">Update</button>
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




var shouldCaluculate = true;


                            </script>
@endsection
