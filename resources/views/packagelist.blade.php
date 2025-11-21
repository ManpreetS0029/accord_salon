@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Packages</div>

                    <div class="panel-body">
                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Package Name</th>

                                    <th>Price</th>
                                    <th>Services Included</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>{{ $package->title }}</td>

                                        <td>{{ $package->price }}</td>

                                        <td>
                                            @foreach( $package->packageservices  as $service )
                                                {{ $service->service->name  }} ({{ $service->service->price  }})
                                                <br />
                                            @endforeach

                                        </td>

                                        <td>
                                            <a class="btn btn-success"  href="{{ route('package.edit', $package->id) }}">Edit</a>

                                                {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['package.destroy', $package->id] ]) }}
                                            {{ Form::hidden('id', $package->id) }}
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                            {{ Form::close() }}
                                        </td>
                                    </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $packages->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
