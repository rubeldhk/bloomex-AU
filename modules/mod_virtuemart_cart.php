<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
* VirtueMart MiniCart Module
*
* @version $Id: mod_virtuemart_cart.php,v 1.1 2005/09/29 20:02:56 soeren_nb Exp $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2004 Soeren Eberhardt
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/

/* Load the virtuemart main parse code */
require_once( $mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php' );

global $VM_LANG, $sess, $mm_action_url, $my;

$mm_action_url	= ( SECUREURL != "" ? SECUREURL : $mm_action_url );
if( $my->id ) {
	$sCartLink	= $sess->url($mm_action_url."index.php?page=checkout.index&option=com_virtuemart");
}else {
	$sCartLink	= $sess->url($mm_action_url."index.php?page=shop.cart&option=com_virtuemart");
}

?>
<table width="100%" cellspacing="10">
    <tr>
        <td>
        	<?php include (PAGEPATH.'shop.basket_short.php') ?><br/>
            <a href="<?php echo $sCartLink; ?>"><input style="padding:2px 10px 2px 10px;" class="button" type="button" value="<?php echo $VM_LANG->_PHPSHOP_CART_SHOW ?>"/></a>
        </td>
    </tr>
</table>
