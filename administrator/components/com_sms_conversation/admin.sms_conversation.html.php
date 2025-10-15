<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<?php
/**
* @version $Id: admin.content.html.php 4070 2006-06-20 16:09:29Z stingrey $
* @package Joomla
* @subpackage Content
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


/**
* @package Joomla
* @subpackage Content
*/
class HTML_sms_conversation {

	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/

	function showsms_texts( &$rows,  $pageNav,$search ) {
		global $my, $acl, $database, $mosConfig_offset;
		mosCommonHTML::loadOverlib();
		?>


		<form action="index2.php?option=com_sms_conversation" method="post" name="adminForm">
                    <table class="adminheading" style="width: 300px;float: left;">
		<tr>
			<td align="left">
			Filter:
			</td>
			<td>
			<input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="2%">
			#
			</th>
			<th width="2%">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th align="center" width="15%">
			Number
			</th>
			<th align="center" width="15%">
			Title
			</th>
            <th align="center" width="15%">
                Last Message
            </th>
            <th align="center" width="15%">
                Last Action Date
            </th>
            <th align="center" width="15%">
                Last Action Type
            </th>

		  </tr>
		<?php
		$k = 0;
		$nullDate = $database->getNullDate();
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			$link 	= 'index2.php?option=com_sms_conversation&task=edit&hidemainmenu=1&id='. $row['id'];

			//$date = mosFormatDate( $row->date, _CURRENT_SERVER_TIME_FORMAT );
            $checkbox_row = (object) $row;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $checkbox_row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td  align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
				<?php echo $checked; ?>
				</td>
				<td align="center">

					<a   href="<?php echo $link; ?>" title="Edit Number">
					<?php echo htmlspecialchars($row['number'], ENT_QUOTES); ?>
					</a>
                                                                                        <div class="new_message" id="number_<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $row['number']), ENT_QUOTES); ?>">NEW MESSAGE</div>

				</td>
				<td align="center">

					<a href="<?php echo $link; ?>" title="Edit Number">
					<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>
					</a>

				</td>
                <td align="center">
                        <?php echo $row['last_message']; ?>
                </td>
                <td align="center">
                        <?php echo $row['action_date']; ?>
                </td>
                <td align="center">
                        <?php echo $row['last_action_type']; ?>
                </td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>
		<?php mosCommonHTML::ContentLegend(); ?>

		<input type="hidden" name="option" value="com_sms_conversation" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
<script language="javascript" type="text/javascript">

 jQuery(document).ready(function() {

function check_new_message(){
    var   url = '/administrator/components/com_sms_conversation/send_get_sms.php';

        var post_data = {
            action:'check_new_messages'     
        }
          jQuery.post( url, post_data, function( data ) {
               var result = $.parseJSON(data);
              if(result){
                      for (var prop in result) {
                            jQuery('#number_'+result[prop]).show()
                        }
              }

          });  

}
check_new_message()

      });

		</script>
                
                
                <style>
                    .new_message {
                            color: blue;
                            position: relative;
                            left: 85px;
                            top: -12px;
                            font-weight: bold;
                            display: none;
                            width: 100px;
                    }
                </style>
		<?php
	}


