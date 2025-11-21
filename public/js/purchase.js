
$(document).ready(function() { 
    var pCount = 1;

 $(document).on("change", "#purchase_manual_product", function(event){

	var vals = $(this).val();

	if( vals > 0 )
     {
	 if( !checkIfItemExist(vals)) {
	    //alert("Please enter barcode.");
	    //$("#autosearchproduct").hide();
	     ajaxGetProductPurchase(vals, true);
	 }
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
    
    $(document).on("keyup", "#purchase_productbarcode", function(event){

	var vals = $(this).val();

	if( vals == "" )
	{
	    //alert("Please enter barcode.");
	    
	    return ;
	}

	if(event.keyCode == 13 ) {

	    
	    //TODO: this will be work for barcode scanner
	    $(this).val("");
	    //$("#autosearchproduct").hide();
	    ajaxGetProductPurchase(vals);

	}
	

    });
    
    
        $(window).keydown(function(event){
	if(event.keyCode == 13) {
	    event.preventDefault();
	    return false;
	}
	});

    function ajaxGetProductPurchase(vals, byid = false )
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
	    
	    if (item) {

		
		$("#purchase_invoicetable").find("tr").each(function(index, element) {

		    
		    if(  item.id == $(element).find('.purchase_ids').val()  )
		    {
			var qnty =  parseInt($(element).find(".purchase_qnty").val());
			qnty++;
			$(element).find(".purchase_qnty").val(qnty);

			finds = true;
			calculatePurchase();
		    }
		    //alert( $(element).find(".sale_qnty").val() );
		    
		    

		});
		
		if( finds == false ){
		    addProductItemPurchase(item);
		}
	    }
	    else
	    {
		alert("Sorry! No product found.");
	    }

	}, "json");
    }
    

    function addProductItemPurchase(item)
    {
	var newString = strPurchaseRow.replace("{rowno}",pCount);

	var hdnName = item["name"].replace("\"", "&quot;");

	
	newString = newString.replace("{ids}",  item['id'] );
	newString = newString.replace("{purchase_product_taxes}",  item['tax'] );
	newString = newString.replace("{product_names}", "("+item["productcode"]+"/"+item["barcode"]+")"+" "+hdnName);
	newString = newString.replace("{productname}", "("+item["productcode"]+"/"+item["barcode"]+")"+" "+item["name"] );
	newString = newString.replace("{unitprice}", item["purchaseprice"] );

	newString = newString.replace("{qnty}",  1 );
	newString = newString.replace("{totalprice}",  item["purchaseprice"]  );

	//alert(newString);
	
	$("#purchase_invoicetable").append( newString );
	pCount += 1;
	calculatePurchase();
    }





    function calculatePurchase()
    {

	var totalTax = 0;
	var totalTaxableAmount = 0;
	var totalFullAmount = 0;
	var grandTotal = 0;
	var totalDiscount = 0;

	$("#purchase_invoicetable").find("tr").each( function(index, element) {



	    if( $(element).find(".purchase_ids").val() > 0 )
	    {

		var unitPrice = parseFloat($(this).find(".purchase_unit_price").val());
		
		if( isNaN(unitPrice) )
		{
		    unitPrice = 0;
		}
		var qnty = parseFloat($(this).find(".purchase_qnty").val());
		if( isNaN(qnty) )
		{
		    qnty = 0;
		}
		var discountType = $(this).find(".purchase_discounttype").val();
		var discountValue = parseFloat($(this).find(".purchase_discountvalue").val());
		if( isNaN(discountValue) )
		{
		    discountValue = 0;
		}
		var totalPrice = unitPrice * qnty;
		totalFullAmount += totalPrice;
		
		var tax = parseFloat($(this).find(".purchase_product_taxes").val());
		if( isNaN(tax))
		{
		    tax = 0;
		}
		
		var discount = 0;
		if( discountType == 'percent' && discountValue > 0  )
		{
		    discount = totalPrice * discountValue / 100;
		}
		else if( discountType == 'absolute' && discountValue > 0  )
		{
		    discount = discountValue;
		}
		var taxableAmount = totalPrice - discount;
		totalTaxableAmount += taxableAmount;
		
		var taxAmount = 0;
		if( tax > 0 ){
		    taxAmount = taxableAmount * tax / 100;
		}
		grandTotal += taxableAmount + taxAmount;
		totalTax += taxAmount;
		totalDiscount += discount;


		$(this).find(".purchase_discount_amount").text( roundTo(discount, 2) );
		$(this).find(".purchase_product_total_price").text( roundTo(taxableAmount, 2) );
	    }




	});

		    var globaldiscount = 0;
	    var globalDiscountType = $(".global_discounttype").val();
	    var globalDiscountValue = $("#global_discountvalue").val();
	    
	if( globalDiscountType == "percent" &&  globalDiscountValue > 0 )
	    {
		    globaldiscount = totalTaxableAmount * globalDiscountValue / 100;
	    }
	    else if( globalDiscountType == "absolute" &&  globalDiscountValue > 0 )
	    {
		globaldiscount = globalDiscountValue;
	    }

	    //totalTaxableAmount -= globaldiscount;

	    

	
	    $("#purchase_total_amount_without_discount").text( roundTo( totalFullAmount ,2) );

			
	$("#global_discount_amount").text(roundTo( globaldiscount,2) );
	    $("#purchase_taxable_amount").text( roundTo(totalTaxableAmount,2) );
	$("#purchase_total_discount").text( roundTo( parseFloat(totalDiscount) + parseFloat(globaldiscount),2) );
	    $("#purchase_total_tax").text( roundTo(totalTax,2) );
	    $("#purchase_grand_total").text( roundTo(grandTotal - globaldiscount,2) );

    }

        $(document).on("change", ".sale_discounttype", function() {

	calculateAll();
	});

            $(document).on("keyup", ".purchase_discountvalue", function() {

	calculatePurchase();
	});

    $(document).on("change", ".global_discounttype", function() {

	calculatePurchase();
    });
    $(document).on("keyup", "#global_discountvalue", function(){
	calculatePurchase();
    });

    $(document).on("click","#add_product_button", function(){

	var vals = $("#purchase_manual_product").val();
	if( vals > 0 )
	{
	    if( !checkIfItemExist(vals) ){
		ajaxGetProductPurchase(vals, true );
	    }
	}
	
    });

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
    
    // add purchase functionality
    if( $(".purchase_products").length > 0 )
    {
	$(document).on("change",".purchase_products", function(e)
		       {
			   var that = this;
			   //ajax call to get product details from common
			   getProductDetails($(this).val(), function(data){

			       var parents =  $(that).parents("tr");
			       var purchasePrice = parseFloat(data.purchaseprice);
			       parents.find(".purchase_unit_price").val(purchasePrice);
			       parents.find(".purchase_product_taxes").val(data.tax);
			       calculatePurchase();
			   });
			   
		       });
    }

    $(document).on("keyup", ".purchase_unit_price", function() {

	calculatePurchase();

    });

    $(document).on("blur", ".purchase_unit_price", function() {

	if( $(this).val() == '' || $(this).val() < 0  )
	{
	    $(this).val(0);
	}
	calculatePurchase();

    });

    $(document).on("keyup", ".purchase_qnty", function() {

	calculatePurchase();

    });
    $(document).on("blur", ".purchase_qnty", function() { 
	if( $(this).val() == '' || $(this).val() < 0  )
	{
	    $(this).val(0);
	}
	calculatePurchase();
    });

    $(document).on("blur", ".purchase_discount_value", function() { 
	
	calculatePurchase();
    });
    $(document).on("blur", ".purchase_qnty", function() { 
	
	calculatePurchase();
    });
    

    

    
    $(document).on("keyup", ".purchase_discount_value", function() {

	calculatePurchase();

    });
    $(document).on("change", ".purchase_discount_type", function() {

	calculatePurchase();

    }); 

/*    $(".add_more_product").on("click", function(){

	//alert($("#purchase_invoicetable tr").length);

	strPurchaseRow1 = strPurchaseRow.replace('{mark}', $("#purchase_invoicetable tr").length + 1 );
	$("#purchase_invoicetable").append(strPurchaseRow1);
	$("#purchase_invoicetable").find(".purchase_products").select2();
	
    }); */

    $(document).on("click", ".deleteitembtn", function() {

	$(this).parents("tr").remove();
	calculatePurchase();
    });

    
    function checkIfItemExist(id)
    {
	var finds = false;
	$("#purchase_invoicetable").find("tr").each(function(index, element) {


	    
	    var ids = $(element).find(".purchase_ids").val();


	    if( ids == id ) {
		var elem = $(element).find(".purchase_qnty");
		var qnty =  parseInt(elem.val());
		qnty++;
		elem.val(qnty);
		

		calculatePurchase();
		
		finds = true;
	    }

	    

	    

	});

	return finds;
    }

    	$("#billdate").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#billdate").inputmask("99/99/9999");

    	$("#deliverydate").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#deliverydate").inputmask("99/99/9999");

    $("#paymentduedate").datepicker({ 'format' : 'dd/mm/yyyy' });
    $("#paymentduedate").inputmask("99/99/9999");
    $("#chequedate").datepicker({ 'format' : 'dd/mm/yyyy' });
    $("#chequedate").inputmask("99/99/9999");


    if(shouldCaluculate == true )
    {
	calculatePurchase();
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

});
