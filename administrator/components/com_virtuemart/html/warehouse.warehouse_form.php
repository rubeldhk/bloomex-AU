<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
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
mm_showMyFileName(__FILE__);

//First create the object and let it print a form heading
$formObj = new formFactory($VM_LANG->_PHPSHOP_WAREHOUSE_FORM_LBL);
//Then Start the form
$formObj->startForm();

$warehouse_id = mosgetparam($_REQUEST, 'warehouse_id');
$option = empty($option) ? mosgetparam($_REQUEST, 'option', 'com_virtuemart') : $option;

if (!empty($warehouse_id)) {
    $q = "SELECT w.*,wi.lat,wi.lng FROM jos_vm_warehouse w
         LEFT JOIN jos_vm_warehouse_info  wi on wi.warehouse_id=w.warehouse_id 
         WHERE w.warehouse_id='$warehouse_id'";
    $db->query($q);
    $db->next_record();
}
?><br />

<table class="adminform">
    <tr> 
        <td><b><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_FORM_LBL ?></b></td>
        <td>&nbsp;</td>
    </tr>
    <tr> 
        <td align="right" ><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_LIST_LBL ?>:</td>
        <td>
            <input type="text" class="inputbox" name="list_warehouse" value="<?php $db->sp("list_warehouse") ?>" size="5" />
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_WAREHOUSE_LIST_NAME_AMOUNT_TIP); ?>
        </td>
    </tr>
    <tr align="center">
        <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
        <td align="right" ><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_LIST_NAME ?>:</td>

        <td> 
            <input type="text" class="inputbox" name="warehouse_name" value="<?php $db->sp("warehouse_name") ?>" size="16" />
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_WAREHOUSE_LIST_NAME_AMOUNT_TIP); ?>
        </td>
    </tr>
    <tr align="center">
        <td colspan="2" >&nbsp;</td>
    </tr>
    <tr> 
        <td align="right" ><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_LIST_CODE ?>:</td>
        <td> 
            <input type="text" class="inputbox" name="warehouse_code" value="<?php $db->sp("warehouse_code") ?>" size="5" />
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_WAREHOUSE_LIST_CODE_AMOUNT_TIP); ?>
        </td>
    </tr>
    <tr align="center">
        <td colspan="2" >&nbsp;</td>
    </tr>
    <tr>
        <td align="right" ><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_LIST_TIMEZONE ?>:</td>
        <td>
            <input type="text" class="inputbox" name="timezone" value="<?php $db->sp("timezone") ?>"  size="25" />
        </td>
    </tr>
    <tr> 
        <td align="right" ><?php echo $VM_LANG->_PHPSHOP_WAREHOUSE_LIST_EMAIL ?>:</td>

        <td> 
            <input type="text" class="inputbox" name="warehouse_email" value="<?php $db->sp("warehouse_email") ?>" size="25" />
            <?php echo mm_ToolTip($VM_LANG->_PHPSHOP_WAREHOUSE_LIST_EMAIL_AMOUNT_TIP); ?>
        </td>
    </tr>
    <tr>
        <td align="right" >Person Name:</td>

        <td>
            <input type="text" class="inputbox" name="person_name" value="<?php $db->sp("person_name") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >Company name:</td>

        <td>
            <input type="text" class="inputbox" name="company_name" value="<?php $db->sp("company_name") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >Street Number:</td>

        <td>
            <input type="text" class="inputbox" name="street_number" value="<?php $db->sp("street_number") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >Street Name:</td>

        <td>
            <input type="text" class="inputbox" name="street_name" value="<?php $db->sp("street_name") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >District:</td>

        <td>
            <input type="text" class="inputbox" name="district" value="<?php $db->sp("district") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >Postal Code:</td>

        <td>
            <input type="text" class="inputbox" name="postal_code" value="<?php $db->sp("postal_code") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right" >City:</td>

        <td>
            <input type="text" class="inputbox" name="city" value="<?php $db->sp("city") ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right">State:</td>
        <td>
            <input type="text" class="inputbox" name="state" value="<?php echo $db->sp("state"); ?>" size="2" />
        </td>
    </tr>
    <tr>
        <td align="right">Phone:</td>
        <td>
            <input type="text" class="inputbox" name="phone" value="<?php echo $db->sp("phone"); ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right">Lat(auto):</td>
        <td>
            <input type="text" class="inputbox" name="lat" value="<?php echo $db->sp("lat"); ?>" size="25" />
        </td>
    </tr>
    <tr>
        <td align="right">Lng(auto):</td>
        <td>
            <input type="text" class="inputbox" name="lng" value="<?php echo $db->sp("lng"); ?>" size="25" />
        </td>
    </tr>

    <tr>
        <td align="right" >Published:</td>

        <td>
            <select name="published">
                <option value="1" <?php echo $db->f("published") ? " selected " : "" ?>>Yes</option>
                <option value="0" <?php echo $db->f("published") ? "" : " selected " ?>>No</option>
            </select>
        </td>
    </tr>
    <tr align="center">
        <td colspan="2" >&nbsp;</td>
    </tr>

</table>
<?php
// Add necessary hidden fields
$formObj->hiddenField('warehouse_id', $warehouse_id);

$funcname = !empty($warehouse_id) ? "updatewarehouse" : "addwarehouse";

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm($funcname, $modulename . '.warehouse_list', $option);
?>