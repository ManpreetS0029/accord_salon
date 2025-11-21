@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Client Packages</div>

                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')

                    <form action="" method="get">
                        <div class="row">

                            <div class="col-md-2">
                                <label for="">Package Id</label>
                                {{ Form::text('searchtext', '', ['class' => 'form-control'] )}}
                            </div>

                            <div class="col-md-3">
                                <label for="">Client Name</label>
                                {{ Form::text('searchtextclientname', '', ['class' => 'form-control'] )}}
                            </div>

                            <div class="col-md-3">
                                <label for="">Client Phone</label>
                                {{ Form::text('searchtextclientphone', '', ['class' => 'form-control'] )}}
                            </div>


                            <div class="col-md-3">
                                <label for="">&nbsp;</label><br />

                                {{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
                            </div>
                        </div>
                    </form>
                    <br />

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>Package ID</th>
                                    <th>Client Id/Name</th>
                                    <th>Package Name</th>
                                    <th>Phone</th>
                                    <th>Package Type</th>
                                    <th>Actual Price</th>
                                    <th>Gifted Price</th>
                                    <th>Started Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                //var_dump( $clientpackages);
                                $zx = 1;
                                foreach ($clientpackages as $id => $package) {
                                ?>

                                    <tr>
                                        <td><?php echo $zx; ?></td>
                                        <td>ACC#<?php echo $package->id ?></td>
                                        <td> <?php echo $package->client['clientname'] ?></td>
                                        <td><?php echo $package->packagename ?></td>
                                        <td><?php echo $package->client['phone'] ?></td>
                                        <td><?php echo (($package->packagetype) == 1 ? "Cash" : "Composite Package") ?></td>
                                        <td><?php echo $package->actualprice ?></td>
                                        <td><?php echo $package->giftedprice ?></td>
                                        <td><?php echo  date('d/m/Y', strtotime($package->startedate));  ?></td>
                                        <td>

                                            <a class="btn btn-success" href="{{ route('clientspackage.edit', $package->id) }}">Edit</a>

                                            {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['clientspackage.destroy', $package->id] ]) }}
                                            {{ Form::hidden('id', $package->id) }}
                                            {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                            {{ Form::close() }}

                                        </td>
                                    </tr>

                                <?php $zx++;
                                } ?>


                            </tbody>
                        </table>

                    </div>
                    {{ $clientpackages->links() }}

                </div>

            </div>
        </div>
    </div>



    @endsection