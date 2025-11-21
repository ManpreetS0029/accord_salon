@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
    <div class="panel-heading"><a href="<?php echo route('sale.paymenthistory', $sale->id ); ?>">< Back</a> Update Sale Payment </div>

                    <div class="panel-body">
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
					    <span style="color:#f00;"> Amount Pending: <?php echo number_format($sale->paidprice - $sale->getTotalPaidAmount( ), 2); ?></span><br />
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

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">


    
                    {{ Form::open(array( 'method' => 'post', 'route' => ['sale.updatepayment' , $sale->id, $payment->id] )) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">


                                
				<div class="form-group row">
				    <div class="row">
					<div class="col-md-3">
					    {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
					    {{ Form::select('paymentmodeid', $paymentmodes, $payment->paymentmodeid, array( 'class' => 'form-control', 'id' => 'paymentmodeid') ) }}
					</div>

					<div class="col-md-3">
					    {{ Form::label('amount', 'Amount Paid') }} *<br />
					    {{ Form::text('amount',  $payment->amount , array('class' => 'form-control', 'id' => 'paymentmodeid' ) ) }}
					    
				</p>
					</div>

					
				    </div>
				</div>




				<div id="sale_other_payment_info" class="form-group row " style="display: <?php if( $payment->paymentmodeid == '5' ) { echo 'block'; } else { echo 'none'; } ?>;">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
					    {{ Form::text('other', $payment->other , array('class' => 'form-control' ) ) }}
					</div>
				    </div>
				</div>

				<div id="sale_bank_payment_info" class="form-group row " style="display:  <?php  if( $payment->paymentmodeid == '2' ) { echo 'block'; } else { echo 'none'; } ?>;">
				    <div class="row">
					<div class="form-group col-md-3">
					    {{ Form::label('bankname', 'Bank Name') }} 
					    {{ Form::text('bankname', $payment->bankname , array('class' => 'form-control'  ) ) }}
					</div>

					<div class="form-group col-md-3">
					    {{ Form::label('bankaccountno', 'Bank Account No') }} 
					    {{ Form::text('bankaccountno', $payment->bankaccountno , array('class' => 'form-control' ) ) }}
					</div>

					
					<div class="form-group col-md-3">
					    {{ Form::label('chequeno', 'Cheque No') }} 
					    {{ Form::text('chequeno', $payment->chequeno , array('class' => 'form-control' ) ) }}
					</div>
					<div class="form-group col-md-3">
					    {{ Form::label('chequedate', 'Cheque Date') }} 
					    {{ Form::text('chequedate', intval($payment->chequedate) > 0 ? date("d/m/Y", strtotime( $payment->chequedate)) : '' , array('class' => 'form-control' ) ) }}
					</div>
					
				    </div>
				</div>
				


				<div class="form-group row">
				    <div class="row">
					<div class="col-md-3">
					    <label>Payment Status</label>
					    <br />					    
					    
					    {{Form::radio('ispaymentdone', '0', $payment->ispaymentdone == '0' ? true : false , array( 'id' => 'paypending') )}} {{ Form::label('paypending', 'Pending') }}
					    {{Form::radio('ispaymentdone', '1', $payment->ispaymentdone == '1' ? true : false, array( 'id' => 'payapproved') )}} {{ Form::label('payapproved', 'Approved') }} 					    {{Form::radio('ispaymentdone', '2', $payment->ispaymentdone == '2' ? true : false, array( 'id' => 'payfailed') )}} {{ Form::label('payfailed', 'Failed') }}
					</div>
				    </div>
				    <br />
				    <div class="row">
					<div class="col-md-6">
					    {{ Form::label('paymentfailedreason', 'Payment Failed Reason') }} *<br />
					    {{ Form::textarea('paymentfailedreason',  $payment->paymentfailedreason , array('class' => 'form-control', "style" => "width: 100%;" ,'id' => 'paymentfailedreason' ) ) }}
					    
				</p>
					</div>

					
				    </div>
				</div>


                                    
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" class="btn btn-primary">Update Payment</button>
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
