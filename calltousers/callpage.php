<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<?php
include "extensions.php";
$extension_id = $_GET['ext']?$_GET['ext']:$extensions[0];
$user = $user_pass[0];
$password = $user_pass[1];
?>


<div id="last_update"></div>
<div id="call_status"></div>

    <div id='details' style='display: none;'>
<div id="order_details"></div>
        <div id="table_abandonment" style='float:left;clear: left;'>

        </div>

        <div id="table_details" style='float:left;clear: left;margin: 10px;'>

        </div>
        <div id="order_items"  style='float:left;clear: left;margin: 10px;'>
        </div>
            <div id="ring" style='visibility: hidden;float:left;clear: left;margin: 10px;'></div>
        <div id="end_call"  style='float:left;clear: left;margin: 10px;'>
            <input type="button" onclick="late_call()"   style='float:left;background-color:#2B17D0;margin-left: 5px;color: white;padding: 5px;cursor: pointer;' id='late_call_button' value='Call Later'>
            <input type="button" onclick="done_call()"   style='float:left;background-color:red;margin-left: 5px;color: white;padding: 5px;cursor: pointer;' id='end_call_button' value='Done'>
          </div>
    </div>
<div id='missed_call' style="border:1px solid #ccc;float: right"></div>
<script type= text/javascript>
    
  function late_call(){
      var order_id = $('#user_order_id').text()
      var user_id = $('#user_id').text()
    $.post( "get_order_phone_number.php",{post_name:'late_call',order_id:order_id,user_id:user_id}, function( data ) {
     doSetTimeout()
    $('#details').hide()
     }); 
      
}
  function done_call(){
  var order_id = $('#user_order_id').text()
            var user_id = $('#user_id').text()
    $.post( "get_order_phone_number.php",{post_name:'done_call',order_id:order_id,user_id:user_id}, function( data ) {
     doSetTimeout()
    $('#details').hide()
     }); 
}

  function late_number(phone,order_id,user_id){

    $.post( "get_order_phone_number.php",{post_name:'late_call',order_id:order_id,user_id:user_id}, function( data ) {

             $('.'+phone).remove()

     }); 
      
}
  function done_number(phone,order_id,user_id){

    $.post( "get_order_phone_number.php",{post_name:'done_call',order_id:order_id,user_id:user_id}, function( data ) {
             $('.'+phone).remove()
     }); 
}







      function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
}
    
    
$( document ).ready(function() {

//        $.post( "get_order_phone_number.php",{post_name:'abandonment'}, function( data ) {
//        console.log(data)
//      });
         

      
    doSetTimeout()
        
 
   });
   
   function  doSetTimeout(){

    setTimeout(function() {
                var ext = getUrlParameter('ext');
         $.post( "get_order_phone_number.php",{post_name:'curl',ext:ext}, function( data ) {
     if($.trim(data) != 'NO' ){
                   var result = $.parseJSON(data);
           var order_id = result[2];
           var phone_number = result[1];
           var user_id = result[3];
           var type = result[4];
           var res = 'call now';
  
           var status = "<strong>"+res+"</strong>"
           var html = "order id = <span id='user_order_id'>"+order_id+"</span><br>"
           html += "user_id = <span id='user_id'>"+user_id+"</span><br>"
           html += "call  cause = <span id='user_call_type'>"+type+"</span><br>"
           html += "phone number = <span id='user_phone_number'>"+phone_number+"</span><br>"
           $('#order_details').html(html)
           $('#call_status').html(status)
                 $('.'+phone_number).remove()
        $('#ring').html('<audio id="player"  src="oldphone.wav" controls="true"  autoplay="true"></audio>')
  if(type == 'occasion'){
          
        $.post( "get_order_phone_number.php",{post_name:'details',order_id:order_id}, function( data ) {
            if(data){
                   var result = $.parseJSON(data)
                    var table_user = "Information About Customer <br><table border='1' class='user_details'>\n\
                                        <tr>\n\
                                            <td>Customer  Name</td>\n\
                                            <td>Customer Last  Name</td>\n\
                                            <td>Email</td>\n\
                                            <td>Recipient Name</td>\n\
                                            <td>Occassion</td>\n\
                                            <td>Delivery Date</td>\n\
                                            <td>Customer Comments</td>\n\
                                            <td>Customer Note</td>\n\
                                            <td>Total Price</td>\n\
                                        </tr>\n\
                                        <tr>\n\
                                            <td>"+result.user_details.first_name+"</td>\n\
                                            <td>"+result.user_details.last_name+"</td>\n\
                                            <td>"+result.user_details.user_email+"</td>\n\
                                            <td>"+result.user_details.recipient_name+"</td>\n\
                                            <td>"+result.user_details.order_occasion_name+"</td>\n\
                                            <td>"+result.user_details.ddate+"</td>\n\
                                            <td>"+result.user_details.customer_comments+"</td>\n\
                                            <td>"+result.user_details.customer_note+"</td>\n\
                                            <td>"+result.user_details.order_total+"</td>\n\
                                        </tr>\n\
                                        "
                    table_user +="</table>";
                    var table_order = "Products List <br><table  class='order_details' border='1'><tr><td>Product Name</td><td>Product SKU</td><td>Quantity</td></tr>";
                           
                   $.each(result.order_details,function( ) {
                            var order = $(this)[0];
                              table_order +=  "<tr>\n\
                                            <td>"+order.order_item_name+"</td>\n\
                                            <td>"+order.order_item_sku+"</td>\n\
                                            <td>"+order.product_quantity+"</td>\n\
                                        </tr>";
                                               
                   })
                    table_order +="</table>";
                    
                   $('#table_details').html(table_user)
                   $('#order_items').html(table_order)
                       $('#table_abandonment').html('')
                    $('#details').show()
                  
               
            }
      });
      
      }else if(type == 'missed'){
    $('#table_details').html('')
     $('#order_items').html('')
         $('#table_abandonment').html('')
   $('#details').show()
          
   } else if(type == 'abandonment'){
                      
             
                         $.post( "get_order_phone_number.php",{post_name:'abandonment',user_id:user_id}, function( data ) {
            if(data){
      
                   var result = $.parseJSON(data)
              if(result.user_details){
                  
                    var table_user = "Information About Customer <br><table border='1' class='user_details'>\n\
                                        <tr>\n\
                                            <td>First Name</td>\n\
                                            <td>Last  Name</td>\n\
                                            <td>Customer city</td>\n\
                                            <td>Customer Address</td>\n\
                                            <td>Zip</td>\n\
                                        </tr>\n\
                                        <tr>\n\
                                            <td>"+result.user_details.first_name+"</td>\n\
                                            <td>"+result.user_details.last_name+"</td>\n\
                                            <td>"+result.user_details.city+"</td>\n\
                                            <td>"+result.user_details.address_1+"</td>\n\
                                            <td>"+result.user_details.zip+"</td>\n\
                                        </tr>\n\
                                        "
                    table_user +="</table>";
                    var timestamp = result.abandonment.date
                   var date = new Date(timestamp*1000);
                   var year =  date.getUTCFullYear();
                   var month =   date.getUTCMonth()+1;
                   var day =  date.getUTCDate();
                    var hours = date.getUTCHours();
                    var minutes = "0" + date.getUTCMinutes();
                    var seconds = "0" + date.getUTCSeconds();
                var formattedTime = day + '-' + month + '-' + year +'  '+ hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);


                    var table_abandonment = "Time  Leave Site   = <span style='font-weight: bold;'>"+formattedTime+"</span>";
                                       
                    var table_order = "Products List <br><table  class='order_details' border='1'><tr><td>Product Name</td><td>Product SKU</td></tr>";
                           
                   $.each(result.abandonment_product_list,function( ) {
                            var order = $(this)[0];
                              table_order +=  "<tr>\n\
                                            <td>"+order.name+"</td>\n\
                                            <td>"+order.sku+"</td>\n\
                                        </tr>";
                                               
                   })
                    table_order +="</table>";
                    
                   $('#table_details').html(table_user)
                   $('#order_items').html(table_order)
                   $('#table_abandonment').html(table_abandonment)
              }
                    $('#details').show()

            }

                                    })    

                  
        } 
              check_new_call(phone_number,order_id,type,user_id)
    }          
     
    else{

                    var res = 'no call now';
            $('#call_status').html(res)                    
            var d = new Date();
            var t = d.toLocaleTimeString()
            $('#last_update').html('last update  '+ t)  

         $('#order_details').html('')
          $('#table_details').html('')
           $('#order_items').html('')
           $('#ring').html('')
           $('#details').hide()    
           
            doSetTimeout();                
    }

    })

    });
}

