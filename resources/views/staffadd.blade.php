@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Staff Member</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'staff.store')) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            {{ Form::label('firstname', 'First Name') }} *
                                            {{ Form::text('firstname', '', array('class' => 'form-control') ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('lastname', 'Last Name') }} *
                                            {{ Form::text('lastname', '', array('class' => 'form-control') ) }}
                                        </div>

<div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('dob', 'Date of Birth') }}  (dd/mm/yyyy)
                                                {{ Form::text('dob', '', array('class' => 'form-control', 'placeholder' => 'dd/mm/yyyy') ) }}
                                            </div>
</div>


<div class="form-group">
    {{ Form::label('gender', 'Gender') }} *<br />
    {{ Form::radio('gender', '0', true,   array('class' => 'form-radio') ) }} Male &nbsp;&nbsp;
    {{ Form::radio('gender', '1', '', array('class' => 'form-radio') ) }} Female
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('address', 'Address') }}
                                                {{ Form::textarea('address', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('email', 'Email') }}
                                                {{ Form::text('email', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('mobile', 'Mobile') }} *
                                                {{ Form::text('mobile', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('phone', 'Phone') }}
                                                {{ Form::text('phone', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('designation', 'Designation') }} *
                                                {{ Form::text('designation', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('hiringdate', 'Hiring Date') }} (dd/mm/yyyy)
                                                {{ Form::text('hiringdate', '', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-8">

                                                {{ Form::label('hiringdate', 'Id Proof') }}<br />
                                                <div class="col-md-6">
                                                {{ Form::text('idprooftype', '', array('class' => 'form-control', 'placeholder' => 'Type' ) ) }}
                                                </div>
                                                <div class="col-md-6">
                                                    {{ Form::text('idproofvalue', '', array('class' => 'form-control','placeholder' => 'Number' ) ) }}
                                                </div>
                                            </div>
                                        </div>


                                    </div>

				    <div class="col-lg-6">
					<div style="border: 1px solid #ccc; padding: 15px;">
					    <h4>Update Salary Info</h4><br />
					<div class="row" >
					    
                                            <div class="form-group col-md-4">
						{{ Form::label('salary', 'Salary*') }}<br />
						{{ Form::text('salary', '', array('class' => 'form-control','placeholder' => 'Salary' ) ) }}
					    </div>
					    
                                            <div class="form-group col-md-6">
						{{ Form::label('commission', 'Commission Percent*') }}<br />
						{{ Form::text('commission', '', array('class' => 'form-control','placeholder' => 'Commission' ) ) }}
					    </div>

					    <div class="form-group col-md-6">
						{{ Form::label('fromdate', 'From Date   (dd/mm/yyyy)*') }}<br />
						{{ Form::text('fromdate', '', array('class' => 'form-control dates','placeholder' => 'From Date' ) ) }}
					    </div>
					</div>
<!-- 					    <div class="row">
						<div class="col-md-6">
						    <button type="submit" name="update_salary" value="1" class="btn btn-primary">Update Salary Info</button>
						</div>
					    </div>
					    -->
					
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
