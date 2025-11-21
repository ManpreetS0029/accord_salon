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
                        {{ $expenses->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
