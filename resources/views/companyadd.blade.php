@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Company</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array(   'route' => ['company.store' ])) }}
                            {{ csrf_field() }}
                                <div class="panel-body container-fluid">

                                    <div class="row row-lg">

                                        <div class="col-lg-6">

                                        <div class="form-group">
                                            {{ Form::label('companyname', 'Company Name') }} *
                                            {{ Form::text('companyname', '', array('class' => 'form-control') ) }}
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('gstno', 'GST NO.') }} 
                                            {{ Form::text('gstno', '', array('class' => 'form-control') ) }}
                                        </div>

<div class="form-group">
                                            {{ Form::label('address', 'Address') }} *
                                            {{ Form::textarea('address', '', array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group">
                                            {{ Form::label('state', 'Select State') }} *
                                                <br />
                                                {{ Form::select('state', $states , '', array('class' => 'form-control', 'data-plugin' => "select2", 'id' => 'states' ) ) }}
                                        </div>

<div class="form-group">
                                            {{ Form::label('city', 'Select City') }} *
                                                <br />
<div id="states_container">              {{ Form::select('cityid', array('null' => 'Select') , '', array('class' => 'form-control', 'id' => 'cityid', 'data-plugin' => "select2" ) ) }}
</div>
                                        </div>

                                



                                        </div>

					<div class="col-lg-6">
					    <h4>Contact Person</h4>
					    <div class="form-group">
						{{ Form::label('name', 'Name') }} *
						{{ Form::text('name', '', array('class' => 'form-control') ) }}
                                            </div>

					    <div class="form-group">
						{{ Form::label('designation', 'Designation') }} 
						{{ Form::text('designation', '', array('class' => 'form-control') ) }}

                                            </div>

					    <div class="form-group">
						{{ Form::label('phone', 'Phone') }} *
						{{ Form::text('phone', '', array('class' => 'form-control') ) }}
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

                                </div></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
