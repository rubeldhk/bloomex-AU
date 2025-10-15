<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: coupon.coupon_field.php,v 1.5 2005/11/05 14:11:57 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 * @author Erich Vinson
 * http://virtuemart.net
 */
mm_showMyFileName(__FILE__);
global $page;
$url_coupon_code = false;
?>
<script type="text/javascript">
    var url_coupon_code = false;
    <?php
    if (isset($_GET['page']) AND ($_GET['page'] == 'checkout.index' || $_GET['page'] == 'checkout.index') AND isset($_SESSION['checkout_ajax']['coupon_code']) AND !empty($_SESSION['checkout_ajax']['coupon_code'])) {
        $url_coupon_code = htmlspecialchars($_SESSION['checkout_ajax']['coupon_code']);
    }
            $hide_coupon_field = 'style="display:none;"';

        if (isset($_GET['page']) AND ($_GET['page'] == 'checkout.index' || $_GET['page'] == 'checkout.index')){
            $hide_coupon_field = 'style="display:block;"';
        }
    ?>
</script>

<div class="coupon_field" <?php echo $hide_coupon_field;?>>
    <div class="show_field" <?php echo ($url_coupon_code != false) ? 'style="display: block;"' : ''; ?>>
        <p class="coupon_text_desc">If you have a discount coupon code, please enter it below and click SUBMIT button:</p>
        <form class="d-flex align-items-center flex-wrap gap-2" role="form" method="post">
            <div class="form-group">
                <label for="coupon_code"></label>
                <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?php echo ($url_coupon_code != false) ? $url_coupon_code : ''; ?>">
            </div>
            <button type="submit" class="btn btn-default" id="coupon_btn">Submit</button>
            <div class="result"></div>
        </form>
    </div>
</div>
