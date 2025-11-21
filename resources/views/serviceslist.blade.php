@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Services</div>

                    <div class="panel-body">
                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Discount Type/Amount</th>

                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ( $services as $service )
                                    <tr>
                                        <td>{{ $service->category->name }}</td>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->price }}</td>
                                        <td>{{ $service->discounttype }} @if( '' != $service->discounttype) / {{ $service->discount }}@endif
                                        </td>


                                        <td>
                                            <a class="btn btn-success"  href="{{ route('services.update',  $service->id ) }}/edit">Edit</a>

                                        {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['services.destroy', $service->id] ]) }}
                                            {{ Form::hidden('id', $service->id) }}
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                            {{ Form::close() }}
                                        </td>
                                    </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $services->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
