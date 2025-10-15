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

// no direct
defined( '_VALID_MOS' ) or die( 'Restricted access' );
global  $mosConfig_absolute_path;
/*require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_database.php" );
require_once( $mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_html.php" );*/
/**
* @package Joomla
* @subpackage Category
*/
class HTML_AjaxOrder {
	function selectDeliveryOption( $aInfomation  ) {
		global $mosConfig_live_site, $mosConfig_lang;
	?>
		<script type="text/javascript">
			function moveMonth( sCurrentDate ) {
                             var del_state = $j("input[name='current_state_tax']").val().split('_');
				$j("#loadCalendarAjax").css('display','block');
				$j.post( "index.php",
					{ 	option: 				"com_ajaxorder",
						task: 				"selectDeliveryOption",
						delivery_date:			sCurrentDate,
                                                delivery_state: del_state[1],
						delivery_default_date:	"<?php echo $aInfomation['delivery_default_date']; ?>",
						delivery_postalcode:	$j("input[name='zip_checked_value']").val(),
						shipping_method:		$j("input[name='shipping_method']").val(),
                                                user_info_id: $j("input[name='ship_to_info_id']:checked").val()
					},
					function(data){
						$j('#selectDeliveryOptionData').html(data);
					}
				);
			}

			function chooseShippingMethod( sCurrentDate, sShippingMethod ) {
				$j("input[name='shipping_method']").val(sShippingMethod);
				$j("#loadCalendarAjax").css('display','block');
				$j.post( "index.php",
					{ 	option: 				"com_ajaxorder",
						task: 					"selectDeliveryOption",
						delivery_date:			sCurrentDate,
						delivery_postalcode:	$j("input[name='zip_checked_value']").val(),
						shipping_method:		$j("input[name='shipping_method']").val()
					},
					function(data){
						$j('#selectDeliveryOptionData').html(data);
					}
				);
			}

			//function changeDay( date, bHideDeliverySameDay, nSpecialDeliver,  freeshipping, nShippingSurcharge, PostCodeReturn) {
                        function changeDay( date, message) {
				$j('#currentSelectDay').html(date);

                                if (message)
                                {
                                    $j('#message').css('display','block');
                                    $j('#message').html(message);
				}
                                else
                                {
                                    $j('#message').html("");
                                    $j('#message').css('display','none');
				}
                                /*
				if( nSpecialDeliver ) {
					$j('#specialDeliver').css('display','block');
					$j('#nSpecialDeliver').html(nSpecialDeliver);
				}else {
					$j('#nSpecialDeliver').html("");
					$j('#specialDeliver').css('display','none');
				}

				if( nShippingSurcharge ) {
					$j('#shippingSurcharge').css('display','block');
					$j('#nShippingSurcharge').html(nShippingSurcharge);
				}else {
					$j('#nShippingSurcharge').html("");
					$j('#shippingSurcharge').css('display','none');
				}

                                if( PostCodeReturn ) {
					$j('#PostCodeReturn').css('display','block');
					$j('#nPostCodeReturn').html(PostCodeReturn);
				}else {
					$j('#nPostCodeReturn').html("");
					$j('#PostCodeReturn').css('display','none');
				}

				if( bHideDeliverySameDay || freeshipping == 1 ) {
					$j('#deliverSameDay').css('display','none');
				}else {
					$j('#deliverSameDay').css('display','block');
				}
                                */

			}
                        
            function chooseDay (date, total_delivery_price)
            {
                $j.modal.close();
                
                $j('#delivery_date_2').val(date);
                
                $j('#checkout_delivery_date_error').hide();
                $j('#checkout_delivery_date_loader').show();
                
                $j.ajax({
                    type: "POST",
                    url: "index.php",
                    async: true,
                    dataType: 'json',
                    data: ({
                        option: 'com_ajaxorder',
                        task: 'CheckoutAjax',
                        action: 'GetShippingPrice',
                        delivery_date: date,
                        user_info_id: $j("input[name='ship_to_info_id']:checked").val()
                    }),
                    success: function (data) {
                        if (data.result)
                        {
                            $j('#checkout_delivery_date_loader').hide();
                        }
                        else
                        {
                            $j('#delivery_date_2').val('');
                            
                            $j('#checkout_delivery_date_error').find('td').find('.checkout_ajax_error').html('');
                    
                            $j.each(data.error, function( key, value ) {
                                $j('#checkout_delivery_date_error').find('td').find('.checkout_ajax_error').append(value+'<br/>');
                            });
                            
                            $j('#checkout_delivery_date_loader').hide();
                            $j('#checkout_delivery_date_error').show();
                        }
                    }
                });
            }
            
            $j('input[name="checkout_shipping_method_radio"]').click(function () {

                var $t = $j(this);
                
                $j.ajax({
                    type: "POST",
                    url: "index.php",
                    async: true,
                    dataType: 'json',
                    data: ({
                        option: 'com_ajaxorder',
                        task: 'CheckoutAjax',
                        action: 'SetShippingMethod',
                        shipping_method: $t.val()
                    }),
                    success: function (data) {
                        if (data.result)
                        {
                            $j.ajax({
                                type: "POST",
                                url: "index.php",
                                async: true,
                                data: ({
                                    option: "com_ajaxorder",
                                    task: "selectDeliveryOption",
                                    product_id_string: $j("input[name='product_id_string']").val(),
                                    delivery_date: $t.attr('current_date'),
                                    delivery_postalcode: $j("input[name='zip_checked_value']").val(),
                                    shipping_method: $t.val(),
                                    user_id: $j("input[name='user_id']").val(),
                                    user_info_id: $j("input[name='ship_to_info_id']:checked").val()
                                }),
                                success: function (data) {
                                    $j('#selectDeliveryOptionData').html(data);
                                }
                            });
                        }   
                        else
                        {
                            $j.modal.close();
                            
                            $j('#checkout_delivery_date_error').find('td').find('.checkout_ajax_error').html(data.error);
                            $j('#checkout_delivery_date_loader').hide();
                            $j('#checkout_delivery_date_error').show();
                        }
                    }
                });
            });
            
            <?php
            if (!$_SESSION['checkout_shipping_method'])
            {
                ?>
                $j('input[name="checkout_shipping_method_radio"]:first').prop('checked', true);
                $j('input[name="checkout_shipping_method_radio"]:first').trigger('click');
                <?php
            }
            ?>

              /*
		//function chooseDay ( date, freeshipping, add_cutofftime_fee, nShippingSurcharge ) {
                       function chooseDay (date, total_delivery_price,type)
                       {
				//console.log("chosen:" + freeshipping);
              
//				if( add_cutofftime_fee == "add_cutofftime_fee" )
//                                {
//					$j("input[name=cutofftime]").val(0);
//				}
//
//				document.getElementById('free_shipping').value = freeshipping;

                                $j("input[name='deliver_fee_type']").val(type);
				document.getElementById('delivery_date_2').value = date;
                                //$j('#calcualte-total-deliver-fee').html('$'+total_delivery_price);
                                $j("input[name='deliver_fee']").val(total_delivery_price);

				$j.modal.close();
				sOptionString	= $j("input[name='zip_checked_value']").val();
   				changDeliver(sOptionString, "");

				//$j("input[name=deliver_reduce_surcharge]").val(nShippingSurcharge);
				//alert($j("input[name=deliver_reduce_surcharge]").val());
			}*/
		</script>
		<div id="selectDeliveryOptionData">
			<h2 class="select-delivery-option"><?php echo _DELIVERY_CALENDAR; ?></h2>
			<div class="delivery-calendar">
				<div class="delivery-calendar-left">
					<?php echo _DELIVERY_CALENDAR_NOTE; ?>
					<div class="print-calendar">
						<div class="month-actions">
							<?php
								if( $aInfomation['PreDeliveryDate'] ) {
									$sPreOnClick	= "moveMonth('".$aInfomation['PreDeliveryDate']."');return false;";
								}else {
									$sPreOnClick	= "return false;";
								}

								$aCurrentDeliveryDate	= explode("/", $aInfomation['CurrentDeliveryDate']);
								$sMonthLabel	= date("F Y", strtotime($aCurrentDeliveryDate[2]."-".$aCurrentDeliveryDate[0]."-01 00:00:00"));
								if( $mosConfig_lang == 'french') {
									$aEnglishMonth 		= array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
									$aFrenchMonth 		= array("Janvier", "F&egrave;vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&egrave;cembre");
									$sMonthLabel 	= str_replace($aEnglishMonth, $aFrenchMonth, $sMonthLabel);
								}
							?>
							<a href="#" onclick="<?php echo $sPreOnClick; ?>" class="pre-month" id="asdfasd">&nbsp;</a>
							<?php echo $sMonthLabel; ?>
							<a href="#" onclick="moveMonth('<?php echo $aInfomation['NextDeliveryDate'];?>');return false;" class="next-month">&nbsp;</a>
						</div>
						<?php echo $aInfomation['Calendar']; ?>
					</div>
					<div id="loadCalendarAjax"><?php echo _AJAX_WAITTING; ?></div>
				</div>
				<div class="delivery-calendar-right">

					<div id="selectDayNote" class="select-day-note">
						<div id="deliverySurcharge">
							<b><?php echo _DELIVERY_SURCHARGE; ?></b><br/>
							<?php if( $aInfomation['ShippingMethod'] ) { ?>
								<div id="yourAddressDelivery">
									<?php echo $aInfomation['ShippingMethod']['text']; ?>
								</div>
							<?php } ?>
							<div class="other-delivery">
                                                                <div id="message">
                                                                </div>
								<div id="specialDeliver">
									&nbsp;&nbsp;- <?php echo _DELIVERY_SPECIAL_DAY; ?> : <span id="nSpecialDeliver" class="delivery-money"></span>
								</div>
                                                                <!--
								<div id="shippingSurcharge" style="display:none;">
									&nbsp;&nbsp;- <?php echo _DELIVERY_EXTRA_SURCHARGE; ?>: <span id="nShippingSurcharge" class="delivery-money"></span>
								</div>
                                                                <div id="PostCodeReturn" style="display:none;">
                                                                    <b>The reason that you can not make an order for this day</b>:
									<span id="nPostCodeReturn" class="delivery-money"></span>
								</div>
                                                                -->
								<?php
									//$aCurrentDeliveryDate = explode( "/", $aInfomation['CurrentDeliveryDate'] );

									//if( $aInfomation['CutOffTime'] && intval($aCurrentDeliveryDate[1]) == intval(date("j")) && intval($aCurrentDeliveryDate[0]) == intval(date("m")) && intval($aCurrentDeliveryDate[2]) == intval(date("Y")) ) {
								?>
								<div id="deliverSameDay" style="display:none;">
									&nbsp;&nbsp;- <?php echo _DELIVERY_SAME_DAY; ?>: <span class="delivery-money"><?php echo $aInfomation['CutOffTime']; ?> </span>
								</div>
								<?php //} ?>
								<?php if( $aInfomation['YourAddressDelivery'] ) { ?>
								<div id="yourExtraAddressDelivery">
									&nbsp;&nbsp;- <?php echo _DELIVERY_FOLLOW_ADDRESS; ?>: <span class="delivery-money"><?php echo $aInfomation['YourAddressDelivery']; ?></span>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="select-date-note"><?php echo _DELIVERY_CALENDAR_NOTE2; ?></div>
					<?php

						$sDeliverDateLabel	= date("l, M d Y", strtotime($aInfomation['CurrentDeliveryDate']));
						if( $mosConfig_lang == 'french') {
							$aEnglishMonth 		= array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oc", "Nov", "Dec");
							$aFrenchMonth 		= array("Jan", "F&egrave;v", "Mar", "Avr", "Mai", "Jui", "Juil", "Ao&ucirc;t", "Sep", "Oct", "Nov", "D&egrave;c");
							$sDeliverDateLabel 	= str_replace($aEnglishMonth, $aFrenchMonth, $sDeliverDateLabel);

							$aEnglishWeek 	= array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
							$aFrenchWeek 	= array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
							$sDeliverDateLabel 	= str_replace($aEnglishWeek, $aFrenchWeek, $sDeliverDateLabel);
						}

					?>
					<div id="currentSelectDay" class="current-select-day">
						<?php echo $sDeliverDateLabel; ?>
					</div>
					
				</div>

			</div>
		</div>
	<?php
		exit(0);
	}


	function loadOrderHistory( $option, $rows  ) {
		global $mosConfig_live_site;
		$sImagePath	= $mosConfig_live_site."/administrator/images/";
	?>
		<table width="100%" class="adminform">
			<tr>
				<th width="25%" style="text-align:left;">Date Added</th>
				<th width="10%">Customer Notified?</th>
				<th width="10%">Warehouse Notified?</th>
				<th width="10%">Status</th>
				<th width="45%">Comment</th>
			<tr>
			<?php
				foreach ( $rows as $item) {
			?>
				<tr>
					<td><?php echo $item->date_added; ?></td>
					<td style="text-align:center;"><img src="<?php echo ( intval( $item->customer_notified) > 0 ) ? $sImagePath."tick.png" : $sImagePath."publish_x.png"; ?>"/></td>
					<td style="text-align:center;"><img src="<?php echo ( intval( $item->warehouse_notified) > 0 ) ? $sImagePath."tick.png" : $sImagePath."publish_x.png"; ?>"/></td>
					<td style="text-align:center;"><strong><?php echo $item->order_status_code; ?></strong></td>
					<td style="text-align:left;"><?php echo ( $item->comments != "" ) ? $item->comments : "./."; ?></td>
				</tr>
			<?php
				 }
			?>
		</table>
		<?php
		exit(0);
	}


	function loadAjaxOrder( $option, $aInfomation, $aList ) {
		global $mosConfig_live_site;
		$sImagePath	= $mosConfig_live_site."/administrator/images/";
	?>
		<script type="text/javascript">
			$.jtabber({
				mainLinkTag: "#Tab a", // much like a css selector, you must have a 'title' attribute that links to the div id name
				activeLinkClass: "selected", // class that is applied to the tab once it's clicked
				hiddenContentClass: "hiddencontent", // the class of the content you are hiding until the tab is clicked
				showDefaultTab: 1, // 1 will open the first tab, 2 will open the second etc.  null will open nothing by default
				showErrors: true, // true/false - if you want errors to be alerted to you
				effect: 'slide', // null, 'slide' or 'fade' - do you want your content to fade in or slide in?
				effectSpeed: 'fast' // 'slow', 'medium' or 'fast' - the speed of the effect
			});

		</script>

		<style type="text/css">
			table.order-header{
				margin:5px 0px 5px 0px;
			}

			table.order-header td {
				background-color:#BE4C34;
				font:bold 12px Tahoma, Verdana;
				text-transform:uppercase;
				text-align:center;
				line-height:160%;
				color:#FFF;
			}

			table.adminform th{
				font:bold 12px Tahoma, Verdana;
				text-align:center;
			}

			table.adminform {
				margin:0px 0px 10px 0px;
			}

			table.adminform td{
				font:normal 11px Tahoma, Verdana;
				line-height:140%;
				padding:5px;
			}

			table.adminform td.title{
				font:bold 11px Tahoma, Verdana;
				line-height:140%;
			}

			table.adminform td.title2{
				font:bold 11px Tahoma, Verdana;
				text-align:right;
				padding-right:8px;
				line-height:140%;
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

			input.button {
				cursor:pointer;
			}


			#Tab a, #Tab a:active, #Tab a:visited {
				font:normal 11px Tahoma;
				border-top:1px solid #CCC;
				border-left:1px solid #CCC;
				border-right:1px solid #CCC;
				text-decoration:none;
				background:#E6D48E;
				margin-right:5px;
				padding:5px;
				outline:none;
				display:block;
				float:left;
				color:#000;
			}

			#Tab a.selected, #Tab a.selected:active, #Tab a.selected:visited {
				text-decoration:none;
				background:#C51D1D;
				color:#fff;
				outline:none;
			}

			.hiddencontent {
				padding:10px 5px 5px 5px;
				border:1px solid #D5D5D5;
				background:#fff;
				display:none;
			}

			.clear {
				clear:both;
			}
		</style>

		<table class="order-header" cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td>
					<div class="close-button">(X)Close</div>
					Order Detail
				</td>
			</tr>
		</table>


		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td width="40%" style="border:none;vertical-align:top;">
					<table width="100%" class="adminform">
						<tr>
							<th colspan="2">Purchase Order</th>
						</tr>
						<tr>
							<td width="35%" class="title">Order Number:</td>
							<td width="65%"><?php echo sprintf("%08d", $aInfomation["OrderInfo"]->order_id ); ?></td>
						</tr>
						<tr>
							<td class="title">Delivery Date:</td>
							<td><?php echo $aInfomation["OrderInfo"]->ddate; ?></td>
						</tr>
						<tr>
							<td class="title">Customer Instructions:</td>
							<td><?php echo $aInfomation["OrderInfo"]->customer_comments; ?></td>
						</tr>
						<tr>
							<td class="title">Occasion:</td>
							<td><?php echo $aInfomation["OrderInfo"]->customer_occasion; ?></td>
						</tr>
						<tr>
							<td class="title">Order Date:</td>
							<td><?php echo date ("d-M-Y H:i ", $aInfomation["OrderInfo"]->cdate); ?></td>
						</tr>
						<tr>
							<td class="title">Order Status:</td>
							<td><?php echo $aInfomation["OrderInfo"]->order_status; ?></td>
						</tr>
						<tr>
							<td class="title">Card Message:</td>
							<td><?php echo nl2br( $aInfomation["OrderInfo"]->customer_note ); ?></td>
						</tr>
						<tr>
							<td class="title">IP Address:</td>
							<td><?php echo $aInfomation["OrderInfo"]->ip_address; ?></td>
						</tr>
						<tr>
							<td class="title">Customer ID:</td>
							<td><?php echo $aInfomation["OrderInfo"]->user_id; ?></td>
						</tr>
					</table>
				</td>
				<td width="60%" style="border:none;vertical-align:top;">
					<div id="Tab">
						<a href="#" title="orderStatusChange">Order Status Change</a>
						<a href="#" title="orderHistory">Order History</a>
						<a href="#" title="editOrder">Edit Order</a>
						<a href="#" title="editCardMessage">Edit Card Message</a>
						<a href="#" title="specialInstructionsComments">Special Instructions & Comments</a>
						<div class="clear"></div>
					</div>
				  	<div id="orderStatusChange" class="hiddencontent">
						<table width="100%" class="adminform">
							<tr>
								<th colspan="3">Order Status Change</th>
							</tr>
							<tr>
								<td width="15%" class="title">
									Order Status:
								</td>
								<td width="55%">
									<?php
										echo $aList['OrderStatus'];
										echo $aList['OrderWareHouse'];
										echo $aList['OrderPriority'];
									?>
								</td>
								<td width="30%">
									<input name="current_priority_inside" value="<?php echo $aInfomation["OrderInfo"]->priority;?>" type="hidden">
									<input name="current_warehouse_inside" value="<?php echo $aInfomation["OrderInfo"]->warehouse;?>" type="hidden">
									<input name="current_order_status_inside" value="<?php echo $aInfomation["OrderInfo"]->order_status;?>" type="hidden">
									<div id ="updateOrderStatusReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
									<input type="button" class="button update-status-inside" id ="<?php echo $aInfomation["OrderInfo"]->order_id;?>" name="update-status-inside" value="Update Status" />
								</td>
							</tr>
							<tr>
								<td width="15%" class="title">
									Comment:
								</td>
								<td width="55%">
									<textarea name="order_comment_inside" rows="4" cols="35"></textarea>
								</td>
								<td width="30%">
									<input name="notify_warehouse_inside" checked="checked"  type="checkbox"> Notify Production<br>
									<input name="notify_customer_inside" checked="checked"  type="checkbox"> Notify Customer?<br>
									<input name="include_comment_inside" checked="checked"  type="checkbox"> Include this comment?
								</td>
							</tr>
						</table>
				 	</div>
					<div id="orderHistory" class="hiddencontent" title="<?php echo $aInfomation["OrderInfo"]->order_id;?>">
						<div style="display:block;height:30px;clear:both;">
							<input style="margin-bottom:10px;color:#FF3300;float:right;" type="button" class="button refresh-order-history" id ="<?php echo $aInfomation["OrderInfo"]->order_id;?>" name="refresh-order-history" value="Refresh Order History" />
						</div>
						<div id ="refreshOrderHistory">
							<table width="100%" class="adminform">
								<tr>
									<th width="25%" style="text-align:left;">Date Added</th>
									<th width="10%">Customer Notified?</th>
									<th width="10%">Warehouse Notified?</th>
									<th width="10%">Status</th>
									<th width="45%">Comment</th>
								</tr>
								<?php
									foreach ($aInfomation["OrderHistoryInfo"] as $item) {
								?>
									<tr>
										<td><?php echo $item->date_added; ?></td>
										<td style="text-align:center;"><img src="<?php echo ( intval( $item->customer_notified) > 0 ) ? $sImagePath."tick.png" : $sImagePath."publish_x.png"; ?>"/></td>
										<td style="text-align:center;"><img src="<?php echo ( intval( $item->warehouse_notified) > 0 ) ? $sImagePath."tick.png" : $sImagePath."publish_x.png"; ?>"/></td>
										<td style="text-align:center;"><strong><?php echo $item->order_status_code; ?></strong></td>
										<td style="text-align:left;"><?php echo ( $item->comments != "" ) ? $item->comments : "./."; ?></td>
									</tr>
								<?php
									 }
								?>
							</table>
						</div>
					</div>
					<div id="editOrder" class="hiddencontent">
						<div id ="updateOrderReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
						<table width="100%" class="adminform" border="1">
							<tr>
								<td colspan="3">
									<div id="loadOrderCartDetailReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
									<div id="loadOrderCartDetail">
										<table width="100%" class="adminform" border="1">
											<tr>
												<th width="40">#</th>
												<th width="15%" style="text-align:left;">SKU</th>
												<th width="60%" style="text-align:left;">Product Name</th>
												<th width="10%">Quantity</th>
												<th width="10%" colspan="2">Actions</th>
											</tr>
											<?php
												$i 			= 0;
												$nSubTotal 	= 0;
												$nTaxTotal 	= 0;
												foreach( $aInfomation["OrderItem"] as $Item ) {
													$i++;
													$nSubTotal	+=  $Item->product_final_price * $Item->product_quantity;
													$nTaxTotal	+=  ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
											?>
											<tr>
												<td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
												<td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
												<td style="padding-right:10px;"><?php echo $Item->order_item_name; ?></td>
												<td style="text-align:center;vertical-align:top;">
													<input type="text" name="order_item_quantity<?php echo $Item->order_item_id; ?>" value="<?php echo $Item->product_quantity; ?>" size="5" maxlength="3"  />
												</td>
												<td style="text-align:center;vertical-align:top;">
													<input title="Update Quantity" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Quantity" type="image" border="0" class="update-quantity" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
												</td>
												<td style="text-align:center;vertical-align:top;">
													<input title="Delete Product Item" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/delete_f2.gif" alt="Delete Product Item" type="image" border="0" class="delete-order-item" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
												</td>
											</tr>
											<?php
												}
											?>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" width="85%" colspan="2">Delivery Fee:<?php echo $aList['OrderStandingShpping']; ?></td>
								<td style="background-color:#CBDCED;" width="15%">
									<input title="Update Standard Shipping" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Standard Shipping" type="image" border="0" class="update-standard-shipping" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>"  onclick="return false;"/>
								</td>
							</tr>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" width="85%">Coupon Discount:</td>
								<td style="background-color:#CBDCED;text-align:center;" width="5%"><input type="text" name="order_coupon_discount" value="<?php echo $aInfomation["OrderInfo"]->coupon_discount; ?>" size="5" maxlength="14"  /></td>
								<td style="background-color:#CBDCED;" width="10%">
									<input title="Update Coupon Discount" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Coupon Discount" type="image" border="0" class="update-coupon-discount" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;" />
								</td>
							</tr>
							<tr>
								<td style="background-color:#CBDCED;" class="title2">Discount:</td>
								<td style="background-color:#CBDCED;text-align:center;"><input type="text" name="order_discount" value="<?php echo $aInfomation["OrderInfo"]->order_discount; ?>" size="5" maxlength="14"  /></td>
								<td style="background-color:#CBDCED;">
									<input title="Update Discount" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Discount" type="image" border="0" class="update-discount" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;" />
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<table width="100%" class="adminform" border="1">
										<tr>
											<th colspan="3">Add Product</th>
										</tr>
										<tr>
											<th width="80%" style="text-align:left;">Product Name</th>
											<th width="10%">Quantity</th>
											<th width="10%">Action</th>
										</tr>
										<tr>
											<td style="vertical-align:top;"><?php echo $aList['cboProduct']; ?></td>
											<td style="text-align:center;vertical-align:top;">
												<input type="text" name="add_order_item_quantity" value="1" size="5" maxlength="3"  />
											</td>
											<td style="text-align:center;">
												<input title="Add Product" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Add Product" type="image" border="0" class="add-product" id="<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div id="editCardMessage" class="hiddencontent">
						<table width="100%" class="adminform">
							<tr>
								<th colspan="4">Edit Card Message</th>
							</tr>
							<tr>
								<td><strong>Card Message:</strong></td>
								<td><textarea name="order_customer_note" rows="5" cols="30"><?php echo html_entity_decode($aInfomation["OrderInfo"]->customer_note);?></textarea></td>
								<td><strong>Signature:</strong></td>
								<td><textarea name="order_customer_signature" rows="5" cols="30"><?php echo html_entity_decode($aInfomation["OrderInfo"]->customer_signature);?></textarea></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td colspan="3">
									<div id ="updateCardMessageReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
									<input type="button" class="button update-card-message" id ="<?php echo $aInfomation["OrderInfo"]->order_id;?>" name="update-card-message" value="Update Card Messange" />
								</td>
							</tr>
						</table>
					</div>
					<div id="specialInstructionsComments" class="hiddencontent">
						<table width="100%" class="adminform">
							<tr>
								<td width="50%"><textarea name="order_customer_comments" rows="8" cols="40"><?php echo $aInfomation["OrderInfo"]->customer_comments;?></textarea></td>
								<td width="50%">
									<div id ="updateSpecialInstructionsReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
									<input type="button" class="button update-special-instructions" id ="<?php echo $aInfomation["OrderInfo"]->order_id;?>" name="update-special-instructions" value="Update Special Instructions" />
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px;border:none;">
					<table cellpadding="0" cellspacing="0" width="100%" border="0">
						<tr>
							<td width="50%" style="border:none;vertical-align:top;">
								<table width="100%" class="adminform">
									<tr>
										<th colspan="2">Billing Information</th>
									</tr>
									<tr>
										<td width="35%" class="title2">Title:</td>
										<td width="65%"><?php echo $aList["BillingInfoType"]; ?></td>
									</tr>
									<tr>
										<td class="title2">First Name<font color="red">*</font>:</td>
										<td><input type="text" name="bill_first_name" value="<?php echo $aInfomation["BillingInfo"]->first_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Last Name<font color="red">*</font>:</td>
										<td><input type="text" name="bill_last_name" value="<?php echo $aInfomation["BillingInfo"]->last_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Middle Name:</td>
										<td><input type="text" name="bill_middle_name" value="<?php echo $aInfomation["BillingInfo"]->middle_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Company Name:</td>
										<td><input type="text" name="bill_company_name" value="<?php echo $aInfomation["BillingInfo"]->company; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Country<font color="red">*</font>:</td>
										<td><?php echo $aList['BillingInfoCountry']; ?></td>
									</tr>
									<tr>
										<td class="title2">State/Province/Region<font color="red">*</font>:</td>
										<td><?php echo $aInfomation['BillingInfoState']; ?></td>
									</tr>
									<tr>
										<td class="title2">Zip Code / Postal Code<font color="red">*</font>:</td>
										<td><input type="text" name="bill_zip_code" value="<?php echo $aInfomation["BillingInfo"]->zip; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">City<font color="red">*</font>:</td>
										<td><input type="text" name="bill_city" value="<?php echo $aInfomation["BillingInfo"]->city; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Address 1<font color="red">*</font>:</td>
										<td><input type="text" name="bill_address_1" value="<?php echo $aInfomation["BillingInfo"]->address_1; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Address 2:</td>
										<td><input type="text" name="bill_address_2" value="<?php echo $aInfomation["BillingInfo"]->address_2; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Phone<font color="red">*</font>:</td>
										<td><input type="text" name="bill_phone" value="<?php echo $aInfomation["BillingInfo"]->phone_1; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Evening Phone:</td>
										<td><input type="text" name="bill_evening_phone" value="<?php echo $aInfomation["BillingInfo"]->phone_2; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Fax:</td>
										<td><input type="text" name="bill_fax" value="<?php echo $aInfomation["BillingInfo"]->fax; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Email:</td>
										<td><input type="text" name="bill_email" value="<?php echo $aInfomation["BillingInfo"]->user_email; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">&nbsp;</td>
										<td>
											<div id="update_billing_result" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
											<input class="button update-billing" id="<?php echo $aInfomation["BillingInfo"]->order_info_id; ?>" name="update_billing" value="Update Billing Info" type="button">
										</td>
									</tr>
								</table>
							</td>
							<td width="50%" style="border:none;vertical-align:top;">
								<table width="100%" class="adminform">
									<tr>
										<th colspan="2">Shipping Information</th>
									</tr>
									<tr>
										<td width="35%" class="title2">Title:</td>
										<td width="65%"><?php echo $aList["ShippingInfoType"]; ?></td>
									</tr>
									<tr>
										<td class="title2">First Name<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_first_name" value="<?php echo $aInfomation["ShippingInfo"]->first_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Last Name<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_last_name" value="<?php echo $aInfomation["ShippingInfo"]->last_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Middle Name:</td>
										<td><input type="text" name="deliver_middle_name" value="<?php echo $aInfomation["ShippingInfo"]->middle_name; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Company Name:</td>
										<td><input type="text" name="deliver_company_name" value="<?php echo $aInfomation["ShippingInfo"]->company; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Country<font color="red">*</font>:</td>
										<td><?php echo $aList['ShippingInfoCountry']; ?></td>
									</tr>
									<tr>
										<td class="title2">State/Province/Region<font color="red">*</font>:</td>
										<td><?php echo $aInfomation['ShippingInfoState']; ?></td>
									</tr>
									<tr>
										<td class="title2">Zip Code / Postal Code<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_zip_code" value="<?php echo $aInfomation["ShippingInfo"]->zip; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">City<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_city" value="<?php echo $aInfomation["ShippingInfo"]->city; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Address 1<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_address_1" value="<?php echo $aInfomation["ShippingInfo"]->address_1; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Address 2:</td>
										<td><input type="text" name="deliver_address_2" value="<?php echo $aInfomation["ShippingInfo"]->address_2; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Phone<font color="red">*</font>:</td>
										<td><input type="text" name="deliver_phone" value="<?php echo $aInfomation["ShippingInfo"]->phone_1; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Evening Phone:</td>
										<td><input type="text" name="deliver_evening_phone" value="<?php echo $aInfomation["ShippingInfo"]->phone_2; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Fax:</td>
										<td><input type="text" name="deliver_fax" value="<?php echo $aInfomation["ShippingInfo"]->fax; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">Email:</td>
										<td><input type="text" name="deliver_email" value="<?php echo $aInfomation["ShippingInfo"]->user_email; ?>" size="40" /></td>
									</tr>
									<tr>
										<td class="title2">&nbsp;</td>
										<td>
											<div id="update_deliver_result" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
											<input class="button update-deliver" id="<?php echo $aInfomation["ShippingInfo"]->order_info_id; ?>" name="update_shipping" value="Update Shipping Info" type="button">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="border:none;">
					<div id="loadOrderItemDetailReport" style="display:none;font: bold 11px Tahoma;color:#FF6600;line-height:24px;"></div>
					<div id="loadOrderItemDetail">
						<table width="100%" class="adminform" border="1">
							<tr>
								<th width="20">#</th>
								<th width="54%" style="text-align:left;">Product Name</th>
								<th width="7%" style="text-align:left;">SKU</th>
								<th width="7%">Quantity</th>
								<th width="10%">Product Price (Net)</th>
								<th width="10%">Product Price (Gross)</th>
								<th width="10%">Total</th>
							</tr>
							<?php
								$i 			= 0;
								$nSubTotal 	= 0;
								$nTaxTotal 	= 0;
								foreach( $aInfomation["OrderItem"] as $Item ) {
									$i++;
									$nSubTotal	+=  $Item->product_final_price * $Item->product_quantity;
									$nTaxTotal	+=  ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
							?>
							<tr>
								<td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
								<td style="padding-right:10px;">
									<strong><?php echo $Item->order_item_name; ?></strong>
									<div style="margin: 5px 0px 0px 0px;"><?php echo strip_tags($Item->product_attribute); ?></div>
								</td>
								<td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
								<td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
								<td style="text-align:center;vertical-align:top;"><?php echo LangNumberFormat::number_format( $Item->product_item_price, 2, ".", "" ); ?></td>
								<td style="text-align:center;vertical-align:top;"><?php echo LangNumberFormat::number_format( $Item->product_final_price, 2, ".", "" ); ?></td>
								<td style="text-align:center;vertical-align:top;"><strong><?php echo LangNumberFormat::number_format( $Item->product_final_price * $Item->product_quantity, 2, ".", "" ); ?></strong></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">SubTotal:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $nSubTotal, 2, ".", "" ); ?></strong></td>
							</tr>
							<?php if( $aInfomation["OrderInfo"]->order_discount > 0 ) { ?>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Discount:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong>-<?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_discount, 2, ".", "" ); ?></strong></td>
							</tr>
							<?php
								}
							?>
							<?php if( $aInfomation["OrderInfo"]->coupon_discount > 0 ) { ?>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Coupon Discount:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong>-<?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->coupon_discount, 2, ".", "" ); ?></strong></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Tax Total:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $nTaxTotal, 2, ".", "" ); ?></strong></td>
							</tr>
								<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Fee:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_shipping, 2, ".", "" ); ?></strong></td>
							</tr>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Tax:</td>
								<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_shipping_tax, 2, ".", "" ); ?></strong></td>
							</tr>
							<tr>
								<td style="background-color:#CBDCED;" class="title2" colspan="6">Total:</td>
								<td style="background-color:#CBDCED;color:#FF3300;">

								<strong><?php echo LangNumberFormat::number_format( $nSubTotal + $aInfomation["OrderInfo"]->order_shipping - $aInfomation["OrderInfo"]->coupon_discount - $aInfomation["OrderInfo"]->order_discount, 2, ".", "" ); ?></strong></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td style="border:none;vertical-align:top;">
					<?php
						$aDeliverInfo	= explode( "|", $aInfomation["OrderInfo"]->ship_method_id );

					?>
					<table width="100%" class="adminform">
						<tr>
							<th colspan="2">Deliver Information</th>
						</tr>
						<tr>
							<td width="35%" class="title">Carrier:</td>
							<td width="65%"><?php echo $aDeliverInfo[1]; ?></td>
						</tr>
						<tr>
							<td class="title">Delivery Mode:</td>
							<td><?php echo $aDeliverInfo[2]; ?></td>
						</tr>
						<tr>
							<td class="title">Delivery Price:</td>
							<td>$<?php echo $aDeliverInfo[3]; ?></td>
						</tr>
						<tr>
							<td class="title">Delivery Extra Price:</td>
							<td><?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_shipping - $aDeliverInfo[3], 2, ".", "" ); ?></td>
						</tr>
					</table>
				</td>
				<td style="border:none;vertical-align:top;">
					<table width="100%" class="adminform" border="1">
						<tr>
							<th width="40%" style="text-align:left;">Payment Method</th>
							<th width="20%" style="text-align:left;">Account Name</th>
							<th width="20%">Account Number</th>
							<th width="20%">Expire Date</th>
						</tr>
						<tr>
							<td style="vertical-align:top;"><?php echo $aInfomation["PaymentInfo"]->payment_method_name; ?></td>
							<td style="vertical-align:top;"><?php echo $aInfomation["PaymentInfo"]->order_payment_name; ?></td>
							<td style="text-align:center;vertical-align:top;">
								<?php
									echo HTML_AjaxOrder::asterisk_pad( $aInfomation["PaymentInfo"]->account_number, 0, true );
									//echo '<br/>(CVV Code: '.$aInfomation["PaymentInfo"]->order_payment_code.')' ;
								?>
							</td>
							<td style="text-align:center;vertical-align:top;"><?php echo date("M-Y", $aInfomation["PaymentInfo"]->order_payment_expire); ?></td>
						</tr>
					</table>
					<table width="100%" class="adminform" border="1">
						<tr>
							<th style="text-align:left;">Payment Log</th>
						</tr>
						<tr>
							<td style="vertical-align:top;">
								<?php
									if( $aInfomation["PaymentInfo"]->order_payment_log ) {
										echo $aInfomation["PaymentInfo"]->order_payment_log;
									}else {
										echo "./.";
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table><br/><br/>
		<?php
		exit(0);
	}


	function loadOrderItemDetail( $option, $aInfomation ) {
	?>
		<table width="100%" class="adminform" border="1">
			<tr>
				<th width="20">#</th>
				<th width="54%" style="text-align:left;">Product Name</th>
				<th width="7%" style="text-align:left;">SKU</th>
				<th width="7%">Quantity</th>
				<th width="10%">Product Price (Net)</th>
				<th width="10%">Product Price (Gross)</th>
				<th width="10%">Total</th>
			</tr>
			<?php
				$i 			= 0;
				$nSubTotal 	= 0;
				$nTaxTotal 	= 0;
				foreach( $aInfomation["OrderItem"] as $Item ) {
					$i++;
					$nSubTotal	+=  $Item->product_final_price * $Item->product_quantity;
					$nTaxTotal	+=  ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
			?>
			<tr>
				<td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
				<td style="padding-right:10px;">
					<strong><?php echo $Item->order_item_name; ?></strong>
					<div style="margin: 5px 0px 0px 0px;"><?php echo strip_tags($Item->product_attribute); ?></div>
				</td>
				<td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
				<td style="text-align:center;vertical-align:top;"><strong><?php echo $Item->product_quantity; ?></strong></td>
				<td style="text-align:center;vertical-align:top;"><?php echo LangNumberFormat::number_format( $Item->product_item_price, 2, ".", "" ); ?></td>
				<td style="text-align:center;vertical-align:top;"><?php echo LangNumberFormat::number_format( $Item->product_final_price, 2, ".", "" ); ?></td>
				<td style="text-align:center;vertical-align:top;"><strong><?php echo LangNumberFormat::number_format( $Item->product_final_price * $Item->product_quantity, 2, ".", "" ); ?></strong></td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">SubTotal:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $nSubTotal, 2, ".", "" ); ?></strong></td>
			</tr>
			<?php if( $aInfomation["OrderInfo"]->order_discount > 0 ) { ?>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Discount:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong>-<?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_discount, 2, ".", "" ); ?></strong></td>
			</tr>
			<?php
				}
			?>
			<?php if( $aInfomation["OrderInfo"]->coupon_discount > 0 ) { ?>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Coupon Discount:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong>-<?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->coupon_discount, 2, ".", "" ); ?></strong></td>
			</tr>
			<?php
				}
			?>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Tax Total:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $nTaxTotal, 2, ".", "" ); ?></strong></td>
			</tr>
				<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Fee:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_shipping, 2, ".", "" ); ?></strong></td>
			</tr>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Delivery Tax:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $aInfomation["OrderInfo"]->order_shipping_tax, 2, ".", "" ); ?></strong></td>
			</tr>
			<tr>
				<td style="background-color:#CBDCED;" class="title2" colspan="6">Total:</td>
				<td style="background-color:#CBDCED;color:#FF3300;"><strong><?php echo LangNumberFormat::number_format( $nSubTotal + $aInfomation["OrderInfo"]->order_shipping - $aInfomation["OrderInfo"]->coupon_discount - $aInfomation["OrderInfo"]->order_discount, 2, ".", "" ); ?></strong></td>
			</tr>
		</table>
		<?php
		exit(0);
	}


	function loadOrderCart( $option, $aInfomation ) {
		global $mosConfig_live_site;
	?>
		<table width="100%" class="adminform" border="1">
			<tr>
				<th width="40">#</th>
				<th width="15%" style="text-align:left;">SKU</th>
				<th width="60%" style="text-align:left;">Product Name</th>
				<th width="10%">Quantity</th>
				<th width="10%" colspan="2">Actions</th>
			</tr>
			<?php
				$i 			= 0;
				$nSubTotal 	= 0;
				$nTaxTotal 	= 0;
				foreach( $aInfomation["OrderItem"] as $Item ) {
					$i++;
					$nSubTotal	+=  $Item->product_final_price * $Item->product_quantity;
					$nTaxTotal	+=  ( $Item->product_final_price - $Item->product_item_price ) * $Item->product_quantity;
			?>
			<tr>
				<td style="text-align:center;vertical-align:top;"><?php echo $i; ?>.</td>
				<td style="text-align:left;vertical-align:top;"><?php echo $Item->order_item_sku; ?></td>
				<td style="padding-right:10px;"><?php echo $Item->order_item_name; ?></td>
				<td style="text-align:center;vertical-align:top;">
					<input type="text" name="order_item_quantity<?php echo $Item->order_item_id; ?>" value="<?php echo $Item->product_quantity; ?>" size="5" maxlength="3"  />
				</td>
				<td style="text-align:center;vertical-align:top;">
					<input title="Update Quantity" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update Quantity" type="image" border="0" class="update-quantity2" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
				</td>
				<td style="text-align:center;vertical-align:top;">
					<input title="Delete Product Item" src="<?php echo $mosConfig_live_site?>/components/com_virtuemart/shop_image/ps_image/delete_f2.gif" alt="Delete Product Item" type="image" border="0" class="delete-order-item2" id="<?php echo $Item->order_item_id; ?>[----]<?php echo $aInfomation["OrderInfo"]->order_id; ?>" onclick="return false;"/>
				</td>
			</tr>
			<?php
				}
			?>
		</table>
		<?php
		exit(0);
	}


	function asterisk_pad($str, $display_length, $reversed = false) {

		$total_length = strlen($str);

		if($total_length > $display_length) {
			if( !$reversed) {
				for($i = 0; $i < $total_length - $display_length; $i++) {
					$str[$i] = "*";
				}
			}
			else {
				for($i = $total_length-1; $i >= $total_length - $display_length; $i--) {
					$str[$i] = "*";
				}
			}
		}

		return($str);
	}

}
?>
