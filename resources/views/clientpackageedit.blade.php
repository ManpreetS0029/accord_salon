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
<?php // var_dump($package); die();

$countPackage = count($package->packageSales);  //var_dump($countPackage); die();

/*if ($countPackage > 0) {
        echo '<div style="position: absolute; z-index:50000000; width: 100%; height: 100%; background-color: #eee; opacity: 0.2;">
    </div>';
    } */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">Update Package</div>
                <div class="panel-body container-fluid">

                    @include('common.errors')
                    @include('common.success')

                    <div class="panel" style="padding: 0 30px; box-shadow: none;">
                        {{ Form::open(array( 'method' => 'PUT','route' => ['clientspackage.update', $package->id ])) }}
                        {{ csrf_field() }}
                        <div style="float: right;">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModal">
                                Add Payment
                            </button>
                            <br /><br />
                            <?php

                            echo "Sales Pending Payment: " . number_format($package->client->getSalesPendingAmount(), 2);
                            echo "<br />";
                            echo "Client Advance: " . number_format($package->client->getTotalAdvanceAmount(), 2);
                            echo "<br />";
                            echo "Total Pending Payment from client: " . number_format($pendingAmountFromClient, 2);



                            ?>
                        </div>

                        <div class="form-group row">
                            <div class="row">
                                <div class="col-md-4">
                                    {{ Form::label('packagename', "Package Name")  }}
                                    <div class="form-group">
                                        {{ Form::text('packagename', $package->packagename , array('class' => 'form-control', 'disabled' => 'disabled')) }}
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
                                        {{ Form::select('clientid', $clients, $package->clientid , array('class' => 'form-control', 'data-plugin' => "select2", 'id' => 'clientid', 'disabled' => 'disabled') ) }}
                                    </div>
                                </div>
                            </div>

                            {{ Form::label('title', 'Select Package Type') }} *
                            <br />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{ Form::select('packagetype',  array('0'=> 'Select','1' => 'Cash Discount', '2' => 'Composite Package'), $package->packagetype , array('class' => 'form-control', 'data-plugin' => "select2", 'disabled' => 'disabled')) }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    {{ Form::label('title', 'Start Date') }} *
                                    <div class="form-group">
                                        {{ Form::text('startedate', date('d/m/Y', strtotime($package->startedate) ) , array('class' => 'form-control dates')) }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    {{ Form::label('title', 'Duration') }} *
                                    <div class="form-group">
                                        {{ Form::select('duration', $duration,
                                            $package->duration , array('class' => 'form-control')) }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    {{ Form::label('title', 'Actual Price') }} *
                                    <div class="form-group">
                                        {{ Form::text('actualprice', $package->actualprice , array('class' => 'form-control', 'id' =>'actualprice')) }}
                                    </div>
                                </div>

                                <?php
                                if ($package->packagetype == 1) // if package type is Cash Package
                                { ?>
                                    <div class="col-md-3" style="display: block;" id="giftedprice">
                                        {{ Form::label('title', 'Gifted Price') }} *
                                        <div class="form-group">
                                            {{ Form::text('giftedprice', $package->giftedprice , array('class' => 'form-control')) }}
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>

                            <?php
                            if ($package->packagetype == 2) // if package type is Composite Package
                            {

                            ?>
                                <section id="compositepackage" style="display: block;">

                                    <h3 style="margin: 0px;">Composite Package </h3>
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th width="375px">Item</th>
                                                <th>Quantity</th>
                                                <th>Used</th>
                                                <th>
                                                    <button class="add_row_edit" type="button">+ Add Item</button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="composite_package_item_list" class="edit_package_list">

                                            <?php
                                            $Items = $package->packageItems;

                                            $ItemsUsed =  $package->usedItemsList();

                                            $x = 0;
                                            foreach ($Items as $Item) {
                                                $x++;
                                            ?>

                                                <tr class="composite_item">

                                                    <td class="item_a">
                                                        <div style="width: 350px !important;">
                                                            {{ Form::select('item[]', $services,
                                               $Item['itemid'] , array('class' => 'serviceitem form-control ',  'id' => 'serviceitemid'.$x )
                                               ) }}

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group" style="width: 350px !important;">
                                                            {{ Form::text('itemquantity[]', $Item['quantity'] , array
                                                ('class' => 'form-control itemquantity', 'style' => 'width: 100px; ')) }}
                                                            <span class="help-block">Quantity Should be Greator than used qnty. </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $usedQnty = 0;
                                                        if (count($ItemsUsed) > 0) {
                                                            if (isset($ItemsUsed[$Item['itemid']]) && $ItemsUsed[$Item['itemid']] > 0) {

                                                                $usedQnty = $ItemsUsed[$Item['itemid']];
                                                            }
                                                        }

                                                        echo  '<span class="used_item_qnty">' . $usedQnty . '</span>';

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $showDeleteBtn = true;
                                                        if (count($ItemsUsed) > 0) {
                                                            if (isset($ItemsUsed[$Item['itemid']]) && $ItemsUsed[$Item['itemid']] > 0) {

                                                                $showDeleteBtn = false;
                                                            }
                                                        }
                                                        ?>
                                                        <?php if ($showDeleteBtn == true) { ?>
                                                            <button class="delete_row" type="button">Delete</button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>


                                    </table>



                                </section>
                            <?php } ?>
                            <br /><br />
                            <div class="row">
                                <div class="col-md-6">

                                    <button type="reset" class="btn btn-primary">Reset</button>

                                    {{ Form::submit('Save',  array('class' => 'btn btn-primary'))  }}
                                </div>
                            </div>
                            {{ Form::close() }}

                            <br />
                            <h4>Package Billing</h4>
                            <?php
                            $sales = $package->packageSales;
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sale ID</th>
                                            <th>Sale Date</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Pending Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($sales as $sale) {
                                        ?>
                                            <tr>
                                                <td><?php echo $sale->id; ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($sale->created_at)); ?></td>
                                                <td><?php echo $sale->paidprice; ?></td>
                                                <td><?php echo number_format($sale->getTotalPaidAmount(), 2); ?></td>
                                                <td><?php echo number_format($sale->actualPendingAmount(), 2); ?></td>
                                                <td>
                                                    <?php if ($sale->actualPendingAmount() > 0) {

                                                    ?>
                                                        <button data-link="<?php echo route('allpayment.create', ['saleid' => $sale->id, 'iframe' => '1']); ?>" type="button" class="btn btn-primary" data-toggle="modal"
                                                            data-target="#exampleModal">
                                                            Add Payment
                                                        </button>

                                                    <?php
                                                    }


                                                    ?>
                                                    <form action="<?php echo route('clientspackage.edit', ['clientspackage' => $package->id]); ?>" method="post" style="display:inline;">
                                                        <input type="hidden" name="saleid" value="<?php echo $sale->id; ?>">
                                                        <input type="hidden" name="delete" value="1">
                                                        <button name="delete_sale" value="delete_sale" type="submit" class="btn btn-danger deletebtn">Delete</button>
                                                    </form>


                                                    <a href="<?php echo route('sale.edit', ['sale' => $sale->id]); ?>" class="btn btn-primary">
                                                        Edit
                                                    </a>

                                                    <?php

                                                    ?>
                                                </td>
                                            </tr>
                                        <?php

                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
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
                    src="<?php echo route('allpayment.create', ['clientid' => $package->client->id, 'iframe' => '1']); ?>"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<script>
    var services = <?php echo json_encode($services2); ?>;
    var datas = "";
    if (services.length > 0) {
        for (var x = 0; x < services.length; x++) {
            datas += '<option value="' + services[x]["id"] + '">' + services[x]["category"]["name"] + '-' + services[x]["name"] + ' - (' + services[x]["price"] + ')' + '</option>';
        }
    }
</script>

@endsection