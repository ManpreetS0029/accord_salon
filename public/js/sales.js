$(document).ready(function(){
var pCount = 1;
var packages = "";
var editPageGrandTotal =    parseFloat($("#grandtotal").text());
if(typeof  selectedPackage  == "undefined")
{
    selectedPackage = "";
}
    if( $("#hs_product_tab").length > 0 )
    {
	
	$("#hs_product_tab").on("click", function() {

	    setTimeout( function() {
		$("#productbarcode").focus();

		
	    }, 500);
	});
    }


    if( $(".addbtns").length > 0 )
    {
	$(document).on("click",".addbtns", function(e) {

	    var ids = 0;
	    if( jsonObjForServices.length > 0 )
	    {
		for ( var x = 0; x < jsonObjForServices.length; x++ )
		{
		    if( jsonObjForServices[x]['id'] == $(this).attr("id") )
		    {
			var vals = "<input type='hidden' name='hdnaddedservice[]' value='"+jsonObjForServices[x]['id']+"' />";
			$("#servicebox").append( "<div class='addedservicebtn'>"+jsonObjForServices[x]["name"]+" ("+jsonObjForServices[x]["price"]+") <div data-ids='"+jsonObjForServices[x]["id"]+"' class='close'>X</div>"+vals+"</div>" );
		    }
		}
	    }



	});

	$(document).on("click", "#servicebox .close", function(){

	    $(this).parent(".addedservicebtn").remove();
	});



    }

    
    // sale code
    if( $(".addbtnssale").length > 0  || $("#invoicetable").length > 0 )
    {

		
	$(document).on("click",".addbtnssale", function(e) {

	    var ids = 0;
		var rowsCount = $("#invoicetable tr").length;

		 

	    if (jsonObjForServices.length > 0) {
		
			for (var x = 0; x < jsonObjForServices.length; x++) {

				
		    if( jsonObjForServices[x]['id'] == $(this).attr("id") && jsonObjForServices[x]['types'] == $(this).data("type") )
		    {
			
			// same product is already exist
			/*if( checkIfItemExist($(this).attr("id"), $(this).data("type") ) )
			{
			    
			    return;
			} */

			var newString = rowsString.replace("{rowno}",rowsCount+1);

			newString = newString.replace("{ids}", jsonObjForServices[x]['id'] );
			newString = newString.replace("{itemtype}",$(this).data("type"));
			//dont'remove below line
			newString = newString.replace("{itemtype}",$(this).data("type"));

			newString = newString.replace("{taxes}", jsonObjForServices[x]["tax"]);
			newString = newString.replace("{product_servicename}", jsonObjForServices[x]["name"] );
			newString = newString.replace("{unitprice}", jsonObjForServices[x]["price"] );

			newString = newString.replace("{qnty}",  1 );
			newString = newString.replace("{totalprice}",  jsonObjForServices[x]["price"]  );

			// newString = newString.replace("{totalprice}",  jsonObjForServices[x]["price"]  );

			var strStaff = '';
			if( jsonObjForServices[x]['types'] == 'package' )
			{

			    if( jsonObjForServices[x]['services'].length > 0 )
			    {
				for( var k = 0; k < jsonObjForServices[x]['services'].length ; k++ )
				{
					//packageserviceid
				    //strStaff += '<div>'+jsonObjForServices[x]['services'][k]['name']+': <select size="4" multiple name="staffs_'+jsonObjForServices[x]["id"]+'_'+jsonObjForServices[x]['services'][k]["id"]+'[]" class="form-control">'+staffOptions+'</select></div>';
				    strStaff += '<div class="service_staff_box" data-name="staffs'+rowsCount+'_'+jsonObjForServices[x]["id"]+'_'+jsonObjForServices[x]['services'][k]["id"]+'_'+jsonObjForServices[x]['services'][k]["packageserviceid"]+'">'+jsonObjForServices[x]['services'][k]['name']+'<br /><button type="button" class="addstaff">Add Staff</button></div>';
				}
			    }
			}
			else if( jsonObjForServices[x]['types'] == 'service' )
			{
			    strStaff += '<div class="service_staff_box" data-name="staffs'+rowsCount+'_services_'+jsonObjForServices[x]["id"]+'"><button type="button" class="addstaff">Add Staff</button></div>';
			}

			newString = newString.replace("{staffstring}",  strStaff  );

			$("#invoicetable").append( newString );
			pCount += 1;
			calculateAll();
		    }
		}
	    }
	});

	$(document).on("keyup", ".sale_qnty", function(){

	    if( $(this).val() == '' )
	    {
		return ;
	    }
	    if( parseInt( $(this).val() ) <= 0  )
	    {
		$(this).val(1);
	    }
	    calculateAll()
	    //var qnty = parseInt( $(this).val() ) > 0  ? parseInt( $(this).val() )  : 1;
	    //$(this).val(qnty);

	    // var pricing = qnty * $(this).parents("tr").children(".sale_ids")

	});



	$(document).on("keyup", ".sale_discountvalue", function(){


	    calculateAll()
	    //var qnty = parseInt( $(this).val() ) > 0  ? parseInt( $(this).val() )  : 1;
	    //$(this).val(qnty);

	    // var pricing = qnty * $(this).parents("tr").children(".sale_ids")

	});



    }



    $(document).on("keyup", "#taxpercent", function() {

	// if( $(this).val() > 0 ) {
	calculateAll();
	// }

    });

    function ajaxDeleteItemFromEdit(orgId, callback )
    {
        if (orgId > 0) {

            $.get(urls + "/common/deletesaleitem/" + orgId, function (data) {

                callback();

            });
        }



    }

    $(document).on("click", ".deleteitembtn", function() {


        if( confirm('Are you sure?') ) {



                if( $(this).parents("tr").find(".org_ids").length > 0 )
                {


                    ajaxDeleteItemFromEdit( $(this).parents("tr").find(".org_ids").val(), function(){

                        if ($("#package_name").length > 0 && $("#package_name").val() != "") {
                            ajaxGetPackageLeftItemsOrMoney($("#package_name").val());
                        }

                    });
                }





            $(this).parents("tr").remove();

            calculateAll();
        }


    });

    $(document).on("change", ".sale_discounttype", function() {

	calculateAll();
    });
    $(document).on("change", ".global_discounttype", function() {

	calculateAll();
    });

    $(document).on("keyup", "#global_discountvalue", function() {



	// if( $(this).val() > 0 && $(".global_discounttype").val() != '' )
	//  {
	calculateAll();
	//  }



    });

    
    $(document).on("keyup", ".class_unitprice", function() {



	// if( $(this).val() > 0 && $(".global_discounttype").val() != '' )
	//  {
	calculateAll();
	//  }



    });
    



    function checkIfItemExist(id, types)
    {
	var finds = false;
	$("#invoicetable").find("tr").each(function(index, element) {

	    var sale_itemtype = $(element).find(".sale_itemtype").val();
	    
	    var ids = $(element).find(".sale_ids").val();


	    if( ids == id && types == sale_itemtype ) {
		var elem = $(element).find(".sale_qnty");
		var qnty =  parseInt(elem.val());
		qnty++;
		elem.val(qnty);
		

		calculateAll();
		
		finds = true;
	    }

	    

	    

	});

	return finds;
    }

    function calculateAll()
    {
	if( $("#printbtn").length > 0 )
	{
	    $("#printbtn").attr("disabled", true );
	}
	var subTotal = 0;
	var globalDiscountAbleTotalAmount = 0;
	var servicesTaxableAmount = 0;
	var rows = $("#invoicetable").find("tr");
	var totalTax = 0;
	rows.each(function(index, element) {

	    //alert( $(element).find(".sale_qnty").val() );
	    var qnty =  parseInt($(element).find(".sale_qnty").val());
	    var unitprice = parseFloat($(element).find(".class_unitprice").val());

	    var discountAmount = 0;
	    var discountType = $(element).find(".sale_discounttype").val();
	    var discountValue = parseFloat($(element).find(".sale_discountvalue").val());

	    
	    if( discountType == 'percent' &&  discountValue > 0 )
	    {
		discountAmount = qnty * unitprice * discountValue / 100;
	    }
	    else if( discountType == 'absolute' &&  discountValue > 0 )
	    {
		discountAmount =   discountValue ;
	    }

	    var totalPrice =  ( unitprice * qnty ) - discountAmount;

	    // no global discount for product
	    var taxAmount = 0;
	    if( $(element).find(".sale_itemtype").val() == "product")
	    {
		taxAmount =  totalPrice - totalPrice / ( (100 + parseFloat($(element).find(".sale_taxes").val() ) ) / 100 );
		totalTax += taxAmount;
		  
	    }
	    else
	    {
		servicesTaxableAmount += totalPrice;
		globalDiscountAbleTotalAmount += totalPrice;
	    }
	    
	    $(element).find(".totalprice").html( roundTo(totalPrice,2));
	    $(element).find(".sale_discount_amount").html(roundTo(discountAmount,2));

	    subTotal += totalPrice;

	});


	//global fields processing
	var globalDiscountType = $(".global_discounttype").val();
	var globalDiscountValue =  parseFloat($("#global_discountvalue").val());
	var discountAmount = 0;
	if( globalDiscountType == "percent" && globalDiscountValue > 0 )
	{
	    discountAmount = globalDiscountValue * globalDiscountAbleTotalAmount / 100;  //subTotal / 100;

	}
	else if( globalDiscountType == "absolute" && globalDiscountValue > 0 )
	{
	    discountAmount =   globalDiscountValue;

	}

	
	
	
	//if( $("#taxpercent").val() > 0 )
	{
	    globalDiscountAbleTotalAmount = globalDiscountAbleTotalAmount - discountAmount;
	    //tax = parseFloat( (subTotal - discountAmount)   *  $("#taxpercent").val() / 100  );
	    taxAmount1 = globalDiscountAbleTotalAmount - (globalDiscountAbleTotalAmount  /  ((100 + servicesTaxPercent)/100) );
	    totalTax += taxAmount1; 
	}
	
	 

	$("#subtotal").html(roundTo(subTotal,2));
	$("#global_discount_amount").html( roundTo(discountAmount,2) );
	$("#taxable_amount").html( roundTo(subTotal - discountAmount - totalTax,2));
	$("#taxamount").html( roundTo(totalTax, 2 ) );
	$("#grandtotal").html( roundTo( subTotal - discountAmount,2) );

      /*  if( $("#package_name").length > 0 && $("#package_name").val() > 0 )
        {

        }
        else */
      if( $("#use_advance_amount").length > 0 &&  $("#use_advance_amount").prop("checked") == true )
        {

            $("#amountpaid").val( 0 );
        }
        else
	  {
          $("#amountpaid").val( roundTo( subTotal - discountAmount,2) );
	  }

    }



    $(window).keydown(function(event){
	if(event.keyCode == 13) {
	    event.preventDefault();
	    return false;
	}
    });


    // usage of add product button
    $(document).on("click", "#add_product_button", function(e){

	//get product from server getproduct
	var vals = $("#manual_productbarcode").val();
	if( vals == "" )
	{
	    alert("Please enter barcode.");
	    return;
	}


	

	ajaxGetProduct(vals, true);

    });


    $(document).on("keyup", "#productbarcode", function(event){

	var vals = $(this).val();

	if( vals == "" )
	{
	    //alert("Please enter barcode.");
	    
	    return ;
	}

	if(event.keyCode == 13 ) {

	    
	    //TODO: this will be work for barcode scanner
	    $(this).val("");
	    $("#autosearchproduct").hide();
	    ajaxGetProduct(vals);

	}
	

    });


    $(document).on("change", "#manual_productbarcode", function(event){

	var vals = $(this).val();

	if( vals > 0 )
	{
	    //alert("Please enter barcode.");
	    //$("#autosearchproduct").hide();
	    ajaxGetProduct(vals, true);
	}
	else
	{
	    return ;
	}
	//var that = this;
	//auto search
	/*
	  $.get("<?php echo URL::to('/'); ?>/product/searchproduct/" + vals, function (data) {

	  if (data.length > 0) {
	  var listdata = "";
	  for (var x = 0; x < data.length; x++) {

	  // listData += ' { "id" : "'+data[x]["barcode"]+'", "text" : "'+data[x]["name"]+'" } ';
	  //data[x]["productcode"]
	  //	 listdata += "<li class='product_search_list_items' id='" + data[x]["barcode"] + "'>(" + data[x]["productcode"] + "/" + data[x]["barcode"] + ") " + data[x]["name"] + "</li>";
	  }
	  //$("#autosearchproduct").show();
	  //$("#autosearchproduct").html(listdata);
	  $(that).select2( 'data', { "id" : "123456", "text" : "anmes" }  );
	  }
	  else
	  {
	  $(that).select2( 'data', { });
	  }

	  }, "json"); */

    });

    
    $(document).on("click", ".product_search_list_items", function(event){

	$("#manual_productbarcode").val( $(this).attr("id"));

	$(this).parents("ul").hide();
    });


    
    function ajaxGetProduct(vals, byid = false)
    {
	var funcName = '/product/searchproduct';
	if( byid == true  )
	{
	    funcName = '/common/getproductwithid';
	}
	var finds = false;
	$.get(urls+""+funcName+"/" + vals, function (data) {

	    var item = false;
	    if( byid == true && data )
	    {
		item = data;
	    }
	    else if (data.length > 0) {

		item = data[0];
	    }
	    if( item ) {
		/*$("#invoicetable").find("tr").each(function(index, element) {

		    
		    if(  item.id == $(element).find('.sale_ids').val() && $(element).find('.sale_itemtype').val() == 'product' )

		    {
			var qnty =  parseInt($(element).find(".sale_qnty").val());
			qnty++;
			$(element).find(".sale_qnty").val(qnty);

			finds = true;
			calculateAll();
		    }
		    //alert( $(element).find(".sale_qnty").val() );
		    
		    

		}); */
		
		if( finds == false ){
		    addProductItem(item);
		}
	    }
	    else
	    {
		alert("Sorry! No product found.");
	    }

	}, "json");
    }

    
    

    
    if($("#printbtn").length > 0 )
    {
	$("#printbtn").on("click", function() {

	    //
	    //"http://localhost/testabc.php"
	    $.post( "http://localhost/escpos-php-development/example/receipt-with-logo.php" , { "items" : jsonForPrintItems, 'subtotal' : subTotal, 'discount' : totalDiscount, 'taxamount' : totalTax, 'grandtotal' : paidPrice }, function(data) {

		console.log(data);

	    });
	});
    }


    function addProductItem(item)
    {
		var rowCount = $("#invoicetable tr").length;
		var newString = rowsString.replace("{rowno}",rowCount+1);

	
	newString = newString.replace("{ids}",  item['id'] );
	newString = newString.replace("{taxes}",  item['tax'] );
	newString = newString.replace("{itemtype}", "product" );
	// dont remove below line
	newString = newString.replace("{itemtype}", "product" );

	newString = newString.replace("{taxes}", item['tax']);
	
	newString = newString.replace("{product_servicename}", "("+item["productcode"]+"/"+item["barcode"]+")"+" "+item["name"] );
	newString = newString.replace("{unitprice}", item["price"] );

	newString = newString.replace("{qnty}",  1 );
	newString = newString.replace("{totalprice}",  item["price"]  );

//	var staffStr = '<div><select name="staffs_product_'+item['id']+'">'+staffOptions+'</select></div>';

					    var staffStr = '<div class="service_staff_box" data-name="staffs'+rowCount+'_product_'+item['id']+'"><button type="button" class="addstaff">Add Staff</button></div>';


	newString = newString.replace("{staffstring}", staffStr  );

	
	$("#invoicetable").append( newString );
	pCount += 1;
	calculateAll();
    }



        if( $("#chequedate").length > 0 )
    {
	$("#chequedate").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#chequedate").inputmask("99/99/9999");
    }
    
    $("#paymentmodeid").on("change", function() {

	if( $(this).val() == '5' ) //other
	{
	    $("#sale_other_payment_info").show();
	    	    $("#sale_bank_payment_info").hide();
	}
	else if( $(this).val() == '2')
	{
	    $("#sale_other_payment_info").hide();
	    $("#sale_bank_payment_info").show();
	}
	else
	{
	    	    $("#sale_other_payment_info").hide();
	    	    $("#sale_bank_payment_info").hide();

	}
    });

    $("#paymentmodeid2").on("change", function() {

        if( $(this).val() == '5' ) //other
        {
            $("#sale_other_payment_info2").show();
            $("#sale_bank_payment_info2").hide();
        }
        else if( $(this).val() == '2')
        {
            $("#sale_other_payment_info2").hide();
            $("#sale_bank_payment_info2").show();
        }
        else
        {
            $("#sale_other_payment_info2").hide();
            $("#sale_bank_payment_info2").hide();

        }
    });

    function setupSecondPaymentMode()
	{
        if( $("#package_name").length > 0 && $("#package_name").val() > 0 )
        {
            return;
        }

        if( $("#amountpaid").val() != "" && $("#paymentmodeid2").val() != "" )
        {
            var amt = parseFloat($("#grandtotal").text()) - $("#amountpaid").val();
            $("#amountpaid2").val( amt > 0 ? amt : 0  );
        }
        else
        {
            $("#amountpaid2").val("0");
        }

	}

	function advancePaymentDisplay()

	{
        if( $("#package_name").length > 0 && $("#package_name").val() > 0 )
        {
            return;
        }

        //advance_box
		var grandTotal = parseFloat($("#grandtotal").text());
		var payment1 = parseFloat( $("#amountpaid").val() );
		var payment2 = $("#amountpaid2").val() != "" ? $("#amountpaid2").val() : 	0;

        var amt =   (grandTotal - payment1 - payment2 ) * -1;
        if( amt > 0 )
		{
			$("#advance_box").html("<br /><div class='alert alert-success'>Advance Amount: "+ roundTo(amt, 2 )+'</div>');
		}
		else
		{
            $("#advance_box").html("<br /><div class='alert alert-danger'>Pending Amount: "+roundTo(Math.abs(amt), 2)+'</div>');
		}
	}

    $("#paymentmodeid2").on("change", function(){


        setupSecondPaymentMode();
        advancePaymentDisplay();

	});

    $("#amountpaid").on("keyup", function(){


        setupSecondPaymentMode();
        advancePaymentDisplay();
	});

    $("#amountpaid2").on("keyup", function(){

        advancePaymentDisplay();
    });

    $(document).on("click", ".addstaff", function(){
	var parentss = $(this).parents(".service_staff_box");
	parentss.append('<div class="service_done_assign"><select class="form-control" name="'+parentss.data("name")+'[]">'+staffOptions+'</select><div class="row"><div class="col-md-4"><button type="button"  class="remove_service_done"><i class="fa fa-remove"></i></button></div></div>');
    });

    $(document).on("click", ".remove_service_done", function(){
	$(this).parents(".service_done_assign").remove();
    });

	// Live Preview of Balance Amount
    $('#receivedamount').on("keyup", function(){
		//alert ("changed")
		console.log(" receive amount "+ $('#receivedamount').val());
		var balanceamount = ( parseFloat($('#grandtotal').text())  -  parseFloat( $('#receivedamount').val() ) );
		console.log(balanceamount);
		if ( !isNaN( balanceamount ) )
		{
            balanceamount =  ( (balanceamount * -1 ) ).toFixed(2) ;
            console.log(balanceamount);
			$('#balanceamount').text( ""+balanceamount );
			if( balanceamount >= 0 ) {

                $('#balanceamount').parent("th").css({ "background-color" : "#0f0" });
			}
			else
			{

                $('#balanceamount').parent("th").css({ "background-color" : "#f00" });
			}

        }
        else
		{
            $('#balanceamount').text("Error");
            $('#balanceamount').parent("th").css({ "background-color" : "#ccc" });

        }
	});


    //get client advance
	$("#client_dropdown").on("change", function(){

        ajaxGetClientAdvance($(this).val());
        ajaxGetUnCompletedPackages($(this).val());

	});

    // run on pageload
	if($("#client_dropdown").val() !== "" )
    {
        ajaxGetClientAdvance($("#client_dropdown").val());
        ajaxGetUnCompletedPackages($("#client_dropdown").val(), selectedPackage );
    }

    // run on pageload
    if(   selectedPackage != "" )
    {
        ajaxGetPackageLeftItemsOrMoney(selectedPackage);
    }


	$(document).on("change", "#package_name", function(){


        ajaxGetPackageLeftItemsOrMoney($(this).val());
	});

    var leftMoney = 0;
    var pendingServices = "";
	$("#btn_save_sale").on("click", function(e){



		if( $("#package_name").length > 0 && $("#package_name").val() != "" )
		{

			//validate package
			if( packages == "" )
			{
				alert("You can not use this package. Something went wrong.");
                return false;
			}
			else
			{
                for( var x = 0; x < packages.length; x++ ) {

                	if( packages[x]["id"] == $("#package_name").val() ) {

						//cash
                        if (packages[x]["packagetype"] == "1") {

                        	if( parseFloat($("#grandtotal").text()) - editPageGrandTotal > leftMoney )
							{
								alert("This package has not sufficient balance.");
                                return false;
							}
                        }
                        else {

                        	 if(  isAddedItemsMatchedWithPackageItems() == false )
							 {
                                 alert("Items Mismatched.");
                                 e.preventDefault();
							 	return false;
							 }

                        }
                    }
                }
			}
		}

	});



	function isAddedItemsMatchedWithPackageItems()
	{
        var data = pendingServices;
        var qnty =  Object.assign({}, data["qnty"] );

		var returnType = true;

		var zk = 0;
		var alreadyAddedItems = $(".org_ids");
        if( $(".sale_ids").length > 0 )
        {


            $(".sale_ids").each(function(index, element) {
                var that = this;
                var skip = 0;

                if( $(this).parents("tr").find(".org_ids").length > 0 && selectedPackage == $("#package_name").val() )
                {
                    skip = "1";
                }

                if( skip == "1" )
                {

                }
                else {


                    if ($(this).data("type") == "package" || $(this).data("type") == "product") {

                        returnType = false;
                    }
                    else {


                    	var valData =  qnty[$(this).val()];
                        if (valData == undefined ) {
                            //alert("Items Mismatched.");

                            returnType = false;
                        }
                        else {

                            qnty[$(this).val()] = parseInt(qnty[$(this).val()]) - parseInt($(".sale_qnty").eq(index).val());

                            //console.log(qnty[$(this).val()]);
                            if (parseInt(qnty[$(this).val()]) < 0) {

                                returnType = false;
                            }
                        }


                    }
                }
            });


        }

        return returnType ;
	}

	function ajaxGetPackageLeftItemsOrMoney(packageId)
	{

        $("#package_dropdown_pending_list").html("");
        leftMoney = "";
        pendingServices = "";
        if (packageId > 0) {
            console.log(packageId);
            $.get(urls + "/common/packageleftitemsormoney/" + packageId, function (data) {

                    console.log(data);

                    if( typeof data["leftmoney"] != "undefined" )
					{
						$("#package_dropdown_pending_list").html("<div  class='alert alert-success'>Amount left in package: "+data["leftmoney"]+"</div>");
                        leftMoney = data["leftmoney"];
					}
					else if( typeof data["qnty"] != "undefined" && typeof data["services"] != "undefined"  )
					{
						pendingServices = data;
						var txt = "";
						for( var x = 0; x < data["services"].length; x++  )
						{
							var currentService = data["services"][x];
							var qnty =  data["qnty"][currentService["id"]];
							if(  qnty > 0 )
							{
								txt += "<div>"+currentService["category"]["name"]+" - "+currentService["name"]+"("+currentService["price"]+"), Quantity: "+qnty+"</div>";
                            }
                        }
                        $("#package_dropdown_pending_list").html("<div  class='alert alert-success'>Items left in package:"+ (txt == "" ? "none" : "<br />"+txt ) +"</div>");

					}
                }
//clientadvanceinfo
                , "json");
        }
	}

    function ajaxGetClientAdvance(clientId) {

        if (clientId > 0){
            console.log(clientId);
            $.get(urls + "/common/getclientadvance/" + clientId, function (data) {
//clientadvanceinfo
				var txt = "";

                if( data["in_100_list"] == "1" )
                {
                    txt += "<div style='background-color: #0d3625; color: #fff; padding: 6px;'>In First 100 client list</div> <br />";
                }

            	if( data["advance_amount"] > 0  )
				{
                    txt += "Approved Payment Advance: "+data["advance_amount"]+"<br />";
				}
				if( data["sale_pending_amount"] > 0 )
                {
                    txt += "Total Sales Pending amount: "+data["sale_pending_amount"]+"<br />";
                }



                if( data["advance_amount"] > 0  )
                {
                    txt += '<label><input type="checkbox" name="use_advance_amount" id="use_advance_amount" value="1" > Use Advance Amount</label>';


                }
                $("#clientadvanceinfo").html(txt );

            }, "json");
   	 }
   	 else
		{
            $("#clientadvanceinfo").html("" );
		}

	}

	function ajaxGetUnCompletedPackages(clientId, selected = "", readonly = ""   )
	{
		//unCompletedPackages
		//getClientUncompletedPackages
        $("#package_dropdown_container").html("" );
        $("#package_dropdown_pending_list").html("" );
        packages = "";
        if (clientId > 0) {
            console.log(clientId);
            $.get(urls + "/common/getclientuncompletedpackages/" + clientId+"?selected="+selected, function (data) {
//clientadvanceinfo
				console.log(data);
				var selectBox = "";

				if( data.length > 0 )
				{
                    packages = data;

                    selectBox +="<br /><label>Select package</label>";
                    selectBox += "<select class='form-control'  name='package_name' id='package_name' "+readonly+" ><option value=''>Select Package</option>";
					for( var x = 0; x < data.length; x++ )
					{
						var packageName = "";
						if( data[x]["packagetype"] == "1" )
						{
							packageName += data[x]["packagename"]+"-Cash "+ data[x]["actualprice"]+ "->"+data[x]["giftedprice"] ;
						}
						else
						{
                            packageName += data[x]["packagename"]+"-Composite "+ data[x]["actualprice"];
						}

						var iselected = '';
						if( selected == data[x]["id"])
                        {
                            iselected = " selected ";
                        }
                        selectBox += '<option value="'+data[x]["id"]+'" '+iselected+'>'+packageName+'</option>';
					}

                    selectBox += "</select>";
					$("#package_dropdown_container").html(selectBox);
                    $("#package_dropdown_container #package_name").select2();

				}
            }, "json");
        }
	}

$('.modal').on('shown.bs.modal', function (event) {
    
     var aTag = $(event.relatedTarget);
      
      
   console.log(aTag.data("link"));
        if( aTag.data("link") != "" )
        {
             
            
            $(this).find('iframe').attr('src', aTag.data("link") );
             
           // $("#frames").attr("src", aTag.attr("href") );
        } 
    
    });

});
