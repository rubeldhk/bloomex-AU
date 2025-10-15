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

class HTML_MailChimp {
	
	function reminderForm( $sMsg ) {
		global $mosConfig_live_site;
		
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
	?>
		<script type="text/javascript">
		<!--
			function checkSave() {
				if( document.getElementById("occPerson").value == "" ) {
					alert("Please enter person info.");
					return false;
				}
					
				if( document.getElementById("occOccation").value == "" ) {
					alert("Please enter occation info.");
					return false;
				}
					
				if( document.getElementById("occEmail").value == "" ) {
					alert("Please enter email address info.");
					return false;
				}
					
				if( !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(document.getElementById("occEmail").value)) ) {
					alert("Your email address is incorrect.");
					return false;
				}	
				
				return true;
			}
		//-->
		</script>
		<table cellpadding="0" cellspacing="0" border="0" class="occ-form" width="562">
			<tr>
				<td>								
					<div class="occ-form">
						<h3 class="occ-form-heading">
							Remember Every Special Occation
						</h3>
						<div class="occ-form-inner">
							<?php 
								if( !empty($sMsg) ) {
							?>	
								<p style=" font-size:14px;margin:0 0 20px;font-weight: bold;color:#0216d0;"><?php echo $sMsg; ?></p>
							<?php 	
								}else{
							?>		
									<p style=" font-size:14px;margin:0 0 20px;">Always for prepared for Birthday, Anniversaries, and any other special occation in your life. Use our simple and free Email Reminder Service to make sure you remember the special people in your life.</p>
									<p style="font-size:14px; color:#2F1A5F;font-weight:bold;margin:0;">Our Reminder Service makes it easy to...</p>
									<ul style="">
										<li>Request reminder emails for as many occations as you need.</li>
										<li>Receive reminders 3 days before the date you request so you always have plenty of time to make your gift decision.</li>
										<li>Receive Special Bloomex offers</li>
									</ul>
									<form name="FMainChimp" id="FMainChimp" action="" method="POST" onsubmit="return checkSave();"  >
										<input  type="hidden" name="task" value="reminderSave"/>
										<div class="input-text">
											<label>Person</label>
											<input type="text" class="inputbox" name="occPerson" id="occPerson" size="35" value="" />
										</div>
										<div class="input-text">
											<label>Occation</label>
											<input type="text" class="inputbox" name="occOccation" id="occOccation" size="35" value="" />
										</div>
										<div class="input-text">
											<label>Date</label>
											<input type="text" class="inputbox" name="occDate" id="occDate" size="10" maxlength="10" style="width:100px;" readonly="readonly" value="<?php echo date("Y-m-d");?>"  />
											<input name="reset" type="reset" class="button" onclick="return showCalendar('occDate', 'y-mm-dd');" value="..." />
										</div>
										<div class="input-text">
											<label>Your Email</label>
											<input type="text" class="inputbox" name="occEmail" id="occEmail" size="35" value="" />
										</div>
										<div class="input-text">
											<input type="submit" class="button" value="Submit">
										</div>
									</form>
							<?php 
								}
							?>
						</div>
					</div>
				</td>
			</tr>
		</table>
	<?php	
	}
	
	
	function loadMailChimp( $option, $aInfomation, $aList ) {
		global $mosConfig_live_site;
		$sImagePath	= $mosConfig_live_site."/administrator/images/";
	}
	
}
?>
