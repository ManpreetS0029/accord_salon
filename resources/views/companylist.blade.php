@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Companies</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
					<th>Company Name</th>
					<th>GST. No.</th>
				        <th>Address</th>
					<th>City</th>

					<th>State</th>
					<th>Contact Person</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($companies as $item)
					<tr>
					    <td>{{ $item->companyname }}</td>
					    <td>{{ $item->gstno }}</td>
					    <td>{{ $item->address }}</td>
					    <td>{{ $item->city->city_name }}</td>
					    <td>{{ $item->city->state->name }}</td>
					    <td>
						@foreach( $item->contactpersons as $person ) 
						    Name: {{ $person->name }}<br />
						    Designation: {{ $person->designation }}<br />
						    Phone: {{ $person->phone }}<br />
						@endforeach
						
				

					    </td>

					    <td>

						<a class="btn btn-success"  href="{{ route('company.edit',$item->id  ) }}">Edit</a>

						{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['company.destroy', $item->id] ]) }}
						{{ Form::hidden('id', $item->id) }}
						{{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
						{{ Form::close() }}


					    </td>
					</tr>

                                    @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $companies->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
