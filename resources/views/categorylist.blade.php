@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Category</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($categories as $cat)
                                <tr>
                                    <td>{{ $cat->name }}</td>
                                    <td>{{ $cat->description }}</td>
                                    <td>

                                        <a class="btn btn-success"  href="{{ route('category.edit',$cat->id  ) }}">Edit</a>

                                    {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['category.destroy', $cat->id] ]) }}
                                        {{ Form::hidden('id', $cat->id) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                        {{ Form::close() }}


                                    </td>
                                </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $categories->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
