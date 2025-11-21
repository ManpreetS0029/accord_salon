@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Send Messages</div>

                <div class="panel-body">

                    @include('common.errors')
                    @include('common.success')
                    <div class="panel">
                        {{ Form::open(array('route' => ('sendmessages.store'))) }}
                        {{ csrf_field() }}

                        <div class="panel-body container-fluid">

                            <div class="row">
                                <div class="md-12">
                                    Balance Of Messages: <?php echo isset($balance_messages['response']) ? $balance_messages['response'] : ''; ?>

                                </div>
                            </div>
                            <br />

                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ Form::label('testphone', 'Enter Test Phone Number:') }} *
                                    {{ Form::text('testphone', '', array('class' => 'form-control') ) }}
                                    <br />
                                    {{ Form::submit('Test Send Message',  array('class' => 'btn btn-primary', 'value' => 'test_send_msg', 'name' => 'test_send_msg'))  }}
                                </div>
                            </div>


                            <div class="row row-lg">

                                <div class="col-lg-6">

                                    <div class="form-group">
                                        {{ Form::label('message12', 'Message') }} <br />
                                        <span id="displaybox"><span id="textarealength">0</span>/160 (Max 160 characters for 1 message)</span>
                                        {{ Form::textarea('message12', '', array('class' => 'form-control', 'id' => 'msgbox') ) }}
                                    </div>



                                </div>
                            </div>


                            <div class="row row-lg">

                                <div class="col-lg-6">


                                    <div class="form-group">
                                        {{ Form::label('clients', 'Select Client') }} *

                                        <br />
                                        <?php
                                        if (count($clientList)  > 0) {

                                        ?>
                                            <label for="allchecked">
                                                <input type="checkbox" name="allchecked" id="allchecked" value="1"> Select all
                                            </label>
                                            <div style="width: 100%; height: 300px; overflow-y: auto; border: 1px solid #aaa; padding: 10px;">
                                                <?php

                                                foreach ($clientList as $client) {
                                                ?>

                                                    <label style="width: 100%; display: block;">
                                                        <input class="client_id_boxes" type="checkbox" name="clientids[]" id="" value="<?php echo $client->phone; ?>"> <?php echo $client->clientname . ' ( ' . $client->phone . ' )'; ?>
                                                    </label>

                                                <?php } ?>
                                            </div>
                                        <?php

                                        }
                                        ?>





                                    </div>

                                </div>

                            </div>




                            <br><br>
                            <div class="row">
                                <div class="col-md-6">

                                    <button type="reset" class="btn btn-primary">Reset</button>

                                    {{ Form::submit('Send Message',  array('class' => 'btn btn-primary', 'value' => 'send_msg' , 'name' => 'send_msg'))  }}
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