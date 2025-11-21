@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Product</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'product.store')) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">

                                       <div class="form-group">
                                            {{ Form::label('hsncode', 'HSN Code') }} *
                                            {{ Form::text('hsncode', '', array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="form-group">
                                            {{ Form::label('name', 'Product Code') }} *
                                            {{ Form::text('productcode', '', array('class' => 'form-control stop_submit', 'id' => 'productcode' ) ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('barcode', 'Barcode') }} *
                                            {{ Form::text('barcode', '', array('class' => 'form-control stop_submit', 'id' => 'barcode' ) ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('name', 'Product Name') }} *
                                            {{ Form::text('name', '', array('class' => 'form-control') ) }}
                                        </div>
<div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('price', 'Sale Price') }} *
                                            {{ Form::text('price', '0', array('class' => 'form-control') ) }}
                                        </div>
                                        </div>

<div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('purchaseprice', 'Purchase Price') }} *
                                            {{ Form::text('purchaseprice', '0', array('class' => 'form-control') ) }}
                                        </div>
                                        </div>
<div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('tax', 'Tax Percentage') }} *
                                            {{ Form::text('tax', '0', array('class' => 'form-control') ) }}
                                        </div>
                                        </div>



                                        <div class="form-group">
                                            {{ Form::label('description', 'Description') }}
                                            {{ Form::textarea('description', '', array('class' => 'form-control') ) }}
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('startstock', 'Stock In Hand') }} *
                                                {{ Form::text('startstock', '0', array('class' => 'form-control') ) }}
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
