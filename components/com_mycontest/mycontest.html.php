<?php
/**
* @version $Id: admin.Category.html.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
global  $mosConfig_absolute_path;
/*require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" );*/
/**
* @package Joomla
* @subpackage Category
*/
class HTML_MyContest {
	function landingPage( ) {
		global $mosConfig_live_site, $mosConfig_lang;
	?>		
		<center>
			<table width="559" cellpadding="0" cellspacing="0" border="0" >
				<tr>
					<td>
						<div class="landing-page">
							<a href="http://www.youtube.com/user/AllianceFilms#p/u/0/sMhCWFL464c" class="trailer-page" target="_blank">&nbsp;</a>
							<a href="hoodwinkedtoo-rules.html" class="rules-page" target="_blank">&nbsp;</a>
							<a href="hoodwinkedtoo-enter.html" class="enter-page">&nbsp;</a>						
						</div>
					</td>
				</tr>
			</table>		
		</center>
	<?php	
	}
	
	
	function makeForm( ) {
		global $mosConfig_live_site;
	?>
		<script type="text/javascript">
			function checkForm() {
				oForm	= document.contestForm;
				
				if( oForm.first_name.value == "" ) {
					alert("Please enter First Name");
					oForm.first_name.focus();
					return false;
				}
				
				if( oForm.last_name.value == "" ) {
					alert("Please enter Last Name");
					oForm.last_name.focus();
					return false;
				}
				
				if( oForm.address.value == "" ) {
					alert("Please enter Address");
					oForm.address.focus();
					return false;
				}
				
				if( oForm.city.value == "" ) {
					alert("Please enter City");
					oForm.city.focus();
					return false;
				}
				
				if( oForm.province.value == "" ) {
					alert("Please enter Province");
					oForm.province.focus();
					return false;
				}
				
				
				if( oForm.postal_code.value == "" ) {
					alert("Please enter Postal Code");
					oForm.postal_code.focus();
					return false;
				}
				if( !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(oForm.email_address.value)) ) {
					alert("Please enter Email Address");
					oForm.email_address.focus();
					return false;
				}
								
				if( oForm.telephone.value == "" ) {
					alert("Please enter Telephone");
					oForm.telephone.focus();
					return false;
				}
				
				if( oForm.genger.value == "" ) {
					alert("Please select Gender");
					return false;
				}
				
				if( oForm.desc.value == "" ) {
					alert("Please enter Description");
					oForm.desc.focus();
					return false;
				}
				
				if( oForm.agree.checked == false ) {
					alert("Please confirm read and agree to the Contest rules");
					return false;
				}
				
				oForm.task.value	= "saveForm";
				return true;
			}
		</script>
		<center>
			<form method="POST" action="index.php?option=com_mycontest" name="contestForm" onsubmit="return checkForm();">
			<input type="hidden" name="task" value="">
			<table width="559" cellpadding="2" cellspacing="0" border="0" class="enter-page" >
				<tr>
					<td colspan="2" style="height:90px;">&nbsp;</td>
				</tr>
				<tr>
					<td width="10%">&nbsp;</td>
					<td class="title" width="90%">Items marked <span class="require">*</span> are required fields<br/><br/></td>					
				</tr>
				<tr>
					<td width="10%">&nbsp;</td>
					<td class="title" width="90%">First name<span class="require">*</span>:</td>					
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="first_name" size="40" /></td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td class="title">Last name<span class="require">*</span>:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="last_name" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Address<span class="require">*</span>:</td>					
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="address" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">City<span class="require">*</span>:</td>					
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="city" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Province<span class="require">*</span>:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="province" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Postal code<span class="require">*</span>:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="postal_code" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Email address<span class="require">*</span>:</td>					
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="email_address" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Telephone<span class="require">*</span>:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field"><input type="text" name="telephone" size="40" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Gender<span class="require">*</span>:</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field">
						<select name="genger" size="1">
							<option value="" selected>Select Gender</option>
							<option value="female">female</option>
							<option value="male">male</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="title">Why is your mother or Granny the greatest person ever?<span class="require">*</span>:<br/> </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="text-field">
						<textarea name="desc" rows="10" cols="55"></textarea>
						<span style="display:block;text-align:right;">(100 Words or Less)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"><input type="checkbox" name="notification" value="1" /> Please send me more info on new products and specials from Bloomex.ca.</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"><input type="checkbox" name="keep_me" value="1" /> Please keep me informed of upcoming films, contests and special announcements from Alliance Films</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"><input type="checkbox" name="agree" value="1" /> <span class="require">*</span>I have read and agree to the Contest rules.</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"></td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field">
						<input type="submit" name="submit" value="Submit Form" />
					</td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"><br/><br/><br/></td>
				</tr>
			</table>
			</form>	
		</center>
		<?php	
	}
	
	
	function thankYou( ) {
		global $mosConfig_live_site;
	?>
		<center>
			<form method="POST" action="index.php?option=com_mycontest" name="contestForm" onsubmit="return checkForm();">
			<input type="hidden" name="task" value="">
			<table width="559" cellpadding="4" cellspacing="0" border="0" class="enter-page" >
				<tr>
					<td colspan="2" style="height:90px;">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><span class="require"><br/><br/><br/><b>Thank you. We have received your entry.</b></span></td>
				</tr>
				<tr>
					<td class="title"></td>
					<td class="text-field"><br/><br/><br/></td>
				</tr>
			</table>
			</form>	
		</center>
		<?php	
	}
}
?>
