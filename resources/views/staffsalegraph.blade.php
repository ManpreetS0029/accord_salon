@extends('layouts.app')

@section('content')
    <?php $arrMonth = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December' );
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Staff Sale Details</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
			<form method="get" action="" >
			    <div class="row">
					<div class="form-group  col-md-2">
						<label>Date From</label><br />
						<input type="text" autocomplete="off" class="dates_nodefault form-control" value="<?php echo Request::get('datefrom') ; ?>" name="datefrom">
					</div>
					<div class="form-group  col-md-2">
						<label>Date To</label><br />
						<input type="text" autocomplete="off" class="dates_nodefault form-control" value="<?php echo Request::get('dateto') ; ?>" name="dateto">
					</div>

				<div class="form-group  col-md-2">
				    <label>Month</label><br />
				    <select id="" name="month" class="form-control">
					<option value="">Select</option>

					<?php  foreach( $arrMonth as  $key => $item ) { 

					?>
					<option value="<?php echo $key; ?>" <?php if(Request::get('month') == $key) { echo 'selected'; } ?> ><?php echo $item; ?></option>
					<?php 
					}
					?>
    			    </select>
				</div>

				
				<div class="form-group col-md-2">
				    <label for="">Year</label>
				    <input class="form-control" name="years" type="text" value="<?php echo Request::get('years') ; ?>"/></div>
				    <div class="form-group  col-md-3">
					<label for="">&nbsp;</label><br />
					<button type="submit" class="btn btn-primary">Search</button>
				    </div>
			    </div>

			</form>
			<h2>Details List</h2>
						<p>
							Total Sales: <strong><?php echo number_format($totalamount,2); ?></strong>,
							Product Sales: <strong><?php echo number_format($totalProductSale,2); ?></strong>
						</p>
						<p>
							Grand Total: <strong><?php echo number_format($totalamount + $totalProductSale,2); ?></strong>
						</p>
			<!-- Below table is when month and year selected -->
			<canvas id="myChart"></canvas>



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
