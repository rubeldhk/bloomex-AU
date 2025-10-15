<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: order.order_status_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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
mm_showMyFileName( __FILE__ );

$order_mark_id = $db->getEscaped(mosGetParam( $_REQUEST, 'order_mark_id' ));
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

//First create the object and let it print a form heading
$formObj = new formFactory( $VM_LANG->_PHPSHOP_ORDER_PRINT_PO_MARK );
//Then Start the form
$formObj->startForm();
if (!empty($order_mark_id)) {
  $q = "SELECT * FROM #__{vm}_order_mark WHERE order_mark_id='$order_mark_id'";
  $db->query($q);
  $db->next_record();
}  
?><br />
<table class="adminform">
    <tr> 
      <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_MARK_FORM_LBL ?></strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_ORDER_MARK_LIST_CODE ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="order_mark_code" value="<?php $db->sp("order_mark_code") ?>" size="2" maxlength="3" />
      </td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_ORDER_MARK_LIST_NAME ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="order_mark_name" value="<?php $db->sp("order_mark_name") ?>" size="16" />
      </td>
    </tr>
    <tr align="center">
      <td colspan="2">&nbsp;</td>
    </tr>    
</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'order_mark_id', $order_mark_id );

$funcname = !empty($order_mark_id) ? "ordermarkupdate" : "ordermarkadd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.order_mark_list', $option );
?>