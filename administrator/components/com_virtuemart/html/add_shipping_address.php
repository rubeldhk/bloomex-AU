         <?php
            $db = new ps_DB;
            $q = "SELECT * FROM #__{vm}_user_info WHERE user_id='" . $user_id . "' ";
            $q .= "AND address_type='BT'";
            $db->query($q);
            $db->next_record();
            ?>

            <div style="display:none;" id="addShippingInfoForm" >

                <div id="ticket_5051_po" style="display:none;">
                    <span onclick="closeNotificationPopup('ticket_5051_po');" id="ticket_5051_po_close">&nbsp;</span>
                    Please Note: All Hospitals and Funeral Homes require a Vase for "Bouquets" - to add a vase to your order <a href="<?php echo $mosConfig_live_site; ?>/index.php?page=shop.browse&category_id=82&option=com_virtuemart&Itemid=231">Click Here</a> - Thank You!
                </div>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="update-shipping-table" style="color: #a23232;">
                    <tr class="sectiontableheader">
                        <th colspan="3" align="center">
                            <?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_SHIPTO_LBL ?>
                        </th>
                    </tr>
                    <tr style="display:none;">
                        <td align="left">&nbsp;</td>
                        <td colspan="2" align="left">(<b>* = Required</b>)</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">(<b> <span style="font-size:10px; color:#a23232"> We are unable to ship to PO Boxes - Sorry for any inconvenience</span></b>)</td>
                    </tr>
                    <tr>
                        <td width="10%" nowrap="nowrap" align="right" id="address_type"><b><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_TYPE_LABEL ?>*</b>: </td>
                        <td colspan="2" width="90%" class="new-checkout-address-right">
                            <select name="address_type2" id="address_type2" size="1" class="inputbox">
                                <option value="B">Business</option>
                                <option value="R">Residential</option>
                            </select>
                        </td>
                    </tr>
                    <!--<tr>
                       <td width="10%" nowrap="nowrap" align="right" id="address_type_name_shipping"><b><?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADDRESS_LABEL ?>*</b>: </td>
                       <td width="90%"><input name="address_type_name_shipping" size="40" value="" class="inputbox" type="text"></td>
                    </tr>-->
                    <tr>
                        <td nowrap="nowrap" align="right" id="first_name_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_FIRST_NAME ?>*:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input name="first_name_shipping" size="40" value="" class="inputbox" type="text" maxlength="32" id="first_name_shipping_po" onkeypress="showNotificationPopup(this.value, 'first_name_shipping_po');" onmouseout="showNotificationPopup(this.value, 'first_name_shipping_po');" /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="last_name_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_LAST_NAME ?>*:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input name="last_name_shipping" size="40" value="" class="inputbox" type="text" maxlength="32" id="last_name_shipping_po" onkeypress="showNotificationPopup(this.value, 'last_name_shipping_po');" onmouseout="showNotificationPopup(this.value, 'last_name_shipping_po');"  /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="company_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_COMPANY_NAME ?>:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input name="company_shipping" size="40" value="" class="inputbox" type="text" maxlength="32" id="company_shipping_po" onkeypress="showNotificationPopup(this.value, 'company_shipping_po');" onmouseout="showNotificationPopup(this.value, 'company_shipping_po');"  /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="address_suite" style="vertical-align:top;"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_SUITE ?>: </b>
                        </td>
                        <td class="new-checkout-address-right" colspan="2">
                            <input name="address_suite" size="40" value="" class="inputbox"  type="text" maxlength="24"  />

                        </td>
                        <!--<td rowspan="2" > We are unable to ship to PO Boxes - Sorry for any inconvenience </td>-->
                      <!-- <td nowrap="nowrap" align="right" id="address_1_shipping" style="vertical-align:top;"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_ADDRESS_1 ?>*:</b> </td>
                       <td>
                            <input id="address_1_shipping_po" name="address_1_shipping" size="40" value="" class="inputbox" type="text" maxlength="64" onkeypress="showNotificationPopup(this.value, 'address_1_shipping_po');" onmouseout="showNotificationPopup(this.value, 'address_1_shipping_po');" /><br/>
                            We are unable to ship to PO Boxes - Sorry for any inconvenience
                     </td>-->
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="address_street_number" style="vertical-align:top;"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NUMBER ?> *:</b>
                        </td>
                        <td class="new-checkout-address-right" colspan="2">
                            <input name="address_street_number" size="40" value="" class="inputbox" type="text" maxlength="24"  />

                        </td>

                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="address_street_name" style="vertical-align:top;"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_STREET_NAME ?> *:</b>
                        </td>
                        <td class="new-checkout-address-right" colspan="2">
                            <input  name="address_street_name" size="40" value="" class="inputbox" type="text" maxlength="64"  />
                        </td>

                    </tr>
                    </tr>

                    <tr>
                        <td nowrap="nowrap" align="right" id="city_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_CITY ?>*:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input name="city_shipping" size="40" value="" class="inputbox" type="text" maxlength="32" id="city_shipping_po" onkeypress="showNotificationPopup(this.value, 'city_shipping_po');" onmouseout="showNotificationPopup(this.value, 'city_shipping_po');" /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="zip_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_ZIP ?>*:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input name="zip_shipping" size="40" value="" class="inputbox" type="text" maxlength="4" /></td>
                    </tr>
                    <tr style="display:none">
                        <td nowrap="nowrap" align="right" id="td_country_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_COUNTRY ?>*:</b></td>
                        <td class="new-checkout-address-right" colspan="2"><input type="hidden" name="country_shipping" value="AUS"/>
                            <?php echo "Australia"; //$ps_html->list_country("country_shipping", "AUS", "onchange='changeStateList(\"state_shipping\", \"country_shipping\");'", 'country_shipping');  ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="td_state_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_STATE ?>*: </b> </td>
                        <td class="new-checkout-address-right" colspan="2">
                            <select class="inputbox" name="state_shipping" size="1" id="state_shipping"></select>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right" id="phone_1_shipping"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE ?>*:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input type="text" name="phone_1_shipping" size="40" value="" class="inputbox"  maxlength="32" /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right"><b><?php echo $VM_LANG->_PHPSHOP_SHOPPER_FORM_PHONE2 ?>:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input type="text" name="phone_2_shipping" size="40" value="" class="inputbox" maxlength="32" /></td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" align="right"><b><?php echo $VM_LANG->_PHPSHOP_ORDER_PRINT_EMAIL ?>:</b> </td>
                        <td class="new-checkout-address-right" colspan="2"><input type="text" name="email_shipping" size="40" value="" class="inputbox" maxlength="32" /></td>
                    </tr>
                    <tr>
                        <td align='center' valign='top'>
                            <input class="new_checkout_upd_address_button" id="updateShippingInfo" value="<?php echo $VM_LANG->_PHPSHOP_USER_FORM_ADD_SHIPTO_LBL; ?>" type="button" style="font-size:12px;">
                        </td >
                        <td valign="top" align="right" colspan="2">
                            <input class="new_checkout_upd_address_button" id="closeShippingInfo" value="<?php echo $VM_LANG->_VM_CLOSE ?>" type="button" >
                            <div id="msgReportUpdateShipping" class="msgReport" style="text-align:left;float:left;display:block;width:100%;"></div>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="user_info_id_shipping" value="<?php echo $db->f("user_info_id"); ?>" />
                <input type="hidden" name="user_id_shipping" value="<?php echo $db->f("user_id"); ?>" />
                <input type="hidden" name="func_shipping" value="" />
                <input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
                <input type="hidden" name="user_name" value="<?php echo $user_email;  ?>" />
                <input type="hidden" name="account_email" value="<?php echo $user_email; ?>" />
            </div>
            
                        <script type="text/javascript">
                function showNotificationPopup(sValue, sID) {
                    if ((sValue.toLowerCase().indexOf("hospital") !== -1 || sValue.toLowerCase().indexOf("funeral") !== -1)) {
//                        document.getElementById("ticket_5051_po").style.display = "block";
//                        document.getElementById("ticket_5051_po").style.top = parseInt(jQuery("#" + sID).position().top) + "px";

                        //alert ( jQuery("#" + sID).position().top +"======" +jQuery("#" + sID).position().left);
                    } else {
                        //document.getElementById("ticket_5051_po").style.display	= "none";
                    }

                    /*if( document.getElementById(sID).value == ""   ) {
                     document.getElementById("ticket_5051_po").style.display	= "none";
                     }*/
                }

                function closeNotificationPopup(sID) {
                    document.getElementById("ticket_5051_po").style.display = "none";
                }
            </script>