@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Expense Master</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

                        <form action="" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Search by Name</label>
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
                                @foreach ($expenses as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>

                                        <a class="btn btn-success"  href="{{ route('expensemaster.edit',$item->id  ) }}">Edit</a>


                                    </td>
                                </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $expenses->links('vendor.pagination.custom') }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
