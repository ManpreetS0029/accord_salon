@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Staff Details</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
			<form method="get" action="" >
			    <div class="row">
				<div class="form-group  col-md-2">
				    <label>Month</label><br />
				    <select id="" name="month" class="form-control">
					<option value="">Select</option>
					<?php
					foreach( $months as $k => $item ) {
					$month = Request::get('month');
					if( $month  == '' )
					{
					     $month = date("m");
					}
					?>
					<option value="<?php echo $k; ?>"  <?php if( $month == $k ) { echo 'selected'; } ?> ><?php echo $item; ?></option>
					<?php 
					
					}
					?>
				    </select>
				</div>

				
				<div class="form-group col-md-2">
				    <label for="">Year</label>
				    <input class="form-control" name="years" type="text" value="<?php echo  Request::get('years')  == '' ? date("Y") : Request::get('years') ; ?>"/></div>
				    <div class="form-group  col-md-3">
					<label for="">&nbsp;</label><br />
					<button type="submit" class="btn btn-primary">Search</button>
				    </div>
			    </div>

			</form>



			<h2>Attendance List</h2>
			<!-- Below table is when month and year selected -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
					<th>Attendance Date</th>
					<th>Attendance</th>
				        <th>Add Date</th>
    				        <th>Update Date</th>
					<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>


				    
					<tr>
					    <td></td>
					    <td></td>
					    <td></td>
<td></td>
<td></td>
<td></td>
					    <td>


					    </td>
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
