@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Attendance</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => ('attendance.store'))) }}
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

                                                {{ Form::label('attendance_date', 'Date') }} *
                                                {{ Form::text('attendance_date', ( Request::get('attendance_date') ? Request::get('attendance_date') :  date("d/m/Y") ) , array('class' => 'form-control dates_nodefault', 'id' => 'attendance_date') ) }}
                                            </div>


                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('attendance', 'Attendance') }} *
                                                {{ Form::select('attendance',  $attendanceTypes ,  1  , array('class' => 'form-control', 'id' => 'attendance') ) }}
                                            </div>
                                        </div>



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
