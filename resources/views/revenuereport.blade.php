@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Revenue Report</div>

                    <div class="panel-body">
                      @include('common.errors')
                        @include('common.success')
                        <div class="panel">


                            <div class="panel-body container-fluid">

                                {{ Form::open(array( 'method' => 'get', 'route' => 'revenuereport.index')) }}
                                <div class="row row-lg">

                                    <div class="col-lg-12">

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                {{ Form::label('datefrom', 'Date From') }} *
                                                <br />
                                                <?php
                                                 $dates = Request::get('datefrom') != "" ? Request::old('datefrom') : date("d/m/Y");
                                                ?>
                                                {{ Form::text('datefrom',$dates , array('class' => 'form-control dates_nodefault', 'autocomplete' => 'off') ) }}
                                            </div>

                                            <div class="form-group col-md-4">
                                                {{ Form::label('dateto', 'Date To') }} *
                                                <br />
                                                <?php
                                                $dates = Request::get('dateto') != "" ? Request::old('dateto') : date("d/m/Y");
                                                ?>
                                                {{ Form::text('dateto', $dates, array('class' => 'form-control dates_nodefault', 'autocomplete' => 'off') ) }}
                                            </div>


                                    <div class="col-md-4">

    <br />

                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>

                            </div>
                                    {{ Form::close() }}
                                </div>

                                <div class="container-fluid">

                                    <div class="row">
                            <div class="col-md-6">

                                <div class="row">

                                    <div class="col-md-12">
                                    <h3>Services</h3>
                                    </div>

                                </div>

                                    <div class="row">
                                    <div class="col-md-4">
                                      Amount:
                                    </div>
                                        <div class="col-md-4">
                                           <?php echo number_format($serviceSales, 2); ?>
                                        </div>



                                    </div>


                                <div class="row">
                                    <div class="col-md-4">
                                        Total Discount:
                                    </div>
                                    <div class="col-md-4">
                                        <?php echo number_format($serviceDiscount, 2); ?>
                                    </div>

                                </div>








                                <div class="row">
                                    <div class="col-md-4">
                                        Taxable Amount:
                                    </div>
                                    <div class="col-md-4">
                                        <?php echo number_format($serviceTaxableAmount, 2); ?>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        Total Tax:
                                    </div>
                                    <div class="col-md-4">
                                        <?php echo number_format($serviceTax, 2); ?>
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <hr />
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Service Amt(A):</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <strong> <?php echo number_format($serviceTotalPaidPrice, 2); ?></strong>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr />
                                    </div>

                                </div>
                                <br>


                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Staff Commission</h3>

                                    </div></div>

                                <div class="row">
                                    <div class="col-md-4">Total Commission</div>
                                    <div class="col-md-4"><?php echo number_format(round($staffCommision), 2); ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12"><hr></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Commission</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <strong><?php echo number_format(round($staffCommision), 2); ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr />
                                    </div>

                                </div>

                            </div>

                                        <!-- Products and Expenses -->
                                        <div class="col-md-6">
                                            <div class="row">

                                                <div class="col-md-12">
                                                    <h3>Products</h3>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Amount:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format($productSales, 2); ?>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Discount:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format($productDiscount, 2); ?>
                                                </div>

                                            </div>






                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Taxable Amount:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($productTaxableAmount), 2); ?>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Tax:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($productTax), 2); ?>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr />
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Product Amt(B):</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong> <?php echo number_format(round($productTotalPaidPrice), 2); ?></strong>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr />
                                                </div>

                                            </div>
                                            <br>
                                            <br> <br> <br>

                                            <div class="row">
                                                <div class="col-md-12">
                                                <h3>Expenses</h3></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Expenses
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo number_format(round($totalExpenses), 2); ?>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Salaries
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo number_format(round($totalSalary), 2); ?>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr />
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    Total Cash:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($grandExpensesCash), 2); ?>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    By Cheque:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($grandExpensesCheque), 2); ?>
                                                </div>

                                            </div>


                                            <div class="row">
                                                <div class="col-md-4">
                                                    By Card:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($grandExpensesCard), 2); ?>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    Other:
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo number_format(round($grandExpensesOther), 2); ?>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Total Expenses(C):</strong>
                                                </div>
                                                <div class="col-md-4">

                                                    <strong><?php echo number_format(round($grand_expenses), 2); ?></strong>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr />
                                                </div>

                                            </div>

                                        </div>

                            </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3>Totals</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">Total Sale:</div>
                                        <div class="col-md-4"><?php echo number_format(round($grand_sale), 2); ?></div>
                                    </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        Total Amount Pending:
                                    </div>
                                    <div class="col-md-4">
                                        <?php echo number_format(round($grand_sale_pending_amount), 2); ?>
                                    </div>

                                </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                           Advance Adjusted:
                                        </div>
                                        <div class="col-md-4">
                                            <?php echo number_format(round($totalAdvanceAdjusted), 2); ?>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-4">By Cash:</div>
                                        <div class="col-md-4"> <?php echo number_format(round($grand_sale_cash), 2); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">By Card:</div>
                                        <div class="col-md-4"><?php echo number_format(round($grand_sale_card), 2); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">By Cheque:</div>
                                        <div class="col-md-4"><?php echo number_format(round($grand_sale_cheque), 2); ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">By Other:</div>
                                        <div class="col-md-4"><?php echo number_format(round($grand_sale_other), 2); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-4"><strong>Total Collection:</strong></div>
                                        <div class="col-md-4">
                                            <strong>
                                                <?php echo number_format(round($grand_sale_collection), 2); ?>
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr />
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-4">Expenses:</div>
                                        <div class="col-md-4">
                                            <?php echo number_format(round($grand_sale_expenses), 2); ?>

                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-4">Advance Receipt:</div>
                                        <div class="col-md-4">
                                            <?php echo number_format(round($totalAdvanceRecipet), 2); ?>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">Balance Cash:</div>
                                        <div class="col-md-4"><?php echo number_format(round($totalCashBalance), 2); ?></div>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-4">Op. Drawer Balance:</div>
                                        <div class="col-md-4"><?php echo number_format(round($totalOpeningBalance), 2); ?></div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4"><strong>Closing. Drawer Balance:</strong></div>
                                        <div class="col-md-4">
                                         <strong><?php echo number_format(round($totalClosingBalance), 2); ?></strong>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <?php if( count((array)$days) > 0 && $days->isclosed != '1' ) {
          ?>
                                            {{Form::open(['route' => 'revenuereport.store'])}}

                                                {{csrf_field()}}
                                <lable>Opening Balance</lable>
                                                <br>
                                                <input type="text" name="openingbalance" value="<?php echo $days->openingbalance; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                    <div class="col-md-6">
                                                <button type="submit" name="update_opening_balance" value="update_opening_balance" class="btn btn-primary">Update Opening Balance</button>


                                                <button type="submit" name="close_day" value="close_day" class="btn btn-primary">Close Day</button>

                                                {{Form::close()}}
                                            <?php
    } ?>
                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
