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
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Category
 */
class HTML_Deliver
{
     private static $states_array = array(
        'NW' => 'NSW (Australia)',
        'VI' => 'VIC (Australia)',
        'AT' => 'ACT (Australia)',
        'QL' => 'QLD (Australia)',
        'WA' => 'WA (Australia)',
        'SA' => 'SA (Australia)',
        'NT' => 'NT (Australia)',
        'TA' => 'TAS (Australia)',
        'AU' => 'AUK (New Zealand)',
        'BP' => 'BOP (New Zealand)',
        'CA' => 'CAN (New Zealand)',
        'GS' => 'GIS (New Zealand)',
        'HB' => 'HKB (New Zealand)',
        'MW' => 'MWT (New Zealand)',
        'MB' => 'MBH (New Zealand)',
        'NS' => 'NSN (New Zealand)',
        'NL' => 'NTL (New Zealand)',
        'OT' => 'OTA (New Zealand)',
        'SL' => 'STL (New Zealand)',
        'TK' => 'TKI (New Zealand)',
        'WK' => 'WKO (New Zealand)',
        'WG' => 'WGN (New Zealand)',
        'WC' => 'WTC (New Zealand)'
    );
    //============================================= Unavailable delivery OPTION ===============================================
    function showUnavailableDelivery(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Unavailable Delivery by State Manager</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="5%">#</th>
                    <th width="5%" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th width="10%" align="left">Date Range</th>
                    <th width="20%" align="left">States</th>
                    <th width="20%" align="left">Cities</th>
                    <th width="20%" align="left">Postal Codes</th>
                    <th width="20%" align="left">Description</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);

                    $link = 'index2.php?option=com_deliver&act=unavailable_delivery&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);

                    $newStatesList = '';

                    $states = $cities = $postalCodes = '';
                    $jsonData = json_decode(html_entity_decode($row->json_data));
                    if ($jsonData) {
                        if ($jsonData->states) {
                            $states = $jsonData->states;
                        }

                        if ($jsonData->cities) {
                            $cities = implode($jsonData->cities, ',');
                        }

                        if ($jsonData->postalCodes) {
                            $postalCodes = implode($jsonData->postalCodes, ',');
                        }
                    }

