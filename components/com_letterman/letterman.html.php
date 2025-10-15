<?php
/**
* Letterman Newsletter Component
* 
* @package Letterman
* @authors:
* @copyright Soeren Eberhardt <soeren@virtuemart.net>
    (who just needed an easy and *working* Newsletter component for Mambo 4.5.1 and mixed up Newsletter and YaNC)
* @copyright Mark Lindeman <mark@pictura-dp.nl> 
    (parts of the Newsletter component by Mark Lindeman; Pictura Database Publishing bv, Heiloo the Netherland)
* @copyright Adam van Dongen <adam@tim-online.nl>
    (parts of the YaNC component by Adam van Dongen, www.tim-online.nl)
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
*/
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_letterman {

    function listAll ($menuname, &$rows, $letterman_rights, $pageNav )
    {
        global $Itemid, $mosConfig_live_site;
    ?>
    <table width="100%" cellpadding="4" cellspacing="0" border="0" align="center">
        <tr>
        	<td colspan="4" class="componentheading"><?php echo $menuname; ?></td>
			<td align="right" class="componentheading" nowrap="nowrap">
				<?php
				echo '&nbsp;&nbsp;&nbsp;'. _PN_DISPLAY_NR .'&nbsp;';
				$link = 'index.php?option=com_letterman&amp;Itemid='. $Itemid;
				echo $pageNav->getLimitBox( $link );
				?>
			</td>
        </tr>
    <?php 
    if (count ($rows )) { ?>
        <tr>
          <td width="32" align="center" class="sectiontableheader">&nbsp;</td>
          <td width="70%" class="sectiontableheader"><?php echo _E_SUBJECT; ?></td>
          <td width="20%" class="sectiontableheader"><?php echo _E_START_PUB;?></td>
          <td width="10%" align="left" class="sectiontableheader"><?php echo _HEADER_HITS;?></td>
          <td width="32" align="center" class="sectiontableheader">&nbsp;</td>
        </tr>
<?php
        $k = 0;
        $tabclass = array("sectiontableentry1", "sectiontableentry2");
        foreach ($rows as $row) {
            $k = $k ? 0 : 1 ;
        ?>
            <tr class="<?php echo $tabclass[$k]; ?>">
              <td width="32" align="center">&nbsp;</td>
              <td width="70%">
              <a href="<?php echo sefRelToAbs("index.php?option=com_letterman&amp;task=view&amp;Itemid=$Itemid&amp;id=$row->id"); ?>"><?php echo $row->subject; ?></a>
          <?php
          if( $letterman_rights['is_editor'] ) { ?>
              &nbsp;&nbsp;<a title="<?php echo _E_EDIT ?>" href="<?php echo sefRelToAbs("index.php?option=com_letterman&amp;task=edit&amp;Itemid=$Itemid&amp;id=$row->id"); ?>">
              <img src="images/M_images/edit.png" align="center" border="0" alt="" /></a>
          <?php 
          }
          if( $letterman_rights['is_sender'] ) { ?>
              &nbsp;&nbsp;<a title="<?php echo _SEND_BUTTON ?>" href="<?php echo sefRelToAbs("index.php?option=com_letterman&amp;task=sendNow&amp;Itemid=$Itemid&amp;id=$row->id"); ?>">
              <img src="components/com_letterman/images/mail_send.png" align="center" border="0" alt="<?php echo _SEND_BUTTON ?>" /></a>
          <?php 
          }
          ?>
              </td>
              <td width="20%"><?php echo $row->send;?></td>
              <td width="9%" align="center"><?php echo $row->hits;?></td>
              <td width="32" align="center"><?php
          if( $letterman_rights['can_delete'] ) { ?>
              <form name="deleteForm<?php echo $row->id;?>" action="index.php?option=com_letterman" method="post">
              <input type="hidden" name="id" value="<?php echo $row->id ?>" />
              <input type="hidden" name="task" value="remove" />
              <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" />
              </form>
              &nbsp;&nbsp;<a title="<?php echo _CMN_DELETE ?>" href="javascript: if( confirm('Are you sure you want to delete selected item?')) { document.deleteForm<?php echo $row->id;?>.submit(); }">
              <img src="components/com_letterman/images/delete.png" align="center" border="0" height="22" width="22" alt="<?php echo _CMN_DELETE ?>" /></a>
              
          <?php 
          } ?>&nbsp;</td>
            </tr>

        <?php  
        } ?>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" colspan="5" class="sectiontablefooter">
			<?php
			$link = 'index.php?option=com_letterman&amp;Itemid='. $Itemid;
			echo $pageNav->writePagesLinks( $link );
			?>
			</td>
		</tr>
		<tr>
			<td colspan="5" align="center">
			<?php echo $pageNav->writePagesCounter(); ?>
			</td>
		</tr>
    <?php 
    } 
    else { ?>
        <tr><td colspan="5"><?php echo _NOKEYWORD; ?></td></tr>
    <?php 
    } ?>
    </table>
    <?php

    }
function editNewsletter( &$row, &$publist, $option , $glist ) {
      global $mosConfig_absolute_path;
      
		if( function_exists( "botTinymceEditorInit" ))
		  $savetext = "tinyMCE.triggerSave();\n";
		else
		  $savetext = "";
	  ?>
	  <link rel="stylesheet" type="text/css" media="all" href="includes/js/calendar/calendar-mos.css" title="green" />
	  <script type="text/javascript" src="includes/js/calendar/calendar.js"></script>
	  <script type="text/javascript" src="includes/js/calendar/lang/calendar-en.js"></script>
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
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="contentheading" ><?php echo $row->id ? _E_EDIT : _E_ADD; echo ": ".LM_NEWSLETTER_ITEM; ?></td>
			<td width="10%">
			 <?php
             require_once( $mosConfig_absolute_path."/includes/HTML_toolbar.php");
			 mosToolBar::startTable();
			 mosToolBar::save();
			 mosToolBar::spacer(25);
			 mosToolBar::cancel();
			 mosToolBar::endtable();
			?>
			</td>
		</tr>
		</table>
        <br/><br/>
	<form action="index.php" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
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
            <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>" />
	<?php }
		  else { ?>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<?php }
	?>
			<input type="hidden" name="task" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			</form>
	<?php 
  }
  
    function showItem( $row, $gid ) {
      
      ?><div class="componentheading">Newsletter Item <a href="javascript: history.back()">&nbsp;[ back ]</a></div>
      <div align="right" class="createdate">Date: <?php echo $row->created ?></div>
      <div><strong><?php echo $row->title; ?></strong></div><br/>
      <div><?php echo $row->text; ?></div>
    <?php
    
    }
    
	function sendNewsletter( &$row, $option , $grouplist, $admin_email ) {
		global $lm_params;
	  ?> 
      <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>		  
      <script language="Javascript" src="includes/js/overlib_mini.js"></script>
	  <table cellpadding="4" cellspacing="0" border="0" width="100%">
		  <tr>
			<td class="contentheading" ><?php echo LM_SEND_NEWSLETTER ?></td>
		  </tr>
		</table>
	  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="adminForm">

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminform">
            <tr>
                <td width="250"><strong><?php echo LM_SEND_TO_GROUP ?>:</strong></td>
                <td width="85%"><?php echo $grouplist; ?></td>
            </tr>
            <tr>
                <td width="250"><strong><?php echo LM_CONFIRMED_ACCOUNTS_ONLY ?></strong></td>
                <td width="85%"><input type="checkbox" name="confirmed_accounts" value="1">
				<?php echo mosToolTip( LM_CONFIRMED_ACCOUNTS_ONLY_TIP ) ?></td>
            </tr>
			<tr>
				<td><strong><?php echo LM_MAIL_FROM ?>:</strong></td>
				<td><input class="inputbox" type="text" name="mailfrom" size="25" value="<?php echo $admin_email; ?>" style="width:200px" ></td>
			</tr>
			<tr>
				<td><strong><?php echo LM_REPLY_TO ?>:</strong></td>
				<td><input class="inputbox" type="text" name="replyto" size="25" value="<?php echo $admin_email; ?>" style="width:200px" ></td>
			</tr>
            <tr>
                <td width="250"><strong><?php echo LM_DISABLE_TIMEOUT ?>:</strong></td>
                <td width="85%"> <input type="checkbox" checked="checked" name="disable_timeout" value="1">
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
				<td><strong><?php echo _E_SUBJECT ?></strong></td>
				<td><?php echo $row->subject; ?></td>
			</tr>
			<tr>
				<td valign="top"><strong><?php echo LM_MSG_HTML ?>:</strong></td>
				<td valign="top"><?php echo $row->html_message; ?></td>
			</tr>
			<tr>
				<td valign="top"><strong><?php echo LM_MSG ?>:</strong></td>
				<td valign="top"><?php echo htmlspecialchars($row->html_message); ?></td>
			</tr>
			<tr>
				<td valign="top"><strong><?php echo LM_TEXT_MSG ?>:</strong></td>
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
	
	function subscribe( $subscriber){
	  global $database, $my, $Itemid;
      
      $check = "SELECT id FROM #__users, #__letterman_subscribers WHERE user_id=id AND user_id='".$my->id."'";
      $database->setQuery( $check );
      $database->loadObject( $result );
      
      $name = addslashes(mosGetParam( $_REQUEST,'subscriber_name','Subscriber' ));
      $email = addslashes(mosGetParam( $_POST, 'email'));
      
      if( !$result ) {
        if(!empty($email)){
            saveSubscriber( $name, $email);
        }
        else {
            //new subscriber
            HTML_letterman::showField( "subscribe");
        }
      }
      else {
        mosRedirect( "index.php?option=com_letterman&Itemid=$Itemid" , LM_ALREADY_SUBSCRIBED );
      }
	}
	
	function unsubscribe($subscriber){
	  global $database, $my, $Itemid;
      $name = addslashes(mosGetParam( $_POST,'subscriber_name'));
      $email = addslashes(mosGetParam( $_POST, 'email'));
      
      if( !empty($email) ) {
        $check = "SELECT subscriber_name FROM #__letterman_subscribers WHERE subscriber_email='$email'";
        $database->setQuery( $check );
        $database->loadObject( $result );
        if( $result ) {
            //delete
            deleteSubscriber( $result->subscriber_name, $email );
        }
        else {
          mosRedirect( "index.php?option=com_letterman&Itemid=$Itemid" , _ERROR_PASS );
        }
      }
      else{
        //new subscriber
        HTML_letterman::showField("unsubscribe", $subscriber);
      }
      
	}
    
	function showField( $action, $subscriber = ''){
		global $Itemid, $letters, $database, $my;
		
        if( $my->id ) {
          if( $action == "subscribe") {
            $query="SELECT name as subscriber_name, email as subscriber_email FROM #__users WHERE id = '" . $my->id . "'";
            $database->setQuery($query);
            $database->loadObject($subscriberdata);
          }
          elseif( $action == "unsubscribe") {
            $query="SELECT subscriber_name, subscriber_email FROM #__letterman_subscribers WHERE user_id=" . $my->id;
            $database->setQuery($query);
            $database->loadObject($subscriberdata);
          }
		}
        if( empty($subscriberdata)) {
          $subscriberdata =new  stdClass();
          $subscriberdata->subscriber_email = ""; 
          $subscriberdata->subscriber_name = ""; 
        }
        
		$query = "SELECT id FROM #__modules WHERE module = 'mod_lettermansubscribe'";
		$database->setQuery( $query );
		$id = $database->loadResult();
        if( $id ) {
          $module = new mosModule( $database );
          $module->load( $id );
          $params =new  mosParameters( $module->params );
          $hide_name_field = $params->get( "hide_name_field", "0" );
        }
        else {
          $hide_name_field = 0;
        }
        $action_lbl = ($action=="subscribe") ? LM_SUBSCRIBE : LM_UNSUBSCRIBE;

        ?>
          <script type="text/javascript">
          <!--
          
          function validate(){
            if(<?php if($hide_name_field=='0'){ ?>document.showField.subscriber_name.value == "" || <?php } ?>document.showField.email.value == ""){
              alert('<?php echo _SAVE_ERR ?>');
              return false;
            }
            else{
              return true;
            }
          }
          
          //-->
          </script>
            <form method="post" name="showField" action="<?php echo sefRelToAbs("index.php?option=com_letterman&amp;Itemid=$Itemid&amp;task=$action"); ?>">
            <table border="0" cellpadding="0" cellspacing="0" class="contentpane" width="100%">
            <tr>
              <th align="left" colspan="2"><?php echo LM_YOUR_DETAILS ?></th>
              <th width="40%"><br /><br /></th>
            </tr>
        <?php if($hide_name_field=='0'){ ?>
            <tr>
              <td><?php echo _REGISTER_NAME ?></td>
              <td><input type="text" name="subscriber_name" size="32" class="inputbox" maxlength="64" value="<?php echo $subscriberdata->subscriber_name; ?>" <?php if ($action=='unsubscribe' && !empty($subscriberdata->subscriber_name)){echo 'readonly="readonly"';}; ?> /></td>
              <td><br /><br /></td>
            </tr>
        <?php }
            else { ?>
              <input type="hidden" name="subscriber_name" size="32" class="inputbox" maxlength="64" value="<?php echo $subscriberdata->subscriber_name; ?>" <?php if ($action=='unsubscribe' && !empty($subscriberdata->subscriber_name)){echo 'readonly="readonly"';}; ?>>
      <?php } ?>
            <tr>
              <td><?php echo _PROMPT_EMAIL ?></td>
              <td><input type="text" name="email" size="32" class="inputbox" maxlength="64" value="<?php echo $subscriberdata->subscriber_email; ?>" <?php if ($action=='unsubscribe' && !empty($subscriberdata->subscriber_name)){echo 'readonly="readonly"';}; ?>></td>
              <td><br /><br /></td>
            </tr>
            <tr>
              <td colspan="2"><br />
              <input type="submit" name="submit" value="<?php echo $action_lbl; ?>" class="button" onclick="return validate();" /></td>
              <td></td>
            </tr>
          </table>
            </form>
        <?php
	}
    
    function header() {
    
    }
    
    function footer() {
    
    }
    
    function new_bar() {
        global $mosConfig_absolute_path, $mosConfig_live_site, $Itemid;
        require_once( $mosConfig_absolute_path."/includes/HTML_toolbar.php");
        mosToolBar::startTable(); ?>
        <td width="25" align="right">
		<a href="<?php echo sefRelToAbs( "index.php?option=com_letterman&amp;task=edit&amp;Itemid=$Itemid") ?>" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('new','','<?php echo $mosConfig_live_site; ?>/administrator/images/new_f2.png',1);">
		<img name="new" src="<?php echo $mosConfig_live_site; ?>/administrator/images/new.png" height="32" width="32" border="0" />
		</a>
		</td>
        <?php
        mosToolBar::endtable();
    }
    
    function send_bar( $id ) {
        global $mosConfig_absolute_path, $mosConfig_live_site, $Itemid;
        require_once( $mosConfig_absolute_path."/includes/HTML_toolbar.php");
        ?>
        <script language="javascript" type="text/javascript">
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
      <?php
        mosToolBar::startTable(); ?>
 
        <td width="50%" align="right">
		<a href="javascript:submitbutton('sendMail');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('publish','','<?php echo $mosConfig_live_site; ?>/administrator/images/publish_f2.png',1);">
		<img name="publish" src="<?php echo $mosConfig_live_site; ?>/administrator/images/publish.png" align="center" height="32" width="32" border="0" />
		</a>&nbsp;
		</td>
        <td width="50%" align="left">&nbsp;
		<a href="javascript:submitbutton('cancel');" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('back','','<?php echo $mosConfig_live_site; ?>/administrator/images/back_f2.png',1);">
		<img name="back" src="<?php echo $mosConfig_live_site; ?>/administrator/images/back.png" align="center" height="32" width="32" border="0" />
		</a>
		</td>
        <?php
        mosToolBar::endtable();
    }
    
    function subscriber_bar() {
      global $Itemid, $my, $database;
      $subscriber = null;
      if( $my->id ) {
	      $database->setQuery('SELECT user_id FROM `#__letterman_subscribers` WHERE user_id='.$my->id .' OR subscriber_email =\''.$my->email.'\'');
	      $database->loadObject( $subscriber );
      }
?>
        <table align="center">
          <tr>
          <?php
          if(!$subscriber) {
          	?>
            <td>
              <a href="<?php echo sefRelToAbs( 'index.php?option=com_letterman&amp;task=subscribe&amp;Itemid='. $Itemid ) ?>" title="<?php echo LM_SUBSCRIBE ?>">
              <img src="components/com_letterman/images/subscribe.png" alt="<?php echo LM_SUBSCRIBE ?>" align="center" border="0" />&nbsp;<?php echo LM_SUBSCRIBE_TO ?></a>
            </td>
            <?php
          }
          else {
          	?>
            <td>&nbsp;&nbsp;
              <a href="<?php echo sefRelToAbs( 'index.php?option=com_letterman&amp;task=unsubscribe&amp;Itemid='. $Itemid ) ?>" title="<?php echo LM_UNSUBSCRIBE ?>">
              <img src="components/com_letterman/images/unsubscribe.png" alt="<?php echo LM_UNSUBSCRIBE ?>" align="center" border="0" />&nbsp;<?php echo LM_UNSUBSCRIBE_FROM ?></a>
            </td>
            <?php
          }
          ?>
          </tr>
        </table>
<?php
    }
}
?>
