<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: admin.country_form.php,v 1.5 2005/01/27 19:34:00 soeren_nb Exp $
* @package mambo-phpShop
* @subpackage HTML
* Contains code from PHPShop(tm):
* 	@copyright (C) 2000 - 2004 Edikon Corporation (www.edikon.com)
*	Community: www.phpshop.org, forums.phpshop.org
* Conversion to Mambo and the rest:
* 	@copyright (C) 2004-2005 Soeren Eberhardt
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* mambo-phpShop is Free Software.
* mambo-phpShop comes with absolute no warranty.
*
* www.mambo-phpshop.net
*/
mm_showMyFileName( __FILE__ );


?>
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
$formObj = new formFactory('Edit reminder');
//Then Start the form
$formObj->startForm();

$reminder_id= mosgetparam( $_REQUEST, 'reminder_id');
$option = empty($option)?mosgetparam( $_REQUEST, 'option', 'com_virtuemart'):$option;

if (!empty($reminder_id)) {
  $q = "SELECT * FROM #__{vm}_reminder WHERE reminder_id='$reminder_id'"; 
  $db->query($q);  
  $db->next_record();
}
?><br />

<table class="adminform">
    <tr> 
      <td><b><?php echo "Edit Reminders" ?></b></td>
      <td>&nbsp;</td>
    </tr>
      <td width="24%" ALIGN="RIGHT"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST1_1 ?></strong></td>
      <td width="76%"> 
        <input type="text" class="inputbox" name="recip_name" value="<?php $db->p("recip_name") ?>">
        <?php if (isset($reminder_id)) { ?>
        <input type="hidden" name="reminder_id" value="<?php echo $reminder_id ?>">
        <?php } ?>
        <input type="hidden" name="func" value="<?php echo "updateReminder";?>">
        <input type="hidden" name="page" value="admin.reminder_list">
        <input type="hidden" name="task" value="">
        <input type="hidden" name="limitstart" value="<?php echo $limitstart ?>" />
        <input type="hidden" name="option" value="com_virtuemart">
      </td>
    </tr>
    <tr> 
      <td width="24%" ALIGN="RIGHT"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST2 ?></strong></td>
      <td width="76%">
         <input type="text" class="inputbox" name="recip_email" value="<?php $db->p("recip_email") ?>">
      </td>
    </tr>
    <tr> 
    <td width="24%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?></strong></td>
    <td width="76%" >
         <?php $ps_html->list_month("recip_month", $db->f("recip_month")) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", $db->f("recip_day")) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
    </tr>
    <tr> 
      <td width="24%" ALIGN="RIGHT"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST6 ?></strong></td>
      <td width="76%">
       <?php $ps_html->list_user_occasion( "occasion", $db->f("occasion") ) ?>
      </td>
    </tr>
    <tr> 
      <td width="24%" ALIGN="RIGHT"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?></strong></td>
      <td width="76%" valign="TOP">
      <?php editorArea( 'editor1', $db->f('subject'), 'subject', '200', '100', '60', '4' ) ?>
      </td>
    </tr>

  </table>


<?php

// Add necessary hidden fields
$formObj->hiddenField( 'reminder_id', $reminder_id );

$funcname = !empty($reminder_id) ? "updateReminder":"" ;

// Write your form with mixed tags and text fields
// and finally close the form:
$formObj->finishForm( $funcname, $modulename.'.reminder_list', $option );
?>

