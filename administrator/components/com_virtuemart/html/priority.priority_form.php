<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: tax.tax_form.php,v 1.5 2005/09/30 10:14:30 codename-matrix Exp $
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

//First create the object and let it print a form heading
$formObj = new formFactory( $VM_LANG->_PHPSHOP_PRIORITY_FORM_LBL );
//Then Start the form
$formObj->startForm();

$priority_id= mosgetparam( $_REQUEST, 'priority_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if (!empty($priority_id)) {
  $q = "SELECT * FROM #__{vm}_priority WHERE priority_id='$priority_id'"; 
  $db->query($q);  
  $db->next_record();
}
?><br />

<table class="adminform">
    <tr> 
      <td><b><?php echo $VM_LANG->_PHPSHOP_PRIORITY_FORM_LBL ?></b></td>
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_PRIORITY_LIST_LBL ?>:</td>
      <td>
        <input type="text" class="inputbox" name="list_priority" value="<?php $db->sp("list_priority") ?>" size="5" />
         <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRIORITY_LIST_NAME_AMOUNT_TIP ); ?>
       </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_PRIORITY_LIST_NAME ?>:</td>

      <td> 
        <input type="text" class="inputbox" name="priority_name" value="<?php $db->sp("priority_name") ?>" size="16" />
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRIORITY_LIST_NAME_AMOUNT_TIP ); ?>
      </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
      <td align="right" ><?php echo $VM_LANG->_PHPSHOP_PRIORITY_LIST_CODE ?>:</td>
      <td> 
        <input type="text" class="inputbox" name="priority_code" value="<?php $db->sp("priority_code") ?>" size="5" />
        <?php echo mm_ToolTip( $VM_LANG->_PHPSHOP_PRIORITY_LIST_CODE_AMOUNT_TIP ); ?>
      </td>
    </tr>
    <tr align="center">
      <td colspan="2" >&nbsp;</td>
    </tr>
</table>
<?php

// Add necessary hidden fields
$formObj->hiddenField( 'priority_id', $priority_id );

$funcname = !empty($priority_id) ? "updatePriority" : "addPriority";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.priority_list', $option );
?>