@extends('layouts.app')
<?php
        $dobVal = '';
        $hiringDateVale = '';
    if( $staffmember->dob != '' && intval($staffmember->dob) > 0 )
        {

            $dobVal = date( "d/m/Y", strtotime($staffmember->dob) );
        }

if( $staffmember->hiringdate != '' && intval($staffmember->hiringdate) > 0 )
{

    $hiringDateVale = date( "d/m/Y", strtotime($staffmember->hiringdate) );
}
?>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Staff Member</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('method' => 'PUT', 'route' => ['staff.update', $staffmember->id ])) }}
                            {{ csrf_field() }}
                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            {{ Form::label('firstname', 'First Name') }} *
                                            {{ Form::text('firstname', $staffmember->firstname , array('class' => 'form-control') ) }}
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('lastname', 'Last Name') }} *
                                            {{ Form::text('lastname', $staffmember->lastname  , array('class' => 'form-control') ) }}
                                        </div>

<div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('dob', 'Date of Birth') }}  (dd/mm/yyyy)
                                                {{ Form::text('dob', $dobVal, array('class' => 'form-control', 'placeholder' => 'dd/mm/yyyy') ) }}
                                            </div>
</div>


<div class="form-group">
    {{ Form::label('gender', 'Gender') }} *<br />
    {{ Form::radio('gender', '0', true,   array('class' => 'form-radio') ) }} Male &nbsp;&nbsp;
    {{ Form::radio('gender', '1', '', array('class' => 'form-radio') ) }} Female
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                {{ Form::label('address', 'Address') }}
                                                {{ Form::textarea('address', $staffmember->address, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('email', 'Email') }}
                                                {{ Form::text('email', $staffmember->email, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('mobile', 'Mobile') }} *
                                                {{ Form::text('mobile', $staffmember->mobile, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('phone', 'Phone') }}
                                                {{ Form::text('phone', $staffmember->phone, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('designation', 'Designation') }} *
                                                {{ Form::text('designation', $staffmember->designation, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="form-group col-md-6">

                                                {{ Form::label('hiringdate', 'Hiring Date') }} (dd/mm/yyyy)
                                                {{ Form::text('hiringdate', $hiringDateVale, array('class' => 'form-control' ) ) }}
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="form-group col-md-8">

                                                {{ Form::label('hiringdate', 'Id Proof') }}<br />
                                                <div class="col-md-6">
                                                {{ Form::text('idprooftype', $staffmember->idprooftype, array('class' => 'form-control', 'placeholder' => 'Type' ) ) }}
                                                </div>
                                                <div class="col-md-6">
                                                    {{ Form::text('idproofvalue', $staffmember->idproofvalue, array('class' => 'form-control','placeholder' => 'Number' ) ) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-8">

                                                {{ Form::label('status', 'Status') }} *<br />
                                                <label>
                                                {{ Form::radio('activestatus', '1', ($staffmember->activestatus == '1' ? true : false ) ,   array('class' => 'form-radio') ) }} Active &nbsp;&nbsp;</label>
                                                <label>
                                                {{ Form::radio('activestatus', '0', ($staffmember->activestatus != '1' ? true : false ), array('class' => 'form-radio') ) }} In-active
                                                </label>

                                            </div>
                                        </div>


                                    </div>
				    <div class="col-lg-6">
					<div style="border: 1px solid #ccc; padding: 15px;">
					    <h4>Update Salary Info</h4><br />
					    <div class="row" >
						<?php
						$salary = $staffmember->getLatestSalary();
						$salaryAmount = 0;
						$salaryCommission = 0;
						$salaryDate =  '';
						if( $salary )
						{
						$salaryAmount = $salary->salary;
						$salaryCommission = $salary->commission;
						$salaryDate =  date("d/m/Y", strtotime( $salary->fromdate));
						}
						//print_r($salary);
						
						?>
						<div class="form-group col-md-4">
						    {{ Form::label('salary', 'Salary*') }}<br />
						    {{ Form::text('salary', $salaryAmount  , array('class' => 'form-control','placeholder' => 'Salary' ) ) }}
						</div>
						
						<div class="form-group col-md-6">
						    {{ Form::label('commission', 'Commission Percent*') }}<br />
						    {{ Form::text('commission', $salaryCommission  , array('class' => 'form-control','placeholder' => 'Commission' ) ) }}
						</div>

						<div class="form-group col-md-6">
						    {{ Form::label('fromdate', 'From Date   (dd/mm/yyyy)*') }}<br />
						    {{ Form::text('fromdate', $salaryDate , array('class' => 'form-control dates','placeholder' => 'From Date' ) ) }}
						</div>
					    </div>
					    					    <div class="row">
						 <div class="col-md-6">
						 <button type="submit" name="update_salary" value="1" class="btn btn-primary">Update Salary Info</button>
						 </div>
						 </div>
					    
					</div>
					</div>

                                </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}

			    <div class="row">
				<div class="col-md-12">
				    <h3>Salary Increasing History</h3>
				    <table class="table table-striped">
					<thead>
					    <tr>
						<th>Date</th>
						<th>Salary</th>
						<th>Commission</th>
                            <th>Action</th>

					    </tr>
					</thead>
					<tbody>
					    <?php
					    $salaries = $staffmember->salaryIncrementInfo;
					    foreach( $salaries as $salary ) { 
					    ?>
					    <tr class="salary_data_rows">
					    <td class="input_dates"><?php echo date("d/m/Y", strtotime($salary->fromdate)); ?></td>
					    <td class="input_salary"><?php echo $salary->salary; ?></td>
					    <td class="input_commission"><?php echo $salary->commission; ?>%</td>
                            <td>
                                <button type="button" class="btn btn-primary salary_edit_btn">Edit</button>


                            </td>
					    </tr>
                        <tr class="salary_editable_rows editable_rows">
                            <td class="input_dates">
                                <input class="form-control formdates dates_nodefault" type="text" name="fromdate_1" value="<?php echo date("d/m/Y", strtotime($salary->fromdate)); ?>" />
                                <input type="hidden" name="" class="salaryId" value="<?php echo $salary->id; ?>">
                            </td>
                            <td class="input_salary">
                                <input class="form-control salaries" type="text" name="salary_1" value="<?php echo $salary->salary; ?>" />
                            </td>
                            <td class="input_commission">
                                <input style="width: 100px; float: left;" type="text" class="form-control commisions" name="commisions" value="<?php echo $salary->commission; ?>"> &nbsp;%</td>
                            <td>

                                <button type="button" class="btn btn-primary salary_save_btn">Update</button>

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
@endsection
