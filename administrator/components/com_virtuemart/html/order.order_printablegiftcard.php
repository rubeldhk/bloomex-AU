<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
/**
 * @version $Id: order.order_printdetails.php,v 1.7 2005/05/10 18:45:04 soeren_nb Exp $
 * @package mambo-phpShop
 * @subpackage HTML
 * Contains code from PHPShop(tm):
 * 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
 * 	Community: www.phpshop.org, forums.phpshop.org
 * Conversion to Mambo and the rest:
 * 	@copyright (C) 2004-2005 Soeren Eberhardt
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * mambo-phpShop is Free Software.
 * mambo-phpShop comes with absolute no warranty.
 *
 * www.mambo-phpshop.net
 */
mm_showMyFileName(__FILE__);
global $database;


require_once(CLASSPATH . 'ps_checkout.php');
require_once(CLASSPATH . 'ps_product.php');
$ps_product = new ps_product;

$orders = mosgetparam($_REQUEST, 'order_id', null);
$dbc = new ps_DB;
$orders = explode(",", $orders);
?>
    <style>
        #loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url(/images/103.gif) center no-repeat #fff;
        }
    </style>
    <div id="loader"></div>

    <script type="text/javascript">
        window.onload = function() {
            document.getElementById('loader').style.display = "none";
        };
    </script>
<?php
foreach ($orders as $order_id) {
    if (!is_numeric($order_id))
        die('Please provide a valid Order ID!');

    $q = "SELECT * FROM #__{vm}_orders WHERE order_id='$order_id'";
    $db->query($q);

    if ($db->next_record()) {
        ?>
        <br/>
        <style type="text/css" media="print">
            @media print{
                @page {
                    size: landscape;
                }
            }

            .page {
                page-break-before:avoid;
                width:4.25in;
                height: 7in;
                margin-left:6.25in;
                display: flex;
                align-items: center;
            }

            .text {
                width: 100%;
                padding:0.25in;
                word-break: keep-all;
                font-weight: bold;
                font-size: 14pt;
                font-family: Courgette, cursive;
                text-align: justify;
            }
            .signature{
                display: inline-block;
                width:100%;
                font-size: 16pt;
                text-align:right;
            }
        </style>
        <div class="page">

            <div class="text">
                <?php
                if ($db->f("customer_note")) {
                    echo str_replace("\\", "", nl2br(htmlspecialchars_decode($db->f("customer_note"))));
                } else {
                    echo " ";
                }

                if ($db->f("customer_signature")) {
                    echo "<br/><br/><span class=\"signature\">" . str_replace("\\", "", htmlspecialchars_decode(nl2br($db->f("customer_signature"))))."</span>";
                } else {
                    echo " ";
                }
                ?>
            </div>

        </div>

        <?php
    }
    if ($order_id !== end($orders)) {
        echo '<div class="hr_order"></div>';
    }
}
  