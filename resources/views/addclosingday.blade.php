@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Closing Day Details</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array(   'route' => ['closingday.store' ])) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-8">

                                        <div class="form-group">
                                            <label>Date</label>
                                            <?php echo date("d/m/Y"); ?>
                                        </div>

                       <?php if( is_array($days) && count($days) > 0 )
                           {
                               if( $days->isclosed == "1" )
                                   {
                                       ?>
                                       Day is already closed. Can not do anything now.
                                        <?php
                                   }
                                   else
                                       {
                                           ?>
                                        <div class="form-group col-md-3">
                                            <label>Opening Balance</label>

                                            <input type="text" class="form-control" name="openingbalance" value="<?php echo $days->openingbalance; ?>" >
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label>Closing Balance</label>

                                            <input type="text" class="form-control" name="closingbalance" value="<?php echo $days->closingbalance; ?>" readonly >
                                        </div>
                                    </div>
                                </div>
                                            <div class="row">
                                                <div class="col-md-3">



                                                    <button type="submit" name="update_opening_balance" value="update_opening_balance" class="btn btn-primary">Update Opening Balance</button>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" name="regenrate_closing_bal_save" value="regenrate_closing_bal_save" class="btn btn-primary">Regenerate Closing Balance And Close Day</button>
                                                </div>
                                            </div>
                                        </div>



                                    <?php
                                       }

                           }
                           else
                               {
                                   ?>
<div class="row">
                                        <div class="form-group col-md-3">
                                            <label>Opening Balance</label>

                                            <input type="text" class="form-control" name="openingbalance" value="" >
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label>Closing Balance</label>

                                            <input type="text" readonly="readonly" class="form-control" name="closingbalance" value="" >
                                        </div>
</div>
                                        <div class="row">
                                            <div class="col-md-6">

                                                <button type="submit" class="btn btn-primary" name="add_opening_balance" value="add_opening_balance" >Add Opening Balance</button>
                                            </div></div>

                                        <?php
                               }
                           ?>



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
