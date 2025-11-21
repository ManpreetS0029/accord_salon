@extends('layouts.app')

@section('content')

    <style>
        .payment_details { display: none;   }
        .payment_details tr{  border-bottom: 2px solid #fff !important; }
        .payment_details tr td { border-right: 1px solid #fff !important; }
        .payment_details thead{ background-color: #0d3625; color: #fff;}
    </style>
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
              <div class="panel panel-default">


                        
    <div class="panel-heading">
	
	 
	   Add Payment <?php if( is_array($sale) && count($sale) > 0 ) { echo "For Sale"; } else if ( is_array($client) && count($client) > 0  ){ echo "For Client"; } ?>
	 
    </div>

    
                    <div class="panel-body">


                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">


 

    

                            <div class="panel-body container-fluid">



                                {{ Form::open(array( 'method' => 'post', 'route' => [ 'allpayment.store',  $arrParam['name'] => $arrParam['value'], 'iframe' => isset($_GET['iframe']) ? $_GET['iframe'] : '1'  ] )) }}
                                {{ csrf_field() }}

                                <?php

                    if( is_array($sale) && count($sale) > 0    )
                        {


                           $salePendingAmount = $sale->actualPendingAmount();


                           $advanceAmount = $sale->client->getTotalAdvanceAmount();

                                //display list of advance payments if any


                               // $advancePayments = $clientMain->getAdvanceDetails();  //$clientMain->getAdvancePaymentLists();
                                if( $advanceAmount > 0 )// $advancePayments['advance'] > 0 )
                                {
                                    ?>

                                <div style="padding: 10px; border: 1px solid #ccc; margin-bottom: 20px; ">

                                    <h4>Pending Amount: <?php echo number_format($salePendingAmount, 2); ?></h4>
                                   <h4>Advance Amount: <?php echo number_format( $advanceAmount, 2); ?>
                                       <?php
                                       if( $salePendingAmount > 0 ){ ?>

                                       <button type="submit" value="useadvanceamount" class="btn btn-primary" name="useadvanceamount">Use Advance Amount</button>
                                   <?php } ?>
                                   </h4>
                                </div>    
                                    <?php 
                                }
                                
}
                                ?>


                                <?php //client payments

                                if( is_array($client) && count($client) > 0 )
                                {


                                $advanceAmount = $client->getTotalAdvanceAmount();

                                //display list of advance payments if any


                                // $advancePayments = $clientMain->getAdvanceDetails();  //$clientMain->getAdvancePaymentLists();
                                if( $advanceAmount > 0 )// $advancePayments['advance'] > 0 )
                                {
                                $salesPendingAmount = $client->getSalesPendingAmount();
                                ?>

                                <div style="padding: 10px; border: 1px solid #ccc; margin-bottom: 20px; ">

                                    <h4>Sales Pending Amount: <?php echo number_format($salesPendingAmount, 2); ?></h4>
                                    <h4>Advance Amount: <?php echo number_format( $advanceAmount, 2); ?>

                                        <?php if( $salesPendingAmount > 0 ) { ?>
                                        <button type="submit" value="useadvanceamount" class="btn btn-primary" name="useadvanceamount">Use Advance Amount</button>
                                        <?php } ?>
                                    </h4>
                                </div>
                                <?php


                                }
                                }
                                ?>


                                    <h3>Add New Payment</h3>
                                    <div class="form-group col-md-12">
				    <div class="row">

                        <div class="col-md-3">
                            {{ Form::label('paymentdate', 'Payment Date') }} *<br />
                            {{ Form::text('paymentdate', date("d/m/Y"), array('class' => 'form-control dates', 'id' => 'paymentdate') ) }}
                        </div>


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

				<div id="sale_other_payment_info" class="form-group row " style="display: <?php if(Request::old('paymentmodeid') == '5' ) { echo 'block'; } else { echo 'none'; } ?>;">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
					    {{ Form::text('other', '' , array('class' => 'form-control' ) ) }}
					</div>
				    </div>
				</div>

				<div id="sale_bank_payment_info" class="form-group row " style="display: <?php if(Request::old('paymentmodeid') == '2' ) { echo 'block'; } else { echo 'none'; } ?>;">
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
					    {{ Form::text('chequedate', '' , array('class' => 'form-control datepickers' ) ) }}
					</div>

				    </div>
				</div>

                                <?php if( is_array($client) && count($client) > 0 )
                                    {
                                        ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="use_amount_in_sales"><input type="checkbox" name="use_amount_in_sales" id="use_amount_in_sales" value="1">  Add to Sale Payments automatically</label>
                                    </div></div>
                                    <?php
                                    }

                                    ?>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" value="addpayment" name="addpayment" class="btn btn-primary">Add Payment</button>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}



<?php
                            //sale
                            if( is_array($sale) && count($sale) >0 )
                                {
                                    ?>

                            <h3>Payment Details</h3>
                            <!-- Client payment Details -->
                            <div class="table-responsive">
                                <table class="table" border="0">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Add Date</th>
                                        <th>Payment Mode</th>
                                        <th>Payment Paid</th>

                                        <th>Action</th>


                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                    foreach ( $sale->salepayments as $payment ) {


                                    ?>
                                    <tr>
                                        <td><?php echo $payment->id; ?></td>
                                        <td><?php echo number_format($payment->amount,2); ?></td>
                                        <td><?php echo date("d/m/y", strtotime( $payment->created_at)); ?></td>
                                        <td><?php echo $payment->clientPayment->getPaymentModeDisplay(); ?></td>
                                        <td><?php echo $payment->clientPayment->getPaymentPaidStatus();  ?></td>

                                        <td>


                                            {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy', $payment->id  ]] ) }}
                                            {{ Form::hidden('type', 'salepayment' ) }}
                                            {{ Form::hidden('id', $payment->id) }}

                                             <label><input type="checkbox" name="delete_client_payment_too" value="1" /> Adjust Client Payment</label>

                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                            {{ Form::close() }}


                                        </td>


                                    </tr>
                                    <?php } ?>

                                    </tbody>
                                </table>


                            </div>

                            <?php




} ?>

                           <?php

                                //client payments

                                if( is_array($client) && count($client)> 0 ) { ?>


                            <h3>Payment Details</h3>
                            <!-- Client payment Details -->
                            <div class="table-responsive">
                            <table class="table" border="0">
                            <thead>
                            <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Add Date</th>
                            <th>Payment Mode</th>
                            <th>Payment Paid</th>
                            <th>Payment Status</th>
                            <th>Action</th>


                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach ( $client->advancepayments as $payment ) {


                            ?>
                            <tr>
                                <td><?php echo $payment->id; ?></td>
                                <td><?php echo $payment->amount; ?></td>
                                <td><?php echo date("d/m/y", strtotime( $payment->created_at)); ?></td>
                                <td><?php echo $payment->getPaymentModeDisplay(); ?></td>
                                <td><?php echo $payment->getPaymentPaidStatus();  ?></td>
                                <td>
                                    {{ Form::open(array( 'method' => 'put', 'route' => [ 'allpayment.update',  $payment->id, $arrParam['name'] => $arrParam['value'], 'iframe' => isset($_GET['iframe']) ? $_GET['iframe'] : '1'  ] )) }}
                                    {{ csrf_field() }}
                                    <?php if( $payment->paymentmodeid == '2' ) { ?>

                                    <select  name="payment_mode_status_add_select" class="payment_mode_status_add_select form-control" class="form-control">
                                        <option <?php if( $payment->ispaymentdone == '0' ) { echo 'selected'; } ?> value="0">Pending</option>
                                        <option <?php if( $payment->ispaymentdone == '1' ) { echo 'selected'; } ?> value="1">Paid</option>
                                        <option <?php if( $payment->ispaymentdone == '2' ) { echo 'selected'; } ?> value="2">Failed</option>

                                    </select>

                                    <textarea name="payment_failed_reason_textarea" style="<?php if( $payment->ispaymentdone == '2' ) { echo ' display:block;';  } else { echo 'display: none;'; } ?>" class="payment_failed_reason_textarea" value=""><?php echo $payment->paymentfailedreason;  ?></textarea>

                                    <button type="submit" name="payment_status_update" class="btn btn-primary">Update</button>
                                    <?php } ?>
                                    {{ Form::close() }}
                                </td>
                                <td>



                                    {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy', $payment->id  ]] ) }}
                                    {{ Form::hidden('type', 'clientpayment' ) }}
                                    {{ Form::hidden('id', $payment->id) }}

                                    {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                    {{ Form::close() }}


                                    <button type="button"   class="btn btn-info button_payment_details">Details</button>
                                </td>


                            </tr>
                            <tr class="payment_details">
                                <td colspan="7">
                                    <div>
                                    <table style="background-color: #efefef;" class="table table-responsive">
                                        <thead><tr>
                                        <th>Sale Id</th>
                                        <th>Sale Date</th>
                                        <th>Total Amount</th>
                                        <th>Total Amount Paid</th>
                                        <th>Pending Amount</th>
                                         <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        $paymentUsedList = $payment->clientPaymentUsedLists;
                                        foreach( $paymentUsedList as $paymentUsed ) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $paymentUsed->saleid; ?>

                                            </td>
                                            <td><?php echo  date("d/m/Y", strtotime($paymentUsed->sale->created_at)); ?></td>
                                            <td><?php echo number_format( $paymentUsed->sale->paidprice, 2); ?></td>
                                            <td><?php echo number_format($paymentUsed->sale->getTotalPaidAmount(), 2); ?></td>
                                            <td><?php echo number_format($paymentUsed->sale->actualPendingAmount(), 2); ?></td>
                                            <td>
                                                <?php echo number_format($paymentUsed->amount,2); ?>
                                            </td>

                                            <td>


                                                {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy', $paymentUsed->id  ]] ) }}
                                                {{ Form::hidden('type', 'salepayment' ) }}
                                                {{ Form::hidden('id', $paymentUsed->id) }}

                                                <label><input type="checkbox" name="delete_client_payment_too" value="1" /> Adjust Client Payment</label> <br />
<div class="clearfix"></div>
                                                {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                                {{ Form::close() }}

                                                <!-- Add Payment -->
                                                <a class="btn btn-success" href="<?php echo route('allpayment.create',['saleid' =>  $paymentUsed->sale->id, 'iframe' => '1']); ?>" >Add Payment</a>


                                            <!-- Edit Sale -->

                                                <a class="btn btn-success" href="<?php echo route('sale.edit',$paymentUsed->sale->id); ?>" onclick="javascript:window.top.location.href = '<?php echo route('sale.edit',$paymentUsed->sale->id); ?>'; ">Edit Sale</a>



                                            </td>

                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>

                            </tbody>
                            </table>


                        </div>

                        <?php



                        } ?>




                      
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
 
@endsection
