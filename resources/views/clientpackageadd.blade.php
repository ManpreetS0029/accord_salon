@extends('layouts.app')
@section('content')


<div class="container">
    <div class="row">
        <div class="col-md-12 ">

            <div class="panel panel-default">
                <div class="panel-heading">Add Package</div>
                <div class="panel-body container-fluid">

            @include('common.errors')
            @include('common.success')

            {{ Form::open(array( 'route' => ['clientspackage.store' ], "id" => 'clientpackageaddform' )) }}
            {{ csrf_field() }}


                    <div class="panel" style="padding: 0 30px; box-shadow: none;">

                        <div class="form-group row">
                        <div class="row">
                            <div class="col-md-4">
                                {{ Form::label('packagename', "Package Name")  }}
                                <div class="form-group">
                                    {{ Form::text('packagename', '' , array('class' => 'form-control', 'required')) }}
                                    <span class="help-block">Package Name is required</span>
                                </div>
                            </div>
                        </div>
                        </div>

                <div class="form-group row exist_client_info">

                    {{ Form::label('title', 'Select Client') }} *
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            {{ Form::select('clientid', $clients, '' , array('class' => 'form-control', 'data-plugin' => "select2", 'id' => 'clientid', 'required') ) }}
                                <span class="help-block">Client is required</span>
                            </div>
                        </div>
                    </div>

                    {{ Form::label('title', 'Select Package Type') }} *
                    <br />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            {{ Form::select('packagetype', array('0'=> 'Select','1' => 'Cash Discount', '2' => 'Composite Package'), '' , array('class' => 'form-control', 'id' => 'packagetype', 'data-plugin' => "select2",  'required')) }}
                                <span class="help-block">Package type is required</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            {{ Form::label('title', 'Start Date') }} *
                            <div class="form-group">
                                {{ Form::text('startedate', ' ' , array('class' => 'form-control dates' , 'required')) }}
                                <span class="help-block">Start Date is required</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            {{ Form::label('title', 'Duration') }} *
                            <div class="form-group">
                                {{ Form::select('duration', $duration,
                                '' , array('class' => 'form-control' , 'required')) }}
                                <span class="help-block">Duration is required</span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            {{ Form::label('title', 'Actual Price') }} *
                            <div class="form-group">
                            {{ Form::text('actualprice', ' ' , array('class' => 'form-control', 'id' =>'actualprice' , 'required')) }}
                                <span class="help-block">Actual Price is required</span>
                            </div>
                        </div>

                        <div class="col-md-3" style="display: none;" id="giftedprice">
                            {{ Form::label('title', 'Gifted Price') }} *
                            <div class="form-group">
                                {{ Form::text('giftedprice', ' ' , array('class' => 'form-control', 'id' => 'gifted_price_input', 'required')) }}
                                <span class="help-block">Gifted price is required</span>
                            </div>
                        </div>

                    </div>



                    <section id="compositepackage" style="display: none;">
                        <h3 style="margin: 0px;">Composite Package </h3>
                        <table class="table table-responsive">
                            <thead>
                            <tr><th>Item</th><th>Quantity</th> <th><button class="add_row" type="button">+ Add Item</button> </th></tr>
                            </thead>
                            <tbody id="composite_package_item_list">

                            <tr class="composite_item">

                                <td class="item_a">
                                    <div style="width: 350px !important;">

                                        {{ Form::select('item[]', $services,
                                   '' , array('class' => 'serviceitem ',  'id' => 'serviceitemid')) }}

                                    </div>
                                </td>
                                <td>
                                    {{ Form::text('itemquantity[]', ' ' , array('class' => 'form-control', 'required')) }}


                                </td>
                            </tr>

                            </tbody>


                        </table>

                    </section>
                    <section>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                {{ Form::select('paymentmodeid', $paymentmodes, '' , array('class' => 'form-control', 'id' => 'paymentmodeid') ) }}
                            </div>

                            <div class="col-md-3">
                                {{ Form::label('title', 'Paid Amount') }} *
                                <div class="form-group">
                                    {{ Form::text('paidamount', '' , array('class' => 'form-control', 'id'=>'paidamount')) }}
                                </div>
                            </div>

                            <div class="col-md-3" style="color: #F00; display: none;" id="balanceamount">

                                {{ Form::label('title', 'Balance') }}:

                                    <span>500</span>
                                    <label><input  type="checkbox" name="addasadvance" id="addasadvance" value="1"> Add as Advance</label>

                            </div>



                        </div>
                    </div>

                    <div id="sale_other_payment_info" class="form-group" style="<?php if(Request::old('paymentmodeid') != '5' ) {  echo 'display:none'; } ?>">
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
                                {{ Form::text('other', '' , array('class' => 'form-control' ) ) }}
                            </div>
                        </div>
                    </div>

                    <div id="sale_bank_payment_info" class="form-group " style="<?php if(Request::old('paymentmodeid') != '2' ) {  echo 'display:none'; } ?>">
                        <div class="row">
                            <div class="form-group col-md-3">
                                {{ Form::label('bankname', 'Bank Name') }}
                                {{ Form::text('bankname', '' , array('class' => 'form-control' ) ) }}
                            </div>

                            <div class="form-group col-md-3">
                                {{ Form::label('bankaccountno', 'Bank Account No') }}
                                {{ Form::text('bankaccountno', '' , array('class' => 'form-control' ) ) }}
                            </div>


                            <div class="form-group col-md-3">
                                {{ Form::label('chequeno', 'Cheque No') }}
                                {{ Form::text('chequeno', '' , array('class' => 'form-control' ) ) }}
                            </div>
                            <div class="form-group col-md-3">
                                {{ Form::label('chequedate', 'Cheque Date') }}
                                {{ Form::text('chequedate', '' , array('class' => 'form-control dates' ) ) }}
                            </div>

                        </div>
                    </div>
    </section>
                    <section>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    {{ Form::label('paymentmodeid', 'Payment Mode') }} *<br />
                                    {{ Form::select('paymentmodeid2', $paymentmodes, '' , array('class' => 'form-control', 'id' => 'paymentmodeid2') ) }}
                                </div>

                                <div class="col-md-3">
                                    {{ Form::label('title', 'Paid Amount') }} *
                                    <div class="form-group">
                                        {{ Form::text('paidamount2', '' , array('class' => 'form-control', 'id'=>'paidamount2')) }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div id="sale_other_payment_info2" class="form-group" style="<?php if(Request::old('paymentmodeid2') != '5' ) {  echo 'display:none'; } ?>">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('otherpaymentmethod', 'Other Payment Mode') }} *
                                    {{ Form::text('other2', '' , array('class' => 'form-control' ) ) }}
                                </div>
                            </div>
                        </div>

                        <div id="sale_bank_payment_info2" class="form-group" style="<?php if(Request::old('paymentmodeid2') != '2' ) {  echo 'display:none'; } ?>">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {{ Form::label('bankname', 'Bank Name') }}
                                    {{ Form::text('bankname2', '' , array('class' => 'form-control' ) ) }}
                                </div>

                                <div class="form-group col-md-3">
                                    {{ Form::label('bankaccountno', 'Bank Account No') }}
                                    {{ Form::text('bankaccountno2', '' , array('class' => 'form-control' ) ) }}
                                </div>


                                <div class="form-group col-md-3">
                                    {{ Form::label('chequeno', 'Cheque No') }}
                                    {{ Form::text('chequeno2', '' , array('class' => 'form-control' ) ) }}
                                </div>
                                <div class="form-group col-md-3">
                                    {{ Form::label('chequedate', 'Cheque Date') }}
                                    {{ Form::text('chequedate2', '' , array('class' => 'form-control dates' ) ) }}
                                </div>

                            </div>
                        </div>
                    </section>

                </div>
                </div>
            </div>
            </div>




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


<script>
    var services = <?php echo json_encode($services2); ?>;
    var datas = "";
    if( services.length > 0 )
    {
        for( var x = 0; x < services.length; x++ )
        {
            datas += '<option value="'+services[x]["id"]+'">'+services[x]["category"]["name"]+'-'+services[x]["name"]+'('+services[x]["price"]+')'+'</option>';
        }
    }



</script>

@endsection