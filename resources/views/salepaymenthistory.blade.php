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
				<div class="panel-heading"> List Sale Payment History <div style="float: right;"><?php if ($sale->canAddNewPayments() == true) {
																										echo '<a href="' . route('sale.addpayment', $sale->id) . '">Add Payment</a>';
																									} ?> </div>
				</div>

				<div class="panel-body">

					@include('common.errors')
					@include('common.success')

					<div class="row">
						<div class="col-md-12">
							<h4>
								Client Name:
								@if ( $sale->client )
								{{ $sale->client->clientname }}
								@else
								{{$sale->walkin_name}}
								@endif
								, Sale Date:
								<?php echo  date('d/m/Y H:i A', strtotime($sale->created_at)); ?>, Amount: <?php echo  $sale->paidprice; ?>
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
								@foreach ($sale->salepayments as $salePayment)
								<?php
								$x = $x + 1;

								?>
								<tr>
									<td>{{ $x }}</td>

									<td>
										{{ $salePayment->paymentmode->name ?? 'N/A' }}
										@if($salePayment->paymentmodeid == 5 )
										{{ $salePayment->other }}
										@endif
									</td>
									<td>
										@if($salePayment->paymentmodeid == 2 )
										Bank: {{ $salePayment->bankname }}<br />
										Account No: {{ $salePayment->bankaccountno }}<br />
										Cheque No: {{ $salePayment->chequeno }}<br />
										Cheque Date:
										@if( intval($salePayment->chequedate) > 0 )

										{{ date("d/m/Y", strtotime($salePayment->chequedate)) }}
										@else N/A
										@endif
										<br />

										@else
										N/A
										@endif
									</td>
									<td>
										{{ $salePayment->amount }}
									</td>

									<td>
										@if( $salePayment->ispaymentdone == '2' )
										Failed <br /> {{ $salePayment->paymentfailedreason }}
										@elseif( $salePayment->ispaymentdone == '0' )
										Under Review
										@else
										Yes
										@endif
									</td>

									<td><?php echo date("d/m/Y h:i:s A", strtotime($salePayment->created_at)); ?> | <?php echo date("d/m/Y h:i:s A", strtotime($salePayment->updated_at)); ?></td>

									<td>

										<a class="btn btn-success" href="{{ route('sale.editpayment',[$sale->id, $salePayment->id ] ) }}">Edit</a>

										{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['sale.destroy', $sale->id] ]) }}
										{{ Form::hidden('id', $sale->id) }}
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