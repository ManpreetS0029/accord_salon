@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Clients</div>

                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')

		    <form action="" method="get">
			<div class="row">
			    <div class="col-md-3">
				<label for="">Client Name/ Phone</label>
				{{ Form::text('searchtext', request()->get('searchtext'), ['class' => 'form-control'] )}}
			    </div>

                <div class="col-md-3">
                    <label for="">Last Visit</label>
                    {{ Form::select('lastvisit',
[ null => 'Select', '1' => 'More than 3 Months'],request()->get('lastvisit'), ['class' => 'form-control'] )}}
                </div>


			    <div class="col-md-4">
				<label for="">&nbsp;</label><br />
                                
				{{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
			    </div>
			</div>
		    </form>
		    <br />
    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                            </thead>
    
                            <tbody>
                            @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->clientname }}</td>
                                <td>{{ date("d/m/Y", strtotime($client->dob)) }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->address }}<br />
                                   @if ($client->city) {{ $client->city }}, @endif {{ $client->state }} {{ $client->zipcode }}
                                </td>
                                <td>{{ $client->phone }} @if ($client->phone2 != '') , {{ $client->phone2 }} @endif </td>

                                <td>
                                    <a class="btn btn-success"  href="{{ route('clients.edit', $client->id) }}">Edit</a>
                                    <a class="btn btn-success"  href="{{ route('clients.paymentlist', $client->id) }}">Payment List</a>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['clients.destroy', $client->id] ]) }}
                                    {{ Form::hidden('id', $client->id) }}
                                    {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                    {{ Form::close() }}


                                    </td>
                            </tr>

                            @endforeach




                            </tbody>
                        </table>

                    </div>
                    {{ $clients->links() }}


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
