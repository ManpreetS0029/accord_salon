@extends('layouts.app')

@section('content')
    <style>
     .alert-danger {
	 color: #a94442;
	 background-color: #f2dede !important;
	 border-color: #ebccd1;
     }
    </style>

<div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
    <div class="panel-heading"> List Purchase Payment History <div style="float: right;"><?php if( $purchase->canAddNewPayments() == true ) { echo '<a href="'.route('purchase.addpayment', $purchase->id).'">Add Payment</a>'; } ?> </div></div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

			<div class="row">
			    <div class="col-md-12">
				<h4>
				    Company Name:

    @if( $purchase->company )
					{{ $purchase->company->companyname }}
@endif
				    , Purchase Date:
				    <?php echo  date('d/m/Y H:i A', strtotime($purchase->created_at)); ?>, Amount: <?php echo  $purchase->grandtotal; ?>
				</h4>
				<br /><br />
			    </div>
			</div>

        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
					<th>#</th>
                                        <th>Payment Mode</th>
                                        <th>Bank Details</th>
					<th>Amount</th>
					<th>Payment Done</th>
					<th>Payment Date</th>
					<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
				    <?php $x = 0; ?>
				    @foreach ($purchase->payments as $payment)
					<?php
					$x = $x+1;

					?>
					<tr >
					    <td>{{ $x }}</td>

					    <td>
						{{ $payment->paymentmode->name }}
						@if($payment->paymentmodeid == 5 )
						    {{ $payment->other }}
						@endif
					    </td>
					    <td>
						@if($payment->paymentmodeid == 2 )
						    Bank: {{ $payment->bankname }}<br />
						    Account No: {{ $payment->bankaccountno }}<br />
						    Cheque No: {{ $payment->chequeno }}<br />
						    Cheque Date:
						    @if( intval($payment->chequedate) > 0 )
							
							{{  date("d/m/Y", strtotime($payment->chequedate)) }}
						    @else N/A
						    @endif
						    <br />
						    
						@else
						    N/A
						@endif    
					    </td>
					    <td>
						{{ $payment->amount }}
					    </td>

					    <td>
						@if( $payment->ispaymentdone == '2' )
						    Failed <br /> {{ $payment->paymentfailedreason }}
						@elseif( $payment->ispaymentdone == '0' )
						    Under Review
						@else
						    Yes
						@endif
					    </td>
					    
					    <td><?php echo date("d/m/Y h:i:s A", strtotime( $payment->created_at )); ?> | <?php echo date("d/m/Y h:i:s A", strtotime($payment->updated_at )); ?></td>

					    <td>

						<a class="btn btn-success"  href="{{ route('purchase.editpayment',[$purchase->id, $payment->id ] ) }}">Edit</a>

						{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['purchase.destroy', $purchase->id] ]) }}
						{{ Form::hidden('id', $purchase->id) }}
						{{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
						{{ Form::close() }}


					    </td>
					</tr>

					@endforeach




                                </tbody>
                            </table>

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
