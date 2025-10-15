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
class HTML_Survey_after_order {


    //============================================= Location OPTION ===============================================
    function showSurvey( &$rows, &$pageNav, $option, $lists ) {
        mosCommonHTML::loadOverlib();
        $aRate	= array();
        $aRate['1']	=  '1 (poor)';
        $aRate['2']	=  '2 (below average)';
        $aRate['3']	=  '3 (average)';
        $aRate['4']	=  '4 (above average)';
        $aRate['5']	=  '5 (excellent)';

        ?>
        <form action="index2.php" method="post" name="adminForm">
            <table class="adminheading">
                <tr>
                    <th>Customers Survey list</th>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">



                            <b>Filter By (order_id,customer_id):&nbsp;</b>
                        <input type="text" value="<?php echo $lists['filter_key'];?>" name="filter_key" class="filter_key" size="30"   onChange="document.adminForm.submit();" />

                        <b>Order By:&nbsp;</b>
                       <select name="order_by" class="order_by"  onChange="document.adminForm.submit();">
                            <option value="1" <?php if($lists['order_by']=='1') echo 'selected';?>>Book Datetime DESC</option>
                            <option value="2" <?php if($lists['order_by']=='2') echo 'selected';?>>Book Datetime ASC</option>
                            <option value="3" <?php if($lists['order_by']=='3') echo 'selected';?>>Survey Date DESC</option>
                            <option value="4" <?php if($lists['order_by']=='4') echo 'selected';?>>Survey Date ASC</option>
                       </select>
                        <input type="button" onclick="download()" value="download filtered list">
                    </td>
                </tr>
            </table>
            <table class="adminlist">
                <tr>
                    <th width="5">#</th>
                    <th width="5" nowrap="nowrap" align="left">Order ID</th>
                    <th width="5" nowrap="nowrap" align="left"> Customer ID</th>
                    <th width="5" nowrap="nowrap" align="left">How did you place your order?</th>
                    <th width="5" nowrap="nowrap" align="left">How would you rate the Bloomex website</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate the Bloomex product selection and prices</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate the Bloomex ordering process</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate your Customer Service experience (if applicable)</th>
                    <th width="5" nowrap="nowrap" align="center">How was your experience overall</th>
                    <th width="5" nowrap="nowrap" align="center">How likely are you to recommend Bloomex to others</th>
                    <th width="30" nowrap="nowrap" align="center">Comments</th>
                    <th width="5" nowrap="nowrap" align="center">Survey Date</th>
                    <th width="5" nowrap="nowrap" align="center">Book Datetime</th>
                    <th width="5" nowrap="nowrap" align="center">Survey Send Datetime</th>
                    <th width="5" nowrap="nowrap" align="center">Email Open Datetime</th>
                    <th width="5" nowrap="nowrap" align="center">Survey Page Open Datetime</th>
                </tr>
                <?php
                $k = 0;
                for ($i=0, $n=count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    mosMakeHtmlSafe($row);
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td><?php echo $pageNav->rowNumber( $i ); ?></td>
                        <td align="left"><?php echo $row->order_id; ?></td>
                        <td align="left"><?php echo $row->user_id; ?></td>
                        <td align="left"><?php echo $row->place_order; ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_the_bloomex_website]) ? $aRate[$row->how_would_you_rate_the_bloomex_website] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_the_bloomex_product_selection_and_prices]) ? $aRate[$row->how_would_you_rate_the_bloomex_product_selection_and_prices] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_the_bloomex_ordering_process]) ? $aRate[$row->how_would_you_rate_the_bloomex_ordering_process] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_your_customer_service_experience]) ? $aRate[$row->how_would_you_rate_your_customer_service_experience] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_was_your_experience_overall]) ? $aRate[$row->how_was_your_experience_overall] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_likely_are_you_to_recommend_bloomex_to_others]) ? $aRate[$row->how_likely_are_you_to_recommend_bloomex_to_others] : ""); ?></td>
                        <td align="center"><?php echo $row->comments; ?></td>
                        <td align="center"><?php echo date("Y-m-d H:i:s",$row->survey_date); ?></td>
                        <td align="center"><?php echo $row->book_datetime; ?></td>
                        <td align="center"><?php echo $row->survey_send_datetime; ?></td>
                        <td align="center"><?php echo $row->email_open_datetime; ?></td>
                        <td align="center"><?php echo $row->survey_page_open_datetime; ?></td>

                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
            </table>
            <?php echo $pageNav->getListFooter(); ?>

            <input type="hidden" name="option" value="<?php echo $option; ?>" />

            <input type="hidden" name="task" value="" />

            <input type="hidden" name="hidemainmenu" value="0">
        </form>
        <style>
            table.adminlist th{
                background: #ccc !important;
            }
        </style>
        <script>

            function download() {
                jQuery.ajax({
                    data:
                        {
                            option: 'com_survey_after_order',
                            task: 'download',
                            order_by: $('.order_by').val(),
                            filter_key: $('.filter_key').val()
                        },

                    type: "POST",
                    url: "index3.php",
                    success: function (data)
                    {
                        //Convert the Byte Data to BLOB object.
                        var blob = new Blob([data], {type: "application/octetstream"});
                        var url = window.webkitURL;
                        link = url.createObjectURL(blob);
                        var a = $("<a />");
                        a.attr("download", 'customers-survey-list-au.csv');
                        a.attr("href", link);
                        $("body").append(a);
                        a[0].click();
                        $("body").remove(a);

                    }

                });
            }

        </script>
        <?php
    }



}
?>
