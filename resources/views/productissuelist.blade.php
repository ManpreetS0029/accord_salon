@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Issued Products</div>

                    <div class="panel-body">
                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Product Name</th>

                                    <th>Qnty.</th>
    <th>Issue Date</th>
    <th>Issue To</th>

    <th>Remarks</th>

    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
					<tr>
					    <td>{{ $item->productname }}</td>

                                            <td>{{ $item->qnty }}</td>

					    <td>{{ date("d/m/Y", strtotime($item->issuedate)) }}</td>
					    <td>{{ $item->staffname }}</td>

<td>{{ $item->remarks }}</td>


                                        <td>
                                            <a class="btn btn-success mybtns"  href="{{ route('productissue.edit', $item->id) }}">Edit</a>
                                            
{{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['productissue.destroy', $item->id] ]) }}
                                            {{ Form::hidden('id', $item->id) }}
                                            {{ Form::submit('Delete', ['class' => 'btn mybtns btn-danger deletebtn']) }}
                                            {{ Form::close() }}
                                        </td>
                                    </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $items->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
