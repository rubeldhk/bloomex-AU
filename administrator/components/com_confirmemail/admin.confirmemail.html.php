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

/**
* @package Joomla
* @subpackage Category
*/
class HTML_ConfirmEmail {
	
	function showConfirmEmailList( $option, $table,$text_confirm,$date_conf ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="" method="post" enctype="multipart/form-data" name="adminForm">
                    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/administrator/components/virtuemart/html/jquery.js" ></script>
                    <link rel="stylesheet" href="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-ui.css" />
                    <script src="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-1.9.1.js"></script>
                    <script src="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-ui.js"></script>
                    <script>
$(function() {
$( "#dfdate" ).datepicker();
});
</script>
		<table class="adminheading">
			<tr>
				<th>Confirm Email Manager</th>
			</tr>
			<tr>
				<td  style="padding:0px 20px 10px 0px;">
                                    Select file <input type="file" id='filename' name="filename"> 
                                    <input type="submit" value="Upload" >
				</td>
			</tr>
                        <tr>
                            <td>
                               <?php echo $text_confirm; ?>
                            </td>
                        </tr>
                          <tr>
                            <td>
                                <p>Date: <input type="text" id="dfdate" name="dfdate" value="<?php echo $date_conf; ?> "></p>
                            </td>
                        </tr>
		</table>
		<table id="emailList" class="adminlist">
                    		<tr>
			<th width="3%" ></th>
			<th width="5%">Order number</th>
			<th width="10%" nowrap="nowrap" align="left">First name</th>
			<th width="10%" nowrap="nowrap" align="left">User email</th>
			<th width="5%" nowrap="nowrap" align="left">Order Status</th>
			<th width="10%" nowrap="nowrap" align="center">Tracking number(if exist)</th>
			<th width="50%" nowrap="nowrap" align="center">Order history</th>
                                </tr>
		<?php echo $table; ?>
		</table>
		

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="task" value="GetList" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
	
		<?php
	}
	//============================================= Location OPTION ===============================================
	function showConfirmEmail( $option ,$text_confirm ) {
            global $mosConfig_live_site;
		mosCommonHTML::loadOverlib();
		?>
		<form action="" method="post" enctype="multipart/form-data" name="adminForm">
                    <script type="text/javascript" src="<?php echo $mosConfig_live_site ?>/administrator/components/virtuemart/html/jquery.js" ></script>
                    <link rel="stylesheet" href="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-ui.css" />
                    <script src="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-1.9.1.js"></script>
                    <script src="http://<?php echo $_SERVER["SERVER_NAME"]?>/administrator/components/com_confirmemail/calendar/jquery-ui.js"></script>
                    <script>
$(function() {
$( "#dfdate" ).datepicker();
});
</script>
		<table class="adminheading">
			<tr>
				<th>Confirm Email Manager</th>
			</tr>
			<tr>
				<td style="padding:0px 20px 10px 0px;">
                                    Select file <input type="file" id='filename' name="filename"> 
                                    <input type="submit" value="Upload" >
				</td>
                                
			</tr>
                        <tr>
                            <td>
                               <?php echo $text_confirm; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>Date: <input type="text" id="dfdate" name="dfdate"></p>
                            </td>
                        </tr>
		</table>
		<table id="emailList" class="adminlist">
		
		</table>
		

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="task" value="GetList" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
	
		<?php
	}
	
	
}
?>
