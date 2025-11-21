@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Edit Product</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array( 'method' => 'PUT', 'route' => ['product.update', $product->id] )) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <?php if( $product->stockavailable <= 0 ){

?>

                                <div class="alert alert-danger">Out Of Stock</div>
<?php 
}
                                ?>

                                <div class="row row-lg">

                                    <div class="col-lg-6">
                             <div class="form-group">
                                            {{ Form::label('hsncode', 'HSN Code') }} *
                                            {{ Form::text('hsncode', $product->hsncode , array('class' => 'form-control') ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('name', 'Product Code') }} *
                                            {{ Form::text('productcode', $product->productcode, array('class' => 'form-control stop_submit', 'id' => 'productcode' ) ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('barcode', 'Barcode') }} *
                                            {{ Form::text('barcode', $product->barcode, array('class' => 'form-control stop_submit', 'id' => 'barcode' ) ) }}
                                        </div>


                                        <div class="form-group">
                                            {{ Form::label('name', 'Product Name') }} *
                                            {{ Form::text('name', $product->name, array('class' => 'form-control') ) }}
                                        </div>
<div class="row">
                                        <div class="form-group col-md-6">

                                            {{ Form::label('price', 'Sale Price') }} *
                                            {{ Form::text('price', $product->price , array('class' => 'form-control') ) }}
                                        </div>
</div>

<div class="row">
    <div class="form-group col-md-6">
        {{ Form::label('purchaseprice', 'Purchase Price') }} *
        {{ Form::text('purchaseprice', $product->purchaseprice, array('class' => 'form-control') ) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6">
	{{ Form::label('tax', 'Tax Percentage') }} *
	{{ Form::text('tax', $product->tax, array('class' => 'form-control') ) }}
    </div>
</div>

{{ Form::label('description', 'Description') }}
{{ Form::textarea('description', $product->description, array('class' => 'form-control') ) }}


                                        </div>


<div class="row">
                                                                                                                  <h4>
                                                                                                                  Stock Available:  {{ $product->stockavailable }}
</h4>
                                                                                                                  </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('add', 'Add Stock') }} *
                                                {{ Form::text('addstock','' , array('class' => 'form-control') ) }}
                                            </div>
                                        </div>
<div class="row">
                                                                                          <div class="col-md-6">
                                                                                          <button name="updatestock" value="1"  type="submit" class="btn btn-primary">Update Stock</button>
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
