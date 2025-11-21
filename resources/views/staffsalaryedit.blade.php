@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Salary</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('method' => 'PUT', 'route' => ( ['salary.update',   $salaryPaidDetails->id]))) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">
                                <div class="row row-lg">

                                    <div class="col-md-4">


                                        <div class="form-group">
                                            {{ Form::label('staffname', 'Select Staff') }} *
                                            {{ Form::select('staffname', $staff , $salaryPaidDetails->staffid , array('class' => 'form-control', 'id' => 'staffname', 'data-plugin' => 'select2') ) }}
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_month', 'Month') }} *
                                                {{ Form::select('salary_month', $months ,  str_pad($salaryPaidDetails->months,2,0, STR_PAD_LEFT)  , array('class' => 'form-control', 'id' => 'salary_month') ) }}
                                            </div>

                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_year', 'Year') }} *
                                                {{ Form::text('salary_year', $salaryPaidDetails->years, array('class' => 'form-control', 'required') ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('paymentmodeid', 'Payment Mode') }} *
                                                {{ Form::select('paymentmodeid',  $paymentmodes ,  $salaryPaidDetails->paymentmodeid  , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('salary_amount', 'Amount') }} *
                                                {{ Form::text('salary_amount', $salaryPaidDetails->amount, array('class' => 'form-control', 'required') ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('remarks', 'Remarks') }}
                                                {{ Form::text('remarks', $salaryPaidDetails->remarks, array('class' => 'form-control') ) }}
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

                                        {{ Form::submit('Update',  array('class' => 'btn btn-primary'))  }}
                                    </div>
                                </div>

                            </div> {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var editSalaryPage = '1';
    </script>
@endsection
