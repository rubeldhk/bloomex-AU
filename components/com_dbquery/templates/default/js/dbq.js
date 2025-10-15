/* - - - - - - - - - - - - - - - - - - - - - - -
 JavaScript
 14 August 2006 16:45:06
 - - - - - - - - - - - - - - - - - - - - - - - */


wFORMS.behaviors['validation'].showAlert = function (nbTotalErrors) {
    var placeHolder = document.getElementById('alertMessagePlaceHolder');
    placeHolder.innerHTML = wFORMS.arrErrorMsg[8].replace('%%',nbTotalErrors); 
    placeHolder.style.display = 'block';
}

function clearForm(formId){
         var foo=document.getElementById(formId);
         foo.reset();
         var i;
         var tags=foo.getElementsByTagName("input");
         for(i=0;i<tags.length;i++){
             switch (tags[i].type.toUpperCase()){
                    case "TEXT":
                         tags[i].value="";
                         break;
                    case "CHECKBOX":
                         tags[i].checked=false;
                         break;
                    case "RADIO":
                         tags[i].checked=false;
                         break;
             }
         }
         tags=foo.getElementsByTagName("textarea");
         for(i=0;i<tags.length;i++){
             tags[i].value="";
         }
         tags=foo.getElementsByTagName("option");
         for(i=0;i<tags.length;i++){
             tags[i].selected=false;
             tags[i].parentNode.selectedIndex=0;
         }
}