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
class HTML_ProFlower {
	
	//============================================= POSTAL CODE OPTION ===============================================
	function showProFlower( &$rows, &$pageNav, $option, $aList ) {
		global $mosConfig_live_site;
		mosCommonHTML::loadOverlib();
		$sImgLoading	= "$mosConfig_live_site/administrator/components/com_virtuemart/html/jquery_ajax.gif";
		echo '<script type="text/javascript" src="'.$mosConfig_live_site.'/administrator/components/com_virtuemart/html/jquery.js" ></script>';
		?>
		
		<style type="text/css">
			table.adminlist tr th {
				font:bold 12px Tahoma, Verdana;
				padding:5px 0px 5px 0px;
			}	
			
			table.adminlist tr.items td {
				font:normal 12px Tahoma, Verdana;
				padding:10px 0px 10px 0px;
			}
			
			div.data table td {
				padding:10px;
			}
			
			div.data table th {
				text-align:center;
				padding:10px;
			}
			
			div.order-header {
				font:bold 12px Tahoma, Verdana;
				background-color:#BE4C34;				
				text-transform:uppercase;
				margin:0px 0px 2px 0px;
				padding:5px 0px 5px 0px;
				text-align:center;
				line-height:160%;
				color:#FFF;
			}
			
			div.close-button{
				font:bold 11px Tahoma, Verdana;
				margin:0px 0px 0px 10px;
				text-transform:none;
				line-height:170%;	
				color:#FFFF00;
				cursor:pointer;
				float:left;					
			}		
			
				
		</style>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
			<tr>
				<th>ProFlowers Order Manager</th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="right">
					<b>Filter:</b>
					&nbsp;<input name="text_filter" type="text" size="10" maxlength="10" value="<?php echo $_REQUEST['text_filter']; ?>">&nbsp;&nbsp;
					<?php echo $aList['status_filter'];?>&nbsp;&nbsp;
					<b>Order By:</b>&nbsp;
					<?php echo $aList['orderby'];?>
					&nbsp;&nbsp;
					<input type="button" value="Clear" onclick="location.href='index2.php?option=com_proflower';"/>
				</td>
			</tr>
		</table>
		<table class="adminlist" cellpadding="5">
		<tr>
			<th width="80" align="center">#</th>
			<th width="10%" align="left">ProFlower ID</th>
			<th width="14%" align="left">Bloomex Order ID</th>
			<th width="14%" align="left">Sender Name</th>
			<th width="12%" align="center">Delivery Date</th>
			<th width="12%" align="center">Status Changed</th>
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
			<tr class="items <?php echo "row$k"; ?>">
				<td align="center"><b><?php echo $pageNav->rowNumber( $i ); ?></b></td>
				<td style="text-align:left;font:bold 11px 'Tahoma';color:red;">
					<?php echo "<a href=\"#\" onclick=\"return false;\" class=\"order-history-detail\" rel=\"". $i ."\" title=\"".$row->order_id."\">" . $row->proflower_id . "</a>" ; ?>
				</td>
				<td style="text-align:left;font:bold 11px 'Tahoma';color:blue;">
					<?php echo "<a href=\"#\" onclick=\"return false;\" class=\"order-history-detail\" rel=\"". $i ."\" title=\"".$row->order_id."\">" . $row->order_id . "</a>" ; ?>
				</td>
				<td style="text-align:left;"><?php echo $row->sender_name; ?></td>				
				<td style="text-align:center;"><?php echo date( "Y/d/m H:i:s", strtotime($row->ddate)); ?></td>
				<td style="text-align:center;"><?php echo date( "Y/d/m H:i:s", $row->mdate); ?></td>
				<td style="text-align:center;"><?php echo $row->order_status_name; ?></td>
				<td style="text-align:center;"><?php echo ( $row->shiped > 0 ? "<b style='color:green'>Yes</b>" : "<b>No</b>" ); ?></td>
				<td style="text-align:center;"><?php echo ( $row->warehouse ? $row->warehouse : "N/A" ); ?></td>				
				<td><?php echo date( "Y/d/m H:i:s", $row->cdate); ?></td>
			</tr>
			<tr>
				<td id="rowRefreshOrderHistory<?php echo $row->order_id; ?>" colspan="10" class="rowData">
					<div class="data" style="display:none;" id="refreshOrderHistory<?php echo $row->order_id; ?>"></div>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		
		<script type="text/javascript">			
			 $(".order-history-detail").click(function () {	 	
		     	orderID		= $(this).attr("title");		     	
		     	if( !orderID ) {
		 			alert('Load order information wrong');
		 			return;
		 		}
		 		
		 		$("#refreshOrderHistory" + orderID).html('<div style="font: bold 11px Tahoma;color:#FF6600;line-height:24px;"><img src="<?php echo $sImgLoading; ?>" align="absmiddle"/> Loading...</div>'); 	
		     				     			
     			$.post( "index2.php",
			   	  	{ option: 				"com_ajaxorder", 
			   	  	  task: 				"loadOrderHistory", 
			   	  	  id: 					orderID
			   	  	},					   	  	  
				   	function(data){		
				   		closeAll();
				   		
				     	$("#refreshOrderHistory"+ orderID).css("display", "block"); 				     	
				     	$("#rowRefreshOrderHistory"+ orderID).css("background-color", "#B0C4DE"); 
				     	$("#refreshOrderHistory"+ orderID).html('<div class="order-header"><div onclick="closeAll();" class="close-button">(X)Close</div>Order History</div>' + data); 	
				   	}
				);
			 });
			 
			 function closeAll() {
			 	$(".data").css("display", "none"); 		
			 	$(".rowData").css("background-color", "#FFFFFF"); 	 	
			 }
		</script>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="act" value="postal_code" />
		<input type="hidden" name="task" value="" />		
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">		
		</form>
		<?php
	}
}
?>
