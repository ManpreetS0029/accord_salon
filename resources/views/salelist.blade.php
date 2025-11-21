@extends('layouts.app')

@section('content')
<style>
    .alert-danger {
        color: #a94442;
        background-color: #f2dede !important;
        border-color: #ebccd1;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Sales
                    <div style="float: right">
                        <a href="<?php echo route('sale.create'); ?>"><strong>+ Add Sale</strong></a>
                    </div>
                </div>

                <div class="panel-body">

                    @include('common.errors')
                    @include('common.success')
                    <?php
                    $queryStr = $_SERVER['QUERY_STRING'] ?? null;
                    $link = '?exportdata=1';
                    if ($queryStr != '') {
                        parse_str($queryStr, $qrArr);
                        unset($qrArr['exportdata']);
                        $qrArr['exportdata'] = "1";
                        $link =  '?' . http_build_query($qrArr);
                    } else {
                    }
                    ?>
                    <div class="row">
                        <div style="float: right; margin-bottom: 10px;">
                            <a style="padding: 10px; margin-bottom: 10px;" href="<?php echo $link; ?>">Download Excel</a>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12" style="border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; text-align: center;">
                            <div class="row">
                                <div class="col-md-2" style="background-color: #dfe;  padding-top:10px; padding-bottom: 10px;">
                                    Total Sale: &#8377; <?php echo number_format($totalsale, 2); ?>
                                </div>

                                <div class="col-md-2" style="background-color: #efd;  padding-top:10px; padding-bottom: 10px;">
                                    Total Paid: &#8377; <?php echo number_format($totalpaidamount, 2); ?>
                                </div>

                                <div class="col-md-2" style="background-color: #def;  padding-top:10px; padding-bottom: 10px;">
                                    Total Cash: &#8377; <?php echo number_format($totalcashpaid, 2); ?>
                                </div>

                                <div class="col-md-2" style="background-color: #fed;  padding-top:10px; padding-bottom: 10px;">
                                    Total Pending: &#8377; <?php echo number_format($totalpendingamount, 2); ?>
                                </div>

                                <div class="col-md-2" style="background-color: #edf;  padding-top:10px; padding-bottom: 10px;">
                                    Under Review: &#8377; <?php echo number_format($totalunderreviewamount, 2); ?>
                                </div>

                                <div class="col-md-2" style="background-color: #efd;  padding-top:10px; padding-bottom: 10px;">
                                    Failed: &#8377; <?php echo number_format($totalfailedamount, 2); ?>
                                </div>
                            </div>





                        </div>
                    </div>
                    <br /><br />
                    <form method="get">
                        <div class="row">

                            <div class="form-group col-md-2">
                                <label>Client Name / Bill No.</label>
                                {{ Form::text('clientname', request('clientname', ''), ['class' => 'form-control'] )}}
                            </div>

                            <div class="form-group col-md-2">
                                <label>Date From</label>
                                {{ Form::text('datefrom', request('datefrom','') , ['class' => 'form-control', 'id'=>'datefrom', 'autocomplete' => 'off'] )}}
                            </div>

                            <div class="form-group col-md-2">
                                <label>Date To</label>
                                {{ Form::text('dateto', request('dateto',''), ['class' => 'form-control', 'id' => 'dateto', 'autocomplete' => 'off' ] )}}
                            </div>

                            <div class="form-group col-md-2">

                                {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                {{ Form::select('paymentmodeid', $paymentmodes, request('paymentmodeid', '') , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}


                            </div>


                            <div class="form-group col-md-2">
                                <label>Payment Status</label>
                                <?php $arr = [null => 'Select', '0' => 'Pending', '1' => 'Success', '2' => 'Failed'];  ?>

                                {{ Form::select('paymentstatus',$arr, request('paymentstatus', null), ['class' => 'form-control'] ) }}
                            </div>

                            <div class="form-group col-md-2">
                                <label for="">&nbsp;</label><br />

                                {{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
                            </div>


                        </div>
                    </form>
                    <br />
                    <br />
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client Name</th>

                                    <th title="Total Bill Amount">Amount</th>
                                    <th title="Amount Paid">Amt. Paid</th>
                                    <th title="Amount Under Review">Amt. Review</th>
                                    <th title="Amount Pending that need to be paid.">Amt. Pending</th>
                                    <th>Sale Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $x = 0;
                                $todayTotalSale = 0;
                                $totalSale = 0;
                                $todayPendingAmount = 0;
                                $totalPendingAmoun = 0;
                                ?>
                                @foreach ($sales as $item)
                                <?php
                                $x = $x + 1;
                                $pendingAmount = $item->paidprice - ($salePayments[$item->id] ?? 0);
                                $clientName = empty($allClients[$item->clientid]) ? $item->walkin_name : $allClients[$item->clientid]->clientname;
                                ?>
                                <tr <?php if ($pendingAmount > 0) {
                                        echo 'class="alert alert-danger"';
                                    } ?>>
                                    <td>{{ $x }}/ACCD{{ $item->id }}</td>
                                    <td>

                                        {{ $clientName }}

                                    </td>
                                    <td>{{ $item->paidprice }}</td>
                                    <td>{{ number_format( $salePayments[$item->id] ?? 0 ) }}</td>
                                    <td>{{ number_format( 0 ) }}</td>
                                    <td>{{ number_format( $pendingAmount ) }}</td>



                                    <td><?php echo date("d/m/Y h:i:s A", strtotime($item->created_at)); ?></td>

                                    <td>

                                        <a title="Payment History" href="{{route('sale.paymenthistory',$item->id)}}" style="vertical-align: -8px;"><i class="fa fa-2x fa-rupee" alt="payment history"></i></a> &nbsp;
                                        <a class="btn btn-success" href="{{ route('sale.edit',$item->id  ) }}">Edit</a>
                                        <?php if ($user->role == 'Super Admin') { ?>
                                            {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['sale.destroy', $item->id] ]) }}
                                            {{ Form::hidden('id', $item->id) }}

                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}

                                            {{ Form::close() }}
                                        <?php } ?>

                                    </td>
                                </tr>

                                @endforeach




                            </tbody>
                        </table>

                    </div>
                    <?php

                    if (method_exists($sales, 'links')) { ?>
                        {{ $sales->links() }}

                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection