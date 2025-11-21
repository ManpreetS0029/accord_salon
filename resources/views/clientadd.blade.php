@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Client</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('url' => 'clients')) }}
                                {{ csrf_field() }}
                                <div class="panel-body container-fluid">
                                    <div class="row row-lg">

                                        <div class="col-lg-6">


                                            <div class="form-group">
                                            {{ Form::label('clientname', 'Client Name') }} *
                                            {{ Form::text('clientname', '', array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('dob', 'Date of Birth') }} * (dd/mm/yyyy)
                                                {{ Form::text('dob', '', array('class' => 'form-control', 'placeholder' => 'dd/mm/yyyy') ) }}
                                            </div>
                                            </div>

                                            <div class="form-group">
                                                {{ Form::label('email', 'Email Address') }}
                                                {{ Form::text('email', '', array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="form-group">
                                                {{ Form::label('address', 'Address') }}
                                                {{ Form::text('address', '', array('class' => 'form-control') ) }}
                                            </div>
                                            <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('city', 'City') }}
                                                {{ Form::text('city', '', array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="form-group col-md-6">
                                                {{ Form::label('state', 'State') }}
                                                {{ Form::text('state', '', array('class' => 'form-control') ) }}
                                            </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                {{ Form::label('zipcode', 'Zip Code') }}
                                                {{ Form::text('zipcode', '', array('class' => 'form-control') ) }}
                                            </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                {{ Form::label('phone', 'Phone') }} *
                                                {{ Form::text('phone', '', array('class' => 'form-control') ) }}
                                            </div>

                                                <div class="form-group col-md-6">
                                                {{ Form::label('phone2', 'Phone 2') }}
                                                {{ Form::text('phone2', '', array('class' => 'form-control') ) }}
                                            </div>
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('description', 'Description') }}
                                                {{ Form::textarea('description', '', array('class' => 'form-control') ) }}
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
