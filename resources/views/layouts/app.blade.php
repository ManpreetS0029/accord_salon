<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Khurl Salon Management') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet">
    
    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body style="padding-bottom: 60px;">
      <div id="app">
      <?php if( isset($_GET['iframe']) && $_GET['iframe'] == '1' ) {
          
      } else {  ?> 
            <nav class="navbar navbar-default navbar-static-top" style="margin-bottom: 0;">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Khurl Salon Management') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Services<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{  route('services.index') }}">List Services</a>
                                </li>
                                <li>
                                    <a href="{{  route('services.create') }}">+ Add Service</a>
                                </li>

                                <li>
                                    <a href="{{  route('category.index') }}">List Category</a>
                                </li>

                                <li>
                                    <a href="{{  route('category.create') }}">+ Add Category</a>
                                </li>

                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                 Clients<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{  route('clients.create') }}">+ Add Client</a>
                                </li>
                                <li>
                                    <a href="{{  route('clients.index') }}">List Clients</a>
                                </li>
                                <li>
                                    <a href="{{  route('clients.listfirsthundred') }}">First 100 Clients</a>
                                </li>
                                <li>
                                    <a href="{{  route('clientspackage.create') }}">Add Package</a>
                                </li>
                                <li>
                                    <a href="{{  route('clientspackage.index') }}">List Package</a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Packages<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{  route('package.create') }}">+ Add Package</a>
                                </li>
                                <li>
                                    <a href="{{  route('package.index') }}">List Packages</a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Sales<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{  route('sale.create') }}">+ Add Sale</a>
                                </li>
                                <li>
                                    <a href="{{  route('sale.index') }}">List Sales</a>
                                </li>
                            </ul>
                        </li>

      
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Purchase<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{  route('purchase.create') }}">+ Add Purchase</a>
                                </li>
                                <li>
                                    <a href="{{  route('purchase.index') }}">List Purchases</a>
                                </li>
                                      <li>
                                    <a href="{{  route('company.create') }}">+ Add Company</a>
                                </li>
                                <li>
                                    <a href="{{  route('company.index') }}">List Companies</a>
                                </li>
                            </ul>
                        </li>


                          <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                          Products<span class="caret"></span>
                          </a>
                    
                          <ul class="dropdown-menu" role="menu">
                          <li>
                          <a href="{{  route('product.create') }}">+ Add Product</a>
                          </li>
                          <li>
                          <a href="{{  route('product.index') }}">List Products</a>
                          </li>
                          </ul>
                          </li>


                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Others<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">



                                        <li><a href="{{  route('attendance.create') }}">+  Add Staff Attendance</a></li>
                                        <li><a href="{{  route('attendance.index') }}"> Attendance List</a></li>

                                        <li><a href="{{  route('staff.salegraph') }}">Staff Sales Report</a></li>
                                        <li><a href="{{  route('salary.create') }}">+ Add Salary</a></li>

                                        <li><a href="{{  route('salary.index') }}">List Salaries Paid</a></li>

                                        <li>
                                            <a href="{{  route('staff.create') }}">+ Add Staff</a>
                                        </li>
                                        <li>
                                            <a href="{{  route('staff.index') }}">List Staff</a>
                                        </li>


                                        <li>
                                            <a href="{{  route('productissue.create') }}">+ Issue Product</a>
                                        </li>
                                        <li>
                                            <a href="{{  route('productissue.index') }}">List Issue Products</a>
                                        </li>




                                        <li>
                                            <a href="{{  route('expensemaster.create') }}">+ Add Expense Master</a>
                                        </li>
                                        <li>
                                            <a href="{{  route('expensemaster.index') }}">List Expense Master</a>
                                        </li>

                                        </li>
                                        <li>
                                            <a href="{{  route('expense.create') }}">+ Add Expenses</a>
                                        </li>
                                        <li>
                                            <a href="{{  route('expense.index') }}">List Expenses</a>
                                        </li>



                                        <li>
                                            <a href="{{  route('revenuereport.index') }}"> Daily Report</a>
                                        </li>

      
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Expenses<span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="{{  route('expensemaster.create') }}">+ Add Expense Master</a>
                        </li>
                        <li>
                            <a href="{{  route('expensemaster.index') }}">List Expense Master</a>
                        </li>

                        </li>
                        <li>
                            <a href="{{  route('expense.create') }}">+ Add Expenses</a>
                        </li>
                        <li>
                            <a href="{{  route('expense.index') }}">List Expenses</a>
                        </li>