function check_new_call(phone_number,order_id,type,user_id){
    setTimeout(function() {
        var ext = getUrlParameter('ext');
          $.post( "get_order_phone_number.php",{post_name:'check',phone_number:phone_number,ext:ext}, function( data ) {
                       var d = new Date();
            var t = d.toLocaleTimeString()
            $('#last_update').html('last update  '+ t) ; 
     if($.trim(data) != 'NO' ){ 
         archive_old_call(phone_number,order_id,type,user_id)

          doSetTimeout(); 
      }else{
          check_new_call(phone_number,order_id,type,user_id)
      }
          })
    },4000)
    
}

 function archive_old_call(phone_number,order_id,type,user_id){
phone_number = $.trim(phone_number)
         var html = "<div class='"+phone_number+"'  style='border:1px solid #ccc;'>order id = <span >"+order_id+"</span><br>"
           html += "phone number =<span>"+phone_number+"</span><br>"  
           html += "user id =<span>"+user_id+"</span><br>"  
           html += "call  cause=<span>"+type+"</span><br>"  
            html += ' <div style="float:left;" class="inputs">'
            html +='<input type="button" onclick="late_number('+phone_number+','+order_id+','+user_id+')"   style="float:left;background-color:#2B17D0;margin-left: 5px;color: white;padding: 5px;cursor: pointer;"  value="Call Later">'
            html +=' <input type="button" onclick="done_number('+phone_number+','+order_id+','+user_id+')"   style="float:left;background-color:red;margin-left: 5px;color: white;padding: 5px;cursor: pointer;"  value="Done">'
            html +='</div><div style="height:60px"></div></div>';
     $('#missed_call').append(html)

 }

</script>


<style>
    .order_details tr td,.user_details tr td{
        padding: 10px;
        
    }
    .order_details  tr:first-child td,.user_details tr:first-child td{
        padding: 10px;
        color:red;
        font-size: 14px;
        
    }
</style>