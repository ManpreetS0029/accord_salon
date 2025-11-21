@extends('layouts.app')

@section('content')

    <style>
        .modal-dialog {
            width: 100%;
            height: 100%
        }

        .modal-content {
            width: 100%;
            height: 100%;
        }

        .modal-body {
            width: 100%;
            height: 70%;
        }

        iframe {
            border: 0px;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Client</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')

                        <div class="panel">

                            <div style="float: right;">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModal">
                                    Add Payment
                                </button>
                                <br/><br/>
                                {{ Form::open(array( 'method' => 'GET', 'route' => ['clients.paymentlist',$client->id] )) }}

                                {{ Form::submit('Payment List',  array('class' => 'btn btn-primary'))  }}

                                {{ Form::close() }}
                                <br/><br/>
                                <?php

                                echo "Sales Pending Payment: " . number_format($client->getSalesPendingAmount(), 2);
                                echo "<br />";
                                echo "Client Advance: " . number_format($client->getTotalAdvanceAmount(), 2);
                                echo "<br />";
                                echo "Total Pending Payment from client: " . number_format($client->clientPendingPaymentRegardingPackages(), 2);

                                ?>
                            </div>



                            {{ Form::open(array( 'method' => 'PUT', 'route' => ['clients.update',$client->id] )) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            {{ Form::label('clientname', 'Client Name') }} *
                                            {{ Form::text('clientname', $client->clientname, array('class' => 'form-control'  ) ) }}
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('dob', 'Date of Birth') }} * (dd/mm/yyyy)
                                                {{ Form::text('dob', date("d/m/Y", strtotime($client->dob)), array('class' => 'form-control', 'placeholder' => 'dd/mm/yyyy') ) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('email', 'Email Address') }}
                                            {{ Form::text('email', $client->email , array('class' => 'form-control') ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('address', 'Address') }}
                                            {{ Form::text('address', $client->address, array('class' => 'form-control') ) }}
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('city', 'City') }}
                                                {{ Form::text('city', $client->city, array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="form-group col-md-6">
                                                {{ Form::label('state', 'State') }}
                                                {{ Form::text('state', $client->state , array('class' => 'form-control') ) }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('zipcode', 'Zip Code') }}
                                                {{ Form::text('zipcode', $client->zipcode, array('class' => 'form-control') ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                {{ Form::label('phone', 'Phone') }} *
                                                {{ Form::text('phone', $client->phone, array('class' => 'form-control') ) }}
                                            </div>

                                            <div class="form-group col-md-6">
                                                {{ Form::label('phone2', 'Phone 2') }}
                                                {{ Form::text('phone2', $client->phone2, array('class' => 'form-control') ) }}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {{ Form::label('description', 'Description') }}
                                            {{ Form::textarea('description', $client->description, array('class' => 'form-control') ) }}
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Manage Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe style="width: 100%; height: 100%;"
                            src="<?php echo route('allpayment.create', ['clientid' => $client->id, 'iframe' => '1']); ?>"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
@endsection
