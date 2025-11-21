
$('*[data-plugin="select2"]').select2();

function cityCallBack(data)
{
    $("#cityid").html(data);
    $('#cityid').trigger('change');
    
}


function roundTo(n, digits) {
    var negative = false;
    if (digits === undefined) {
        digits = 0;
    }
    if( n < 0) {
        negative = true;
        n = n * -1;
    }
    var multiplicator = Math.pow(10, digits);
    n = parseFloat((n * multiplicator).toFixed(11));
    n = (Math.round(n) / multiplicator).toFixed(2);
    if( negative ) {
        n = (n * -1).toFixed(2);
    }
    return n;
}
function fetchCity( stateId, selected = '', callBack  = function(data){ } )
{
    $.post( urls+"/common/getcities", { 'stateid' : stateId, 'cityid' : selected, "_token": _token }, function(data) {

	callBack(data)
	//             $("#cityid").html(data);
	//           $('#cityid').trigger('change');
    });
}

function getProductDetails(pid, callback)
{
    $.get(urls+"/common/getproductwithid/"+pid, function(data) {

	callback(data);
    },"json");
}


$(document).ready(function(){


    if( typeof company_edit_state != 'undefined' )
    {
	fetchCity( company_edit_state, company_edit_city, 'cityCallBack' );
    }
    
    if( $("#states").length > 0 )
    {

	$("#states").on("change", function() {

	    fetchCity( $(this).val(), '', 'cityCallBack' );
	    
	});
    }
    
    

    if( $("#expensemasterid").length > 0 )
    {
	$("#expensemasterid").select2( {  });
    }


    if( $(".deletebtn").length > 0 )
    {
	$(document).on("click",".deletebtn", function(e){

	    return confirm("Are you sure to delete?");

	});
    }

    if( $("#issuedate").length > 0 )
    {
	$("#issuedate").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#issuedate").inputmask("99/99/9999");
    }
    if( $("#dob").length > 0 )
    {
	$("#dob").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#dob").inputmask("99/99/9999");
    }

    if( $("#hiringdate").length > 0 )
    {
	$("#hiringdate").datepicker({ 'format' : 'dd/mm/yyyy' });
	$("#hiringdate").inputmask("99/99/9999");
    }

    if( $(".dates").length > 0 ) {
        $(".dates").datepicker({'format': 'dd/mm/yyyy'});
        $(".dates").inputmask("99/99/9999");

    if ($(".dates").length != 1){
            $(".dates").datepicker("setDate", new Date());
    }

    }

    if( $(".dates_nodefault").length > 0 ) {
        $(".dates_nodefault").datepicker({'format': 'dd/mm/yyyy'});
        $(".dates_nodefault").inputmask("99/99/9999");

    }

    if( $(".stop_submit").length > 0 )
    {
	    $(".stop_submit").keydown(function(event){
	if(event.keyCode == 13) {
	    event.preventDefault();
	    return false;
	}
	    });

    }



    
});
