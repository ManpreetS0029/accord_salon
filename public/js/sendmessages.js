/**
 * Created by hardeepsingh on 23/10/18.
 */
$(document).ready(function(){

    if( $("#allchecked").length > 0 )
    {
        $("#allchecked").on("click", function(){

            if( $(this).prop("checked") == true )
            {
                $(".client_id_boxes").prop("checked", true);
            }
            else
            {
                $(".client_id_boxes").prop("checked", false);
            }
        });
    }

});


var textAreaObj = document.getElementById("msgbox");
var displayObj = document.getElementById("textarealength");
var displaybox = document.getElementById("displaybox");
textAreaObj.onkeyup = function()
    {
        displayObj.innerText = this.value.length;
        if( this.value.length > 160 )
        {

            displaybox.style.color = "#f00";
            textAreaObj.style.border = "2px solid #f00";

        }
        else
        {
            displaybox.style.color = "#000";
            textAreaObj.style.border = "2px solid #000";
        }
    }

