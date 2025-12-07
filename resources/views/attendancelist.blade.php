@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading">List Attendance </div>

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

                                    {{ Form::select('attendance_month', $months , Request::exists( "attendance_month") ?  Request::get( "attendance_month") :  date("m")  , array('class' => 'form-control', 'id' => 'attendance_month') ) }}
                                </div>
                                <div class="col-md-3">
                                    <label for="">Year</label>
                                    {{ Form::text('attendance_year', Request::exists( "attendance_year") ? Request::get( "attendance_year") : date("Y") , array('class' => 'form-control') ) }}
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
                                    <th>#</th>
                                    <th>Staff Name</th>
                                    <th>Phone</th>
                                    <th>Month</th>
                                    <th>Year</th>
                                    <th>Date</th>
                                    <th>Add date</th>
                                    <th>Attendance Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                <?php
                                $x = 0;
                                $totalPresents = 0;
                                foreach ($attendanceList as $item )
                                {
                                    $x++;

                                        if( $item->attandence == 'Present')
                                        {
                                            $totalPresents += 1;
                                        }
                                        else  if( $item->attandence == 'Half Day')
                                        {
                                            $totalPresents += 0.5;
                                        }
                                        else  if( $item->attandence == 'Holiday')
                                        {
                                            $totalPresents += 1;
                                        }
                                ?>

                                <tr>
                                    <td><?php echo $x; ?></td>
                                    <td><?php echo $item->staff->getName(); ?></td>
                                    <td><?php echo $item->staff->mobile; ?></td>
                                    <td><?php echo $months[date("m", strtotime($item->attandance_date))]; ?></td>
                                    <td><?php echo date("Y", strtotime($item->attandance_date)); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime( $item->attandance_date)); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime( $item->created_at)); ?></td>
                                    <td><?php echo $item->attandence; ?> </td>

                                    <td>



                                        {{form::open(array( 'method' => 'PUT', 'route' => ['attendance.update', $item->id]))}}
                                        {{csrf_field()}}

                                        <button style="background-color: #5cb85c !important; color: #fff;" type="submit" name="present" value="present" class="btn">Present</button>

                                        <button style="background-color: #d9534f !important; color: #fff;" type="submit" name="absent" value="absent" class="btn">Absent</button>
                                        <button style="background-color: #ffc107 !important; color: #fff;" type="submit" name="halfday" value="halfday" class="btn">Half Day</button>
                                        <button style="background-color: #007bff !important; color: #fff;" type="submit" name="holiday" value="holiday" class="btn">Holiday</button>
                                        {{form::close()}}
<br />
                                        {{form::open(array( 'method' => 'DELETE', 'route' => ['attendance.destroy', $item->id]))}}
                                        {{csrf_field()}}

                                        <button type="submit" class="btn btn-danger deletebtn">Delete</button>
                                        {{form::close()}}

                                    </td>
                                </tr>
                                <?php
                                }


                                ?>

<tr style="background-color: #000; color: #fff;">
    <td colspan="7" style="text-align: right;">Total Presents</td>
    <td colspan="2"><?php echo $totalPresents; ?></td>
</tr>


                                </tbody>
                            </table>

                        </div>

                        {{ $attendanceList->links('vendor.pagination.custom') }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
