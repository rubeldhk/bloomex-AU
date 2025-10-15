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

$order_occasion_id = mosGetParam( $_REQUEST, 'order_occasion_id' );
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

//First create the object and let it print a form heading
$formObj = new formFactory( $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_LBL );
//Then Start the form
$formObj->startForm();

if (!empty($order_occasion_id)) {
  $q = "SELECT * FROM #__{vm}_order_occasion WHERE order_occasion_id='$order_occasion_id'"; 
  $db->query($q);  
  $db->next_record();
}  
?><br />
<table class="adminform">
    <tr> 
      <td><strong><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_LBL ?></strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_CODE ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="order_occasion_code" value="<?php $db->sp("order_occasion_code") ?>" size="5" maxlength="5" />
      </td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_NAME ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="order_occasion_name" value="<?php $db->sp("order_occasion_name") ?>" size="25" />
      </td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_LIST_ORDER ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="list_order" value="<?php $db->sp("list_order") ?>" size="3" maxlength="3" />
      </td>
    </tr>
     <tr> 
      <td width="22%" align="right"  valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_PUBLISHED ?>:</td>
      <td  > 
          <input type="checkbox"  name="published" onclick="if(this.checked==true){this.value=1;}else {this.value=0;}" <?php if($db->f("published")==1) {echo "checked";} ?> value="<?php if($db->f("published")==1) {echo "1";}else{echo "0";} ?>"  />
      </td>
    </tr>

    <tr> 
      <td width="22%" align="right"  valign="top"><?php echo $VM_LANG->_PHPSHOP_ORDER_OCCATION_FORM_DESCRIPTION ?>:</td>
      <td width="78%" ><?php 
	  	  editorArea( 'editor1', $db->f("order_occasion_desc"), 'order_occasion_desc', '400', '200', '70', '15' )
?>
      </td>
    </tr>

    <tr align="center">
      <td colspan="2">&nbsp;</td>
    </tr>    
</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField( 'order_occasion_id', $order_occasion_id );

$funcname = !empty($order_occasion_id) ? "orderOccasionUpdate" : "orderOccasionAdd";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.order_occasion_list', $option );
?>