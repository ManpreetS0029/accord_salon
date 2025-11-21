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
        <nav class="navbar navbar-default navbar-static-top">
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

                                      <li>
                                    <a href="{{  route('staff.salegraph') }}">+ Add Purchase</a>
                                </li>

      <li>
                                    <a href="{{  route('staff.create') }}">+ Add Staff</a>
                                </li>
                                <li>
                                    <a href="{{  route('staff.index') }}">List Staff</a>
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
                                    <a href="{{  route('productissue.create') }}">+ Issue Product</a>
                                </li>
                                <li>
                                    <a href="{{  route('productissue.index') }}">List Issue Products</a>
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

			     @yield('content')
			     <div class="container-fluid" style="background-color: #efefef; padding:10px 0; position:fixed; bottom:0; left:0; width:100%; text-align: center;">
			     &copy; Copyright <?php echo date("Y").' - '.(date("Y") + 1); ?> All rights reserved. <a href="http://khurlitsolutions.com" target="_blank">Khurl IT Solutions</a>
			     </div>
			     </div>

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

                                  
                                  
                            			<?php if( isset($view_name)) {
                                                      if( $view_name == 'saleadd' || $view_name == 'saleedit' || $view_name == 'salelist' || $view_name == 'saleaddpayment' || $view_name == 'saleeditpayment' ||  $view_name == 'packageadd' || $view_name == 'packageedit' ) {
?>
                                  <script src="{{ asset('js/sales.js?ver=2.1') }}"></script>                                                      <?php
                                                      }
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
     foreach( $staff as $mem ) { 

         $labelNames .= '"'.$mem->firstname." ".$mem->lastname.'",';
         $salesVal .= round($mem->totalMemberSale).",";
         
     }



          ?>
 	

    var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
    labels: [<?php echo rtrim($labelNames,","); ?>],
        datasets: [{
            label: "Sales By Staff",
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
               data: [<?php echo rtrim($salesVal,","); ?>],
               
        }]
    },

    // Configuration options go here
    options: {}
});


    <?php } ?>

</script>
<?php } ?>

</body>
</html>
