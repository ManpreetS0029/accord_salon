@extends('layouts.app')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
              <div class="panel panel-default">


                        
    <div class="panel-heading">
	<?php 
	
	$showAddOrUsePayment = true;
        
	
	if( count($sale) > 0 ) {



	    ?>
	  Add Sale Payment
	<?php } else if ( count($clientPackage) > 0 ) {
	?>
	   Add Package Payment
	<?php
	
	} else {
	?>
	   Add Client Payment
	<?php 
	} ?>

    </div>

    
                    <div class="panel-body">

                        <?php if( count($sale) > 0  ) {
                        $clientMain = $sale->client;

                        if( $sale->actualPendingAmount() <= 0 )
                       {
                           $showAddOrUsePayment = false;
                       }
                            ?>
             <!-- If Sale selected -->           
			<div class="row">
			    <div class="col-lg-6">
    				<p style="font-size: 16px;">
				    Client Name:
				    @if ( $sale->client )
					{{ $sale->client->clientname }}
				    @else
					{{$sale->walkin_name}}
				    @endif
				    <br /> Sale Date:
				    <?php echo  date('d/m/Y H:i A', strtotime($sale->created_at)); ?><br /> Amount: <?php echo  $sale->paidprice; ?>
				</p>
			    </div>                        	
			    <div class="col-lg-6" style="text-align: right;">
					<p style="font-size: 16px;">Amount Paid: <?php echo number_format($sale->getTotalPaidAmount(), 2); ?><br /> 
                        <span style="color:#f00;"> Amount Pending: <span class="amtpending"><?php echo number_format($sale->paidprice - $sale->getTotalPaidAmount( ), 2); ?></span></span><br />
					    <?php
					    if( $sale->getAmountUnderReview() > 0 ) {
					    ?>
					    Amount Under Review: <?php echo  number_format($sale->getAmountUnderReview(), 2); ?>
					    <?php 
					    }
					    ?>
					</p>
				    </div>
</div>
             
             <!-- Sale payment Details -->
             <h3>Payment Details</h3>
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
                                 <td><?php echo $payment->salepaymentid; ?></td>
                                 <td><?php echo $payment->amount; ?></td>
                                 <td><?php echo date("d/m/y", strtotime( $payment->created_at) ); ?></td>
                                 <td><?php echo $payment->getPaymentModeDisplay(); ?></td>
                                 <td><?php echo $payment->getPaymentPaidStatus();  ?></td>
                                   <td>

						 
						 
						{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy',  $payment->salepaymentid ] ] ) }}
                                                {{ Form::hidden('type', 'salepayment' ) }}
						{{ Form::hidden('id', $payment->salepaymentid) }}
						{{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
						{{ Form::close() }}


					    </td>
                                  
                                  
                             </tr>
                        <?php } ?>
                              
                         </tbody>
                     </table>

                 
             </div>
             
<!-- ./ If Sale selected -->       
<?php } 
else if (  count($clientPackage) > 0 ) {
$clientMain = $clientPackage->client;
 
if( $clientPackage->actualPendingAmount() <= 0 )
{
    $showAddOrUsePayment = false;
}
?>
<!-- if client package selected -->
<div class="row">
        <div class="col-lg-6">
            <p style="font-size: 16px;">
            Client Name:
            @if ( $clientPackage->client )
            {{ $clientPackage->client->clientname }}
            @endif
            <br /> Sale Date:
            <?php echo  date('d/m/Y H:i A', strtotime($clientPackage->created_at)); ?><br /> Amount: <?php echo  $clientPackage->actualprice; ?>
        </p>
        </div>                        	
        <div class="col-lg-6" style="text-align: right;">
            <p style="font-size: 16px;">Amount Paid: <?php echo number_format($clientPackage->getTotalPaidAmount(), 2); ?><br /> 
                <span style="color:#f00;"> Amount Pending: <span class="amtpending"><?php echo number_format($clientPackage->actualprice - $clientPackage->getTotalPaidAmount( ), 2); ?></span></span><br />
                <?php
                if( $clientPackage->getAmountUnderReview() > 0 ) {
                ?>
                Amount Under Review: <?php echo  number_format($clientPackage->getAmountUnderReview(), 2); ?>
                <?php 
                }
                ?>
            </p>
        </div>
</div>

<h3>Payment Details</h3>
             <!-- Client Package payment Details -->
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
                             
                     foreach ( $clientPackage->packagepayments as $payment ) {
                                                                             
                                                                        
                             ?>
                             <tr>
                                  <td><?php echo $payment->salepaymentid; ?></td>
                                 <td><?php echo $payment->amount; ?></td>
                                 <td><?php echo date("d/m/y", strtotime( $payment->created_at)); ?></td>
                                 <td><?php echo $payment->getPaymentModeDisplay(); ?></td>
                                 <td><?php echo $payment->getPaymentPaidStatus();  ?></td>
                                 
                                 
                                         
                                         
                                   <td>

						 
						 
						{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy', $payment->salepaymentid  ]] ) }}
                                                {{ Form::hidden('type', 'packagepayment' ) }}
                                                {{ Form::hidden('id', $payment->salepaymentid) }}
						 
						{{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
						{{ Form::close() }}


					    </td>
                                  
                                  
                             </tr>
                        <?php } ?>
                              
                         </tbody>
                     </table>

                 
             </div>
             
<!-- ./ if client package selected -->
<?php } else if ( count($client) > 0 ) {
//getAdvanceDetails
                            $clientMain = $client;
?>
<div class="row">
    <div class="col-lg-6">
        <p style="font-size: 16px;">
            Client Name:
<?php
                $arrPaymentInfo = $client->getAdvanceDetails();
            //print_r($client->getAdvanceDetails());
            ?>
		{{ $client->clientname }}
            
            <br /> Add Date:
            <?php echo  date('d/m/Y H:i A', strtotime($client->created_at)); ?>
        </p>
    </div>                        	
    <div class="col-lg-6" style="text-align: right;">
           <!-- <p style="font-size: 16px;">Amount Paid: <?php //echo number_format($clientPackage->getTotalPaidAmount(), 2); ?><br />  -->
               <span style="color:#f00;"> Amount Pending: <span class="amtpending"><?php echo number_format( $arrPaymentInfo['actual_amount_pending'] ,2); ?></span></span><br />
            Advance Payment: <?php echo number_format($arrPaymentInfo['advance'], 2); ?>

<?php
            if( $client->getAmountUnderReview() > 0 )
            {
            ?>
                <br/>   Amount Under Review: <?php echo  number_format($client->getAmountUnderReview(), 2); ?>
            <?php 
            }
            ?>

            <?php
            if( $client->getAmountFailed() > 0 )
            {
            ?>
               <br /> Amount Failed: <?php echo  number_format($client->getAmountFailed(), 2); ?>
            <?php 
            }
            ?>
            
        </p>
    </div>
</div>

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
                             
                     foreach ( $client->payments as $payment ) {
                                                                             
                                                                        
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

						 
						 
						{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['allpayment.destroy', $payment->salepaymentid  ]] ) }}
                                                {{ Form::hidden('type', 'packagepayment' ) }}
                                                {{ Form::hidden('id', $payment->salepaymentid) }}
						 
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
                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">


<?php if( $showAddOrUsePayment == true ) { ?>
     <h3>Add New Payment</h3>                       
    
  {{ Form::open(array( 'method' => 'post', 'route' => [ 'allpayment.store',  $arrParam['name'] => $arrParam['value'], 'iframe' => isset($_GET['iframe']) ? $_GET['iframe'] : '1'  ] )) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <?php
                                //display list of advance payments if any

                                $advancePayments = $clientMain->getAdvanceDetails();  //$clientMain->getAdvancePaymentLists();
                                if( $advancePayments['advance'] > 0 )
                                {
                                    ?>

                                <div style="padding: 10px; border: 1px solid #ccc; margin-bottom: 20px; ">

                                   <h4>Advance Amount: <?php echo number_format( $advancePayments['advance'], 2); ?>
                                    <button type="submit" value="useadvanceamount" class="btn btn-primary" name="useadvanceamount">Use Advance Amount</button></h4>
                                </div>    
                                    <?php 
                                }
                                

                                ?>

                                
				<div class="form-group col-md-12">
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
				




                                    
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" value="addpayment" name="addpayment" class="btn btn-primary">Add Payment</button>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                            
                            
                            <?php } else 
                            {
                            ?>
                            <h3 style="color: green;">!Look like no pending payment for this record.</h3>
                            <?php 
                            }?>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
 
@endsection
