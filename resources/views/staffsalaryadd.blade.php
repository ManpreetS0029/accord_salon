@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Salary</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => ('salary.store'))) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">
                                <div class="row row-lg">

                                    <div class="col-md-4">


                                        <div class="form-group">
                                            {{ Form::label('staffname', 'Select Staff') }} *
                                            {{ Form::select('staffname', $staff , '' , array('class' => 'form-control', 'id' => 'staffname', 'data-plugin' => 'select2') ) }}
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_month', 'Month') }} *
                                                {{ Form::select('salary_month', $months ,  date("m")  , array('class' => 'form-control', 'id' => 'salary_month') ) }}
                                            </div>

                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_year', 'Year') }} *
                                                {{ Form::text('salary_year', $year, array('class' => 'form-control', 'required') ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('paymentmodeid', 'Payment Mode') }} *
                                                {{ Form::select('paymentmodeid',  $paymentmodes ,  1  , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_amount', 'Amount') }} *
                                                {{ Form::text('salary_amount', '', array('class' => 'form-control', 'required') ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('remarks', 'Remarks') }}
                                                {{ Form::text('remarks', '', array('class' => 'form-control') ) }}
                                            </div>
                                        </div>



                                    </div>

                                    <div class="col-md-4">
                                        <div id="salary_info"></div>
                                    </div>

                                </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        {{ Form::submit('Save',  array('class' => 'btn btn-primary'))  }}
                                    </div>
                                </div>

                            </div> {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
