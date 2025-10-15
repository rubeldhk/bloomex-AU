<?php 
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: checkout_register_form.php,v 1.13.2.3 2006/04/05 18:16:54 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

$country = mosGetParam( $_REQUEST, 'country', $vendor_country_3_code);
$state = mosGetParam( $_REQUEST, 'state', '');

$missing = mosGetParam( $_REQUEST, "missing", "" );
$missing_style = "color:red; font-weight:bold;";

if (!empty( $missing )) {
	echo "<script type=\"text/javascript\">alert('"._CONTACT_FORM_NC."'); </script>\n";
}
$label_div_style = 'float:left;width:30%;text-align:right;vertical-align:bottom;font-weight: bold;padding-right: 5px;';
$field_div_style = 'float:left;width:60%;';
/**
 * This section will be changed in future releases of VirtueMart,
 * when we have a registration form manager
 */
//$required_fields = Array( 'first_name', 'last_name', 'address_1', 'city', 'zip', 'country', 'phone_1' );

$shopper_fields = array();
// This is a list of all fields in the form
// They are structured into fieldset
// where the begin of the fieldset is marked by 
// an index called uniqid('fieldset_begin')
// and the end uniqid('fieldset_end')
?>
 <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
    <tr>
      <td align="center"><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_REMINDER_TITLE1 ?></strong></td></tr>
      <tr><td align="center"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE2 ?></td></tr>
      <tr><td align="center"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE3 ?></strong></td></tr>
      <tr><td align="left"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE4 ?></td>
 </tr></table>
