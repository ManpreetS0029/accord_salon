$(document).ready(function(){

  //  $('#compositepackage').hide();
  //  $('#giftedprice').hide();

      $(".serviceitem").select2();

     $('.item_a .select2').css("width", "351px"  );



// on Documnet Ready Show Hide Gift Price and Compoiste Package
    var packagetypevalue = $('select[name=packagetype]').val();
    if(packagetypevalue == 1){
        $('#compositepackage').fadeOut(100);
        $('#giftedprice').fadeIn(100);
    }
    if(packagetypevalue == 2){
        $('#compositepackage').fadeIn(100);
        $('#giftedprice').fadeOut(100);
    }


// on CHANGE  Show Hide Gift Price and Compoiste Package
     $('select[name=packagetype]').on('change', function(){

        var packagetypevalue = $('select[name=packagetype]').val();

        // 1 = Cash Discount,  2 = Composite Package
        if(packagetypevalue == 1){
            $('#compositepackage').fadeOut(300);
            $('#giftedprice').fadeIn(300);
        }
        if(packagetypevalue == 2){
            $('#compositepackage').fadeIn(300);
            $('#giftedprice').fadeOut(300);
        }

    });

     var x = 1;
    // Composite Package -> ADD New Item

    $(document).on("click", ".add_row", function(){

        x++;
        var selectBox = '<select name="item[]" class="serviceitem"><option value="">Select</option>'+datas+'</select>';

        var quantityBox = '<input type="text" class="form-control itemquantity" name="itemquantity[]" />';

        var deletebotom = '<button class="delete_row" type="button">Delete</button>';

        var clonedRow = $('<tr><td id="items_'+x+'">'+selectBox+'</td><td>'+quantityBox+'</td><td>'+deletebotom+'</td></tr>');

        $(clonedRow).find(".serviceitem").select2()
        $("#composite_package_item_list").append(clonedRow);

       // $("#composite_package_item_list #items_"+x+" .serviceitem").select2();

    });


    $(document).on("click", ".add_row_edit", function(){

        x++;
        var selectBox = '<div class="form-group"><select name="item[]" class="serviceitem form-control"><option value="">Select</option>'+datas+'</select><span class="help-block">Service is required</span></div>';

        var quantityBox = '<div class="form-group"><input style="width: 100px;" type="text" class="form-control itemquantity" name="itemquantity[]" /><span class="help-block">Quantity is required</span></div>';

        var deletebotom = '<button class="delete_row" type="button">Delete</button>';

        var clonedRow = $('<tr><td id="items_'+x+'">'+selectBox+'</td><td>'+quantityBox+'</td><td>0</td><td>'+deletebotom+'</td></tr>');

        $(clonedRow).find(".serviceitem").select2()
        $("#composite_package_item_list").append(clonedRow);

        // $("#composite_package_item_list #items_"+x+" .serviceitem").select2();

    });

    // Composite Package -> Delete Clicked Item


    $(document).on("click", ".delete_row", function(){
       $(this).parent().parent().remove();
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

    $("#actualprice").on("keyup", function(){

        if( $(this).val() != "" )
        {
            $('#paidamount').val( $('#actualprice').val() );
        }

    });

    $("#paymentmodeid2").on("change", function(){

        if( $(this).val() != "" ) {
            var actualprice = $('#actualprice').val() > 0 ? parseFloat($('#actualprice').val()) : 0;
            var paidamount = $('#paidamount').val() > 0 ? parseFloat( $('#paidamount').val() ) : 0;

            $("#paidamount2").val( actualprice - paidamount );
        }
        else
        {
            $("#paidamount2").val( 0 );
        }
    });


    $("#actualprice, #paidamount, #paidamount2").on("keyup change", function(){
       // alert ("hello");



        var actualprice = $('#actualprice').val() > 0 ? parseFloat( $('#actualprice').val() ) : 0;
        var paidamount = $('#paidamount').val() > 0 ? parseFloat( $('#paidamount').val() ) : 0;

        var paidamount2 = $('#paidamount2').val() > 0 ? parseFloat( $('#paidamount2').val() ) : 0;


        var balanceamount = (actualprice - (paidamount + paidamount2) ) * -1 ;



       /* if( balanceamount > 0 )
        {

            $('#balanceamount').fadeIn(200);
            $('#balanceamount span').text(balanceamount.toFixed(2));
        }
        else
        {
            $('#balanceamount').fadeOut(200);
        } */


       // console.log(paidamount2);



    } );

    $("#clientid").on("change", function(){


        ajaxGetClientAdvance($(this).val());
    });

    function ajaxGetClientAdvance(clientId) {

        if (clientId > 0){
            console.log(clientId);
            $.get(urls + "/common/getclientadvance/" + clientId, function (data) {
//clientadvanceinfo
                var txt = "";

                if( data["advance"] > 0  )
                {
                    txt += "Approved Payment Advance: "+data["advance"]+"<br />";
                }
                if( data["actual_amount_pending"] > 0 )
                {
                    txt += "Pending amount: "+data["actual_amount_pending"]+"<br />";
                }

                if( data["advance_with_pending_cheques"] > 0 )
                {
                    txt += "Advance: "+data["advance_with_pending_cheques"]+"<br />";
                }

                $("#clientadvanceinfo").html(txt );

            }, "json");
        }
        else
        {
            $("#clientadvanceinfo").html("" );
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

    //form validation
   /* $("#clientpackageaddform").on("submit", function(e) {

        var $packageName = $("#packagename");
        var $client = $("#clientid");
        var $packagetype = $("#packagetype");
        var $actualprice = $("#actualprice");
        var $gifted_price_input = $("#gifted_price_input");
        var errors = false;
        if( $.trim( $packageName.val() ) == '' )
        {
            errors = true;
            $packageName.parents(".form-group").addClass("has-error");
        }
        else
        {
            $packageName.parents(".form-group").removeClass("has-error");
        }

        if( $client.val() <= 0 )
        {
            $client.parent(".form-group").addClass( "has-error" );
        }
        else
        {
            $client.parent(".form-group").removeClass( "has-error" );
        }

        if( $packagetype.val() <= 0 )
        {
            $packagetype.parent(".form-group").addClass( "has-error" );
        }
        else
        {
            $packagetype.parent(".form-group").removeClass( "has-error" );
        }

        if( errors == true ) {
             e.preventDefault();
        }

    }); */

    //edit form item qunatity change validation
    $(document).on("blur", ".edit_package_list .itemquantity", function(e){

         var usedQnty = parseFloat(  $(this).parents("tr").find(".used_item_qnty").text() );
        console.log(parseFloat( $(this).val() ));
        if( usedQnty > 0 ) {
            if (usedQnty > parseFloat($(this).val()) || isNaN($(this).val())) {
                $(this).parent(".form-group").addClass("has-error");
            }
            else {
                $(this).parent(".form-group").removeClass("has-error");
            }
        }
        else
        {
            if( parseFloat($(this).val()) <= 0 || isNaN($(this).val()) ) {
                $(this).parent(".form-group").addClass("has-error");
            }
            else {
                $(this).parent(".form-group").removeClass("has-error");
            }
        }

    });

});