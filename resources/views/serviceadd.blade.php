@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Service</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'services.store')) }}
                            {{ csrf_field() }}
                                <div class="panel-body container-fluid">

                                    <div class="row row-lg">

                                        <div class="col-lg-6">

                                            <div class="form-group">
                                                {{ Form::label('servicecategoriesid', 'Service Category') }} *
                                                {{ Form::select('servicecategoriesid',   $categories, '', array('class' => 'form-control') ) }}
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('name', 'Service Name') }} *
                                                {{ Form::text('name', '', array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="form-group">
                                                {{ Form::label('description', 'Description') }}
                                                {{ Form::textarea('description', '', array('class' => 'form-control') ) }}
                                            </div>


                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                {{ Form::label('price', 'Price') }} *
                                                {{ Form::text('price', '0', array('class' => 'form-control') ) }}
                                            </div>
                                            </div>
                                            <div class="form-group">
                                                {{ Form::label('discounttype', 'Discount Type') }}
                                                {{ Form::select('discounttype', array( '' => 'Select', 'abosulte' => 'Abosulte','percent' => 'Percent' ), '', array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('discount', 'Discount Amount') }}
                                                {{ Form::text('discount', '0', array('class' => 'form-control') ) }}
                                            </div>
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
