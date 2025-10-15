<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* The configuration file for VirtueMart
*
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2006 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

global $mosConfig_absolute_path,$mosConfig_live_site;

// Check for trailing slash
if( $mosConfig_live_site[strlen( $mosConfig_live_site)-1] == '/' ) {
	$app = '';
}
else {
	$app = '/';
}
// these path and url definitions here are based on the mambo configuration
define( 'URL', $mosConfig_live_site.$app );
define( 'SECUREURL', 'https://bloomex.ca/' );

if ( @$_SERVER['HTTPS'] == 'on' ) {
	define( 'IMAGEURL', SECUREURL .'components/com_virtuemart/shop_image/' );
} else {
	define( 'IMAGEURL', URL .'components/com_virtuemart/shop_image/' );
}
define( 'COMPONENTURL', URL .'administrator/components/com_virtuemart/' );
define( 'ADMINPATH', $mosConfig_absolute_path.'/administrator/components/com_virtuemart/' );
define( 'CLASSPATH', ADMINPATH.'classes/' );
define( 'PAGEPATH', ADMINPATH.'html/' );
define( 'IMAGEPATH', $mosConfig_absolute_path.'/components/com_virtuemart/shop_image/' );

define('PSHOP_IS_OFFLINE', '');
define('PSHOP_OFFLINE_MESSAGE', '<h2>Our Shop is currently down for maintenance.</h2> Please check back again soon.');
define('USE_AS_CATALOGUE', '');
define('VM_TABLEPREFIX', 'vm');
define('VM_PRICE_SHOW_PACKAGING_PRICELABEL', '');
define('VM_PRICE_SHOW_INCLUDINGTAX', '');
define('VM_PRICE_ACCESS_LEVEL', 'Public Frontend');
define('VM_SILENT_REGISTRATION', '');
define('VM_BROWSE_ORDERBY_FIELD', 'product_price');
define('VM_ONCHECKOUT_SHOW_LEGALINFO', '1');
define('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT', '<br><br><b><font color=red>Please click "CONFIRM" button below to send your order for production and delivery</font></b>');
define('VM_ONCHECKOUT_LEGALINFO_LINK', '35');
define('ENABLE_DOWNLOADS', '');
define('DOWNLOAD_MAX', '3');
define('DOWNLOAD_EXPIRE', '432000');
define('ENABLE_DOWNLOAD_STATUS', 'P');
define('DISABLE_DOWNLOAD_STATUS', 'P');
define('DOWNLOADROOT', '/home/bloomex/public_html/');
define('_SHOW_PRICES', '1');
define('ORDER_MAIL_HTML', '1');
define('HOMEPAGE', 'shop.index');
define('FLYPAGE', 'shop.flypage');
define('CATEGORY_TEMPLATE', 'bloomex');
define('PRODUCTS_PER_ROW', '3');
define('ERRORPAGE', 'shop.error');
define('NO_IMAGE', '/ps_image/noimage.gif');
define('SEARCH_ROWS', '20');
define('SEARCH_COLOR_1', '#f9f9f9');
define('SEARCH_COLOR_2', '#f0f0f0');
define('DEBUG', '');
define('SHOWVERSION', '');
define('PSHOP_ADD_TO_CART_STYLE', 'add-to-cart_blue.gif');
define('TAX_VIRTUAL', '1');
define('TAX_MODE', '0');
define('MULTIPLE_TAXRATES_ENABLE', '1');
define('PAYMENT_DISCOUNT_BEFORE', '1');
define('PSHOP_ALLOW_REVIEWS', '');
define('MUST_AGREE_TO_TOS', '');
define('PSHOP_AGREE_TO_TOS_ONORDER', '');
define('LEAVE_BANK_DATA', '');
define('CAN_SELECT_STATES', '1');
define('SHOW_CHECKOUT_BAR', '');
define('CHECKOUT_STYLE', '1');
define('CHECK_STOCK', '');
define('ENCODE_KEY', 'gtrdddjklllsu755298h');
define('NO_SHIPPING', '');
define('NO_SHIPTO', '');
define('AFFILIATE_ENABLE', '');
define('PSHOP_ALLOW_FRONTENDADMIN_FOR_NOBACKENDERS', '');
define('PSHOP_IMG_RESIZE_ENABLE', '');
define('PSHOP_IMG_WIDTH', '170');
define('PSHOP_IMG_HEIGHT', '200');
define('PSHOP_COUPONS_ENABLE', '1');
define('PSHOP_PDF_BUTTON_ENABLE', '');
define('PSHOP_SHOW_PRODUCTS_IN_CATEGORY', '1');
define('PSHOP_SHOW_TOP_PAGENAV', '1');
define('PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS', '1');

/* OrderByFields */
global $VM_BROWSE_ORDERBY_FIELDS;
$VM_BROWSE_ORDERBY_FIELDS = array( 'product_name','product_price' );

/* Shipping Methods Definition */
global $PSHOP_SHIPPING_MODULES;
$PSHOP_SHIPPING_MODULES[0] = "standard_shipping";
?>