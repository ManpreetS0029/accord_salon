@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row">

            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Client-Payments</div>

                    <div class="panel-body">
                        @include('common.errors')
                        @include('common.success')

                        <form action="" method="get">
                            <div class="row">

                                <div class="col-md-3">
                                    <label for="">Date</label>
                                    <?php

                                    $dates =  Request::old('paymentdate') != '' ? Request::old('paymentdate') : '';
                                    ?>
                                    {{ Form::text('paymentdate', $dates, ['class' => 'form-control datepickers'] )}}
                                </div>

                                <div class="col-md-3">
                                    <label for="">Amount</label>

                                    {{ Form::text('paymentamount',  Request::old('paymentamount'), ['class' => 'form-control'] )}}
                                </div>

                                <div class="col-md-3">
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
                                    <th>#</th>
                                    <th>Payment Id</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Status</th>
                                    <th>Payment Failed Reason</th>
                                    <th>Payment Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

								<?php
								//var_dump( $clientpackages);
								$zx = 1;
								foreach(  $paymentlist as $payment ){
								?>

                                <tr>
                                    <td><?php echo $zx; ?></td>
                                    <td><?php echo $payment->id ?></td>
                                    <td> <?php echo $payment->amount; ?></td>

                                    <td><?php echo $payment->getPaymentModeDisplay(); ?></td>
                                    <td><?php echo $payment->getPaymentPaidStatus(); ?></td>
                                    <td><?php echo $payment->paymentfailedreason; ?></td>
                                    <td><?php echo  date('d/m/Y', strtotime($payment->created_at));  ?></td>
                                    <td>


                                        <a class="btn btn-danger" onclick="return confirm('Are you sure to delete?');"  href="{{ route('clients.paymentlist', $clientid) }}?delid=<?php echo  $payment->id; ?>">Delete</a>
	                                    <?php        /*
                                                                                {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['clientspackage.destroy', $package->id] ]) }}
                                                                                {{ Form::hidden('id', $package->id) }}
                                                                                {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                                                                {{ Form::close() }}
                                        */ ?>
                                    </td>
                                </tr>

								<?php $zx++; }?>


                                </tbody>
                            </table>

                        </div>
                        {{ $paymentlist->links() }}

                    </div>

                </div>
            </div>
        </div>



@endsection
