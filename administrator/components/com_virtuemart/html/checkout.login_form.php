<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 *
 * @version $Id: checkout.login_form.php,v 1.6.2.2 2006/03/10 15:55:15 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage html
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
mm_showMyFileName(__FILE__);

global $sef, $mosConfig_absolute_path, $database;


include $mosConfig_absolute_path . '/administrator/components/com_virtuemart/classes/ps_product.php';
$ps_product = new ps_product;

$query = "SELECT 
    `p`.`product_id`
    FROM (
        `jos_vm_product` AS `p`
    ) 
    WHERE 
        `p`.`product_publish`='Y'  and `p`.`product_thumb_image` != ''
    order by RAND() LIMIT 0, 4";

$database->setQuery($query);
$products_obj = $database->loadObjectList();
$products = array();

foreach ($products_obj as $product_obj) {
    $products[] = $product_obj->product_id;
}
$product_ordering_a = array(
    1 => array(
        'title' => 'sort by rating',
        'type' => 'desc'
    ),
    2 => array(
        'title' => 'sort by price',
        'type' => 'desc'
    ),
    3 => array(
        'title' => 'sort by price',
        'type' => 'asc'
    ),
);
$product_ordering = $_COOKIE['product_ordering'] ?? '';
$sorting_class = 'glyphicon-sort';
if ($product_ordering === 'desc') {
    $sorting_class = 'glyphicon-sort-by-attributes-alt';
} elseif ($product_ordering === 'asc') {
    $sorting_class = 'glyphicon-sort-by-attributes';
}
$sortbypriceLabel = 'Sort by price';
include_once './modules/breadcrumbs.php';
?>
<script>
    function onSubmit(token) {
        document.getElementById("login").submit();
        console.log("submit login")
    }
</script>
<style>
    .vertical-divider {
        position: relative;
    }
    .vertical-divider::after {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 100%;
        width: 1.2px;
        background-color: #939393;
    }
    label,h4{
        padding: 4px;
    }
    .card {
        padding: 20px;
        background: #fff;
    }
    .form-input {
        width: 30%;
        margin-right: 5px;
        margin-top: 4px;
        border: 1px #e7e7e7 solid;
        padding: 4px 4px;
    }
    .btn-login {
        margin-left: 2px;
        border: 1px #e7e7e7 solid;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: bold;
    }
    .btn-login:hover {
        background-color: #dcdcdc;
    }
    .link-blue {
        color: #5050e4;
    }
    .link-gray {
        color: #8f8f8f;
    }
    .btn-help {
        background-color: #d52e2e;
        color: white;
        border: none;
        border-radius: 30px;
        padding: 7px 12px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
    }
    .btn-help:hover {
        background-color: #b92424;
    }
    @media (max-width: 480px) {
        .md-12 {
            width: 100%;
            margin: 5px;
            border-radius: 7px;
            font-size: 18px;
        }
        .md-6-m5 {
            margin-bottom: 5px;
        }
    }
</style>
<div class="container padding0">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-6 vertical-divider">
            <h3>Welcome Back!</h3>
            <div class="card">

                    <h4>Log into your Bloomex Account: </h4>
                    <form role="form" action="/index.php" method="post" name="login" id="login">
                        <div class="form-group">
                            <input type="text" class="form-input md-12" name="username" id="username" placeholder="Enter email" onchange="localStorage.setItem('user_email', this.value); identifyKlaviyo(this.value)">
                            <input type="password" class="form-input md-12" name="passwd" id="passwd" placeholder="Password">
                            <label>
                                <input type="checkbox" name="remember" class="inputbox" value="yes"> Remember me
                            </label>

                            <button type="submit" class="btn btn-default md-12 btn-login" id="loginButton">Login</button>
                        </div>
                        <input type="hidden" name="op2" value="login">
                        <input type="hidden" name="option" value="login">
                        <input type="hidden" name="return" value="/<?php echo $sef->real_uri; ?>/">
                    </form>
                    Log In Problems? <a class="link-blue" href="/lost-password/">Click here.</a>

            </div>
            <h3>Hours & Contact Information</h3>
            <div class="card">
                <p><strong>Support Hours:</strong><span class="link-gray"> 7 AM EST to 11 PM EST</span></p>
                <p><strong>Phone:</strong><a href="tel:+1800905147" class="link-blue"> 1.800.905.147</a></p>
                <p><strong>Email:</strong> <a class="link-blue" href="mailto:care@bloomex.com.au">care@bloomex.com.au</a></p>
            </div>
        </div>

        <!-- Правая колонка: Форма обратной связи -->
        <div class="col-md-6">
            <h3>Need Help? Contact Us Here</h3>
            <div class="card">
                <form action="/index.php" method="post">
                    <div class="row" style="margin-bottom: 12px;">
                        <div class="col-md-6 md-6-m5">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name">
                        </div>
                        <div class="col-md-6">
                            <label for="email">Email<span style="color:red">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="number" class="form-control" name="phone" id="phone" placeholder="Enter your phone">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" name="message" id="message" rows="3" placeholder="Your message"></textarea>
                    </div>
                    <button type="submit" class="btn-help md-12">Submit</button>
                    <input type="hidden" name="op2" value="need_help">
                    <input type="hidden" name="option" value="need_help">
                    <input type="hidden" name="return" value="/<?php echo $sef->real_uri; ?>/">
                </form>
            </div>
        </div>
    </div>
    <div class="container bottom_category">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 title">
                <div class="flower">
                    <img alt="page not found" src="/templates/bloomex_adaptive/images/Flower.svg">
                </div>
                <h1>Australia’s #1 Flower Delivery - Trusted by Millions</h1>
                <p class="sort_by_select"><?php echo $sortbypriceLabel; ?><span
                            class="glyphicon <?php echo $sorting_class; ?> "></span></p>
            </div>
        </div>
    </div>
    <?php
    echo $ps_product->show_product_list($products);
    ?>
</div>

<script src="/administrator/components/com_virtuemart/html/checkout.login.js?ver=2"></script>