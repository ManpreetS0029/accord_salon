/**
 * Created by hardeepsingh on 28/08/18.
 */
function updateInputTopost()
{
    var vals = "";
    $(".select_checks").each(function(){


        if( $(this).prop("checked") == true )
        {
            vals += $(this).val()+","
        }
        $("#group_save_attendance").val(vals);
    });
}

function updateSalary( salaryId, salary, commission, fromdate, callback )
{
    $.ajax({
        url: urls+'/common/updatesalary/',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: $.param( { 'id' : salaryId, 'salary' : salary, 'fromdate' : fromdate , 'commission' : commission } ) ,
        type: 'POST',
        cache: false,
        'datatype': 'json',
        'contenttype' : 'application/json',
        success: function (response) {
            callback(response);
        },
        error: function (response) {
            //$('#errormessage').html(response.message);
        }
    });

    /*$.post(urls+'/common/updatesalary/', { 'id' : salaryId, 'salary' : salary, 'fromdate' : fromdate }, function(data){

        console.log(data);
    }, "json"); */
}

$(".salary_edit_btn").on("click", function(ev){

    var myParent = $(this).parents("tr");
    myParent.next(".editable_rows").show(); //css('display','table-row');


    myParent.hide();

});

$("#selectall").on("click", function(){

    if( $(this).prop("checked") == true )
    {
        $(".select_checks").prop("checked", true);
    }
    else
    {
        $(".select_checks").prop("checked", false);
    }

    updateInputTopost()
});


$(".select_checks").on("click", function(){

    updateInputTopost()
});

$(".salary_save_btn").on("click", function(){

    var myParent = $(this).parents("tr");
     //css('display','table-row');

    var salaryId = myParent.find(".salaryId").val();
    var amount = myParent.find(".salaries").val();
    var commission = myParent.find(".commisions").val();
    var dates = myParent.find(".formdates").val();
    $(this).append(' <i class="icons_load fa fa-spinner fa-pulse"></i>');
         updateSalary( salaryId,amount, commission, dates, function(datas) {

            $(".icons_load").remove();
            var prev = myParent.prev(".salary_data_rows");
             if( typeof datas["error"] != undefined && datas["error"] == "1")
             {
                alert(datas["msg"]);

             }
             else
             {

                 prev.find(".input_dates").html(dates);
                 prev.find(".input_salary").html(amount);
                 prev.find(".input_commission").html(commission+"%");
                 prev.show();
                 myParent.hide();
                 alert("Successfully updated.");
             }

        });

});


