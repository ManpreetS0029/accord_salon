@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Salary Paids</div>

                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')

                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Staff Name</label>

                                {{ Form::select('searchtext', $staff ,  ''  , array('class' => 'form-control', 'id' => 'searchtext', 'data-plugin' => 'select2' ) ) }}

                            </div>
                            <div class="col-md-3">
                                <label for="">Month </label>

                                {{ Form::select('salary_month', $months , Request::exists( "salary_month") ?  Request::get( "salary_month") :  date("m")  , array('class' => 'form-control', 'id' => 'salary_month') ) }}
                            </div>
                            <div class="col-md-3">
                                <label for="">Year</label>
                                {{ Form::text('salary_year', Request::exists( "salary_year") ? Request::get( "salary_year") : $years, array('class' => 'form-control') ) }}
                            </div>
                            <div class="col-md-4">
                                <label for="">&nbsp;</label><br />

                                {{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
                            </div>
                        </div>
                    </form>
                    <br />

                    <div class="table-responsive">
                        <table class="table table-striped ">
                            <thead style="background-color: #000; color: #fff;">
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Phone</th>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Add date</th>
                                    <th>Payment Mode</th>
                                    <th>Remarks</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $totalAmount = 0;
                                foreach ($salarypaidlist as $item) {
                                    $totalAmount += $item->amount;
                                ?>

                                    <tr>
                                        <td><?php echo $item->staff->getName(); ?></td>
                                        <td><?php echo $item->staff->mobile; ?></td>
                                        <td><?php echo $months[str_pad($item->months, 2, 0, STR_PAD_LEFT)] . ' (' . $item->months . ') '; ?></td>
                                        <td><?php echo $item->years; ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($item->created_at)); ?></td>
                                        <td><?php echo $paymentmodes[$item->paymentmodeid]; ?> </td>
                                        <td><?php echo $item->remarks; ?></td>
                                        <td><?php echo number_format($item->amount, 2); ?></td>
                                        <td>
                                            <a class="btn btn-primary" href="<?php echo route('salary.edit', $item->id); ?>">Edit</a>
                                            {{form::open(array( 'method' => 'DELETE', 'route' => ['salary.destroy', $item->id], 'style' => 'display: inline;'))}}
                                            {{csrf_field()}}
                                            <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                            {{form::close()}}
                                        </td>
                                    </tr>
                                <?php
                                }

                                ?>
                                <tr style="background-color: #000; color: #fff;">
                                    <th colspan="7" style="text-align: right;">Total:</th>
                                    <th colspan="2"><?php echo number_format($totalAmount, 2); ?></th>
                                </tr>

                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection