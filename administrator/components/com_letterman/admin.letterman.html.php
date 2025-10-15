<?php
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @author: soeren
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_letterman {
	function shownewsletter( &$rows, $search, $pageNav, $option ) {

		  ?>
  <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>		  
	<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
	<form action="index2.php" method="post" name="adminForm">
	<table class="adminheading">
		<tr>
			<th colspan="2" class="inbox"><?php echo LM_NM ?></th>
		</tr>
		<tr>
			<td width="75%" nowrap="nowrap">&nbsp;</td>
			<td><?php echo _SEARCH_TITLE ?>:
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
	</table>
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title" width="200"><?php echo _E_SUBJECT ?></th>
			<th class="title" width="200"><?php echo LM_MESSAGE ?></th>
			<th class="title" width="100"><?php echo _E_CREATED ?></th>
			<th class="title" width="100"><?php echo LM_LAST_SENT ?></th>
			<th class="title" width="65"><?php echo LM_SEND_NOW ?></th>
			<th class="title" width="65"><?php echo _CMN_PUBLISHED ?>?</th>
			<th class="title" width="154"><?php echo LM_CHECKED_OUT ?></th>
		</tr>
		<?php
		$k = 0;
		$i = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$message = $row->message;
			$message = strip_tags($message);
			if (strlen($message) > 80) $message = substr($message, 0 , 78) . " ...";
			?>
			<tr class="row<?php echo $k; ?>">
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);"></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','edit')"><?php echo $row->subject; ?></a></td>
                <td><?php echo $message; ?></td>
                <td><?php echo $row->created; ?></td>
                <td><?php echo $row->send; ?></td>
              <?php
              $task = $row->published ? 'unpublish' : 'publish';
              $now = date( "Y-m-d h:i:s" );
              if ($now <= $row->publish_up && $row->published == "1") {
              	$img = 'publish_y.png';
              } else if (($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") && $row->published == "1") {
              	$img = 'publish_g.png';
              } else if ($now > $row->publish_down && $row->published == "1") {
              	$img = 'publish_r.png';
              } elseif ($row->published == "0") {
              	$img = "publish_x.png";
              }
              $times = '';
              if (isset($row->publish_up)) {
              	if ($row->publish_up == '0000-00-00 00:00:00') {
              		$times .= "<tr><td>Start: Always</td></tr>";
              	} else {
              		$times .= "<tr><td>Start: $row->publish_up</td></tr>";
              	}
              }
              if (isset($row->publish_down)) {
              	if ($row->publish_down == '0000-00-00 00:00:00') {
              		$times .= "<tr><td>".LM_NO_EXPIRY."</td></tr>";
              	} else {
              		$times .= "<tr><td>"._E_FINISH_PUB.": ".$row->publish_down."</td></tr>";
              	}
              }
?>
                <td align="center"><a href="#sendNow" onclick="return listItemTask('cb<?php echo $i; ?>','sendNow')"><img src="../components/com_letterman/images/mail_send.png" alt="<?php echo LM_SEND_NOW ?>" border="0" /></a></td>
				<td align="center"><a href="javascript: void(0);" onmouseover="return overlib('<table border=0 width=100% height=100%><?php echo $times; ?></table>', CAPTION, 'Publish Information', BELOW, RIGHT);" onmouseout="return nd();"  onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="" /></a></td>
                <td><?php echo $row->editor != "" ? $row->editor : "&nbsp;";?></td>
				<?php
				$k = 1 - $k;

			}?>
		</tr>
		<tr>
			<th align="center" colspan="8">
				<?php echo $pageNav->writePagesLinks(); ?></th>
		</tr>
		<tr>
			<td align="center" colspan="8">
				<?php echo $pageNav->writePagesCounter(); ?></td>
		</tr>
		
		<tr>
			<td align="center" colspan="8">
				<?php echo _PN_DISPLAY_NR; $pageNav->writeLimitBox(); ?></td>
		</tr>
		</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
	</form>
	<?php
	}
	/**
	 * Function to display a "compose newsletter" form
	 * @author soeren
	 * @author Adam van Dongen
	 *
	 * @param unknown_type $row
	 */
	function composeNewsletter( $contents=null ) {
		global $database, $mosConfig_absolute_path, $mosConfig_live_site, $option, $my, $lm_params;
		echo '<script src="components/com_letterman/letterman.js" type="text/javascript"></script>';
  		
		mosCommonHTML::loadOverlib();
		
		$tabs = new mosTabs(0);
		$task = mosGetParam( $_REQUEST, 'task');
		$pathA = $mosConfig_absolute_path .'/images/stories';
  		$pathL = $mosConfig_live_site .'/images/stories';
?>
  		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="adminForm">

		<table cellspacing="0" cellpadding="0" width="100%" class="adminheading">
		  <th class="edit"><?php echo LM_COMPOSE_NEWSLETTER; ?>:</th></tr>
		</table>
    	<br />
		<table class="adminform">
			<tr>
				<td width="60%" valign="top">
    				<table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
          				<tr>
		    				<td width="100"><strong><?php echo _E_SUBJECT; ?></strong></td>
		    				<td width="85%"><input type="text" name="subject" class="inputbox" size="40" value="<?php echo @$_REQUEST['subject']; ?>" /></td>
		    			</tr>
		    			<tr>
		    				<td valign="top"><strong><?php echo _E_CONTENT; ?>:</strong></td>
		    				<td><strong><?php echo LM_USABLE_TAGS; ?>:</strong><br />
		    			  	[NAME]<br />
		    			  	[ATTACHMENT filename=""]<br />
		    			  	[CONTENT id="##"]<br />
		    			</tr>
		    			<?php
		    			if( $task == 'composeNow' ) {
		    				echo '<tr><td style="vertical-align:top;font-weight:bold;">Rendered HTML content:</td><td>';
		    					editorArea( 'editor1',  $contents['html_message'] , 'html_message', 750, 450, '100', '30' ) ; 
		    				echo "</td></tr>\n";
		    				echo '<tr><td style="vertical-align:top;font-weight:bold;">Rendered Text content:</td><td>
		    					<textarea name="message" cols="90" rows="30">'. $contents['message'] .'</textarea>
		    					<input type="hidden" name="nl_content" value="'.urlencode($_POST['nl_content']).'" />
		    				</td></tr>';
		    				
		    			}
		    			else {
		    				$content = urldecode( @$_REQUEST['nl_content'] );
		    				if( get_magic_quotes_gpc()) {
		    					$content = stripcslashes($content);
		    				}
		    			?>
		    			<tr>
		    			  	<td>&nbsp;</td>
	    					<td>
	    						<textarea name="nl_content" cols="70" rows="20"><?php echo $content  ?></textarea>
	    					</td>
	    				</tr>
	    				<?php
		    			}
	    				?>
    				</table>
    			</td>
    			<?php
    			if( $task == 'compose' ) {
    				?>
    			
			  		<td valign="top" width="40%">
			    		<?php
			    		$tabs->startPane("content-pane");
			    		
			    		$tabs->startTab(LM_CONTENT_ITEMS,"content-page");
			    		?>
			    		
			    		<table class="adminform" width="100%">
			    		<tr>
			    			<th><?php echo LM_ADD_CONTENT; ?></th>
			    		</tr>
			    			<tr>
			    				<td><?php echo LM_ADD_CONTENT_TOOLTIP; ?></td>
			    			</tr>
			    			<tr>	
			    				<td>
			    				 <?php echo lm_getContentSelectList(); ?> 
			    				</td>
			    			</tr>
			    		</table>
			    		<?php
			    		$tabs->endTab();
			    		$tabs->startTab(LM_ATTACHMENTS,"attachments-page");
			    		?>
			    		
			    		<table class="adminform" width="100%">
			    		<tr>
			    			<th>
			    			<?php echo LM_ATTACHMENTS; ?>
			    			</th>
			    		</tr>
			    			<tr>
			    				<td><?php echo mosToolTip(sprintf(LM_ATTACHMENTS_TOOLTIP, $lm_params->get('attachment_dir', '/media'))). ' '.LM_MULTISELECT; ?></td>
			    			</tr>
			    			<tr>	
			    				<td>
			    				  <?php
			    				  $files = lm_getAttachments($mosConfig_absolute_path . $lm_params->get('attachment_dir', '/media'));
			    				  ?>
			    				  <select name="nl_attachments[]" multiple="multiple" style="width: 250px;" size="10">
			    				    <?php
			    				    if(sizeof($files) > 0){
			    				      	foreach ($files as $file){
			    				        	if(in_array($file, @$_REQUEST['nl_attachments'])){
			    				          		echo '<option selected="selected">' . $file . '</option>';
			    				        	}
			    				        	else {
			    				          		echo '<option>' . $file . '</option>';
			    				        	}
			    				      	}				      
			    				    }
			    				    ?>
			    				  </select>
			    				</td>
			    			</tr>
			    		</table>
			    		<?php
			    		$tabs->endTab();
			    		$tabs->endPane();
			    		?>
	    			</td>
	    			<?php
    			}
    			?>
    		</tr>
    	</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
	  	<input type="hidden" name="task" value="" />
	</form>
  	<?php

	}
	
	function sendNewsletter( &$row, $option , $grouplist, $admin_email ) {
		global $lm_params;
		
	  	?>
	  <script type="text/javascript">
	  function submitbutton(pressbutton) {
	  	var form = document.adminForm;
	  	if (pressbutton == 'cancel') {
	  		submitform( pressbutton );
	  		return;
	  	}
	  	// do field validation
	  	if (getSelectedValue('adminForm','sendto') < 0){
	  		alert( "Please select a group" );
	  	} else if (confirm ("<?php echo LM_WARNING_SEND_NEWSLETTER ?>")) {
	  		submitform( 'sendMail' );
	  	}
	  }
	  </script>	  
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>		  
	<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
	  <table class="adminheading">
		  <tr>
			<th class="massemail"><?php echo LM_SEND_NEWSLETTER ?></th>
		  </tr>
		</table>
		
	  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="adminForm">

		<table class="adminform">
            <tr>
                <td style="text-align:right;" width="250"><strong><?php echo LM_SEND_TO_GROUP ?>:</strong></td>
                <td width="85%"><?php echo $grouplist; ?></td>
            </tr>
            <tr>
                <td style="text-align:right;" width="250"><label for="confirmed_accounts"><strong><?php echo LM_CONFIRMED_ACCOUNTS_ONLY ?></strong></label></td>
                <td width="85%"><input type="checkbox" id="confirmed_accounts" name="confirmed_accounts" value="1" />
				<?php echo mosToolTip( LM_CONFIRMED_ACCOUNTS_ONLY_TIP ) ?></td>
            </tr>
			<tr>
				<td style="text-align:right;"><strong><?php echo LM_MAIL_FROM ?>:</strong></td>
				<td><input class="inputbox" type="text" name="mailfrom" size="25" value="<?php echo $admin_email; ?>" style="width:200px" /></td>
			</tr>
			<tr>
				<td style="text-align:right;"><strong><?php echo LM_REPLY_TO ?>:</strong></td>
				<td><input class="inputbox" type="text" name="replyto" size="25" value="<?php echo $admin_email; ?>" style="width:200px" /></td>
			</tr>
            <tr>
                <td style="text-align:right;" width="250"><label for="disable_timeout"><strong><?php echo LM_DISABLE_TIMEOUT ?>:</strong></label></td>
                <td width="85%"> <input type="checkbox" checked="checked" id="disable_timeout" name="disable_timeout" value="1" />
				<?php echo mosToolTip( LM_DISABLE_TIMEOUT_TIP ) ?></td>
            </tr>
            <?php
            if( strstr( $row->html_message, '[NAME]') === false && strstr( $row->message, '[NAME]') === false) {
            	$mails_per_pageload= $lm_params->get( 'normal_mails_per_pageload', 500 );
            }
            else {
            	$mails_per_pageload = $lm_params->get('personalized_mails_per_pageload' , 100 );
            }
	            ?>
            <tr>
                <td style="text-align:right;" width="250"><label for="mails_per_pageload"><strong><?php echo LM_MAILS_PER_STEP ?>:</strong></label></td>
                <td width="85%"><input type="text" id="mails_per_pageload" name="mails_per_pageload" value="<?php echo $mails_per_pageload ?>" /></td>
            </tr>
            
			<tr><td colspan="2"><hr/></td></tr>
			
			<tr>
				<td style="text-align:right;"><strong><?php echo _E_SUBJECT ?></strong></td>
				<td><?php echo $row->subject; ?></td>
			</tr>
			<tr>
				<td style="text-align:right;" valign="top"><strong><?php echo LM_MSG_HTML ?>:</strong></td>
				<td valign="top"><?php echo $row->html_message; ?></td>
			</tr>
			<tr>
				<td style="text-align:right;" valign="top"><strong><?php echo LM_MSG ?>:</strong></td>
				<td valign="top"><?php echo htmlspecialchars($row->html_message); ?></td>
			</tr>
			<tr>
				<td style="text-align:right;" valign="top"><strong><?php echo LM_TEXT_MSG ?>:</strong></td>
				<td valign="top"><?php echo htmlspecialchars($row->message); ?></td>
			</tr>
		</table>
	
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
	</form>
<?php
	}
	
	function sendMailInfo( $all_rows, $startfrom, $msg ) {
		global $mosConfig_mailfrom;
		$mails_per_pageload  = intval( mosGetParam( $_POST, "mails_per_pageload", 100 ));
		$option  = mosGetParam( $_POST, "option", 'com_letterman' );
		$disable_timeout  = mosGetParam( $_POST, "disable_timeout", '' );
		$id  = mosGetParam( $_POST, "id", '' );
		$sendto = mosGetParam( $_POST, "sendto", null );
		$mailfrom = mosGetParam( $_POST, "mailfrom", $mosConfig_mailfrom );
		$confirmed_accounts = mosGetParam( $_POST, "confirmed_accounts", "0" );
		$replyto = mosGetParam( $_POST, "replyto", false );
		
		echo '<h3>'.LM_SEND_LOG.'</h3>
		<pre>'. $msg .'</pre>
		<p><strong>'.sprintf( LM_NUMBER_OF_MAILS_SENT, $startfrom , $all_rows ).'<strong></p>
		<p>'.sprintf( LM_SEND_NEXT_X_MAILS, $mails_per_pageload ) .'</p>
		<br/>
		<br/>
		<form action="'. $_SERVER['PHP_SELF'] .'" method="post" name="adminForm">
		'.LM_CHANGE_MAILS_PER_STEP.': <input type="text" name="mails_per_pageload" value="'. $mails_per_pageload .'" size="4" />
		<br />
		<br />
			<input type="hidden" name="startfrom" value="'. $startfrom .'" />
			<input type="hidden" name="disable_timeout" value='. $disable_timeout .'" />
			<input type="hidden" name="id" value="'. $id .'" />
			<input type="hidden" name="sendto" value="'. $sendto .'" />
			<input type="hidden" name="mailfrom" value="'. $mailfrom .'" />
			<input type="hidden" name="confirmed_accounts" value="'. $confirmed_accounts .'" />
			<input type="hidden" name="replyto" value="'. $replyto .'" />
			<input type="hidden" name="task" value="sendMail" />
			<input type="hidden" name="option" value="'. $option .'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input style="font-weight:bold;" type="submit" name="send" value="&nbsp;&nbsp;&nbsp;&nbsp;'. _SEND_BUTTON .'&nbsp;&nbsp;&nbsp;&nbsp;" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" name="abort" value="'. _CMN_CANCEL .'" onclick="if( confirm( \''.LM_CONFIRM_ABORT_SENDING.'\' )) { document.location=\''. $_SERVER['PHP_SELF']  .'?option=com_letterman\'; }" />
		</form>
		<br/><br/>';
		
	}
	
	function editNewsletter( &$row, &$publist, $option , $glist ) {
		if( function_exists( "botTinymceEditorInit" ))
		$savetext = "tinyMCE.triggerSave();\n";
		else
		$savetext = "";
	  ?>
	  <link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-mos.css" title="green" />
	  <script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
	  <script type="text/javascript" src="../includes/js/calendar/lang/calendar-en.js"></script>

		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			try {
				document.adminForm.onsubmit();
			}
			catch(e){}
			if (form.subject.value == ""){
				alert( "Newsletter must have a subject" );
			}
			else {
				<?php echo $savetext ?>
				submitform( pressbutton );
			}
		}
		</script>
	<table class="adminheading">
		<tr>
		<?php
		if( !$row->id ) { ?>
		  	<th><?php echo _E_ADD. ": ".LM_NEWSLETTER_ITEM; ?></th>
		  	<?php
		}
		else { ?>
			<th class="edit"><?php echo _E_EDIT. ": ".LM_NEWSLETTER_ITEM; ?></th>
			<?php
		}
		?>
		</tr>
	  </table>
	  
	<form action="index2.php" method="post" name="adminForm">

	<table class="adminform">
			<tr>
				<td width="200"><div style="font-weight:bold;text-align:right"><?php echo _E_SUBJECT ?></div></td>
				<td><input class="inputbox" type="text" name="subject" size="25" value="<?php echo $row->subject; ?>" style="width:500px" ></td>
			</tr>

			<tr>
				<td valign="top"><div style="font-weight:bold;text-align:right"><?php echo LM_MSG_HTML.": </div><br/>".LM_NAME_TAG_USAGE ?></td>
				<td><?php
				editorArea( "html_message", str_replace('&','&amp;',$row->html_message), "html_message", 500, 300, 70, 20 );

				?>
				</td>
			</tr>

			<tr>
				<td valign="top"><div style="font-weight:bold;text-align:right"><?php echo LM_TEXT_MSG.": </div><br/><br/>".LM_NAME_TAG_USAGE ?></td>
				<td><textarea name="message" cols="70" rows="20" style="width:500px; height:300px;"><?php echo str_replace('&','&amp;',$row->message); ?></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top"><div style="font-weight:bold;text-align:right"><?php echo _CMN_PUBLISHED ?>:</div></td>
				<td>
					<?php echo $publist; ?>
				</td>
			</tr>
            <tr>
                <td><div style="font-weight:bold;text-align:right"><?php echo _E_STATE; ?></div></td>
                <td><?php 
                if ($row->published == "1") {
                	echo _CMN_PUBLISHED;
                }
                else {
                	echo _CMN_UNPUBLISHED;
                }
                        ?>
                    </td>
            </tr>
            <tr>
                <td><div style="font-weight:bold;text-align:right"><?php echo _E_ACCESS_LEVEL; ?></div></td>
                <td> <?php echo $glist; ?> </td>
            </tr>
            <tr>
                <td><div style="font-weight:bold;text-align:right"><?php echo _E_START_PUB; ?></div></td>
                <td><input class="inputbox" type="text" name="publish_up" id="publish_up" size="25" maxlength="19" value="<?php echo $row->publish_up; ?>" />
                <input name="reset" type="reset" class="button" onClick="return showCalendar('publish_up', 'y-mm-dd');" value="..." />
                </td>
            </tr>
            <tr>
                <td><div style="font-weight:bold;text-align:right"><?php echo _E_FINISH_PUB; ?></div></td>
                <td><input class="inputbox" type="text" name="publish_down" id="publish_down" size="25" maxlength="19" value="<?php echo $row->publish_down; ?>" />
                    <input name="reset2" type="reset" class="button" onClick="return showCalendar('publish_down', 'y-mm-dd');" value="..." />
                </td>
            </tr>
		</table>


	<?php if (!$row->id) { ?>
            <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>">
	<?php }
		  else { ?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>">
	<?php }
	?>
			<input type="hidden" name="task" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			</form>
	<?php 
	}

	function showSubscribers( $rows, $pageNav, $orderby, $sort, $search, $title = LM_SELECT_SUBSCRIBER ) {
		
		$option = mosGetParam( $_REQUEST, 'option');
		?>
	    <script type="text/javascript"><!--
	    function reorder(order, sort){
	    	document.adminForm.orderby.value=order;
	    	document.adminForm.ordering.value=sort;
	    	submitbutton('subscribers');
	    } //-->
	    </script>
	
	    <form action="index2.php" method="post" name="adminForm">
	    
	    <table class="adminheading">
			<tr>
				<th class="user"><?php echo $title ?></th>
				<td><?php echo _FILTER ?></td>
				<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
				</td>
			</tr>
			</table>
	    
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		    <tr>
		      <th width="2%" class="title">#</th>
		      <th width="2%" class="title"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows ); ?>);" /></th>
		      <th width="30%" class="title"><a href="javascript:reorder('subscriber_name', '<?php if($sort == "ASC" && $orderby == "subscriber_name") { echo "DESC"; } else { echo "ASC"; } ?>');"><?php echo LM_SUBSCRIBER_NAME ?></a></th>
		      <th width="35%" class="title"><a href="javascript:reorder('subscriber_email', '<?php if($sort == "ASC" && $orderby == "subscriber_email") { echo "DESC"; } else { echo "ASC"; } ?>');"><?php echo LM_SUBSCRIBER_EMAIL ?></a></th>
		      <th width="20%" class="title"><a href="javascript:reorder('subscribe_date', '<?php if($sort == "ASC" && $orderby == "subscribe_date") { echo "DESC"; } else { echo "ASC"; } ?>');"><?php echo LM_SIGNUP_DATE ?></a></th>
		      <th width="10%" class="title"><a href="javascript:reorder('confirmed', '<?php if($sort == "ASC" && $orderby == "confirmed") { echo "DESC"; } else { echo "ASC"; } ?>');"><?php echo LM_CONFIRMED ?>?</a></th>
		    </tr>
		<?php
		$i = $k = 0;
		foreach( $rows as $row ) {
			?>
			<tr class="<?php echo "row$k"; ?>">
		      <td><?php echo $i+1+$pageNav->limitstart;?></td>
		      <td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->subscriber_id; ?>" onclick="isChecked(this.checked);" /></td>
		      <td><a href="#edit" onclick="return listItemTask('cb<?php echo $i++;?>','editSubscriber')">
		        <?php echo $row->subscriber_name ? $row->subscriber_name : '[no name]'; ?> </a> </td>
		      <td><?php echo $row->subscriber_email; ?></td>
		      <td><?php echo mosFormatDate( $row->subscribe_date ); ?></td>
		      <td>
		      <?php 
		      $img = $row->confirmed ? 'tick.png' : 'publish_x.png';
		      echo "<img src=\"images/$img\" width=\"12\" height=\"12\" border=\"0\" alt=\"$img\" />";
		      ?>
		      </td>
		    </tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
			<?php echo $pageNav->getListFooter(); ?>
		  <input type="hidden" name="option" value="<?php echo $option;?>" />
		  <input type="hidden" name="task" value="subscribers" />
		  <input type="hidden" name="boxchecked" value="0" />
		  <input type="hidden" name="orderby" value="<?php echo $orderby; ?>" />
		  <input type="hidden" name="ordering" value="<?php echo $sort; ?>" />
		</form>
		<?php

	}
	function showValidationResults( $results ) {
		
		$orderby = mosGetParam($_REQUEST, 'orderby', 'subscriber_name');
		$sort = mosGetParam($_REQUEST, 'ordering', 'ASC');
		
		echo '<h2>Letterman EMAIL VALIDATION RESULTS</h2>';
		echo '<div align="left" style="margin-left:20px;"><ul>';
		$img = '<img hspace="5" src="images/%s" alt="result icon" border="0" />';
		foreach( $results as $result ) {
			echo '<li>';
			switch( $result['result']) {
				case 2: 
					echo sprintf( $img, 'publish_g.png');
					echo 'The email address <strong>'.$result['email'].'</strong> (Subscriber Name: '.$result['name'].') was skipped, because it was already validated or confirmed.';
					break;
				case 1: 
					echo sprintf( $img, 'tick.png');
					echo 'The email address <strong>'.$result['email'].'</strong> (Subscriber Name: '.$result['name'].') was tested and is valid. Letterman has set this subscriber account to <strong>confirmed</strong>.';
					break;
				case 0: 
					echo sprintf( $img, 'publish_x.png');
					echo 'The email address <strong>'.$result['email'].'</strong> (Subscriber Name: '.$result['name'].') was tested and is NOT valid. Letterman has prepended <strong>INVALID_</strong> to the subscriber name.';
					break;
				case -1: 
					echo sprintf( $img, 'disabled.png');
					echo 'The email address <strong>'.$result['email'].'</strong> (Subscriber Name: '.$result['name'].') could not be validated for technical reasons.';
					break;
			}
			echo '<pre>'.$result['result_txt'].'</pre>';
			echo "</li>\n";
		}
		echo "</ul>
		<br/>
		</div>
		<a href=\"".$_SERVER['PHP_SELF']."?option=com_letterman&amp;task=subscribers&amp;orderby=$orderby&amp;sort=$sort\">[ "._BACK." ]</a>
		<br/><br/>";
	}
	
	function editSubscriber( $id){
		global $database, $option;

		$row = new mosLettermanSubscribers( $database );
		// load the row from the db table
		$row->load( $id );

?>
	<form action="index2.php" method="post" name="adminForm">
		<div class="sectionname" align="left"><?php echo ($id != 0) ? _E_EDIT : _E_ADD; echo ' ('.LM_SUBSCRIBER.') '; ?></div><br /><br />
    
    <table class="adminform">
	    <tr>
	       <td><?php echo LM_SIGNUP_DATE ?>:</td>
	       <td><?php echo $row->subscribe_date; ?></td>
	    </tr>
      	<tr>
			<td width="100"><?php echo LM_SUBSCRIBER_NAME ?>:</td>
			<td width="85%"><input type="text" name="subscriber_name" class="inputbox" size="40" value="<?php echo $row->subscriber_name; ?>" /></td>
		</tr>
      <tr>
			<td width="100"><?php echo LM_SUBSCRIBER_EMAIL ?>:</td>
			<td width="85%"><input type="text" name="subscriber_email" class="inputbox" size="40" value="<?php echo $row->subscriber_email; ?>" /></td>
		</tr>
		<tr>
			<td width="100"><?php echo LM_CONFIRMED ?>?:</td>
			<td width="85%">
			  	<select name="confirmed" class="inputbox">
					<option value="1"<?php if($row->confirmed=="1"){ echo " selected=\"selected\""; }?> >
					<?php echo _CMN_YES ?></option>
					<option value="0"<?php if($row->confirmed=="0"){ echo " selected=\"selected\""; }?> >
					<?php echo _CMN_NO ?></option>
				</select>
			</td>
		</tr>    
    </table>
    
    <input type="hidden" name="subscriber_id" value="<?php echo $id ?>" />
	<input type="hidden" name="option" value="<?php echo $option;?>" />
	<input type="hidden" name="task" value="" />
	</form>
<?php
	}
	function assignUsers( $rows ) {

	  ?>
	  <div class="sectionname" align="left"><?php echo LM_USERS_TO_SUBSCRIBERS; ?>:</div><br /><br />
	  <?php
		if( count($rows)==1000) {
			echo '<div class="message">PLEASE NOTE: There could be more users to add, but this form only displays the first 1000 users.</div>';
		}
		?>
	  <form action="index2.php" method="post" name="adminForm">
	  <table class="adminform">
	      <tr>
	        <td valign="top">
	  		<?php
		  	if( $rows ) { ?>
				<div align="right"><?php echo _CMN_SELECT ?>:</div>
			</td>
			<td>
				<select size="10" multiple="multiple" name="selectedUsers[]" class="inputbox"><?php 
				  foreach( $rows as $user ) {
				  	echo "<option value=\"".$user->id."\">".$user->name." (".$user->username.")</option>\n";
				  }
				  ?>
				</select>
			</td>
			<?php
		  }
		  else { ?>
		  	No User you could select!</td>
		  <?php
		  }
		  ?>
		</tr>
	</table>
	  <input type="hidden" name="option" value="com_letterman" />
	  <input type="hidden" name="task" value="" />
	  </form><?php
	}


	function header() {
		echo "<span class=\"sectionname\"><img align=\"middle\" alt=\"letterman logo\" src=\"components/com_letterman/letterman.gif\" /></span>\n";
	}
	function footer() {
	  ?>
		<div align="center" class="small">
		  <a href="<?php echo $GLOBALS['lm_home'] ?>" target="_blank">Letterman Component &copy;2005 Soeren Eberhardt</a>
		  (<a href="http://virtuemart.net/index2.php?option=com_versions&amp;catid=3&amp;myVersion=<?php echo $GLOBALS['lm_version'] ?>" onclick="javascript:void window.open('http://virtuemart.net/index2.php?option=com_versions&amp;catid=3&amp;myVersion=<?php echo $GLOBALS['lm_version'] ?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=580,directories=no,location=no'); return false;" title="Letterman Version Check">Check for latest version</a>)
		</div>
	  <?php
	}
	function settings( $option, &$params, $id ) {
		global $mosConfig_live_site;
		?>
		<script type="text/javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<form action="index2.php" method="post" name="adminForm">
		<table style="width:75%;" class="adminheading">
		<tr>
			<th class="config">
			Letterman Global Settings
			</th>
		</tr>
		</table>

		<table style="width:75%;" class="adminform">
		<tr>
			<th>
			Parameters
			</th>
		</tr>
		<tr>
			<td>
			<?php
			echo $params->render();
			?>
			</td>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>