<?php
if (!$my->id && VM_SILENT_REGISTRATION != '1' ) {
	// These are the fields for registering a completely new user!
	
	// Create a new fieldset
	$shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_ORDER_PRINT_CUST_INFO_LBL;
		$shopper_fields['username'] = _REGISTER_UNAME;
		$shopper_fields['email'] = _REGISTER_EMAIL;
		$shopper_fields['password'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_1;
		$shopper_fields['password2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PASSWORD_2;
	// Finish the fieldset
	$shopper_fields[uniqid('fieldset_end')] = "";
	// Add the new required fields into the existing array of required fields
	$required_fields = array_merge( $required_fields, Array( 'email', 'username','password','password2') );
}
// Now the fields for customer information...Bill To !
        $shopper_fields[uniqid('fieldset_begin')] = $VM_LANG->_PHPSHOP_USER_FORM_BILLTO_LBL;
	$shopper_fields['company'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME;
	$shopper_fields['title'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_TITLE;
	$shopper_fields['first_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME;
	$shopper_fields['last_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME;
	$shopper_fields['middle_name'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_MIDDLE_NAME;
	$shopper_fields['address_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1;
	$shopper_fields['address_2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_2;
	$shopper_fields['city'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY;
	$shopper_fields['zip'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP;
	$shopper_fields['country'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY;
	if (CAN_SELECT_STATES == '1') {
		$shopper_fields['state'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE;
		$required_fields[] = 'state';
	}
	$shopper_fields['phone_1'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE;
	//$shopper_fields['phone_2'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE2;
	$shopper_fields['fax'] = $VM_LANG->_PHPSHOP_SHOPPER_FORM_FAX;
	if (!$my->id && VM_SILENT_REGISTRATION == '1') {
		$shopper_fields['email'] = _REGISTER_EMAIL;
		$required_fields[] = 'email';
	}
	
	// Extra Fields when defined in the language file
	for( $i=1; $i<6; $i++ ) {
		$property = "_PHPSHOP_SHOPPER_FORM_EXTRA_FIELD_$i";
		if( $VM_LANG->$property != "" ) {
			$shopper_fields['extra_field_'.$i] = $VM_LANG->$property;
		}
	}

$shopper_fields[uniqid('fieldset_end')] = "";

// Is entering bank account information possible?

// Form validation function
//vmCommonHTML::printJS_formvalidation( $required_fields );
?>
<script language="javascript" type="text/javascript" src="includes/js/mambojavascript.js"></script>

<form action="<?php echo $mm_action_url ?>index.php" method="post" name="adminForm">
	
<div style="width:90%;">
	<div style="padding:5px;text-align:center;"><strong>(* = <?php echo _CMN_REQUIRED ?>)</strong></div>
   <?php
   foreach( $shopper_fields as $fieldname => $label) {
   		if( stristr( $fieldname, 'fieldset_begin' )) {
   			echo '<fieldset>
			     <legend class="sectiontableheader">'.$label.'</legend>
			     ';
   			continue;
   		}
   		if( stristr( $fieldname, 'fieldset_end' )) {
   			echo '</fieldset>
			     ';
   			continue;
   		}
   		echo '<div id="'.$fieldname.'_div" style="'.$label_div_style;
   		if (stristr($missing,$fieldname)) {
   			echo $missing_style;
   		}
   		echo '">';
        echo '<label for="'.$fieldname.'_field">'.$label.'</label>';
        if( in_array( $fieldname, $required_fields)) {
        	echo '<strong>* </strong>';
        }
      	echo ' </div>
      <div style="'.$field_div_style.'">'."\n";
      	
      	/**
      	 * This is the most important part of this file
      	 * Here we print the field & its contents!
      	 */
   		switch( $fieldname ) {
   			case 'title':
   				$ps_html->list_user_title(mosGetParam( $_REQUEST, 'title', ''), "id=\"user_title\"");
   			break;
   			
   			case 'country':
   				if( CAN_SELECT_STATES ) {
   					$onchange = "onchange=\"changeStateList();\"";
   				}
   				else {
   					$onchange = "";
   				}
   				$ps_html->list_country("country", $country, "id=\"country_field\" $onchange");
   				break;
   			
   			case 'state':
   				echo $ps_html->dynamic_state_lists( "country", "state", $country, $state );
			    echo "<noscript>\n";
			    $ps_html->list_states("state", $state, "", "id=\"state\"");
			    echo "</noscript>\n";
   				break;
			    
				
			case 'agreed':
				echo '<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputbox" />';
				break;
			case 'password':
			case 'password2':
				echo '<input type="password" id="'.$fieldname.'_field" name="'.$fieldname.'" size="30" class="inputbox" />'."\n";
	   			break;
	   		
	   		case 'extra_field_4': case 'extra_field_5':
	   			eval( "\$ps_html->list_extra_field_$i( mosGetParam( \$_REQUEST, 'extra_field_$i'), \"id=\\\"extra_field_$i\\\"\");" );
	   			break;
	   			
   			default:
		        echo '<input type="text" id="'.$fieldname.'_field" name="'.$fieldname.'" size="30" value="'. mosGetParam( $_REQUEST, $fieldname) .'" class="inputbox" />'."\n";
	   			break;
   		}
   		
   		echo '</div>
			      <br/><br/>';
   }
  echo "<fieldset>
        <legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_REMINDER_NEW."</span></legend>";

    ?>
 <table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform"> 
  <input type="hidden" name="option" value="com_virtuemart" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" >
  <input type="hidden" name="user_id" value="<?php echo $my->id ?>" />

  
  <tr>
  <td width="40%" align="right">
  <strong>
   <?php echo "<label for=\"recip_name\">".$VM_LANG->_PHPSHOP_REMINDER_LIST1_1."</label>*"  ?></strong></td>
   <td width="60%" ><input type="text" id="recip_name" name="recip_name" size="50" value="<?php echo $auth["first_name"]." ".$auth["last_name"];?>" class="inputbox" /></td>
  </tr>
 <tr>
  <td width="40%" align="right" >
    <strong>
    <?php echo "<label for=\"recip_email\">".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</label>*" ?></strong></td>
    <td width="60%" ><input type="text" id="recip_email" name="recip_email" size="40" value="<?php $db->sp("email") ?>" class="inputbox" /></td>
  </tr>

  <tr> 
  <td width="40%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?>*</strong></td>
  <td width="60%" >
    <?php $ps_html->list_month("recip_month", $recip_month) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", $recip_day) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
  </tr>


  <tr> 
  <td width="40%" align="right" >
  <?php echo "<label for=\"occasion\">".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</label>" ?></td>
   <td width="60%" >
  <?php $ps_html->list_user_occasion("occasion", $occasion) ?>
  </td></tr>


<tr> 
  <td width="40%" align="right" valign="top"><strong>
  <?php echo "<label for=\"subject\">".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</label>" ?>*</strong></td>
  <td width="60%" ><textarea title="<?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?>" cols="40" rows="4" name="subject" ><?php echo $subject; ?></textarea></td>
  </td></tr>


</table>

 
   <?php
   echo "</fieldset>";  

    echo '
	<div align="center">';
    
	if( !$mosConfig_useractivation && VM_SILENT_REGISTRATION != '1') {
		echo '<input type="checkbox" name="remember" value="yes" id="remember_login2" checked="checked" />
		<label for="remember_login2">'. _REMEMBER_ME .'</label><br /><br />';
	}
	else {
		echo '<input type="hidden" name="remember" value="yes" />';
	}
	echo '
		<input type="submit" value="'. _BUTTON_SEND_REG . '" class="button" onclick="return( submitregistration());" />
	</div>

	<input type="hidden" name="Itemid" value="'. @$_REQUEST['Itemid'] .'" />
	<input type="hidden" name="gid" value="'. $my->gid .'" />
	<input type="hidden" name="id" value="'. $my->id .'" />
	<input type="hidden" name="user_id" value="'. $my->id .'" />
	<input type="hidden" name="option" value="com_virtuemart" />
	
	<input type="hidden" name="useractivation" value="'. $mosConfig_useractivation .'" />
	<input type="hidden" name="func" value="shopperadd" />
	<input type="hidden" name="page" value="reminder.index" />
	</form>
</div>';


	
?>

