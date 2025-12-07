@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 ">
			<div class="panel panel-default">
				<div class="panel-heading">List products</div>

				<div class="panel-body">
					@include('common.errors')
					@include('common.success')

					<div class="row">
						<div class="col-md-12">
							<br /><br />
							<form method="get">
								<div class="row">

									<div class="form-group col-md-4">
										<label>HSN Code/Barcode/Product Name</label>
										{{ Form::text('bynames', '', ['class' => 'form-control'] )}}
									</div>


									<div class="form-group col-md-2">

										{{ Form::label('stockmode', 'Stock') }} *<br />
										{{ Form::select('stockmode', array( null => 'Select', '0' => 'Out of Stock', '1' => 'In Stock' ), '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}


									</div>

									<div class="form-group col-md-2">
										<label for="">&nbsp;</label><br />

										{{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
									</div>


								</div>
							</form>
							<br />
							<br />
						</div>
					</div>


					{{ $products->links() }}

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>HSN Code</th>
									<th>Barcode</th>
									<th>Product Name</th>

									<th>Sale Price</th>
									<th>Stock at Start</th>
									<th>Available Stock</th>
									<th>No Of Solds</th>

									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($products as $item)
								<tr>
									<td>{{ $item->hsncode }}</td>
									<td>{{ $item->barcode }}</td>
									<td>{{ $item->name }}</td>

									<td>{{ $item->price }}</td>

									<td>{{ $item->startstock }}</td>
									<td> <?php if (0 >= $item->stockavailable) { ?><span class="label label-danger">Out Of Stock</span> <?php } else {
																																		echo $item->stockavailable;
																																	} ?> </td>

									<td>{{ $item->soldcount }}</td>

									<td>
										<span style="display: flex; gap: 5px; align-items: center;">
											<a class="btn btn-success mybtns" href="{{ route('product.edit', $item->id) }}" title="Edit">
												Edit
											</a>
											{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['product.destroy', $item->id], 'style' => 'display:inline;' ]) }}
											{{ Form::hidden('id', $item->id) }}
											<button type="submit" class="btn mybtns btn-danger deletebtn" style="padding: 6px 12px;">Delete</button>
											{{ Form::close() }}
										</span>
									</td>
								</tr>

								@endforeach

							</tbody>
						</table>

                    </div>
					{{ $products->links('vendor.pagination.custom') }}


				</div>
			</div>
		</div>
	</div>
</div>
@endsection