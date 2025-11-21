@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Issue A Product</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => 'productissue.store')) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">

                                      <div class="row">

                                        <div class="form-group col-md-6">
                                            {{ Form::label('productid', 'Select Product or Scan Barcode') }} *
                                                <br />
                                                {{ Form::select('productid', $products  , '', array('class' => 'form-control', 'data-plugin' => "select2" ) ) }}
                                        </div>

                                          <div class="form-group col-md-6">
                                <label>&nbsp;</label>
                                              {{ Form::text('barcode', '', array('class' => 'form-control', 'id' => 'barcode', 'placeholder' => 'Click Here and Scan Barcode') ) }}
                                          </div>
                                      </div>

                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                {{ Form::label('qnty', 'Quantity') }} *
                                                {{ Form::text('qnty', '1', array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

<div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('staffid', 'Issue To Staff Member') }} *
                                            <br />
                                            {{ Form::select('staffid', $staff  , '', array('class' => 'form-control', 'id' => 'staffid', 'data-plugin' => "select2" ) ) }}
                                        </div>
</div>
                                        <div class="row">
                                        <div class="form-group col-md-3">
                                            {{ Form::label('issuedate', 'Issue Date') }} *
                                            {{ Form::text('issuedate', date("d/m/Y") , array('class' => 'form-control', 'id' => 'issuedate') ) }}
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
    <script>

        window.onload = function(){

            document.getElementById("barcode").focus();
        }

    </script>
@endsection
