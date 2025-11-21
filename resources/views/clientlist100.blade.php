@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">List Clients</div>

                <div class="panel-body">
                    @include('common.errors')
                    @include('common.success')


			<div class="row">
			    <div class="col-md-3">
				<label for="">Client Name/ Phone</label>
				{{ Form::text('searchtext', '', ['class' => 'form-control', 'id' => 'livesearch'] )}}
			    </div>
			    <div class="col-md-4">
				<label for="">&nbsp;</label><br />
                                

			    </div>
			</div>

		    <br />
                    <div class="table-responsive">
                        <table id="table1" class="table table-striped">
                            <thead>
                            <tr>
                                    <th>#</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Phone</th>

                            </tr>
                            </thead>
    
                            <tbody>
                            @foreach ( $clients as $client )
                               <?php
                               $client->dob = date("d/m/Y", strtotime($client->dob));
                                $client->clientname = $client->clientname == null ? "" : $client->clientname;
                                $client->email = $client->email == null ? "" : $client->email;
                                $client->address = $client->address == null ? "" : $client->address;
                                $client->city = $client->city == null ? "" : $client->city;
                                $client->state = $client->state == null ? "" : $client->state;
$client->phone = $client->phone == null ? "" : trim($client->phone);
$client->phone2 = $client->phone2 == null ? "" : trim($client->phone2);
                               $client->zipcode = $client->zipcode == null ? "" : $client->zipcode;
                               ?>
                            <tr>
                                <td>{{ $client->clientname }}</td>
                                <td>{{ $client->dob }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->address }}<br />
                                   @if ($client->city) {{ $client->city }}, @endif {{ $client->state }} {{ $client->zipcode }}
                                </td>
                                <td>{{ $client->phone }} @if ($client->phone2 != '') , {{ $client->phone2 }} @endif </td>


                            </tr>

                            @endforeach

                            </tbody>
                        </table>

                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<!--   -->

<?php

$jsonclients = json_encode($clients);

//print_r($jsonclients);
?>

<script>
 //  console.log(<?php echo $jsonclients; ?>);

    var clientsList = <?php echo $jsonclients; ?>;
    var listData = "";

    if ( typeof clientsList['data'] != undefined ){

        listData = clientsList['data'];

        var template = "<tr><td>{col0}</td><td>{col1}</td><td>{col2}</td><td>{col3}</td><td>{col4}</td><td>{col5}</td></tr>";


         function prepareTable(myListData)
         {
             var htmlData = "";
             var zx = 0;
             for ( var x = 0; x < myListData.length; x++  )
             {
                 //	dob, email, address, city,
                 var row = template;
                 row = row.replace("{col0}", ++zx  );

                 row = row.replace("{col1}",  myListData[x].clientname  );
                 row = row.replace("{col2}", myListData[x].dob   );
                 row = row.replace("{col3}",  myListData[x].email  );
                 var addresses = "";
                 addresses = myListData[x].address;
                 if( addresses != "" )
                 {
                     addresses += "<br />";
                 }
                 addresses += myListData[x].city ;
                 if( addresses != "" )
                 {
                     addresses += ", ";
                 }
                 addresses += myListData[x].state;
                 if( addresses != "" )
                 {
                     addresses += "  ";
                 }
                 
                 addresses += myListData[x].zipcode;
                 row = row.replace("{col4}", addresses.replace(/(\s)*(,)+(\s)*$/, "") );
                 var phones = "";
                 phones = myListData[x].phone ;
                 if(phones != "")
                 {
                     phones += ", "   ;
                 }
                 phones +=  myListData[x].phone2 ;
                 row = row.replace("{col5}",  phones.replace(/(\s)*(,)+(\s)*$/, "")   );
                 htmlData += row;
             }

             $("#table1 tbody").html(htmlData);
         }


         function filterData(filterText, myListData )
         {
            if( filterText == "" )
            {
                return myListData;
            }
            
            var z = 0;
             var newList = Array();
            filterText = filterText.toLowerCase();
             for ( var x = 0; x < myListData.length; x++  )
             {
                 
                 var clientName = myListData[x]["clientname"].toLowerCase();

                 var email =  myListData[x].email.toLowerCase();
                 var phone =  myListData[x].phone.toLowerCase();
                 var phone2 = myListData[x].phone2.toLowerCase();
                 //var clientName = myListData[x].clientname == null ? "" : myListData[x].clientname;

                 //	dob, email, address, city,
                 if( clientName.indexOf(filterText) >= 0 || email.indexOf(filterText) >= 0  || phone.indexOf(filterText) >= 0  || phone2.indexOf(filterText) >= 0 )
                 {
                     newList[z] = myListData[x];
                     z++;
                 }

                 

             }

             return newList;
         }

        $('#livesearch').on('keyup', function () {

            var myData = filterData($(this).val(), listData );
            //console.log(myData);
            prepareTable(myData);

        });

        prepareTable(listData);
    }
//  console.log(test['data'][0].clientname);
    //console.log(test['data'].length);



   /* $.each(test, function (clientname, dob, email, address, city, state, zipcode, phone,) {
        alert(data.clientname);
    }) */




</script>








<!-- -->

@endsection
