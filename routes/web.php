<?php
    
    /*
     |--------------------------------------------------------------------------
     | Web Routes
     |--------------------------------------------------------------------------
     |
     | Here is where you can register web routes for your application. These
     | routes are loaded by the RouteServiceProvider within a group which
     | contains the "web" middleware group. Now create something great!
     |
     */
    /*
     * Verb    Path                        Action  Route Name
     GET     /users                      index   users.index
     GET     /users/create               create  users.create
     POST    /users                      store   users.store
     GET     /users/{user}               show    users.show
     GET     /users/{user}/edit          edit    users.edit
     PUT     /users/{user}               update  users.update
     DELETE  /users/{user}               destroy users.destroy
     */


    // Artisan::call('cache:clear');
    // Artisan::call('config:clear');
    // Artisan::call('route:clear');

    Route::get('/', 'HomeController@index')->name('home');
    
    Auth::routes();
    
    Route::get('/home', 'HomeController@index')->name('home');
    
    
    
    $router->group([ 'middleware' => ['auth'] ], function($router)
                   {
                   Route::get('/home/popular-services', 'HomeController@getPopularServices')->name('home.popularservices');
                   //$router->resource('user', 'UserController');
                   // categories
                   /*   Route::get('category/add', 'CategoryController@add')->name('category.create');
                    Route::post('category/add', 'CategoryController@save')->name('category.save');
                    Route::get('category', 'CategoryController@index')->name('category.list');
                    Route::get('category/delete/{id}', 'CategoryController@destroy')->name('category.delete');
                    Route::get('category/edit/{id}', 'CategoryController@edit')->name('category.edit');
                    Route::post('category/edit/{id}', 'CategoryController@update')->name('category.update');
                    */
                   Route::get('product/searchproduct/{id}', 'ProductController@searchproduct')->name("product.search");
                   
                   Route::get('product/getproduct/{id}', 'ProductController@getproduct')->name("product.get");
                   
                   //assignstafftooSale
                   
                   Route::get('common/assignstafftoosale/{id}', 'CommonController@assignstafftoosale')->name("common.assignstafftoosale");
                   
                   Route::get('common/getclientadvance/{id}', 'CommonController@getclientadvance')->name("common.getclientadvance");
                   Route::get('common/getclientuncompletedpackages/{id}', 'CommonController@getClientUncompletedPackages')->name("common.getclientuncompletedpackages");
                   Route::get('common/packageleftitemsormoney/{id}', 'CommonController@packageLeftItemsOrMoney')->name("common.packageleftitemsormoney");
                   Route::get('common/deletesaleitem/{id}', 'CommonController@deleteSaleItem')->name("common.deletesaleitem");
                   Route::get('common/getsalarydetails/', 'CommonController@getsalarydetails')->name("common.getsalarydetails");
                   Route::post('common/updatesalary/', 'CommonController@updatesalary')->name("common.updatesalary");
                   
                   
                   Route::post('common/getcities', 'CommonController@getcities')->name("common.getcities");
                   
                   
                   Route::get('common/getproductwithid/{id}', 'CommonController@getproductwithid')->name("common.getproductwithid");
                   
                   Route::get('sale/{id}/paymenthistory', 'SaleController@paymenthistory')->name("sale.paymenthistory");
                   
                   Route::get('sale/{id}/addpayment', 'SaleController@addpayment')->name("sale.addpayment");
                   Route::post('sale/{id}/addpayment', 'SaleController@storepayment')->name("sale.storepayment");
                   Route::get('sale/{id}/editpayment/{id1}', 'SaleController@editpayment')->name("sale.editpayment");
                   Route::post('sale/{id}/editpayment/{id1}', 'SaleController@updatepayment')->name("sale.updatepayment");
                   
                   //purchase extra methods
                   Route::get('purchase/{id}/paymenthistory', 'PurchaseController@paymenthistory')->name("purchase.paymenthistory");
                   
                   Route::get('purchase/{id}/addpayment', 'PurchaseController@addpayment')->name("purchase.addpayment");
                   Route::post('purchase/{id}/addpayment', 'PurchaseController@storepayment')->name("purchase.storepayment");
                   Route::get('purchase/{id}/editpayment/{id1}', 'PurchaseController@editpayment')->name("purchase.editpayment");
                   Route::post('purchase/{id}/editpayment/{id1}', 'PurchaseController@updatepayment')->name("purchase.updatepayment");
                   
                   Route::get('clients/100list', 'ClientsController@listfirsthundred')->name("clients.listfirsthundred");
                   Route::get('clients/nonrepeating', 'ClientsController@nonRepeatingCustomers')->name("clients.nonrepeating");
                   //    Route::get('clients/clientpackagelist', 'ClientsPackageListController@clientpackagelist')->name("clients
                   //.clientpackagelist");
                   Route::get('clients/{id}/paymentlist', 'ClientsController@paymentlist')->name("clients.paymentlist");
                   //Route::get('clients/{id}/addnewpayment', 'ClientsController@addnewpayment')->name("clients.addnewpayment");
                   
                   Route::resource('category', 'CategoryController');
                   Route::resource('clients', 'ClientsController');
                   Route::resource('services', 'ServicesController');
                   Route::resource('package', 'PackageController');
                   Route::resource('product', 'ProductController');
                   Route::resource('sale', 'SaleController');
                   Route::resource('clientspackage', 'ClientsPackageController');
                   Route::resource('allpayment', 'AllPaymentController');
                   Route::resource('salary', 'SalaryController');
                   Route::resource('attendance', 'AttendanceController');
                   Route::resource('revenuereport', 'RevenueReportController');
                   Route::resource('closingday', 'ClosingDayController');
                   Route::resource('sendmessages', 'SendMessages');
                   
                   
                   // Route::resource('clientpackagelist', 'ClientsPackageListController');
                   
                   
                   
                   Route::post('staff/updateattendance', 'StaffController@updateattendance')->name("staff.updateattendance");
                   
                   Route::get('staff/salegraph', 'StaffController@salegraph')->name("staff.salegraph");
                   
                   Route::resource('staff', 'StaffController');
                   Route::get('staff/{id}/saledetails', 'StaffController@saledetails')->name("staff.saledetails");
                   
                   Route::post('expense/addexpenses', 'ExpenseController@addexpenses')->name("expense.addexpenses");
                   
                   
                   
                   Route::resource('expensemaster', 'ExpenseMasterController');
                   Route::resource('expense', 'ExpenseController');
                   Route::resource('productissue', 'ProductIssueController');
                   Route::resource('company', 'CompanyController');
                   Route::resource('companycontact', 'CompanyContactPersonsController');
                   Route::resource('purchase', 'PurchaseController');
                   
                   });
