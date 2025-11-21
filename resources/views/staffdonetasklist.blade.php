@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">

                <div class="panel-heading">List Sales Done BY Staff Member</div>


                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')


    <h3>{{ $staff->firstname.' '.$staff->lastname.' | '.$staff->mobile  }}</h3>
    <br />
                    <?php
                    $queryStr = $_SERVER['QUERY_STRING'];
                    $link = '?exportdata=1';
                    if( $queryStr != '' )
                    {
                        parse_str( $queryStr, $qrArr );
                        unset($qrArr['exportdata']);
                        $qrArr['exportdata'] = "1";
                        $link =  '?'.http_build_query($qrArr);
                    }
                    else
                    {

                    }
                    ?>
                    <div class="row">
                        <div style="float: right; margin-bottom: 10px;">
                            <a style="padding: 10px; margin-bottom: 10px;" href="<?php echo $link; ?>">Download Excel</a>
                        </div>
                    </div>
                    <br />
<form action="" method="get" accept-charset="utf-8">
                            <div class="row">
                            
                            
                            
                            <div class="form-group col-md-2">
                                <label>Date From</label>
                                {{ Form::text('datefrom', '', ['class' => 'form-control datepickers', 'id'=>'datefrom'] )}}
                            </div>
                            
                            <div class="form-group col-md-2">
                                <label>Date To</label>
                                {{ Form::text('dateto', '', ['class' => 'form-control datepickers', 'id' => 'dateto'] )}}
                            </div>

                            <div class="form-group col-md-2">
                                <label for="">&nbsp;</label><br />
                                
                            {{ Form::submit('Search',  array('class' => 'btn btn-primary', 'name' => 'search'))  }}
                            </div>
</div>    

</form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
    <th>Sale ID</th>

    <th>Item Name</th>
                                <th>Sale Date</th>
   
                               <th>Total Price</th>
                               <th>Staff Done Amount </th> 
    
    
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($staffsale as $sale)
                            <tr style="color: <?php if( $sale['itemtype'] == 'product' ) { echo '#f00;'; } else { echo '#333'; } ?>" >

    <td>ACC{{ $sale['saleid'] }}</td>
<td>{{ $sale['itemtitle'] }}</td>
<td>{{ date("d/m/Y", strtotime($sale['date'])) }}</td>
<td>{{ number_format($sale['totalamount'],2) }}</td>
<td>{{ number_format($sale['userdoneamount'],2) }}</td>

    </tr>

                            @endforeach



                                <tr><th colspan="4" style="text-align: right;"> Total Amount</th><th>{{ number_format($totalamount, 2) }}</th></tr>
                                
                                <tr><th colspan="4" style="text-align: right;"> Incentive Amount</th><th>{{ number_format($incentive, 2) }}</th></tr>

                            <tr><th colspan="4" style="text-align: right;"> Total Product Sale Amount</th><th>{{ number_format($totalproductsale, 2) }}</th></tr>

                            <tr><th colspan="4" style="text-align: right;"> Total Services Amount</th><th>{{ number_format($totalservicessale, 2) }}</th></tr>



                            </tbody>

                        </table>

                    </div>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
