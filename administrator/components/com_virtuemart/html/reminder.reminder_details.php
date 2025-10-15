<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: account.index.php,v 1.8 2005/06/23 18:59:16 soeren_nb Exp $
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

require_once(CLASSPATH.'ps_reminder.php');
$ps_reminder = new ps_reminder;

$Itemid = mosgetparam( $_REQUEST, 'Itemid', null);
$submit = mosGetParam( $_REQUEST, "submit" );

$recip_name = mosGetParam( $_REQUEST, "recip_name" );
$recip_email = mosGetParam( $_REQUEST, "recip_email" );
$recip_month = mosGetParam( $_REQUEST, "recip_month" );
$recip_day = mosGetParam( $_REQUEST, "recip_day" );

$subject = mosGetParam( $_REQUEST, "subject" );
$occasion = mosGetParam( $_REQUEST, "occasion");

$reminder_id=mosGetParam( $_REQUEST, "reminder_id" );
//$evdate = time();

/* Set Dynamic Page Title when applicable */
if(is_callable(array('mosMainFrame', 'setPageTitle'))) {
    $mainframe->setPageTitle( $VM_LANG->_PHPSHOP_REMINDER_TITLE );
}

?>

<a href="<?php $sess->purl(URL.'index.php?page=reminder.index&option=com_virtuemart&Itemid='.$Itemid ); ?>">
<img src="<?php echo IMAGEURL ?>ps_image/undo.png" alt="Back"  height="32" width="32" border="0" align="left" />
</a><br/><br/><br/>

<?php 
echo "<fieldset>
        <legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_REMINDER_EDIT."</span></legend>";

?>
<div style="width:90%;">
<!-- Registration form -->


<form action="<?php echo $mm_action_url."index.php?page=reminder.index&option=com_virtuemart&Itemid=".$Itemid ?>" method="post" name="adminForm">
<?php
$q =  "SELECT * FROM #__{vm}_reminder WHERE user_id='" . $auth["user_id"] . "' AND reminder_id='".$reminder_id."' ";

$db->query($q);
$db->next_record();    
?>

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform"> 
  <input type="hidden" name="option" value="com_virtuemart" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" >
  <input type="hidden" name="user_id" value="<?php echo $my->id ?>" />
  <input type="hidden" name="reminder_id" value="<?php echo $reminder_id ?>" />
 
  
 <tr>
  <td width="40%" align="right">
  <strong>
   <?php echo "<label for=\"recip_name\">".$VM_LANG->_PHPSHOP_REMINDER_LIST1_1."</label>*"  ?></strong></td>
   <td width="60%" ><input type="text" id="recip_name" name="recip_name" size="50" class="inputbox" value="<?php $db->p("recip_name") ?>"/></td>
  </tr>

  <tr>
  <td width="40%" align="right" >
    <strong>
    <?php echo "<label for=\"recip_email\">".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</label>*" ?></strong></td>
    <td width="60%" ><input type="text" id="recip_email" name="recip_email" size="40" class="inputbox" value="<?php $db->p("recip_email") ?>"/></td>
  </tr>

  <tr> 
  <td width="40%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?>*</strong></td>
  <td width="60%" >
    <?php $ps_html->list_month("recip_month", $db->f("recip_month")) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", $db->f("recip_day")) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
  </tr>


  <tr> 
  <td width="40%" align="right" >
  <?php echo "<label for=\"occasion\">".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</label>" ?></td>
   <td width="60%" >
  <?php $ps_html->list_user_occasion("occasion", $db->f("occasion")) ?>
  </td></tr>


<tr> 
  <td width="40%" align="right" valign="top"><strong>
  <?php echo "<label for=\"subject\">".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</label>" ?>*</strong></td>
  <td width="60%" ><textarea title="<?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?>" cols="40" rows="4" name="subject" ><?php $db->sp("subject"); ?></textarea></td>
  </td></tr>


  <tr> 
  <td colspan="2" align="center">
    <input type="submit" class="button" name="submit" value="<?php echo _E_EDIT ?>" />
    </td></tr></table>
  </form>

<?php
echo "</fieldset>";  

 ?>



