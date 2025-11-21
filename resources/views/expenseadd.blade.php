@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Expense</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'expense.store')) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">

                                <div class="row">
                                 <div class="form-group col-md-4">
                                            {{ Form::label('expensedate', 'Select Date') }} *
                                                <br />
<?php
$dates = Request::old('expensedate') != "" ? Request::old('expensedate') : date("d/m/Y");
?>
                                                {{ Form::text('expensedate', $dates, array('class' => 'form-control dates') ) }}
                                        </div>

                           <div class="form-group col-md-4">
                                            {{ Form::label('paymentmodeid', 'Payment Mode') }} *
                                                <br />
<?php

                                //                                $selectedId = Request::old('paymentmodeid') != '' ? Request::old('paymentmodeid') : '1';
                                
?>
                                                {{ Form::select('paymentmodeid', $paymentmodes , '', array('class' => 'form-control ') ) }}
                                        </div>

</div>

                                        <div class="form-group">
                                            {{ Form::label('expensemasterid', 'Select Expense Type') }} *
                                                <br />
                                                {{ Form::select('expensemasterid', $expensemaster , '', array('class' => 'form-control') ) }}
                                        </div>

<div class="row">
                                        <div class="form-group col-md-4">
                                            {{ Form::label('amount', 'Amount') }} *
                                            {{ Form::text('amount', '0.00', array('class' => 'form-control') ) }}
                                        </div>
</div>

                                        <div class="form-group">
                                            {{ Form::label('remarks', 'Remarks') }} 
                                            {{ Form::text('remarks', '', array('class' => 'form-control') ) }}
                                        </div>





                                    </div>
                                </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
