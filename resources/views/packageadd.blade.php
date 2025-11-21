@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Client</div>

                    <div class="panel-body">

                        @include('common.errors')
                        @include('common.success')
                        <div class="panel">
                            {{ Form::open(array('route' => ('package.store'))) }}
                            {{ csrf_field() }}

                            <div class="panel-body container-fluid">

                                <div class="row row-lg">

                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            {{ Form::label('title', 'Package Name') }} *
                                            {{ Form::text('title', '', array('class' => 'form-control') ) }}
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                {{ Form::label('price', 'Price') }} *
                                                {{ Form::text('price', '', array('class' => 'form-control') ) }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {{ Form::label('description', 'Description') }}
                                            {{ Form::textarea('description', '', array('class' => 'form-control') ) }}
                                        </div>



                                    </div>
                                </div>

                                <div class="row" style="border: 1px solid #ccc;">
                                    <div class="col-md-12">
                                        <h3>+ Add Services</h3>
                                        <br />




                                        <ul class="nav nav-tabs">
                                            <?php
                                            $x = 0;


                                            foreach ( $categories as $cat  ) { ?>

                                                <li class="<?php echo $x == 0 ? 'active' : ''; ?> "><a data-toggle="tab" href="#tab_<?php echo $cat->id;?>"><?php echo $cat->name; ?></a></li>
                                                <?php
                                                /*foreach( $cat->services as $R )
                                                {
                                                    echo $R->name;

                                                }*/
                                                $x++;
                                            }

                                            ?>


                                        </ul>

                                        <div class="tab-content">

                                            <?php
                                                $x = 0;
                                            $jsonDataArr = array();
                                            $jsonData = '';
                                            foreach ( $categories as $cat  ) { ?>
                                                <div id="tab_<?php echo $cat->id;?>" class="tab-pane fade in <?php echo $x == 0 ? 'active' : ''; ?>">
                                                    <!--<h3><?php echo $cat->name; ?></h3>-->



                                            <?php
                                            foreach( $cat->services as $R )
                                            {
                                                echo '<button type="button" id="'.$R->id.'" class="addbtns btn btn-primary"> + '. $R->name." (".$R->price.")".'</button>';

                                                $jsonDataArr[] = array( "id" => $R->id, "name" => $R->name, "price" =>  $R->price );
                                                $jsonData .= '{ "id" : "'.$R->id.'", "name" : "'.addslashes($R->name).'", "price" : "'.$R->price.'" },';
                                            }

                                            $x++;
                                            ?>
                                                </div>
                                            <?php
                                            }
                                                $jsonData =  rtrim($jsonData,",");

?>

                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12">
<br />
                                        <h3>Services Added</h3>
                                        <br />


<?php
                                      //print_r(  Request::old('hdnaddedservice') );
                                      $arrVals = Request::old('hdnaddedservice');
                                        ?>

                                        <div id="servicebox">

                                            <?php if( is_array($arrVals) && count($arrVals ) > 0 ) {
$arr =  $arrVals ; //Request::old('hdnaddedservice');
                                               foreach( (array)$arr as $serviceid )
                                                   {
                                                       foreach ( $jsonDataArr as $data )
                                                           {

                                                               if( $data['id'] == $serviceid )
                                                                   {
                                                                       ?>

                                                <div class='addedservicebtn'><?php echo $data["name"]." ( ".$data['price']." ) "; ?><div data-ids='<?php echo $data['id']; ?>' class='close'>X</div><input type='hidden' name='hdnaddedservice[]' value='<?php echo $data['id']; ?>' /></div>

                                            <?php
                                                                   }
                                                           }

                                                       ?>

                                            <?php
                                                   }

                                            }?>


                                        </div>
                                    </div>

                                </div>

                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <button type="reset" class="btn btn-primary">Reset</button>

                                        {{ Form::submit('Save',  array('class' => 'btn btn-primary'))  }}
                                    </div>
                                </div>

                            </div> {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    var jsonDataServices = '[<?php echo $jsonData; ?>]';

    var jsonObjForServices = JSON.parse(jsonDataServices);
   // alert(jsonObjForServices.length);
</script>
