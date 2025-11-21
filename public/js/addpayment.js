/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){ 
    

    if( $(".payment_mode_status_add_select").length > 0 )
    {
        $(".payment_mode_status_add_select").on("change", function(event){ 
        
        if( $(this).val() == '2' )
        {
            $(this).parents("td").find(".payment_failed_reason_textarea").css("display", "block");
        }
        else 
        {
            $(this).parents("td").find(".payment_failed_reason_textarea").css("display", "none");
        }
        
        });
        
    }
    
    if( $("#paymentmodeid").length > 0 ) {
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

    }

    $(".button_payment_details").on("click", function(){

        if( $(".payment_details").length > 0 )
        {
            $(".payment_details").hide();
            $(this).parents("tr").next(".payment_details").show();
        }


    } );



});

