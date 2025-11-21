function getAllSalaryDetails( staffId, months, years )
{

    //ajax call
    $.get(urls + "/common/getsalarydetails/?staffid="+staffId+"&months="+months+"&years="+years, function (data) {

        console.log( data );
        var htmlData = "";

        if( typeof data.result["official_salary"] != undefined)
        {
            htmlData += '<p>Salary: '+data["result"]["official_salary"]+'</p>';
            htmlData += '<p>Attendance: '+data["result"]["attendance_count"]+'</p>';
            htmlData += '<p>Calculated Salary: '+data["result"]["calculated_salary"]+'</p>';
            htmlData += '<p>Salary Balance: '+data["result"]["balance_amount"]+'</p>';
            htmlData += '<p>Total Balance: '+data["total_balance"]+'</p>';
        }
        $("#salary_info").html(htmlData);


    }, "json");
}

$(document).on("change", "#staffname", function (ev) {

    var staffId = $("#staffname").val();
    var salary_month = $("#salary_month").val();
    var years = $("#salary_year").val();


        getAllSalaryDetails( staffId, salary_month, years );



});

$(document).on("blur", "#salary_year", function (ev) {

    var staffId = $("#staffname").val();
    var salary_month = $("#salary_month").val();
    var years = $("#salary_year").val();
   getAllSalaryDetails( staffId, salary_month, years );

});
$(document).on("change", "#salary_month", function (ev) {

    var staffId = $("#staffname").val();
    var salary_month = $("#salary_month").val();
    var years = $("#salary_year").val();
    getAllSalaryDetails( staffId, salary_month, years );

});

if( typeof  editSalaryPage != undefined )
{
    var staffId = $("#staffname").val();
    var salary_month = $("#salary_month").val();
    var years = $("#salary_year").val();
    getAllSalaryDetails( staffId, salary_month, years );
}