@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Staff Members</div>

                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')
                    
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Search by Staff Name</label>
                                {{ Form::text('searchtext', request()->get('searchtext'), ['class' => 'form-control'] )}}
                            </div>

                            <div class="col-md-4">
                                <label for="">&nbsp;</label><br />
                                {{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
                            </div>
                        </div>
                    </form>
                    <br />
{{Form::open( array('method' => 'post', 'route' => 'staff.updateattendance', 'id' => 'group_attendance_form'  ))}}
                    {{csrf_field()}}
                    <input type="hidden" id="group_save_attendance" name="group_save_attendance" value="">
<div class="row">
   <div class="col-md-2">Action for selected:</div>
    <div class="col-md-3"><input class="form-control dates" type="text" name="attendance_date" value="<?php echo date("d/m/Y"); ?>" /> </div>
    <input type="hidden" name="select_groups" value="" >
    <div class="col-md-6"><button style="background-color: #5cb85c !important; color: #fff;" type="submit" value="present" name="present" class="btn green">Present</button>

        <button style="background-color: #d9534f !important; color: #fff;" type="submit" name="absent" value="absent" class="btn green">Absent</button>

        <button style="background-color: #ffc107 !important; color: #fff;" type="submit" name="halfday" value="halfday" class="btn green">Half Day</button>

        <button style="background-color:#007bff !important; color: #fff;" type="submit" name="holiday" value="holiday" class="btn green">Holiday</button>
    </div>
</div>
                    {{Form::close()}}
                    <br ><br >

                    <div class="table-responsive">

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" name="selectall" id="selectall">
                                </th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>

                                <th>Address</th>
                                <th>Mobile/Phone</th>
                                <th>Email</th>
                                <th>Designation</th>
                                <th>Hiring Date</th>
                                <th>ID Proof</th>
<th>Attendance</th>
    
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
				@foreach ($staffmembers as $item)
				    <?php $attendance = $item->getAttendanceForDate(date("Y-m-d")); ?>
				    <tr <?php if(!$attendance) { echo 'class="alert alert-danger"'; } ?> >

                        <td>
                            <input type="checkbox" name="selects[]" class="select_checks" value="<?php echo $item->id; ?>" >
                        </td>
                                <td>{{ $item->firstname }} {{ $item->lastname }}</td>

                                <td>
                                    @if ( $item->dob != '' && intval($item->dob) != 0 )
                                    {{ date("d/m/Y", strtotime($item->dob)) }}
                                    @endif

                                </td>

                                <td> @if ( $item->gender == '0')
                                        Male
                                         @else
                                        Female
                                         @endif
                                </td>

                                <td>{{ $item->address }}</td>
                                <td>{{ $item->mobile }} / {{ $item->phone }}</td>

                                <td>{{ $item->email }}</td>
                                <td>{{ $item->designation }}</td>

                                <td>
                                    @if ( $item->hiringdate != '' && intval($item->hiringdate) != 0 )
                                        {{ date("d/m/Y", strtotime($item->hiringdate)) }}
                                    @endif

                                </td>
                                <td>{{ $item->idprooftype }}:  {{ $item->idproofvalue }}</td>

				<td>
				    <?php  
				    if($attendance)
				    {
				    echo $attendance->attandence;
				    }

				    ?><br />
				    <a href="?id=<?php echo $item->id; ?>&act=attn&atype=Present" title="Present" class="attn_btns green">P</a> 
				    <a href="?id=<?php echo $item->id; ?>&act=attn&atype=Half Day" title="Half Day" class="attn_btns orange">HF</a>
				    <a href="?id=<?php echo $item->id; ?>&act=attn&atype=Holiday" title="Holiday" class="attn_btns blue">H</a>
				    <a href="?id=<?php echo $item->id; ?>&act=attn&atype=Absent" title="Absent" class="attn_btns red">A</a>
				    
				</td>
                                <td>
                                    <a class="btn btn-success"  href="{{ route('staff.edit', $item->id) }}">Edit</a>
<br /><br />


                                {{ Form::open([  'class' => 'myeditforms', 'method' => 'DELETE', 'route' => ['staff.destroy', $item->id] ]) }}
                                    {{ Form::hidden('id', $item->id) }}
                                    {{ Form::submit('Delete', ['class' => 'btn btn-danger deletebtn']) }}
                                    {{ Form::close() }}

<br /><br />

                                    <a class="btn btn-success"  href="{{ route('staff.saledetails', $item->id) }}">View Sale</a>
                                    </td>
                            </tr>

                            @endforeach




                            </tbody>
                        </table>

                    </div>

                    {{ $staffmembers->links('vendor.pagination.custom') }}


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
