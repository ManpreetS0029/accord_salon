@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Expenses</div>

                <div class="panel-body">

                    @include('common.errors')
                    @include('common.success')
                    <form method="post">
                        {{ csrf_field() }}
                        <div class="table-responsive">

                            <table class="table table-striped">
                                <tr>
                                    <td>

                                        {{ Form::label('expensedate', 'Select Date') }} *
                                        <br />
                                        <?php
                                        $dates = Request::old('expensedate') != "" ? Request::old('expensedate') : date("d/m/Y");
                                        ?>
                                        {{ Form::text('expensedate', $dates, array('class' => 'form-control dates') ) }}

                                    </td>
                                    <td>


                                        {{ Form::label('paymentmodeid', 'Payment Mode') }} *
                                        <br />
                                        <?php

                                        //                                $selectedId = Request::old('paymentmodeid') != '' ? Request::old('paymentmodeid') : '1';

                                        ?>
                                        {{ Form::select('paymentmodeid', $paymentmodes , '', array('class' => 'form-control ') ) }}
                                    </td>

                                    <td>

                                        {{ Form::label('expensemasterid', 'Select Expense Type') }} *
                                        <br />
                                        {{ Form::select('expensemasterid', $expensemaster , '', array('class' => 'form-control') ) }}

                                    </td>

                                    <td>

                                        {{ Form::label('amount', 'Amount') }} *
                                        {{ Form::text('amount', '0.00', array('class' => 'form-control') ) }}
                                    </td>

                                    <td>
                                        {{ Form::label('remarks', 'Remarks') }}
                                        {{ Form::text('remarks', '', array('class' => 'form-control') ) }}
                                    </td>

                                    <td style="white-space: nowrap;">
                               
                                        <button type="reset" class="btn btn-primary" style="margin-right:5px;">Reset</button>

                                        <button type="submit" class="btn btn-primary">Save</button>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>

                    <form method="get">
                        <div class="table-responsive">
                            <table class="table table-striped">

                                <td>

                                    {{ Form::label('expensedatefrom', 'Date From') }} *
                                    <br />
                                    <?php
                                    $dates = Request::old('expensedatefrom') != "" ? Request::old('expensedatefrom') : '';
                                    ?>
                                    {{ Form::text('expensedatefrom', $dates, array('class' => 'form-control datepickers', 'autocomplete' => 'off') ) }}
                                </td>

                                <td>

                                    {{ Form::label('expensedateto', 'Date From') }} *
                                    <br />
                                    <?php
                                    $dates = Request::old('expensedateto') != "" ? Request::old('expensedateto') : '';
                                    ?>
                                    {{ Form::text('expensedateto', $dates, array('class' => 'form-control datepickers', 'autocomplete' => 'off') ) }}
                                </td>

                                <td>

                                    {{ Form::label('expensecatsearch', 'Search Expense Type') }} *
                                    <br />
                                    {{ Form::select('expensecatsearch', $expensemaster , '', array('class' => 'form-control') ) }}

                                </td>
                                <td>
                                    <br />
                                    <input type="submit" name="search" class="btn btn-primary" value="Search">
                                </td>

                            </table>
                        </div>

                    </form>

                    <div>
                        <table class="table">
                            <tr style="background-color: #ff6666;">
                                <td>Total </td>
                                <td>
                                    <?php echo $totalamount; ?>
                                </td>
                            </tr>
                        </table>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Expense Type</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Create Date</th>
                                    <th>Update Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $item)
                                <tr>
                                    <td>{{ $item->expenseMaster->name }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->remarks }}</td>
                                    <td>{{ date("d/m/Y", strtotime($item->expensedate)) }}</td>
                                    <td>{{ date("d/m/Y h:i A", strtotime($item->created_at)) }}</td>
                                    <td>{{ date("d/m/Y h:i A", strtotime($item->updated_at)) }}</td>
                                    <td>

                                        <a class="btn btn-success" href="{{ route('expense.edit',$item->id  ) }}">Edit</a>

                                        {{ Form::open([ 'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['expense.destroy', $item->id] ]) }}
                                        {{ Form::hidden('id', $item->id) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                        {{ Form::close() }}


                                    </td>
                                </tr>

                                @endforeach


                            </tbody>
                        </table>

                    </div>
                    {{ $expenses->links() }}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection