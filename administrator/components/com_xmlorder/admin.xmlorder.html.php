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
class HTML_XmlOrder {
	
	function exportProduct( $rows, $partnerList, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		
		<script language="javascript" type="text/javascript">
		<!--
			/*function checkItem(elementID) {
				alert(document.getElementById('cb'+elementID).checked);
				if( document.getElementById('cb'+elementID).checked == true ) {
					document.getElementById('cb'+elementID).checked = false;
					isChecked(false);
				}else {
					document.getElementById('cb'+elementID).checked = true;
					isChecked(true);
				}
			}*/
			

			function submitbutton(pressbutton) {
				var form = document.adminForm;
				
				// do field validation
				if ( parseInt(form.boxchecked.value) <= 0 ) {
					alert( 'Please choose product to export!' );
				}else if ( !form.partner_id.value ) {
					alert( 'Please choose partner to export!' );
				} else {
					submitform( pressbutton );
				}
			}
		//-->
		</script>
		
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Export Product Manager</th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="60%">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder&act=account"><b>Partner Account Manager</b></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder"><b>XML Order Manager</b></a><br/><br/>
				</td>
				<td align="right">
					<input type="button" style="font:bold 12px Tahoma,Verdana;cursor:pointer;" value="Export" onclick="submitbutton('export');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" style="font:bold 12px Tahoma,Verdana;cursor:pointer;" value="Export & Save for Cron-Job" onclick="submitbutton('export_save');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="right">
					<b>Please choose Partner: </b>
					<?php echo $partnerList; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
		<table class="adminlist" cellpadding="5">
		<tr>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />			
			<th width="15%" align="left">Product SKU</th>
			<th align="left">Product Name</th>
			<th width="8%" align="left">Product ID</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>"">
				<td><?php echo $checked; ?></td>				
				<td style="text-align:left;"><b><?php echo $row->product_sku; ?></b></td>				
				<td style="text-align:left;"><?php echo $row->product_name; ?></td>				
				<td><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="product" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}
	
	
	function showOrder( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>XML Order Manager</th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="60%">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder&act=account"><b>Partner Account Manager</b></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder&act=product"><b>Export Product Manager</b></a><br/><br/>
				</td>
				<td align="right">
                                    <b>Filter:</b>&nbsp;<input name="text_filter" type="text" size="30" maxlength="30" value="<?php echo isset($_REQUEST['text_filter']) ? htmlspecialchars($_REQUEST['text_filter']) : ''; ?>">&nbsp;&nbsp;
				</td>
			</tr>
		</table>
		<table class="adminlist" cellpadding="5">
		<tr>
			<th width="40" align="center">#</th>
			<!--<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>-->
			<th width="18%" align="left">Partner Order ID</th>
			<th width="15%" align="left">Bloomex Order ID</th>
			<th width="20%" align="left">Partner Name</th>
			<th width="10%" align="center">Delivery Date</th>
			<th width="10%" align="center">Current Order status</th>
			<th width="5%" align="center">Shipped</th>
			<th width="10%" align="center">Warehouse</th>
			<th width="10%" align="left">Created Date</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $pageNav->rowNumber( $i ); ?></td>
				<!--<td><?php echo $checked; ?></td>-->
				<td style="text-align:left;font:bold 11px 'Tahoma';color:red;"><?php echo $row->partner_order_id; ?></td>
				<td style="text-align:left;font:bold 11px 'Tahoma';color:blue;"><?php echo $row->order_id; ?></td>
				<td style="text-align:left;"><?php echo $row->partner_name; ?></td>				
				<td style="text-align:center;"><?php echo date( "Y/d/m H:i:s", strtotime($row->ddate)); ?></td>
				<td style="text-align:center;"><?php echo $row->order_status_name; ?></td>
				<td style="text-align:center;"><?php echo ((isset($row->shiped) AND $row->shiped > 0) ? "<b style='color:green'>Yes</b>" : "<b>No</b>" ); ?></td>
				<td style="text-align:center;"><?php echo ( $row->warehouse ? $row->warehouse : "N/A" ); ?></td>				
				<td><?php echo date( "Y/d/m H:i:s", $row->cdate); ?></td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="postal_code" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}
	
	
	//============================================= POSTAL CODE OPTION ===============================================
	function showAccount( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Partner Account Manager</th>
			</tr>
			<tr>
				<td>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder"><b>XML Order Manager</b></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="index2.php?option=com_xmlorder&act=product"><b>Export Product Manager</b></a><br/><br/>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th align="left">Partner Name</th>
			<th width="15%" align="left">Domain Name</th>
			<th width="10%" class="title">FTP Username</th>
			<th width="10%" nowrap="nowrap" align="left">Latest Product Update Time</th>
			<th width="10%" nowrap="nowrap" align="left">Latest Order Status Update Time</th>
			<th width="10%" nowrap="nowrap" align="left">Latest Import Order Update Time</th>
			<th width="8%" nowrap="nowrap" align="center">Published</th>
			<th width="5%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_xmlorder&act=account&task=editA&hidemainmenu=1&id='. $row->id;

			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? 'Published' : 'Unpublished';
			
			$aOption	= explode( "[--1--]", $row->options );
			
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>				
				<td><a href="<?php echo $link; ?>" title="Edit Account Name"><?php echo $row->partner_name; ?></a></td>
				<td><?php echo $row->domain_name; ?></td>
				<td><?php echo $row->ftp_username; ?></td>
				<td align="left"><?php echo strtotime($row->product_updated_time) > 1259514000 ? date( "Y-m-d H:s a", strtotime($row->product_updated_time)) : "N/A"; ?></td>
				<td align="left"><?php echo strtotime($row->status_updated_time) > 1259514000 ? date( "Y-m-d H:s a", strtotime($row->status_updated_time)) : "N/A"; ?></td>
				<td align="left"><?php echo strtotime($row->import_updated_time) > 1259514000 ? date( "Y-m-d H:s a", strtotime($row->import_updated_time)) : "N/A"; ?></td>
				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
					</a>
				</td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="account" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}


