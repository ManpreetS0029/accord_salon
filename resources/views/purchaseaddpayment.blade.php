@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
    <div class="panel-heading"> <a href="<?php echo route('purchase.edit', $purchase->id); ?>">< Back</a>  Add Sale Payment </div>

                    <div class="panel-body">
			<div class="row">
			    <div class="col-lg-6">
    				<p style="font-size: 16px;">
				    Company Name:
				    @if ( $purchase->company )
					{{ $purchase->company->companyname }}
@endif
				    <br /> Purchase Date:
				    <?php echo  date('d/m/Y H:i A', strtotime($purchase->created_at)); ?><br /> Amount: <?php echo  $purchase->grandtotal; ?>
				</p>
			    </div>                        	
			    <div class="col-lg-6" style="text-align: right;">
					<p style="font-size: 16px;">Amount Paid: <?php echo number_format($purchase->getTotalPaidAmount(), 2); ?><br /> 
					    <span style="color:#f00;"> Amount Pending: <?php echo number_format($purchase->grandtotal - $purchase->getTotalPaidAmount( ), 2); ?></span><br />
					    <?php
					    if( $purchase->getAmountUnderReview() > 0 ) {
					    ?>
					    Amount Under Review: <?php echo  number_format($purchase->getAmountUnderReview(), 2); ?>
					    <?php 
					    }
					    ?>
					</p>
				    </div>
</div>

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">


    
                    {{ Form::open(array( 'method' => 'post', 'route' => ['purchase.storepayment' , $purchase->id] )) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">


                                
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
					    {{ Form::text('chequedate', '' , array('class' => 'form-control' ) ) }}
					</div>
					
				    </div>
				</div>
				




                                    
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" class="btn btn-primary">Add Payment</button>
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
@endsection