</ul>
                        </li>

                            </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
			<!--  @if (Auth::guest())
                             <li><a href="{{ route('login') }}">Login</a></li>
                             <li><a href="{{ route('register') }}">Register</a></li>
                             @else -->

                             <li class="dropdown">
                             <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                             {{ Auth::user()->name }} <span class="caret"></span>
                             </a>

                             <ul class="dropdown-menu" role="menu">

                           <li><a href="{{route('sendmessages.index')}}">Send SMS</a></li>
                             <li>
                             <a href="{{ route('logout') }}"
                             onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                             Logout
                             </a>

                             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                             {{ csrf_field() }}
                             </form>
                             </li>


                             </ul>
                             </li>
			     <!-- @endif -->
			     </ul>
			     </div>
			     </div>
			     </nav>
          
      <?php } ?>

          <?php

          $bal = trim(Request::get("openingBalance"));
          if(  $bal == 'not' )  {

              ?>
           <div class="container-fluid bg-danger" style="padding:10px 50px; margin:0 0 20px 0; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">

          <?php
          echo "Opening Balance needs to be add for today. <a href='".route('closingday.create')."'>Click Here</a>";
          ?>

              </div>
          <?php } ?>
			     @yield('content')
                             
                             <?php if( isset($_GET['iframe'] ) &&  $_GET['iframe'] == 'iframe' ) { ?>
			     <div class="container-fluid" style="background-color: #efefef; padding:10px 0; position:fixed; bottom:0; left:0; width:100%; text-align: center;">
			     &copy; Copyright <?php echo date("Y").' - '.(date("Y") + 1); ?> All rights reserved. <a href="http://khurlitsolutions.com" target="_blank">Khurl IT Solutions</a>
			     </div>
                             <?php } ?>
			     </div>

      <!-- Message Box on Top -->
      <!--<div class="alert alert-success messagebar-top">
          <a href="#" class="close" data-dismiss="alert">&times;</a>
          <strong>Warning!</strong> There was a problem with your network connection.
      </div> -->

			     <!-- Scripts -->

			<script>
                         var _token = "{{ csrf_token() }}";
                         var urls = "<?php echo URL::to('/');  ?>";
                        </script>
    <script src="{{ asset('js/jquery-1.12.3.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>

    <script src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
    <script src="{{ asset('select2/select2.min.js') }}"></script>
                                                        <script src="{{ asset('js/common.js?ver=1.1') }}"></script>

                                  
                                  
                            			<?php

                                        if( isset($view_name)) {
                                                      if( $view_name == 'saleadd' || $view_name == 'saleedit' || $view_name == 'salelist' || $view_name == 'saleaddpayment' || $view_name == 'saleeditpayment' ||  $view_name == 'packageadd' || $view_name == 'packageedit' ) {
?>
                                  <script src="{{ asset('js/sales.js?ver=2.5') }}"></script>
      <?php
                                                      }

            if(  $view_name == 'sendmessages' )
                { ?>
      <script src="{{ asset('js/sendmessages.js?ver=2.5') }}"></script>
      <?php }


if( $view_name == 'purchaseadd' || $view_name == 'purchaseedit' || $view_name == 'purchaseaddpayment' || $view_name == 'purchaseeditpayment' ) {
    ?>
                                  <script src="{{ asset('js/purchase.js?ver=1.0') }}"></script>                                                    
    <?php
}
}
                                         ?>      

						      <script>
						       
					       $(document).ready(function(){


                               if( $(".datepickers").length > 0 )
                               {

                                   $(".datepickers").datepicker({ 'format' : 'dd/mm/yyyy' });
                                   $("datepickers").inputmask("99/99/9999");

                               }


                               if( $("#datefrom").length > 0 )
                               {

                                   $("#datefrom").datepicker({ 'format' : 'dd/mm/yyyy' });
                                   $("#datefrom").inputmask("99/99/9999");

                               }
                               if( $("#dateto").length > 0 )
                               {

                                   $("#dateto").datepicker({ 'format' : 'dd/mm/yyyy' });
                                   $("#dateto").inputmask("99/99/9999");

                               }


                               
							   <?php if( Request::old('state') != '' ) {
						       ?>
							       fetchCity( "<?php echo Request::old('state'); ?>", "<?php echo Request::old('cityid'); ?>", 'cityCallBack' );
							       <?php 
							   } ?>

														   
						       });
						      </script>

                       <?php

