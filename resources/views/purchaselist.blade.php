@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Purchases <div style="float: right">
                        <a href="<?php echo route('purchase.create'); ?>"><strong>+ Add Purchase</strong></a>
                    </div>
                </div>

                <div class="panel-body">


                    @include('common.errors')
                    @include('common.success')

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

                            <div class="form-group col-md-3">
                                <label>Comapny Name</label>
                                {{ Form::select('companyid', $companies, '', ['class' => 'form-control', 'data-plugin="select2"'] )}}
                            </div>

                            <div class="form-group col-md-2">
                                <label>Date From</label>
                                {{ Form::text('datefrom', '', ['class' => 'form-control', 'id'=>'datefrom'] )}}
                            </div>

                            <div class="form-group col-md-2">
                                <label>Date To</label>
                                {{ Form::text('dateto', '', ['class' => 'form-control', 'id' => 'dateto'] )}}
                            </div>

                            <div class="form-group col-md-2">

                                {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                {{ Form::select('paymentmodeid', $paymentmodes, '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}


                            </div>


                            <div class="form-group col-md-2">
                                <label>Payment Status</label>
                                <?php $arr = [null => 'Select', '0' => 'Pending', '1' => 'Success', '2' => 'Failed'];  ?>

                                {{ Form::select('paymentstatus',$arr, '', ['class' => 'form-control'] ) }}
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
                                    <th>Comapny</th>
                                    <th>Invoice No</th>
                                    <th>Bill | Delivery | Due Date</th>
                                    <th>Grand Total</th>
                                    <th>Amt. Paid </th>
                                    <th>Amt. Pending</th>

                                    <!--    <th>Line Total</th> -->
                                    <th>Discount</th>
                                    <th>Tax Amount</th>


                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $item)

                                <tr>
                                    <td>{{ $item->company->companyname }}</td>
                                    <td>{{ $item->invoiceno }}</td>
                                    <td>{{ date("d/m/Y", strtotime($item->billdate)) }} | {{ date("d/m/Y", strtotime($item->deliverydate)) }} | {{ date("d/m/Y", strtotime($item->paymentduedate)) }}</td>
                                    <td>{{ $item->grandtotal }}</td>
                                    <td>

                                        {{ $item->getTotalPaidAmount() }}
                                    </td>
                                    <td>{{ $item->pendingAmount() }}</td>

                                    <td>{{ number_format($item->discountamount,2) }}</td>
                                    <td>{{ number_format($item->totaltaxamount,2) }}</td>


                                    <td>

                                        <a title="Payment History" href="{{route('purchase.paymenthistory',$item->id)}}" style="vertical-align: -8px;"><i class="fa fa-2x fa-rupee" alt="payment history"></i></a> &nbsp;
                                        <a class="btn btn-success" href="{{ route('purchase.edit',$item->id  ) }}">Edit</a>

                                        {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['purchase.destroy', $item->id] ]) }}
                                        {{ Form::hidden('id', $item->id) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                        {{ Form::close() }}


                                    </td>
                                </tr>

                                @endforeach




                            </tbody>
                        </table>

                    </div>
                    <?php

                    if (method_exists($purchases, 'links')) { ?>
                        {{ $purchases->links('vendor.pagination.custom') }}
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection