@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Days</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Opening Balance</th>
                                    <th>Closing Balance</th>

                                    <th>Cretaed At</th>
                                    <th>Updated At</th>


                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($lists as $item)
                                    <tr>
                                        <td>{{ $item->id  }}</td>
                                        <td>{{ date("d/m/Y", strtotime($item->dates)) }}</td>
                                        <td><?php  echo number_format( $item->openingbalance,2); ?></td>
                                        <td><?php  echo number_format( $item->closingbalance,2); ?></td>
                                        <td>{{ date("d/m/Y", strtotime($item->created_at)) }}</td>
                                        <td>{{ date("d/m/Y", strtotime($item->updated_at)) }}</td>



                                    </tr>

                                @endforeach




                                </tbody>
                            </table>

                        </div>
                        {{ $lists->links() }}


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