// for graph
if( $view_name == 'staffsalegraph'  ) {
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<script>

    

      
 <?php if( isset($staff) ) {



     $labelNames = "";
     $salesVal = "";
     $productsVal = "";
     $ids = "";
     foreach( $staff as $mem ) { 

         $labelNames .= '"'.$mem->firstname." ".$mem->lastname.'",';
         $salesVal .= round($mem->totalMemberSale).",";
         $productsVal .= round($mem->totalMemberProductSale).",";
         $ids .= $mem->id.",";
     }



          ?>
 	
var staffIdsArr = [<?php echo rtrim($ids,","); ?>];
    var ctx = document.getElementById('myChart').getContext('2d');
var myRadarChart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
    labels: [<?php echo rtrim($labelNames,","); ?>],
        datasets: [
            {
            label: "Services By Staff",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
               data: [<?php echo rtrim($salesVal,","); ?>],

               
        },
            {
                label: "Products Sale By Staff",
                backgroundColor: 'rgb(18, 132, 21)',
                borderColor: 'rgb(18, 132, 21)',
                data: [<?php echo rtrim($productsVal,","); ?>],


            }

        ]
    },

    // Configuration options go here
    options: {
        onClick: graphClickEvent,
        title: {
            display: true,
            text: 'Reporting'
        },
        tooltips: {
            mode: 'index',
            intersect: false
        },
        responsive: true,
        scales: {
            xAxes: [{
                stacked: true,
            }],
            yAxes: [{
                stacked: true
            }]
        }
    }
});
var urls = "<?php echo URL::to('/staff'); ?>";
 function graphClickEvent(event, array){
      console.log(event);
     if( array[0] )
     {
         console.log(array[0]["_index"]);
         if( staffIdsArr[array[0]["_index"]]  )
         {
             console.log(staffIdsArr[array[0]["_index"]] );

             window.open(urls+"/"+staffIdsArr[array[0]["_index"]]+"/saledetails", '_blank');

         }

     }

 }


    <?php } ?>

</script>
<?php } ?>

<?php
      if( $view_name == 'staffsalaryadd' || $view_name == 'staffsalaryedit'  ) {
      ?>
      <script src="{{ asset('js/salary.js?ver=1.0') }}"></script>
      <?php
      }
      ?>

      <?php
      if( $view_name == 'clientpackageadd' || $view_name == 'clientspackage' || $view_name == 'clientpackageedit'  ) {
      ?>
      <script src="{{ asset('js/clientpackageadd.js?ver=1.0') }}"></script>
      <?php
      }
      ?>
      
       <?php
      if( $view_name == 'allpaidpayment'   ) {
      ?>
      <script src="{{ asset('js/addpayment.js?ver=1.0') }}"></script>
      <?php
      }
      ?>

      <?php
      if( $view_name == 'staffedit' ||  $view_name == 'stafflist'  ) {
      ?>
      <script src="{{ asset('js/staff.js?ver=1.0') }}"></script>
      <?php
      }
      ?>


      @yield('script')

</body>
</html>