                    if (!empty($states)) {
                        foreach ($states as $s) {
                            $newStatesList .= self::$states_array[$s] . ',';
                        }
                    }

                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>"
                               title="Unavailable delivery"><b><?php echo $row->available_from_date . ' - ' . $row->available_until_date; ?></b></a>
                        </td>
                        <td><?php echo rtrim($newStatesList, ','); ?></td>
                        <td><?php echo $cities; ?></td>
                        <td><?php echo $postalCodes; ?></td>
                        <td><?php echo $row->description; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="unavailable_delivery"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }

    function editUnavailableDelivery(&$row, $option, $postalCodesList, $citiesList, $states, $cities, $postalCodes)
    {
        mosCommonHTML::loadBootstrap(true);
        ?>
        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Unavailable Delivery by State Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <script src="/administrator/templates/joomla_admin/js/jquery-2.2.4.min.js"/>
            </script>
            <script type="text/javascript" src="/administrator/templates/joomla_admin/js/moment.min.js"></script>
            <script type="text/javascript"
                    src="/administrator/templates/joomla_admin/js/daterangepicker.min.js"></script>
            <script type="text/javascript" src="/administrator/templates/joomla_admin/js/jquery.dropdown.js"></script>
            <link rel="stylesheet" type="text/css"
                  href="/administrator/templates/joomla_admin/css/daterangepicker.css"/>
            <link rel="stylesheet" type="text/css"
                  href="/administrator/templates/joomla_admin/css/jquery.dropdown.css"/>

            <script>
                $(function () {
                    $('input[name="dateRange"]').daterangepicker({
                        locale: {
                            format: 'YYYY-MM-DD'
                        }
                    });

                    $('.statesSelect').dropdown({
                        multipleMode: 'label'
                    });
                    $('.postalCodesSelect').dropdown({
                        multipleMode: 'label'
                    });
                    $('.citiesSelect').dropdown({
                        multipleMode: 'label'
                    });
                });

            </script>

            <table width="100%" class="table table-condensed">
                <tr class="active">
                    <th colspan="2" class="text-center">Unavailable Delivery by State/City/Zip</th>
                </tr>
                <tr>
                    <td width="20%"><b>Unavailable date range</b></td>
                    <td>

                        <input type="text" class="form-control" name="dateRange"
                               value="<?php echo ($row->available_from_date && $row->available_until_date) ? $row->available_from_date . ' - ' . $row->available_until_date : ''; ?>"/>

                    </td>
                </tr>

                <tr>
                    <td><b>Types</b></td>
                    <td>

                        <div>

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#states" aria-controls="states"
                                                                          role="tab" data-toggle="tab">States
                                        (<?php echo ($states) ? count($states) : 0; ?>)</a></li>
                                <li role="presentation"><a href="#cities" aria-controls="cities" role="tab"
                                                           data-toggle="tab">Cities
                                        (<?php echo ($cities) ? count($cities) : 0; ?>)</a></li>
                                <li role="presentation"><a href="#postalCodes" aria-controls="postalCodes" role="tab"
                                                           data-toggle="tab">Postal Codes
                                        (<?php echo ($postalCodes) ? count($postalCodes) : 0; ?>)</a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">

                                <div role="tabpanel" class="tab-pane active" id="states">
                                    <?php
                                    $check = false;
                                    if (!empty($states)) {
                                        $check = true;
                                    }

                                    ?>
                                    <div class="statesSelect">
                                        <select style="display:none" name="states[]" multiple>
                                            <?php
                                            foreach (self::$states_array as $key => $state_name) {
                                                echo '<option value="' . $key . '" ' . (($check == true and in_array($key, $states)) ? 'selected' : '') . '>' . $state_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="cities">
                                    <div class="citiesSelect">
                                        <select style="display:none" name="cities[]" multiple>
                                            <?php
                                            $check = false;
                                            if (!empty($cities)) {
                                                $check = true;
                                            }

                                            foreach ($citiesList as $key => $city) {
                                                echo '<option value="' . $city->city . '" ' . (($check == true and in_array($city->city, $cities)) ? 'selected' : '') . '>' . $city->city_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="postalCodes">

                                    <div class="postalCodesSelect">
                                        <select style="display:none" name="postalCodes[]" multiple>
                                            <?php
                                            $check = false;
                                            if (!empty($postalCodes)) {
                                                $check = true;
                                            }
                                            foreach ($postalCodesList as $key => $postalCode) {
                                                echo $postalCode->postal_code . '   ' . in_array($postalCode->postal_code, $postalCodes, false);
                                                echo '<option value="' . $postalCode->postal_code . '" ' . (($check == true and in_array($postalCode->postal_code, $postalCodes, true)) ? 'selected' : '') . '>' . $postalCode->postal_code_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </td>
                </tr>
                <tr>
                    <td><b>Description</b></td>
                    <td>
                        <textarea name="description" class="form-control" cols="100"
                                  rows="5"><?php echo(!empty($row->decription) ? $row->decription : ''); ?></textarea>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="unavailable_delivery"/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <style>
            .stateName {
                margin-left: 10px;
                margin-right: 10px;
            }

            .table tr td:first-child, a[data-toggle="tab"] {
                font-size: 18px;
            }

            .tab-content {
                border: 1px solid #ddd;
                padding: 10px;
            }

            .dropdown-display-label .dropdown-selected {
                padding: 5px 20px 5px 10px;
                background: #5bc0de;
            }

            .dropdown-display-label .dropdown-selected .del {
                color: black;
            }

        </style>

        <?php
    }

    //============================================= FREE SHIPPING OPTION ===============================================
    function showShippingSurcharge(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Shipping Surcharge Option Manager</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th class="title" align="left">Specific Date</th>
                    <th width="10%" align="left">Amount</th>
                    <th width="60%" align="center">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);

                    $link = 'index2.php?option=com_deliver&act=shipping_surcharge&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>"
                               title="Edit Shipping Surcharge Option"><b><?php echo date("m-d-Y", strtotime($row->date)); ?></b></a>
                        </td>
                        <td><b>$<?php echo number_format($row->amount, 2, ".", ""); ?></b></td>
                        <td></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="shipping_surcharge"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    function editShippingSurcharge(&$row, $option)
    {
        global $mosConfig_live_site;
        ?>
        <link href="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/skins/aqua/theme.css"
              rel="stylesheet" type="text/css"/>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/calendar.js"
                type="text/javascript"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/lang/calendar-en.js"
                type="text/javascript"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/calendar-setup.js"
                type="text/javascript"></script>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.date.value == "") {
                    alert("You must provide a specific date for shipping surcharge.");
                }
                /*
                else if ( !validateCurrency(form.amount.value) ) {
        alert( "Your shipping surcharge amount is not valid." );
    }
                */
                else {
                    submitform(pressbutton);
                }
            }

            function validateCurrency(amount) {
                var regex = /^[1-9]\d*(?:\.\d{0,2})?$/;
                return regex.test(amount);
            }

            //-->
        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Shipping Surcharge Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">Shipping Surcharge Option Detail</th>
                </tr>
                <tr>
                    <td width="10%"><b>Specific Date:</b></td>
                    <td>
                        <input class="inputbox" type="text" id="date" name="date" size="15" maxlength="10"
                               value="<?php echo(!empty($row->date) ? date("m-d-Y", strtotime($row->date)) : date("m-d-Y")); ?>"
                               style="background-color:#ffffff;" readonly="readonly"/>
                        <input type="button" name="selectDate" id="selectDate" value="..."/>
                    </td>
                </tr>
                <tr>
                    <td><b>Amount:</b></td>
                    <td>
                        <b>$</b> <input class="inputbox" type="text" id="amount" name="amount" size="15" maxlength="7"
                                        value="<?php echo(!empty($row->amount) ? $row->amount : ""); ?>"/>
                        <br/>Example: $12.99 or $12 .... (XX.XX or XX, X: is numberic, $ symbol is not need)
                    </td>
                </tr>
            </table>
            <script language="javascript" type="text/javascript">
                <!--
                Calendar.setup({
                    inputField: "date",   // id of the input field
                    button: "selectDate",
                    ifFormat: "%m-%d-%Y",       // format of the input field
                    showsTime: true,
                    timeFormat: "24",
                    onUpdate: catcalc
                });

                function catcalc(cal) {
                    var date = cal.date;
                    var time = date.getTime()
                    var date2 = new Date(time);
                    document.getElementById("date").value = date2.print("%m-%d-%Y");
                }

                //-->
            </script>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="shipping_surcharge"/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }


    //============================================= FREE SHIPPING OPTION ===============================================
    function showFreeShipping(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Free Shipping Option Manager</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th class="title" align="left">Specific Date</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);

                    $link = 'index2.php?option=com_deliver&act=free_shipping&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>"
                               title="Edit Free Shipping Option"><?php echo date("m-d-Y", $row->freedate); ?></a></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="free_shipping"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    function editFreeShipping(&$row, $option)
    {
        global $mosConfig_live_site;
        ?>
        <link href="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/skins/aqua/theme.css"
              rel="stylesheet" type="text/css"/>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/calendar.js"
                type="text/javascript"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/lang/calendar-en.js"
                type="text/javascript"></script>
        <script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_deliver/calendar/calendar-setup.js"
                type="text/javascript"></script>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.freedate.value == "") {
                    alert("You must provide a specific date for free shipping.");
                } else {
                    submitform(pressbutton);
                }
            }

            //-->
        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Free Shipping Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">Free Shipping Option Detail</th>
                </tr>
                <tr>
                    <td width="10%"><b>Specific Date:</b></td>
                    <td>
                        <input class="inputbox" type="text" id="freedate" name="freedate" size="15" maxlength="10"
                               value="<?php echo(!empty($row->freedate) ? date("m-d-Y", $row->freedate) : ""); ?>"
                               style="background-color:#ffffff;" readonly="readonly"/>
                        <input type="button" name="selectDate" id="selectDate" value="..."/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><br/><br/>&nbsp;</td>
                </tr>
            </table>
            <script language="javascript" type="text/javascript">
                <!--
                Calendar.setup({
                    inputField: "freedate",   // id of the input field
                    button: "selectDate",
                    ifFormat: "%m-%d-%Y",       // format of the input field
                    showsTime: true,
                    timeFormat: "24",
                    onUpdate: catcalc
                });

                function catcalc(cal) {
                    var date = cal.date;
                    var time = date.getTime()
                    var date2 = new Date(time);
                    document.getElementById("freedate").value = date2.print("%m-%d-%Y");
                }

                //-->
            </script>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="free_shipping"/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }


    //============================================= DRIVER OPTION ===============================================
    static function showDriverOption( &$rows, &$pageNav, $option,$filter,$warehousesList ) {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Driver Option Manager</th>
                </tr>
            </table>
            <p style="float: right;">
                Filter: <input name="filter" value="<?php echo $filter;?>" placeholder="Driver name, service, login or id" style= "width:250px">
                Warehouse: <?php echo $warehousesList;?>
            </p>

            <table class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
                    <th class="title"  align="left">Id</th>
                    <th class="title" align="left">Name</th>
                    <th width="18%" align="left">Driver Option Type</th>
                    <th align="left">Information</th>
                    <th align="left">Driver Login</th>
                    <th align="left">Created By</th>
                </tr>
                <?php
                $k = 0;
                for ($i=0, $n=count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);

                    $link 	= 'index2.php?option=com_deliver&act=driver_option&task=editA&hidemainmenu=1&id='. $row->id;
                    $checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber( $i ); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><?php echo $row->id ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Driver Option"><?php echo $row->warehouse_name . " - ". $row->service_name; ?></a></td>
                        <td><?php echo $row->driver_option_type; ?></a></td>
                        <td align="left">
                            <?php
                            if( $row->description ) {
                                $aDriversOptions	= explode( "[--2--]", $row->description );
                                $sDriversName		= "";
                                for( $k = 0; $k < count($aDriversOptions); $k++ ) {
                                    if( trim($aDriversOptions[$k]) ) {
                                        $sDriversName	=  $aDriversOptions[$k]. "<br/>";
                                    }
                                }

                                echo $sDriversName;
                            }
                            ?>
                        </td>
                        <td><?php echo $row->login; ?></a></td>
                        <td><?php echo $row->username; ?></a></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="driver_option" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    static function editDriverOption( &$row, $option, &$aList, $rates_obj = array()) {
        global $mosConfig_live_site,$my;
        ?>
        <script type="text/javascript">
            jQuery( document ).ready(function () {

                jQuery('input[name="login"]').change(function() {
                    let login = jQuery(this).val();

                    if (login != '') {
                        jQuery.ajax({
                            type: 'POST',
                            url: 'index2.php?option=com_deliver&act=driver_option&task=checkDriverLogin',
                            data: {
                                login: login
                            },
                            dataType: 'json',
                            context: this,
                            beforeSend: function() {
                                jQuery('.error').hide();
                            },
                            success: function(json) {
                                if (json.result === false) {
                                    jQuery('.error').show();
                                }
                            },
                            error: function (jqXHR, exception) {
                            },
                            timeout: 15000
                        });
                    }
                });

            });
        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Driver Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New';?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="50%" class="adminform" style="width: 50%; float: left;">
                <tr>
                    <th colspan="2">Driver Option Detail</th>
                <tr>
                <tr>
                    <td width="10%"><b>Service Name:</b><span style="color: red">*</span></td>
                    <td><input class="inputbox" type="text" name="service_name" size="40" maxlength="255" value="<?php echo $row->service_name;?>" /></td>
                </tr>
                <tr>
                    <td width="10%"><b>Driver Option Type:</b></td>
                    <td>
                        <?php echo $aList['driver_option_type'];?>
                    </td>
                </tr>
                <tr>
                    <td width="10%"><b>Warehouse:</b></td>
                    <td><?php echo $aList['warehouse_id'];?></td>
                </tr>
                <tr>
                    <td width="10%"><b>Email:</b></td>
                    <td><input class="inputbox" type="email" size="40" maxlength="255" name="email" value="<?php echo $row->email;?>"></td>
                </tr>
                <tr>
                    <td width="10%"><b>Telephone Number:</b><span style="color: red">*</span></td>
                    <td><input class="inputbox" type="text" size="40" maxlength="20" name="number" value="<?php echo $row->number;?>"></td>
                </tr>
                <tr>
                    <td width="10%"><b>Driver Name:</b><span style="color: red">*</span></td>
                    <td><input class="inputbox" type="text" size="40" maxlength="255" name="driver_name" value="<?php echo $row->driver_name;?>"></td>
                </tr>
                <tr>
                    <td width="10%"><b>Information:</b><span style="color: red"></span></td>
                    <td><textarea style="width: 265px;" name="description" rows="5"><?php echo $row->description;?></textarea></td>
                </tr>
                <tr>
                    <td width="10%"><b>Login:</b></td>
                    <td><input class="inputbox" type="text" size="40" maxlength="255" name="login" value="<?php echo $row->login;?>"><div class="error" style="color:red;display:none;">This login already used</div></td>
                </tr>
                <tr>
                    <td width="10%"><b>Password:</b></td>
                    <td><input class="inputbox" type="text" size="40" maxlength="255" name="password" value=""></td>
                </tr>
            </table>
            <table width="50%" class="adminform" style="width: 50%; float: left;">
                <tr>
                    <th>Zone</th>
                    <th>Rate</th>
                    <th>Rate Driver</th>
                    <th>Rate This Driver</th>
                <tr>
                <tr><td colspan="4" style="text-align: center;">
                        <button id="button_all_minus_1">All -1$</button>
                        <button id="button_all_minus_05">All -0.5$</button>
                        <button id="button_all_default">Set All to Default</button>
                        <button id="button_all_plus_05">All +0.5$</button>
                        <button id="button_all_plus_1">All +1$</button>
                    </td>
                </tr>
                <script type="text/javascript">
                    function update_rates(amount) {
                        if (amount) {

                            $('.driver_rate_input').each(function(index, value) {
                                var amnt =parseFloat($(this).val()) + parseFloat(amount);
                                if( amnt<0) {amnt = 0;}
                                $(this).val(parseFloat(amnt).toFixed(2));
                            });
                        } else {
                            $('.driver_rate_input').each(function(index, value) {
                                $(this).val($(this).attr("data-default"));
                            });
                        }
                    }
                    document.getElementById("button_all_minus_1").addEventListener("click", function(event){
                        event.preventDefault()
                        update_rates(-1)
                    });
                    document.getElementById("button_all_minus_05").addEventListener("click", function(event){
                        event.preventDefault()
                        update_rates(-0.5)
                    });

                    document.getElementById("button_all_default").addEventListener("click", function(event){
                        event.preventDefault()
                        update_rates(false)
                    });
                    document.getElementById("button_all_plus_05").addEventListener("click", function(event){
                        event.preventDefault()
                        update_rates(0.5)
                    });
                    document.getElementById("button_all_plus_1").addEventListener("click", function(event){
                        event.preventDefault()
                        update_rates(1)
                    });

                </script>
                <?php
                foreach ($rates_obj as $rate_obj) {
                    ?>
                    <tr>
                        <td>
                            <a href="/administrator/index2.php?option=com_driver_rates&task=rate_edit&id=<?php echo $rate_obj->id_rate; ?>" target="_black" title="Edit drafult rate in  new tab. Changes will be available after you refresh this one"><?php echo $rate_obj->name; ?></a>
                        </td>
                        <td>
                            <?php echo $rate_obj->rate; ?>
                        </td>
                        <td>
                            <?php echo $rate_obj->rate_driver; ?>
                        </td>
                        <td>
                            <input class="driver_rate_input" name="rate['<?php echo $rate_obj->id_rate; ?>']" value="<?php echo $rate_obj->driver_rate; ?>" data-default="<?php echo $rate_obj->rate_driver; ?>"  />
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="act" value="driver_option" />
            <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
            <input type="hidden" name="old_password" value="<?php echo $row->password; ?>" />
            <input type="hidden" name="created_by" value="<?php echo $row->created_by?$row->created_by:$my->id; ?>" />
            <input type="hidden" name="task" value="" />
        </form>
        <?php
    }



    //============================================= POSTAL CODE OPTION ===============================================
    function showpPostalCode(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Postal Code Manager</th>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="20">#</th>
                    <th width="20" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th class="title">Postal Code</th>
                    <th width="20%" nowrap="nowrap" align="left">Location Name</th>
                    <th width="8%" nowrap="nowrap" align="center">Deliver Day(s)</th>
                    <!--			<th width="12%" nowrap="nowrap" align="center">Price</th>-->
                    <th width="8%" nowrap="nowrap" align="center">Undeliverable Postcode</th>
                    <th width="12%" nowrap="nowrap" align="center">Published</th>
                    <th colspan="2" nowrap="nowrap" width="5%">Reorder</th>
                    <th width="5%" align="left">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_deliver&act=postal_code&task=editA&hidemainmenu=1&id=' . $row->id;

                    $img = $row->published ? 'tick.png' : 'publish_x.png';
                    $task = $row->published ? 'unpublish' : 'publish';
                    $alt = $row->published ? 'Published' : 'Unpublished';

                    $aOption = explode("[--1--]", $row->options);

                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Postal Code Option"><?php echo $row->name; ?></a>
                        </td>
                        <td align="left"><b><?php echo $aOption[0]; ?></b></td>
                        <td align="center"><b><?php echo $aOption[1]; ?></b></td>
                        <!--				<td align="center"><b>$--><?php //echo $aOption[2]; ?><!--</b></td>-->
                        <td align="center">
                            <b>
                                <?php
                                if ($aOption[3] == 0 && $aOption[3] != null) {
                                    echo "<font color='red'>Yes</font>";
                                } else {
                                    echo "No";
                                }
                                ?>
                            </b>
                        </td>
                        <td align="center">
                            <a href="javascript: void(0);"
                               onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $task; ?>')">
                                <img src="images/<?php echo $img; ?>" width="12" height="12" border="0"
                                     alt="<?php echo $alt; ?>"/>
                            </a>
                        </td>
                        <td><?php echo $pageNav->orderUpIcon($i, true); ?></td>
                        <td><?php echo $pageNav->orderDownIcon($i, true); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="postal_code"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    function editPostalCode(&$row, $option, &$lists)
    {
        global $mosConfig_live_site;
        $aOption = explode("[--1--]", $row->options);
        ?>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.name.value == "") {
                    alert("You must provide a name.");
                } else {
                    submitform(pressbutton);
                }
            }

            //-->
        </script>

        <style>
            /* Always set the map height explicitly to define the size of the div
             * element that contains the map. */
            #map {
                height: 100%;
            }

            /* Optional: Makes the sample page fill the window. */
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style>


        <script>
            var geocoder;
            var map;
            var markers = [];

            function initMap() {
                geocoder = new google.maps.Geocoder();
                //Default setup
                var latlng = new google.maps.LatLng(-34.397, 150.644);
                var myOptions = {
                    zoom: 6,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById("map"), myOptions);
                codeAddress('<?php echo $row->name;?>')
            }

            function reinitmap(text) {
                codeAddress(text.value)

            }

            function codeAddress(zipCode) {
                geocoder.geocode({'address': zipCode + ',Australia'}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        //Got result, center the map and put it out there
                        map.setCenter(results[0].geometry.location);
                        for (var i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location
                        });
                        markers.push(marker);
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIq19TVV4qOX2sDBxQofrWfjeA7pebqy4&callback=initMap"
                async defer></script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Postal Code Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">Deliver Option Detail</th>
                <tr>
                <tr>
                    <td width="10%"><b>Postcode:</b></td>
                    <td><input class="inputbox" type="text" onchange="reinitmap(this);" name="name" size="10"
                               maxlength="10" value="<?php echo $row->name; ?>"/></td>
                </tr>
                <tr>
                    <td width="10%"><b>Location Name:</b></td>
                    <td><input class="inputbox" type="text" name="location_name" size="50" maxlength="255"
                               value="<?php echo $aOption[0]; ?>"/></td>
                </tr>
                <tr>
                    <td width="10%"><b>Deliver day(s):</b></td>
                    <td><input class="inputbox" type="text" name="deliver_day" size="10" maxlength="2"
                               value="<?php if (!$aOption[1]) echo "0"; else echo $aOption[1]; ?>"/></td>
                </tr>
                <!--			<tr>-->
                <!--				<td width="10%"><b>Price:</b></td>-->
                <!--				<td><b>$</b><input class="inputbox" type="text" name="price" size="9" maxlength="9" value="-->
                <?php // if( !$aOption[2] ) echo "0"; else echo $aOption[2];
                ?><!--" /></td>-->
                <!--			</tr>-->
                <tr>
                    <td><b>Publish:</b></td>
                    <td><?php echo $lists['publish']; ?></td>
                </tr>
                <tr>
                    <td><b>Undeliverable Postcode:</b></td>
                    <td><?php echo $lists['undeliver']; ?></td>
                </tr>
                <tr>
                    <td><b>Ordering:</b></td>
                    <td><input class="inputbox" type="text" name="ordering" size="10" maxlength="8"
                               value="<?php echo $row->ordering; ?>"/></td>
                </tr>
            </table>
            <div style="width: 100%">
                <div id="map"></div>
            </div>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="postal_code"/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }


    //============================================= DELIVER OPTION ===============================================
    function showUnAvailableDeliver(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>UnAvailable Deliver Date Option Manager</th>
                </tr>
                <!--<tr>
				<td align="right" style="padding-right:50px;">
					<b>Filter By:&nbsp;</b>
					<select name="filter_years" size="1" onchange="document.adminForm.submit();">
						<option value='0' selected>Year</option>
						<?php
                $filter_years = mosGetParam($_POST, "filter_years", 0);
                $yearNow = date("Y");
                for ($i = $yearNow; $i <= $yearNow + 1; $i++) {
                    if (intval($filter_years) == $i) {
                        echo "<option value='$i' selected>$i</option>";
                    } else {
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
                    <th width="20" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th class="title">Date( Month / Day )</th>
                    <th width="40%" nowrap="nowrap" align="left">Description</th>
                    <th width="20%" align="left">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_deliver&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Deliver Option"><b
                                        style="font:bold 11px Tahoma;"><?php echo $row->name; ?></b></a></td>
                        <td align="left"><?php echo $row->options; ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value=""/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }

    function showCalendar(&$events, $option)
    {
        mosCommonHTML::loadBootstrap();


        ?>
        <script language="javascript" type="text/javascript">
            var multydate = '';
            var selected_date_html = '';
        </script>
        <script type="text/javascript"
                src="/administrator/components/com_deliver/e-calendar/js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript"
                src="/administrator/components/com_deliver/e-calendar/js/jquery.e-calendar.js"></script>
        <link rel="stylesheet" href="/administrator/components/com_deliver/e-calendar/css/jquery.e-calendar.css"/>
        <script language="javascript" type="text/javascript">
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function select_type(a) {
                $('.item_name').val(capitalizeFirstLetter(a.value))
                if (a.value == 'surcharge' || a.value == 'ootsurcharge') {
                    $('.price').show()
                } else {
                    $('.price').hide()
                }
            }

            function delete_item(a) {

                var r = confirm("Are you sure you want to delete this item?");
                if (r == true) {

                    $.post("index2.php",
                        {
                            option: "com_deliver",
                            task: "delete",
                            id: a.getAttribute("item")
                        },
                        function (data) {
                            if (data == 'success') {
                                alert('Item Deleted Successfully')
                                location.reload()
                            }
                        }
                    );


                }

            }

            function close_event() {
                $('.c-event-item-class').hide();
                multydate = '';
                selected_date_html = '';
            }

            var events = <?php  echo $events;?>;

            function create_calendar() {

                $('#calendar').eCalendar({
                    events: events
                });
            }

            $(document).ready(function () {
                create_calendar()
                $('.customy').click(function () {
                    $.post("index2.php",
                        {
                            option: "com_deliver",
                            task: "update_sundays"
                        },
                        function (data) {
                            if (data == 'success') {
                                alert('Sundays List Updated Successfully')
                                location.reload()
                            } else {
                                alert(data)
                            }
                        }
                    );
                })

            });


        </script>
        <div class="message" style="color:red;text-align: center"></div><br>
        <div id="calendar"></div>
        <br>
        <div class="info">
            <div class="info_div unavaliable">Unavaliable</div>
            <div class="info_div free">Free Delivery</div>
            <div class="info_div surcharge">Surcharge</div>
            <div class="info_div oot">OOT Closed</div>
            <div class="info_div ootsurcharge">OOT Closed, Rest Surcharge</div>
        </div>
    <?php }

    function editUnAvailableDeliver(&$row, $option, &$lists)
    {
        global $mosConfig_live_site;

        $aDate = explode("/", $row->name);

        ?>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.days.value == "0" || form.months.value == "0") {
                    alert("Please choose month and day!");
                } else {
                    submitform(pressbutton);
                }
            }

            //-->
        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        UnAvailable Deliver Date Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">UnAvailable Deliver Date Option Detail</th>
                <tr>
                <tr>
                    <td width="15%"><b>Month:</b></td>
                    <td>
                        <select name="months" size="1">
                            <option value='0' selected>Month</option>
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                if (intval($aDate[0]) == $i) {
                                    if ($i < 10) {
                                        echo "<option value='$i' selected>0$i</option>";
                                    } else {
                                        echo "<option value='$i' selected>$i</option>";
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo "<option value='$i'>0$i</option>";
                                    } else {
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
                            for ($i = 1; $i <= 31; $i++) {
                                if (intval($aDate[1]) == $i) {
                                    if ($i < 10) {
                                        echo "<option value='$i' selected>0$i</option>";
                                    } else {
                                        echo "<option value='$i' selected>$i</option>";
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo "<option value='$i'>0$i</option>";
                                    } else {
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
                        $yearNow = date("Y");
                        for ($i = $yearNow; $i <= $yearNow + 1; $i++) {
                            if (intval($aDate[2]) == $i) {
                                echo "<option value='$i' selected>$i</option>";
                            } else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                        ?>
					</select>-->
                    </td>
                </tr>
                <tr>
                    <td width="10%"><b>Description:</b></td>
                    <td><input class="inputbox" type="text" name="options" size="100" maxlength="255"
                               value="<?php echo $row->options; ?>"/></td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value=""/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }

    //============================================= SPECIAL DELIVER OPTION ===============================================
    function showSpecialDeliver(&$rows, &$pageNav, $option)
    {
        mosCommonHTML::loadOverlib();
        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Change price by:</th>
                </tr>
                <!--<tr>
				<td align="right" style="padding-right:50px;">
					<b>Filter By:&nbsp;</b>
					<select name="filter_years" size="1" onchange="document.adminForm.submit();">
						<option value='0' selected>Year</option>
						<?php
                $filter_years = mosGetParam($_POST, "filter_years", 0);
                $yearNow = date("Y");
                for ($i = $yearNow; $i <= $yearNow + 1; $i++) {
                    if (intval($filter_years) == $i) {
                        echo "<option value='$i' selected>$i</option>";
                    } else {
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
                    <th width="20" class="title"><input type="checkbox" name="toggle" value=""
                                                        onclick="checkAll(<?php echo count($rows); ?>);"/></th>
                    <th class="title">Date( Month / Day )</th>
                    <th width="40%" nowrap="nowrap" align="left">Price</th>
                    <th width="20%" align="left">&nbsp;</th>
                </tr>
                <?php
                $k = 0;
                for ($i = 0, $n = count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    $link = 'index2.php?option=com_deliver&act=special_deliver&task=editA&hidemainmenu=1&id=' . $row->id;
                    $checked = mosCommonHTML::CheckedOutProcessing($row, $i);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber($i); ?></td>
                        <td><?php echo $checked; ?></td>
                        <td><a href="<?php echo $link; ?>" title="Edit Deliver Option"><b
                                        style="font:bold 11px Tahoma;"><?php echo $row->name; ?></b></a></td>
                        <td align="left"><strong>$<?php echo number_format($row->options, 2, ".", " "); ?></strong></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="special_deliver"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <?php
    }


    function editSpecialDeliver(&$row, $option, &$lists)
    {
        global $mosConfig_live_site;

        $aDate = explode("/", $row->name);

        ?>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                if (pressbutton == 'cancel') {
                    submitform(pressbutton);
                    return;
                }

                // do field validation
                if (form.days.value == "0" || form.months.value == "0") {
                    alert("Please choose month and day!");
                } else {
                    submitform(pressbutton);
                }
            }

            //-->
        </script>

        <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
            <table class="adminheading">
                <tr>
                    <th>
                        Special Deliver Date Option:
                        <small>
                            <?php echo $row->id ? 'Edit' : 'New'; ?>
                        </small>
                    </th>
                </tr>
            </table>

            <table width="100%" class="adminform">
                <tr>
                    <th colspan="2">Special Deliver Date Option Detail</th>
                <tr>
                <tr>
                    <td width="15%"><b>Month:</b></td>
                    <td>
                        <select name="months" size="1">
                            <option value='0' selected>Month</option>
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                if (intval($aDate[0]) == $i) {
                                    if ($i < 10) {
                                        echo "<option value='$i' selected>0$i</option>";
                                    } else {
                                        echo "<option value='$i' selected>$i</option>";
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo "<option value='$i'>0$i</option>";
                                    } else {
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
                            for ($i = 1; $i <= 31; $i++) {
                                if (intval($aDate[1]) == $i) {
                                    if ($i < 10) {
                                        echo "<option value='$i' selected>0$i</option>";
                                    } else {
                                        echo "<option value='$i' selected>$i</option>";
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo "<option value='$i'>0$i</option>";
                                    } else {
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
                        $yearNow = date("Y");
                        for ($i = $yearNow; $i <= $yearNow + 1; $i++) {
                            if (intval($aDate[2]) == $i) {
                                echo "<option value='$i' selected>$i</option>";
                            } else {
                                echo "<option value='$i'>$i</option>";
                            }
                        }
                        ?>
					</select>-->
                    </td>
                </tr>
                <tr>
                    <td width="10%"><b>Price:</b></td>
                    <td><strong>$</strong><input class="inputbox" type="text" name="options" size="10" maxlength="10"
                                                 value="<?php echo $row->options; ?>"/></td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="special_deliver"/>
            <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }


    //============================================= CUT OFF TIME CONFIGURATION ===============================================
    function editCutOffTime(&$row, $option)
    {
        global $mosConfig_live_site;

        $aOptionParam = explode("[--1--]", $row[3]);

        ?>
        <script language="javascript" type="text/javascript">
            <!--
            function submitbutton(pressbutton) {
                var form = document.adminForm;
                // do field validation
                if (form.hours.value == "") {
                    alert("You must choose hour number.");
                    return;
                }

                if (form.minutes.value == "") {
                    alert("You must choose minute number.");
                    return;
                }

                submitform(pressbutton);
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
                    <td width="10%" valign="middle"><b>Limit Time:</b></td>
                    <td style="height:55px;">
                        <select name="hours" size="1" style="font:bold 11px Tahoma;">
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                if (intval($aOptionParam[0] == $i)) {
                                    if ($i < 10) {
                                        echo '<option value="0' . $i . '" selected>0' . $i . '</option>';
                                    } else {
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo '<option value="0' . $i . '">0' . $i . '</option>';
                                    } else {
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select> <b>:</b>
                        <select name="minutes" size="1" style="font:bold 11px Tahoma;">
                            <?php
                            for ($i = 0; $i < 60; $i++) {
                                if (intval($aOptionParam[1] == $i)) {
                                    if ($i < 10) {
                                        echo '<option value="0' . $i . '" selected>0' . $i . '</option>';
                                    } else {
                                        echo '<option value="' . $i . '" selected>' . $i . '</option>';
                                    }
                                } else {
                                    if ($i < 10) {
                                        echo '<option value="0' . $i . '">0' . $i . '</option>';
                                    } else {
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">The Deliver Extra Fee( for The Same Day) Detail:</th>
                <tr>
                <tr>
                    <td width="10%" valign="middle"><b>Price:</b></td>
                    <td style="height:35px;">
                        <b>$</b><input class="inputbox" type="text" name="deliver_fee" size="8" maxlength="8"
                                       value="<?php if (!$aOptionParam[2]) echo "0"; else echo $aOptionParam[2]; ?>"/>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>"/>
            <input type="hidden" name="act" value="cut_off_time"/>
            <input type="hidden" name="id" value="<?php echo $row[0]; ?>"/>
            <input type="hidden" name="task" value=""/>
        </form>
        <?php
    }


}

?>