	/**
	* Writes the edit form for new and existing content item
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param mosContent The category object
	* @param string The html for the groups select list
	*/
	function editsmstext( &$row, $option,$list,$operator,$number ) {
		global $database;
		mosMakeHtmlSafe( $row );
        mosCommonHTML::loadBootstrap(true);
		$tabs = new mosTabs(1);
		?>

		<script language="javascript" type="text/javascript">

 jQuery(document).ready(function() {
var message_ides = "<?php echo $list;?>";
var phone_number = "<?php echo $row->number;?>"
 var conversation_id = "<?php echo $row->id;?>"
function get_history(){
    scroll_t = jQuery('.reply_history').scrollTop();
        var   url = '/administrator/components/com_sms_conversation/send_get_sms.php';
        var post_data = {
            action:'get_history',
            number:jQuery('#phone_number').val()
        }
          jQuery.post( url, post_data, function( data ) {
                     if(data){
                         jQuery('#history').html(data)
                         jQuery('.reply_history').scrollTop(scroll_t);
                     }else{
                         jQuery('#history').html('there is no sms')
                     }
          });  
}
get_history()



   function  chat_update(){
    setTimeout(function() {
        get_history()
        chat_update()
            },20000);
}

    chat_update()

       jQuery( '#send_sms' ).click( function( e ) {
                         jQuery('#send_sms').text('Sending .... ')
                         jQuery('#msg').html('')
           var   url = '/administrator/components/com_sms_conversation/send_get_sms.php';

                if((conversation_id == ''))   {
                    
                                       
                    var post_data = {
                        message_id :message_ides,
                        action:'send_sms',
                        text:jQuery('#introtext').val(),
                        title:jQuery('#title').val(),
                        number:jQuery('#phone_number').val(),
                        operator:'<?php echo $operator;?>',
                        new_number:1
                        
                    }
          jQuery.post( url, post_data, function( data ) {
              if(data.search("{--1--}error")>0){
                  var res = data.split("{--1--}");
                  jQuery('#msg').html(res[0])
              }else{
                  conversation_id = true;
                  jQuery('#phone_number').val(data)
                  jQuery('#msg').html('your sms sent succesfull')
              }
              jQuery('#send_sms').text('Send SMS')
          });   
  
                }else{
                    
                    
                    var post_data = {
                        message_id :message_ides,
                        action:'send_sms',
                        text:jQuery('#introtext').val(),
                        title:jQuery('#title').val(),
                        number:jQuery('#phone_number').val(),
                        operator:'<?php echo $operator;?>',
                        new_number:0
                    }
          jQuery.post( url, post_data, function( data ) {
              if(data.search("{--1--}error")>0){
                      var res = data.split("{--1--}");
                      jQuery('#msg').html(res[0])
                  }else{
                  jQuery('#phone_number').val(data)
                         jQuery('#msg').html('your sms sent succesfull')
                     }
                         jQuery('#send_sms').text('Send SMS')
          });  


                }
        
        } );
        
        
        
        
        
        jQuery('#some_text').click(function(){
            jQuery('#select_text').show()
        })

         jQuery('#close_some_text').click(function(){
            jQuery('#select_text').hide()
        })
        
         jQuery('.text_class').toggle(function(){
            jQuery(this).parent('.select_text_list').find('textarea').removeClass('read_only_class').removeAttr('disabled')
        },function(){
            jQuery(this).parent('.select_text_list').find('textarea').addClass('read_only_class').attr('disabled','disabled')
        })
        
        
         jQuery('.choose_text').click(function(){
            jQuery('#introtext').val(jQuery(this).parent('.select_text_list').find('textarea').val())
            jQuery('#select_text').hide()
        })
        
        
                    });

		</script>
                <style>
                    .read_only_class{
                          background-color: rgb(177, 177, 177);
                    }
                    #select_text{
                        width: 1500px;
                        top: 100px;
                        position: absolute;
                        background-color: #ccc;
                        min-height: 200px;
                        left: 100px;
                        padding-bottom: 20px;
                    }
                    #close_some_text{
                        width: 20px;
                        float: right;
                        top: 5px;
                        cursor: pointer;
                        right: 5px;
                        background-color: #A19494;
                       color: white;
                        position: absolute;
                    }
                    .text_class{
                        width: 200px;
                          height: 30px;
                          background-color: #B29E9E;
                          margin-bottom: 6px;
                          cursor: pointer;
                          font-weight: bold;
                          margin-top: 5px;
                          border-radius: 10px;
                          float: left;
                         clear: both;

                    }
                    .text_class span{
                          padding-top: 5px;
                          display: block;
                    }
                    .select_text_list{
                        float: left;
                        width: 1470px;
                    }
                    .choose_text{
                            float: left;
                            margin-left: 5px;
                            background-color: rgb(178, 158, 158);
                            border: 1px solid #ccc;
                            border-radius: 10px;
                            height: 30px;
                            font-weight: bold;
                            margin-top: 5px;
                            color: white;
                    }
                    .select_text_list textarea{
                       width: 1130px;
                        height: 30px;
                        float: left;
                        margin-left: 10px;
                        margin-top: 5px;
                        font-weight: bold;
                        font-size: 13px;
                        padding-left: 5px;
                    }

                    .customer_reply{
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 14px;
                        border-bottom: 1px solid #ccc;
                        margin-bottom: 5px;
                        padding: 3px 10px;
                        width: 100% ;
                    }
                    .outgoing{
                        float: left;
                        text-align: left;
                    }
                    .incoming{
                        float: right;
                        text-align: right;
                    }
                    .incoming .text{
                        text-align: left;
                        padding: 17px;
                    }
                    .incoming .text_author_icon{
                        text-align: center;
                        float: left;
                        color: white;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 42px;
                        margin-right: 15px;
                        background: #56c1e7;
                        border-radius: 50% 50%;
                        height: 50px;
                        width: 50px;
                    }
                    .incoming .text_author_icon::after{
                        content: 'C';
                    }

                    .incoming .text_time{
                        text-align: right;
                    }
                    .incoming .text_author{
                        text-align: left;
                        padding-bottom: 7px;
                    }

                    .outgoing .text{
                        text-align: right;
                        padding: 10px 0px;
                    }
                    .outgoing .text_author_icon{
                        text-align: center;
                        float: right;
                        color: white;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 42px;
                        margin-left: 15px;
                        background: #fa6f57;
                        border-radius: 50% 50%;
                        height: 50px;
                        width: 50px;
                    }
                    .outgoing .text_author_icon::after{
                        content: 'A';
                    }
                     .text_time, .text_author{

                        color: #999;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-size: 12px;
                    }
                    .outgoing .text_time{
                        text-align: left;
                    }
                    .outgoing .text_author{
                        text-align: right;
                        padding-bottom: 7px;
                    }

                </style>


		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="edit">
			SMS Item:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>

			</th>
		</tr>
		</table>

		<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="40%" valign="top">
           			<table width="100%" class="adminform">
				<tr>
					<td width="100%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<th colspan="2">
							Item Details
							</th>
						</tr>
						<tr>
							<td>
							Title:
							</td>
							<td>
							<input class="text_area" type="text" name="title" id="title" size="30" maxlength="100" value="<?php echo stripcslashes($row->title); ?>" />
							</td>

						</tr>
						<tr>
							<td>
							Phone Number:
							</td>
							<td>
							<input class="text_area" type="text" name="number" size="30" maxlength="100" id="phone_number" value="<?php echo $row->number??$number; ?>" />
							</td>

						</tr>
						<tr>
							<td>
							Some Templates:
							</td>
							<td>
                                     <input  type="button"  size="30" maxlength="100" style="cursor:pointer" id="some_text" value="Select Template" />
							</td>

						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="100%">
					SMS Text:
					<br /><br />
                                        <strong> You can use {user_name} , {user_last_name} , {user_number} instead name,lastname and number and information will  inserted  automatically for each user
                                         </strong> <br /><span style="color:red;">The maximum length of an SMS message is 160 characters </span><br /><br />  <?php

                                         $text = "Hi it's Emily from Bloomex - I'm following up with your recent purchase - we just need a little more information to schedule delivery - is this a good time?";
 ?>

                                         <textarea class="text_area" style='width: 72%;height: 200px;' id='introtext' name="introtext" maxlength="160"><?php echo strip_tags($row->text?$row->text:$text); ?></textarea>

					</td>


				</table>
                
                 <button style="bacground-color:#ccc;cursor: pointer;" type="button" id="send_sms">Send SMS</button>
                 <div style="color:red;" id="msg"><?php echo isset($_GET['msg']) ? $_GET['msg'] : '';?></div>
			</td>

			<td valign="top" width="60%" style="padding: 3px;">

				<table class="adminform">
				<tr>
					<th colspan="2">
					SMS CHAT
					</th>
				</tr>

				</table><br />
				<table width="100%">
				<tr>
					<th colspan="2">
					<div id="history"></div>
					</th>
				
				</tr>

				</table><br />

			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="mask" value="0" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
                <div id="select_text" style="display: none;">
                    <div id="close_some_text" class="some_text">X</div>

                    <div class="select_text_list" ><div class="text_class"> <span>Introduction Text   </span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Introduction Text" />
                        <textarea class="read_only_class" disabled="disabled">Hi it's Emily from Bloomex - I'm following up with your recent purchase - we just need a little more information to schedule delivery - is this a good time?</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>"Yes" Response</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="'Yes' Response" />
                        <textarea class="read_only_class" disabled="disabled">Ok, perfect - let's start with delivery address.</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>"No" Response</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="'No' Response" />
                        <textarea class="read_only_class" disabled="disabled">Ok, no problem, you should have received an email with link to cart to complete sale online - text us back at your convenience or call 1-855-717-1222. We are open 24/7</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>Delivery Date?</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Delivery Date?" />
                        <textarea class="read_only_class" disabled="disabled">When would you like to schedule Delivery?</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>Extras?</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Extras?" />
                        <textarea class="read_only_class" disabled="disabled">Would you like to add a Vase, Card, Chocolates or Teddy Bear to your order?</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>Payment Type?</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Payment Type?" />
                        <textarea class="read_only_class" disabled="disabled">Will you be paying with Visa, MasterCard or Amex?</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>Credit  Card Info?</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Credit  Card Info?" />
                        <textarea class="read_only_class" disabled="disabled">Please provide the following: Credit Card Number, expiry and 3 digit code on back of card.</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>Thank you very much for your</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="Thank you very much for your" />
                        <textarea class="read_only_class" disabled="disabled" >Thank you very much for your business. Have a wonderful day.</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>New Zealand review</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="New Zealand review" />
                        <textarea class="read_only_class" disabled="disabled" >NAME, thank you for chatting with me today. Here is the link where you can leave a review: https://t.ly/3eufy Thank you for your support! Cliff from Bloomex</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>ADELAIDE REVIEW</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="ADELAIDE REVIEW" />
                        <textarea class="read_only_class" disabled="disabled" >NAME, thank you for chatting with me today. Here is the link where you can leave a review: https://t.ly/dCwhD. Thank you for your support! Cliff from Bloomex</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>SYDNEY REVIEW</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="SYDNEY REVIEW" />
                        <textarea class="read_only_class" disabled="disabled" >NAME, thank you for chatting with me today. Here is the link where you can leave a review: https://rb.gy/r7uzvz Thank you for your support! Cliff from Bloomex</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>MELBOURNE REVIEW</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="MELBOURNE REVIEW" />
                        <textarea class="read_only_class" disabled="disabled" >NAME, thank you for chatting with me today. Here is the link where you can leave a review: https://rb.gy/bpnk2w Thank you for your support! Cliff from Bloomex</textarea>
                    </div>
                    <div class="select_text_list" ><div class="text_class"> <span>PERTH REVIEW</span></div>
                        <input   type="button"  size="30" maxlength="100" style="cursor:pointer" class="choose_text" value="PERTH REVIEW" />
                        <textarea class="read_only_class" disabled="disabled" >NAME, thank you for chatting with me today. Here is the link where you can leave a review: https://shorturl.at/uzJS9 Thank you for your support! Cliff from Bloomex</textarea>
                    </div>
                </div>


		<?php

	}


}
?>