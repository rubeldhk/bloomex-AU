<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

mm_showMyFileName( __FILE__ );

require_once(CLASSPATH.'ps_reminder.php');
$ps_reminder = new ps_reminder;

$Itemid = mosgetparam( $_REQUEST, 'Itemid', null);
$submit = mosGetParam( $_REQUEST, "submit" );
$task = mosGetParam( $_REQUEST, "task" );


$recip_name = mosGetParam( $_REQUEST, "recip_name" );
$recip_email = mosGetParam( $_REQUEST, "recip_email" );
$recip_month = mosGetParam( $_REQUEST, "recip_month", date('m',time()));
$recip_day = mosGetParam( $_REQUEST, "recip_day",date('j',time()));

$subject = mosGetParam( $_REQUEST, "subject" );
$occasion = mosGetParam( $_REQUEST, "occasion");

$reminder_id=mosGetParam( $_REQUEST, "reminder_id" );
$missing = mosGetParam( $vars, 'missing' );

$missing_style = "color: Red; font-weight: Bold;";
if (!empty( $missing )) {
    echo "<script type=\"text/javascript\">alert('"._CONTACT_FORM_NC."'); </script>\n";
}


/* Set Dynamic Page Title when applicable */
if(is_callable(array('mosMainFrame', 'setPageTitle'))) {
    $mainframe->setPageTitle( $VM_LANG->_PHPSHOP_REMINDER_TITLE );
}
 ?>

 <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
    <tr>
      <td align="center"><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_REMINDER_TITLE1 ?></strong></td></tr>
      <tr><td align="center"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE2 ?></td></tr>
      <tr><td align="center"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE3 ?></strong></td></tr>
      <tr><td align="left"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE4 ?></td>
 </tr></table>


<?php

