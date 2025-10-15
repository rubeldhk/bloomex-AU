<?php
/**
 * @version $Id: contact.html.php 4157 2006-07-02 17:58:51Z stingrey $
 * @package Joomla
 * @subpackage Contact
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
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
 * @subpackage Contact
 */
class HTML_LandingPages
{

    static function viewPage($Page, $products_obj)
    {
        global $mosConfig_live_site, $mm_action_url, $my, $VM_LANG, $mosConfig_absolute_path, $cur_template, $iso_client_lang,$sef;
        require_once($mosConfig_absolute_path . '/components/com_virtuemart/virtuemart_parser.php');
        ?>
        <?php
        if ($_REQUEST['type'] == 'basket') {
            $lang = $VM_LANG->_BASKET_DELIVERY;
        } elseif ($_REQUEST['type'] == 'sympathy') {
            $lang = $VM_LANG->_SYMPATHY_DELIVERY;
        } else {
            $lang = $VM_LANG->_FLOWER_DELIVERY;
        }

        $sortbypriceLabel = 'Sort by price';
        $product_ordering = isset($_COOKIE['product_ordering']) ? $_COOKIE['product_ordering'] : '';
        $sorting_class = '';
        if ($product_ordering === 'desc') {
            $sorting_class = 'glyphicon-sort-by-attributes-alt';
        } elseif ($product_ordering === 'asc') {
            $sorting_class = 'glyphicon-sort-by-attributes';
        }

        ?>
        <div class="container bottom_category">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 title">
                    <div class="flower">
                        <img alt="<?php echo $Page['city']; ?>"
                             src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/<?php echo $cur_template; ?>/images/Flower.svg"/>
                    </div>
                    <h1 class="landing_title"><?php echo  ($sef->h1)?$sef->h1:$Page['city'] . ' ' . $lang; ?></h1>
                    <p class="sort_by_select"><?php echo $sortbypriceLabel; ?><span
                                class="glyphicon <?php echo $sorting_class; ?> "></span></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description">
                    <?php
                    $landing_content = explode('{readmore}', $Page['content']);
                    ?>
                    <?php echo $landing_content[0]; ?>
                    <?php
                    if (isset($landing_content[1]) AND !empty($landing_content[1])) {
                        ?>
                        <span class="landing_content_more_btn"> more...</span>
                        <span class="landing_content_more"><?php echo $landing_content[1]; ?></span>
                        <?php
                    }
                    //<?php echo str_replace('{readmore}', '', $Page['content']);
                    ?>
                </div>
            </div>
        </div>
<!--        --><?php //include_once $_SERVER['DOCUMENT_ROOT'] . '/modules/adaptive/landingpage_popup.php'; ?>
        <?php

        function signQuery($query, $privateKey)
        {
            $url = parse_url($query);
            $urlPartToSign = $url['path'] . '?' . $url['query'];
            $decodedKey = base64_decode(str_replace(array('-', '_'), array('+', '/'), $privateKey));
            $signature = hash_hmac('sha1', $urlPartToSign, $decodedKey, true);
            $encodedSignature = str_replace(array('+', '/'), array('-', '_'), base64_encode($signature));
            return sprintf('%s&signature=%s', $query, $encodedSignature);
        }

//        echo '<pre>';
//            print_r($Page);
//        echo '</pre>';
        ?>
        <script type="text/javascript">
            //var landing_image = '<?php echo signQuery('https://maps.googleapis.com/maps/api/staticmap?center=' . $Page['lat'] . ',' . $Page['lng'] . '&zoom=12&size=615x265&key=AIzaSyCLbxnzPiq1nrYpTlzwDg4yHosrhNoo5fo', 'ODzJ-le6_6EHn3j734noQNPW9Zs='); ?>';
            var landing_image = '/images/landing_images/<?php echo $Page['id']; ?>.webp';
            var bar_on_map = '<?php echo htmlentities($Page['city'], ENT_QUOTES) . ' ' . $lang; ?>';
            var landing_phone = '<?php echo ($Page['telephone']) ? $Page['telephone'] : '1-877-256-6610'; ?>';
        </script>

        <?php
        require_once(CLASSPATH . 'ps_product.php');
        $ps_product = new ps_product;
        if ($products_obj) {
            echo $ps_product->show_product_list($products_obj);
        }
        /*
        if (isset($sef->description_text_footer) && strstr($sef->description_text_footer, '{readmore}') !== false) {
            $footer_description_a = explode('{readmore}', $sef->description_text_footer);
            $footer_description = $footer_description_a[0];
        } else {
            $footer_description = $sef->description_text_footer??'';
        }

        if (isset($footer_description_a[1]) AND !empty($footer_description_a[1])) {
            $footer_description .= '<span class="landing_content_more_btn"> more...</span><span class="landing_content_more">' . $footer_description_a[1] . '</span>';
        }

        if (!empty($footer_description)) {
            ?>

            <div class="container bottom_category">
                <div class="row">
                    <div class="col-xs-12 description">
                        <div class="text">
                            <?php echo $footer_description; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        */
    }

}
?>