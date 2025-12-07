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

                        <form action="" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Search by Category Name</label>
                                    {{ Form::text('searchtext', request()->get('searchtext'), ['class' => 'form-control'] )}}
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
                        {{ $categories->links('vendor.pagination.custom') }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
