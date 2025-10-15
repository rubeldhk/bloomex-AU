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
class HTML_Survey {


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
                    <th>Survey list</th>
                </tr>
                <tr>
                    <td align="right" style="padding:0px 20px 10px 0px;">
                        <b>Filter By:&nbsp;</b>
                        <input type="text" value="<?php echo $lists['filter_key'];?>" name="filter_key" size="30" />

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
                    <th width="5" nowrap="nowrap" align="center">How closely did your item(s) resemble the product description and photo on the website</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate the freshness, quality and appearance of your item(s)</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate your delivery experience</th>
                    <th width="5" nowrap="nowrap" align="center">How would you rate your Customer Service experience (if applicable)</th>
                    <th width="5" nowrap="nowrap" align="center">How was your experience overall</th>
                    <th width="5" nowrap="nowrap" align="center">How likely are you to recommend Bloomex to others</th>
                    <th width="30" nowrap="nowrap" align="center">Comments</th>
                    <th width="5" nowrap="nowrap" align="center">Survey Date</th>
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
                        <td align="center"><?php echo (!empty($aRate[$row->how_closely_did_your_item_s_resemble_the_product_description]) ? $aRate[$row->how_closely_did_your_item_s_resemble_the_product_description] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_the_freshness_quality_and_appearance_of_your]) ? $aRate[$row->how_would_you_rate_the_freshness_quality_and_appearance_of_your] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_your_delivery_experience]) ? $aRate[$row->how_would_you_rate_your_delivery_experience] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_would_you_rate_your_customer_service_experience]) ? $aRate[$row->how_would_you_rate_your_customer_service_experience] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_was_your_experience_overall]) ? $aRate[$row->how_was_your_experience_overall] : ""); ?></td>
                        <td align="center"><?php echo (!empty($aRate[$row->how_likely_are_you_to_recommend_bloomex_to_others]) ? $aRate[$row->how_likely_are_you_to_recommend_bloomex_to_others] : ""); ?></td>
                        <td align="center"><?php echo $row->comments; ?></td>
                        <td align="center"><?php echo date("Y-m-d H:i:s",$row->survey_date); ?></td>

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
        <?php
    }



}
?>
