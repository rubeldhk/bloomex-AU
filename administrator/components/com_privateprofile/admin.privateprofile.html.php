<?php
/**
* @version $Id: admin.users.html.php 3513 2006-05-15 20:52:25Z stingrey $
* @package Joomla
* @subpackage Users
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
* @subpackage Users
*/
class HTML_PrivateProfile {

	static function edituser( ) {
		global $my, $acl;
		global $mosConfig_live_site;

		mosCommonHTML::loadOverlib();
		
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				form.option.value	= "";
				submitform( pressbutton );
				return;
			}
			
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (trim(form.oldpassword.value) == "") {
				alert( "You must enter old password." );
			} else if (trim(form.password.value) != "" && form.password.value != form.password2.value){
				alert( "Password do not match." );
			}  else if (trim(form.oldpassword.value) == form.password2.value){
				alert( "You must create a new password." );
			} else {
				submitform( pressbutton );
			}
		}

		</script>
		<form action="index2.php" method="post" name="adminForm">

		<table class="adminheading">
		<tr>
			<th class="user">
			User: <small>Update</small>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr>
			<td width="60%" valign="top">
				<table class="adminform">
				<tr>
					<th colspan="2">
					User Details
					</th>
				</tr>				
				<tr>
					<td width="10%">
					<b>Old Password:</b>
					</td>
					<td width="90%">
					<input class="inputbox" type="password" name="oldpassword" size="40" value="" />
					</td>
				</tr><tr>
					<td>
					<b>New Password:</b>
					</td>
					<td>
					<input class="inputbox" type="password" name="password" size="40" value="" />
					</td>
				</tr>
				<tr>
					<td>
					<b>Verify Password:</b>
					</td>
					<td>
					<input class="inputbox" type="password" name="password2" size="40" value="" />
					</td>
				</tr>
		</tr>
		</table>

		<input type="hidden" name="id" value="<?php echo $my->id; ?>" />
		<input type="hidden" name="option" value="com_privateprofile" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>