	function editAccount( &$row, $option, &$lists ) {
		global $mosConfig_live_site;		
		$aOption	= explode( "[--1--]", $row->options );
	?>
		<script language="javascript" type="text/javascript">
		<!--
		function isValidUsername( value ) {
		   var re = /^[A-Za-z0-9_\s]*$/;
		   return (re.test(value));
		}

		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( !form.ftp_username.value ) {
				alert( 'Please enter partner ftp username!' );
			}else if ( !form.ftp_password.value ) {
				alert( 'Please enter partner ftp password!' );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			Partner Account Detail:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Partner Account Detail</th>
			<tr>	
			<tr>
				<td width="10%" valign="top"><b>Partner Name:</b></td>
				<td>
					<input class="inputbox" type="text" name="partner_name" size="70" maxlength="255" value="<?php echo $row->partner_name;?>" /><br/>
					This field is the username of shopper account which you created in virtuemart user manager<br/>
				</td>
			</tr>
			<tr>
				<td width="10%" valign="top"><b>FTP Domain Name:</b></td>
				<td>
					<input class="inputbox" type="text" name="domain_name" size="70" maxlength="255" value="<?php echo $row->domain_name;?>" /><br/>
				</td>
			</tr>
			<tr>
				<td width="10%" valign="top"><b>FTP Username:</b></td>
				<td>
					<input class="inputbox" type="text" name="ftp_username" size="70" maxlength="255" value="<?php echo $row->ftp_username;?>" /><br/>
					This field is the partner ftp username<br/>
				</td>
			</tr>
			<tr>
				<td width="10%" valign="top"><b>FTP Password:</b></td>
				<td>
					<input class="inputbox" type="password" name="ftp_password" size="70" maxlength="255" value="<?php echo $row->ftp_password;?>" /><br/>
					This field is the partner ftp password<br/>
				</td>
			</tr>
			<tr>
				<td><b>Publish:</b></td>
				<td><?php echo $lists['publish'];?></td>
			</tr>	
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="account" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
	//============================================= XmlOrder OPTION ===============================================
	function showUnAvailableXmlOrder( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>UnAvailable XmlOrder Date Option Manager</th>
			</tr>
			<!--<tr>
				<td align="right" style="padding-right:50px;">
					<b>Filter By:&nbsp;</b>
					<select name="filter_years" size="1" onchange="document.adminForm.submit();">
						<option value='0' selected>Year</option>
						<?php
							$filter_years 	= mosGetParam( $_POST, "filter_years", 0 );
							$yearNow		= date( "Y" );
							for ( $i = $yearNow; $i <= $yearNow + 1 ; $i++ ) {
								if( intval($filter_years) == $i ) {
									echo "<option value='$i' selected>$i</option>";
								}else {
									echo "<option value='$i'>$i</option>";
								}
							}
						?>
					</select><br/><br/>
				</td>
			</tr>-->
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Date( Month / Day )</th>
			<th width="40%" nowrap="nowrap" align="left">Description</th>
			<th width="20%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_XmlOrder&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit XmlOrder Option"><b style="font:bold 11px Tahoma;"><?php echo $row->name; ?></b></a></td>
				<td align="left"><?php echo $row->options; ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}


	function editUnAvailableXmlOrder( &$row, $option, &$lists ) {
		global $mosConfig_live_site;		
		
		$aDate	= explode( "/", $row->name );
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( form.days.value == "0" || form.months.value == "0"  ) {
				alert( "Please choose month and day!" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			UnAvailable XmlOrder Date Option:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">UnAvailable XmlOrder Date Option Detail</th>
			<tr>	
			<tr>
				<td width="15%"><b>Month:</b></td>
				<td>
					<select name="months" size="1">
						<option value='0' selected>Month</option>
						<?php
							for ( $i = 1; $i <= 12 ;$i++ ) {
								if( intval($aDate[0]) == $i ) {
									if( $i < 10 ) {
										echo "<option value='$i' selected>0$i</option>";
									}else {
										echo "<option value='$i' selected>$i</option>";
									}
								}else {
									if( $i < 10 ) {
										echo "<option value='$i'>0$i</option>";
									}else {
										echo "<option value='$i'>$i</option>";
									}
								}
							}
						?>
					</select>
				</td>
			<tr>
				<td width="15%"><b>Day:</b></td>
				<td>
					<select name="days" size="1">
						<option value='0' selected>Day</option>
						<?php
							for ( $i = 1; $i <= 31 ;$i++ ) {
								if( intval($aDate[1]) == $i ) {
									if( $i < 10 ) {
										echo "<option value='$i' selected>0$i</option>";
									}else {
										echo "<option value='$i' selected>$i</option>";
									}
								}else {
									if( $i < 10 ) {
										echo "<option value='$i'>0$i</option>";
									}else {
										echo "<option value='$i'>$i</option>";
									}
								}
							}
						?>
					</select>
					<!--&nbsp;<b>/</b>&nbsp;
					<select name="years" size="1">
						<option value='0' selected>Year</option>
						<?php
							$yearNow	= date( "Y" );
							for ( $i = $yearNow; $i <= $yearNow + 1 ; $i++ ) {
								if( intval($aDate[2]) == $i ) {
									echo "<option value='$i' selected>$i</option>";
								}else {
									echo "<option value='$i'>$i</option>";
								}
							}
						?>
					</select>-->
				</td>
			</tr>
			<tr>
				<td width="10%"><b>Description:</b></td>
				<td><input class="inputbox" type="text" name="options" size="100" maxlength="255" value="<?php echo $row->options; ?>" /></td>
			</tr>	
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
	//============================================= SPECIAL XmlOrder OPTION ===============================================
	function showSpecialXmlOrder( &$rows, &$pageNav, $option ) {
		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>Special XmlOrder Date Option Manager</th>
			</tr>
			<!--<tr>
				<td align="right" style="padding-right:50px;">
					<b>Filter By:&nbsp;</b>
					<select name="filter_years" size="1" onchange="document.adminForm.submit();">
						<option value='0' selected>Year</option>
						<?php
							$filter_years 	= mosGetParam( $_POST, "filter_years", 0 );
							$yearNow		= date( "Y" );
							for ( $i = $yearNow; $i <= $yearNow + 1 ; $i++ ) {
								if( intval($filter_years) == $i ) {
									echo "<option value='$i' selected>$i</option>";
								}else {
									echo "<option value='$i'>$i</option>";
								}
							}
						?>
					</select><br/><br/>
				</td>
			</tr>-->
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">#</th>
			<th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class="title">Date( Month / Day )</th>
			<th width="40%" nowrap="nowrap" align="left">Price</th>
			<th width="20%" align="left">&nbsp;</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			mosMakeHtmlSafe($row);
			$link 	= 'index2.php?option=com_XmlOrder&act=special_XmlOrder&task=editA&hidemainmenu=1&id='. $row->id;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $pageNav->rowNumber( $i ); ?></td>
				<td><?php echo $checked; ?></td>
				<td><a href="<?php echo $link; ?>" title="Edit XmlOrder Option"><b style="font:bold 11px Tahoma;"><?php echo $row->name; ?></b></a></td>
				<td align="left"><strong>$<?php echo number_format( $row->options, 2, ".", " " ) ; ?></strong></td>
				<td>&nbsp;</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="special_XmlOrder" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}


	function editSpecialXmlOrder( &$row, $option, &$lists ) {
		global $mosConfig_live_site;		
		
		$aDate	= explode( "/", $row->name );
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if ( form.days.value == "0" || form.months.value == "0"  ) {
				alert( "Please choose month and day!" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
			Special XmlOrder Date Option:
			<small>
			<?php echo $row->id ? 'Edit' : 'New';?>
			</small>
			</th>
		</tr>
		</table>

		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Special XmlOrder Date Option Detail</th>
			<tr>	
			<tr>
				<td width="15%"><b>Month:</b></td>
				<td>
					<select name="months" size="1">
						<option value='0' selected>Month</option>
						<?php
							for ( $i = 1; $i <= 12 ;$i++ ) {
								if( intval($aDate[0]) == $i ) {
									if( $i < 10 ) {
										echo "<option value='$i' selected>0$i</option>";
									}else {
										echo "<option value='$i' selected>$i</option>";
									}
								}else {
									if( $i < 10 ) {
										echo "<option value='$i'>0$i</option>";
									}else {
										echo "<option value='$i'>$i</option>";
									}
								}
							}
						?>
					</select>
				</td>
			<tr>
				<td width="15%"><b>Day:</b></td>
				<td>
					<select name="days" size="1">
						<option value='0' selected>Day</option>
						<?php
							for ( $i = 1; $i <= 31 ;$i++ ) {
								if( intval($aDate[1]) == $i ) {
									if( $i < 10 ) {
										echo "<option value='$i' selected>0$i</option>";
									}else {
										echo "<option value='$i' selected>$i</option>";
									}
								}else {
									if( $i < 10 ) {
										echo "<option value='$i'>0$i</option>";
									}else {
										echo "<option value='$i'>$i</option>";
									}
								}
							}
						?>
					</select>
					<!--&nbsp;<b>/</b>&nbsp;
					<select name="years" size="1">
						<option value='0' selected>Year</option>
						<?php
							$yearNow	= date( "Y" );
							for ( $i = $yearNow; $i <= $yearNow + 1 ; $i++ ) {
								if( intval($aDate[2]) == $i ) {
									echo "<option value='$i' selected>$i</option>";
								}else {
									echo "<option value='$i'>$i</option>";
								}
							}
						?>
					</select>-->
				</td>
			</tr>
			<tr>
				<td width="10%"><b>Price:</b></td>
				<td><strong>$</strong><input class="inputbox" type="text" name="options" size="10" maxlength="10" value="<?php echo $row->options; ?>" /></td>
			</tr>	
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="special_XmlOrder" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	 	
	//============================================= CUT OFF TIME CONFIGURATION ===============================================
	function editCutOffTime( &$row, $option ) {
		global $mosConfig_live_site;
		
		$aOptionParam = explode( "[--1--]", $row[3] );		
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			// do field validation
			if ( form.hours.value == "" ) {
				alert( "You must choose hour number." );
				return;
			}
			
			if ( form.minutes.value == "" ) {
				alert( "You must choose minute number." );
				return;
			}
			
			submitform( pressbutton );
		}
		//-->
		</script>

		<form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th>
				Cut Off Time Configuration:<small>Edit</small>
			</th>
		</tr>
		</table>
		<table width="100%" class="adminform">
			<tr>
				<th colspan="2">Cut Off Time Configuration Detail</th>
			<tr>	
			<tr>
				<td width="10%" valign="middle" ><b>Limit Time:</b></td>
				<td style="height:55px;">
					<select name="hours" size="1" style="font:bold 11px Tahoma;">
						<?php
							for ( $i = 0; $i < 24; $i ++ ) {
								if( intval($aOptionParam[0] == $i ) ) {
									if( $i < 10 ) {
										echo '<option value="0'.$i.'" selected>0'.$i.'</option>';
									}else {
										echo '<option value="'.$i.'" selected>'.$i.'</option>';
									}
								}else {
									if( $i < 10 ) {
										echo '<option value="0'.$i.'">0'.$i.'</option>';
									}else {
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								}
							}						
						?>
					</select> <b>:</b>
					<select name="minutes" size="1" style="font:bold 11px Tahoma;">
						<?php
							for ( $i = 0; $i < 60; $i ++ ) {
								if( intval($aOptionParam[1] == $i ) ) {
									if( $i < 10 ) {
										echo '<option value="0'.$i.'" selected>0'.$i.'</option>';
									}else {
										echo '<option value="'.$i.'" selected>'.$i.'</option>';
									}
								}else {
									if( $i < 10 ) {
										echo '<option value="0'.$i.'">0'.$i.'</option>';
									}else {
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								}
							}						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th colspan="2">The XmlOrder Extra Fee( for The Same Day) Detail:</th>
			<tr>	
			<tr>
				<td width="10%" valign="middle" ><b>Price:</b></td>
				<td style="height:35px;">
					<b>$</b><input class="inputbox" type="text" name="XmlOrder_fee" size="8" maxlength="8" value="<?php  if( !$aOptionParam[2] ) echo "0"; else echo $aOptionParam[2];?>" />
				</td>
			</tr>
		</table>		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="cut_off_time" />
		<input type="hidden" name="id" value="<?php echo $row[0]; ?>" />
		<input type="hidden" name="task" value="" />		
		</form>
		<?php
	}
	
	
}
?>