switch( $task ) {


	case 'saveRegistration':

	global $database, $acl;
	global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_useractivation, $mosConfig_allowUserRegistration;
	global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_fromname;

	if ($mosConfig_allowUserRegistration=='0') {
		mosNotAuth();
		return;
	}

	$row = new mosUser( $database );

	if (!$row->bind( $_POST, 'usertype' )) {
		mosErrorAlert( $row->getError() );
	}

	mosMakeHtmlSafe($row);

	$row->id = 0;
	$row->usertype = '';
	$row->gid = $acl->get_group_id( 'Registered', 'ARO' );

	if ($mosConfig_useractivation == '1') {
		$row->activation = md5( mosMakePassword() );
		$row->block = '1';
	}

	if (!$row->check()) {
		echo "<script> alert('".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
		exit();
	}

	$pwd 				= $row->password;
	$row->password 		= md5( $row->password );
	$row->registerDate 	= date('Y-m-d H:i:s');

	if (!$row->store()) {
		echo "<script> alert('".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();

	$name 		= $row->name;
	$email 		= $row->email;
	$username 	= $row->username;
	$newid          = $row->id;
	//$my 	        = $row->id;

	$subject 	= sprintf (_SEND_SUB, $name, $mosConfig_sitename);
	$subject 	= html_entity_decode($subject, ENT_QUOTES);
	if ($mosConfig_useractivation=="1"){
		$message = sprintf (_USEND_MSG_ACTIVATE, $name, $mosConfig_sitename, $mosConfig_live_site."/index.php?option=com_registration&task=activate&activation=".$row->activation, $mosConfig_live_site, $username, $pwd);
	} else {
		$message = sprintf (_USEND_MSG, $name, $mosConfig_sitename, $mosConfig_live_site);
	}

	$message = html_entity_decode($message, ENT_QUOTES);
	// Send email to user
	if ($mosConfig_mailfrom != "" && $mosConfig_fromname != "") {
		$adminName2 = $mosConfig_fromname;
		$adminEmail2 = $mosConfig_mailfrom;
	} else {
		$query = "SELECT name, email"
		. "\n FROM #__users"
		. "\n WHERE LOWER( usertype ) = 'superadministrator'"
		. "\n OR LOWER( usertype ) = 'super administrator'"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();
		$row2 			= $rows[0];
		$adminName2 	= $row2->name;
		$adminEmail2 	= $row2->email;

	}

	mosMail($adminEmail2, $adminName2, $email, $subject, $message);

	// Send notification to all administrators
	$subject2 = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
	$message2 = sprintf (_ASEND_MSG, $adminName2, $mosConfig_sitename, $row->name, $email, $username);
	$subject2 = html_entity_decode($subject2, ENT_QUOTES);
	$message2 = html_entity_decode($message2, ENT_QUOTES);

	// get superadministrators id
	$admins = $acl->get_group_objects( 25, 'ARO' );

	foreach ( $admins['users'] AS $id ) {
		$query = "SELECT email, sendEmail"
		. "\n FROM #__users"
		."\n WHERE id = $id"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		$row = $rows[0];

		if ($row->sendEmail) {
			mosMail($adminEmail2, $adminName2, $row->email, $subject2, $message2);
		}
	}

	if ( $mosConfig_useractivation == 1 ){
		echo "<fieldset><br><b>"._REG_COMPLETE_ACTIVATE."</b>";
	} else {
		echo "<fieldset><br><b>"._REG_COMPLETE."</b>";
	}



       $q = "INSERT INTO #__{vm}_reminder (user_id, recip_name,";
       $q .= "recip_email,recip_day,recip_month,subject,occasion) ";
       $q .= "VALUES (";
       $q .= "'".$newid."','";
       $q .= mosGetParam( $_REQUEST, "name_user" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_email" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_day" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_month" ). "','";
       $q .= mosGetParam( $_REQUEST, "subject" ). "','";
       $q .= mosGetParam( $_REQUEST, "occasion" ). "')";

       $db->query($q);


      $q = "INSERT INTO #__{vm}_user_info (user_id, address_type,address_type,";
      $q .= "last_name,user_email,perms) ";
      $q .= "VALUES (";
      $q .= "'".$newid."',";
      $q .= "'BT',";
      $q .= "'-default-','";
      $q .= mosGetParam( $_REQUEST, "name_user" )."','";
      $q .= mosGetParam( $_REQUEST, "recip_email" )."',";
      $q .= "'shopper')";

      $db->query($q);


/////////////////////////////////////////	
       //Auth

       echo "<br><font color=#ff0000>";
       echo "<b>".$VM_LANG->_PHPSHOP_REMINDER_THANK."</b>";
       echo "</font><br></fieldset>";


//////////////////////////////////////////	
 	break;

}


switch ( $submit ) {

    case "Edit":

        if (empty($recip_name) OR empty($recip_email) OR empty($subject) OR !(preg_match("/.+@.+\..+/", $recip_email))) {
       echo "<script language='javascript' type='text/javascript'>\n";
       echo " alert( \"".$VM_LANG->_PHPSHOP_REMINDER_ERROR."\" );\n";
       echo "</script>\n";
        } else {
       $q = "UPDATE #__{vm}_reminder SET";
       $q .= " user_id='" . $auth["user_id"] . "' ";
       $q .= ", recip_name='" . $recip_name . "' ";
       $q .= ", recip_email='" . $recip_email . "' ";
       $q .= ", recip_day='" . $recip_day  . "' ";
       $q .= ", recip_month='" . $recip_month  . "' ";
       $q .= ", subject='" . $subject . "' ";
       $q .= ", occasion='" . $occasion . "' ";
       $q .= "WHERE reminder_id='" . $reminder_id . "'";
       $db->query($q);
          }
      break;
  
    case "Save":

       if (empty($recip_name) OR empty($recip_email) OR empty($subject) OR $recip_month==0 OR $recip_day==0 OR !(preg_match("/.+@.+\..+/", $recip_email))) {
       echo "<script language='javascript' type='text/javascript'>\n";
       echo " alert( \"".$PHPSHOP_LANG->_PHPSHOP_REMINDER_ERROR."\" );\n";
       echo "</script>\n";
        } else {
       echo "<fieldset><font color=#ff0000>";
       echo $VM_LANG->_PHPSHOP_REMINDER_THANK;
       echo "</font></fieldset>";
       if ($perm->is_registered_customer($auth['user_id'])) { 
       $q = "INSERT INTO #__{vm}_reminder (user_id, recip_name,";
       $q .= "recip_email,recip_day,recip_month,subject,occasion) ";
       $q .= "VALUES (";
       $q .= "'".$auth["user_id"]."','";
       $q .= mosGetParam( $_REQUEST, "recip_name" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_email" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_day" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_month" ). "','";
       $q .= mosGetParam( $_REQUEST, "subject" ). "','";
       $q .= mosGetParam( $_REQUEST, "occasion" ). "')";
       $db->query($q);
       $db->next_record();
       } else {
       $q = "INSERT INTO #__{vm}_reminder (user_id, recip_name,";
       $q .= "recip_email,recip_day,recip_month,subject,occasion) ";
       $q .= "VALUES (";
       $q .= "'','";
       $q .= mosGetParam( $_REQUEST, "recip_name" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_email" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_day" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_month" ). "','";
       $q .= mosGetParam( $_REQUEST, "subject" ). "','";
       $q .= mosGetParam( $_REQUEST, "occasion" ). "')";
       $db->query($q);
       $db->next_record();
           }
        }
      break;

    case "Delete":

        $q  = "DELETE FROM #__{vm}_reminder ";
        $q .= " WHERE user_id='" . $auth["user_id"] . "'";
        $q .= " AND reminder_id='" . $reminder_id . "'";

      $db->query($q);
     
      break;

 
   }

$evdate = time();

          if (!empty($my->id)) {
//       if ($perm->is_registered_customer($auth['user_id'])) { 

            // USER IS LOGGED IN 

          echo "<fieldset>
          <legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_REMINDER_NEW."</span></legend>";
          ?>
          <div style="width:90%;">
          <!-- Reminder Registration form -->
          <form action="<?php echo $PHP_SELF; ?>" method="post" name="adminForm">
          <table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform"> 
          <input type="hidden" name="option" value="com_virtuemart" />
          <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" >
          <input type="hidden" name="user_id" value="<?php echo $my->id ?>" />
          <tr><td width="40%" align="right">
          <strong>
          <?php echo "<label for=\"recip_name\">".$VM_LANG->_PHPSHOP_REMINDER_LIST1_1."</label>*"  ?></strong></td>
          <td width="60%" ><input type="text" id="recip_name" name="recip_name" size="50" class="inputbox" /></td>
          </tr>
          <tr><td width="40%" align="right" >
          <strong>
          <?php echo "<label for=\"recip_email\">".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</label>*" ?></strong></td>
          <td width="60%" ><input type="text" id="recip_email" name="recip_email" size="40"  class="inputbox" /></td>
          </tr>
          <tr> 
          <td width="40%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?>*</strong></td>
          <td width="60%" >
          <?php $ps_html->list_month("recip_month", $recip_month) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", $recip_day) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
          </tr>
          <tr><td width="40%" align="right" >
          <?php echo "<label for=\"occasion\">".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</label>" ?></td>
          <td width="60%" >
          <?php $ps_html->list_user_occasion_name("occasion", $occasion) ?>
          </td></tr>
          <tr><td width="40%" align="right" valign="top"><strong>
          <?php echo "<label for=\"subject\">".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</label>" ?>*</strong></td>
          <td width="60%" ><textarea title="<?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?>" cols="40" rows="4" name="subject" ><?php echo $subject; ?></textarea></td>
          </td></tr>
          <tr><td colspan="2" align="center">
          <input type="submit" class="button" name="submit" value="<?php echo _E_SAVE ?>" />
          </td></tr></table>
          </form>
          </fieldset></div>
          <?php 
          echo "<div align=left><br><img src=\"".IMAGEURL."ps_image/package.png\" align=\"middle\" height=\"32\" width=\"32\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_ACC_ORDER_INFO."\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<b>".$VM_LANG->_PHPSHOP_REMINDER_INFO."</b></div>" ?>
            </strong>
             <?php    $ps_reminder->list_reminder("1");   
          }
      
          else { 
           // user is not logged in
  ?>
            <fieldset>
                <legend><span class="sectiontableheader"><?php echo $VM_LANG->_PHPSHOP_RETURN_LOGIN ?></span></legend>
                <br />
            <?php 
                        include(PAGEPATH.'checkout.login_form.php');
            ?>
                <br />
            </fieldset><br />
            <?php
          
          
          ?><br />
            <div class="sectiontableheader"><?php echo "New? Registration" ?></div>
                <br />
         
       <?php
       echo "<fieldset>
         <legend><span class=\"sectiontableheader\">Registration</span></legend>";
       ?>
         <div style="width:90%;">
         <!-- Registration form -->
	<script language="javascript" type="text/javascript">
		function submitbutton() {
			var form = document.mosForm;
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (form.name.value == "") {
				alert( "<?php echo html_entity_decode(_REGWARN_NAME);?>" );
			} else if (form.username.value == "") {
				alert( "<?php echo html_entity_decode(_REGWARN_UNAME);?>" );
			} else if (r.exec(form.username.value) || form.username.value.length < 3) {
				alert( "<?php printf( html_entity_decode(_VALID_AZ09), html_entity_decode(_PROMPT_UNAME), 2 );?>" );
			} else if (form.email.value == "") {
				alert( "<?php echo html_entity_decode(_REGWARN_MAIL);?>" );
			} else if (form.password.value.length < 6) {
				alert( "<?php echo html_entity_decode(_REGWARN_PASS);?>" );
			} else if (form.password2.value == "") {
				alert( "<?php echo html_entity_decode(_REGWARN_VPASS1);?>" );
			} else if ((form.password.value != "") && (form.password.value != form.password2.value)){
				alert( "<?php echo html_entity_decode(_REGWARN_VPASS2);?>" );
			} else if (r.exec(form.password.value)) {
				alert( "<?php printf( html_entity_decode(_VALID_AZ09), html_entity_decode(_REGISTER_PASS), 6 );?>" );
                      	} else if (form.name_user.value == "") {
                                alert( "<?php echo html_entity_decode($VM_LANG->_PHPSHOP_REMINDER_ERROR);?>" );
                      	} else if (form.recip_email.value == "") {
                                alert( "<?php echo html_entity_decode($VM_LANG->_PHPSHOP_REMINDER_ERROR);?>" );
                      	} else if ((form.recip_month.value == 0) || (form.recip_day.value == 0)){
                                alert( "<?php echo html_entity_decode($VM_LANG->_PHPSHOP_REMINDER_ERROR);?>" );
        		} else {
				form.submit();
			}
		}
		</script>

          <form action="<?php echo $PHP_SELF; ?>" method="post" name="mosForm">
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2"><?php echo _REGISTER_REQUIRED; ?></td>
		</tr>
		<tr>
                <td width="30%"><?php echo _REGISTER_NAME; ?> *</td>
		<td><input type="text" name="name" size="40" value="" class="inputbox" /></td>
		</tr>
		<tr>
		<td><?php echo _REGISTER_UNAME; ?> *</td>
		<td><input type="text" name="username" size="40" value="" class="inputbox" /></td>
		<tr>
		<td><?php echo _REGISTER_EMAIL; ?> *</td>
		<td><input type="text" name="email" size="40" value="" class="inputbox" /></td>
		</tr>
		<tr>
		<td><?php echo _REGISTER_PASS; ?> *</td>
	  	<td><input class="inputbox" type="password" name="password" size="40" value="" /></td>
		</tr>
		<tr>
                <td><?php echo _REGISTER_VPASS; ?> *</td>
		<td><input class="inputbox" type="password" name="password2" size="40" value="" /></td>
		</tr>
		<tr>
   		  <td colspan=2>
		</td>
		</tr>
		</table>
		
          <?php  
          echo "</fieldset><fieldset><legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_REMINDER_NEW."</span></legend>";
          ?>
          <div style="width:90%;">
          <!-- Reminder Registration form -->
          <table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform"> 
          <tr><td width="40%" align="right">
          <strong>
          <?php echo "<label for=\"name_user\">".$VM_LANG->_PHPSHOP_REMINDER_LIST1_1."</label>*"  ?></strong></td>
          <td width="60%" ><input type="text" id="name_user" name="name_user" size="40" class="inputbox" /></td>
          </tr>
          <tr><td width="40%" align="right" >
          <strong>
          <?php echo "<label for=\"recip_email\">".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</label>*" ?></strong></td>
          <td width="60%" ><input type="text" id="recip_email" name="recip_email" size="40" class="inputbox" /></td>
          </tr>
          <tr> 
          <td width="40%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?>*</strong></td>
          <td width="60%" >
          <?php $ps_html->list_month("recip_month", date('m',time())) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", date('j',time())) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
          </tr>
          <tr><td width="40%" align="right" >
          <?php echo "<label for=\"occasion\">".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</label>" ?></td>
          <td width="60%" >
          <?php $ps_html->list_user_occasion_name("occasion", $occasion) ?>
          </td></tr>
          <tr><td width="40%" align="right" valign="top"><strong>
          <?php echo "<label for=\"subject\">".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</label>" ?>*</strong></td>
          <td width="60%" ><textarea title="<?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?>" cols="40" rows="4" name="subject" ></textarea></td>
          </td></tr>
          </table>
          </div>

        <input type="hidden" name="task" value="saveRegistration" />
	<input type="button" value="<?php echo _BUTTON_SEND_REG; ?>" class="button" onclick="submitbutton()" />
	<input type="hidden" name="option" value="com_virtuemart" />
	<input type="hidden" name="useractivation" value="'. $mosConfig_useractivation .'" />
	</form></fieldset>
                <br />
            </div>
<?php
          }

  //echo "<CENTER><strong>"."You are not a Registered Customer yet"."</strong></CENTER>";


?>

   
<!-- Body ends here -->
