<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 

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
$missing = mosGetParam( $vars, 'missing' );
$missing_style = "color: Red; font-weight: Bold;";
if (!empty( $missing )) {
    echo "<script type=\"text/javascript\">alert('"._CONTACT_FORM_NC."'); </script>\n";
}


/* Set Dynamic Page Title when applicable */
if(is_callable(array('mosMainFrame', 'setPageTitle'))) {
    $mainframe->setPageTitle( $VM_LANG->_PHPSHOP_REMINDER_TITLE );
}



switch ( $submit ) {

    case "Edit":

        if (empty($recip_name) OR empty($recip_email) OR empty($subject) OR !(ereg(".+@.+\..+", $recip_email))) {
       echo "<script language='javascript' type='text/javascript'>\n";
       echo " alert( \"".$VM_LANG->_PHPSHOP_REMINDER_ERROR."\" );\n";
       echo "</script>\n";
        } else {
       $q = "UPDATE #__{vm}_reminder SET";
       $q .= " user_id='" . $auth["user_id"] . "' ";
       $q .= ", recip_name='" . $recip_name . "' ";
       $q .= ", recip_email='" . $recip_email . "' ";
       $q .= ", recip_day='" . $recip_day  . "' ";
       $q .= ", recip_month='" . $recip_month  . "' ";
       $q .= ", subject='" . $subject . "' ";
       $q .= ", occasion='" . $occasion . "' ";
       $q .= "WHERE reminder_id='" . $reminder_id . "'";
       $db->query($q);
          }
      break;
  
    case "Save":

       if (empty($recip_name) OR empty($recip_email) OR empty($subject) OR $recip_month==0 OR $recip_day==0 OR !(ereg(".+@.+\..+", $recip_email))) {
       echo "<script language='javascript' type='text/javascript'>\n";
       echo " alert( \"".$PHPSHOP_LANG->_PHPSHOP_REMINDER_ERROR."\" );\n";
       echo "</script>\n";
        } else {
       echo "<fieldset><font color=#ff0000>";
       echo $VM_LANG->_PHPSHOP_REMINDER_THANK;
       echo "</font></fieldset>";
       if ($perm->is_registered_customer($auth['user_id'])) { 
       $q = "INSERT INTO #__{vm}_reminder (user_id, recip_name,";
       $q .= "recip_email,recip_day,recip_month,subject,occasion) ";
       $q .= "VALUES (";
       $q .= "'".$auth["user_id"]."','";
       $q .= mosGetParam( $_REQUEST, "recip_name" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_email" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_day" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_month" ). "','";
       $q .= mosGetParam( $_REQUEST, "subject" ). "','";
       $q .= mosGetParam( $_REQUEST, "occasion" ). "')";
       $db->query($q);
       $db->next_record();
       } else {
       $q = "INSERT INTO #__{vm}_reminder (user_id, recip_name,";
       $q .= "recip_email,recip_day,recip_month,subject,occasion) ";
       $q .= "VALUES (";
       $q .= "'','";
       $q .= mosGetParam( $_REQUEST, "recip_name" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_email" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_day" ). "','";
       $q .= mosGetParam( $_REQUEST, "recip_month" ). "','";
       $q .= mosGetParam( $_REQUEST, "subject" ). "','";
       $q .= mosGetParam( $_REQUEST, "occasion" ). "')";
       $db->query($q);
       $db->next_record();
           }
        }
      break;

    case "Delete":

        $q  = "DELETE FROM #__{vm}_reminder ";
        $q .= " WHERE user_id='" . $auth["user_id"] . "'";
        $q .= " AND reminder_id='" . $reminder_id . "'";

      $db->query($q);
     
      break;

  
   }

$evdate = time();

?>
 <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
    <tr>
      <td align="center"><strong><?php echo $PHPSHOP_LANG->_PHPSHOP_REMINDER_TITLE1 ?></strong></td></tr>
      <tr><td align="center"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE2 ?></td></tr>
      <tr><td align="center"><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE3 ?></strong></td></tr>
      <tr><td align="left"><?php echo $VM_LANG->_PHPSHOP_REMINDER_TITLE4 ?></td>
 </tr></table>

<?php 
  //echo "<CENTER><strong>"."You are not a Registered Customer yet"."</strong></CENTER>";


echo "<fieldset>
        <legend><span class=\"sectiontableheader\">".$VM_LANG->_PHPSHOP_REMINDER_NEW."</span></legend>";
?>
<div style="width:90%;">
<!-- Registration form -->

<form action="<?php echo $PHP_SELF; ?>" method="post" name="adminForm">
<?php
$q =  "SELECT * FROM #__users WHERE id='" . $auth["user_id"] . "'";
$db->query($q);
$db->next_record();    
?>
  <table width="100%" border="0" cellspacing="0" cellpadding="2" class="adminform"> 
  <input type="hidden" name="option" value="com_virtuemart" />
  <input type="hidden" name="Itemid" value="<?php echo $Itemid ?>" >
  <input type="hidden" name="user_id" value="<?php echo $my->id ?>" />




  <tr>
  <td width="40%" align="right">
  <strong>
   <?php echo "<label for=\"recip_name\">".$VM_LANG->_PHPSHOP_REMINDER_LIST1_1."</label>*"  ?></strong></td>
   <td width="60%" ><input type="text" id="recip_name" name="recip_name" size="50" value="<?php echo $auth["first_name"]." ".$auth["last_name"];?>" class="inputbox" /></td>
  </tr>

  <tr>
  <td width="40%" align="right" >
    <strong>
    <?php echo "<label for=\"recip_email\">".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</label>*" ?></strong></td>
    <td width="60%" ><input type="text" id="recip_email" name="recip_email" size="40" value="<?php $db->sp("email") ?>" class="inputbox" /></td>
  </tr>

  <tr> 
  <td width="40%" align="right" ><strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST4 ?>*</strong></td>
  <td width="60%" >
    <?php $ps_html->list_month("recip_month", $recip_month) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST3 ?>   <?php $ps_html->list_day("recip_day", $recip_day) ?><?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST5 ?>
  </tr>


  <tr> 
  <td width="40%" align="right" >
  <?php echo "<label for=\"occasion\">".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</label>" ?></td>
   <td width="60%" >
  <?php $ps_html->list_user_occasion("occasion", $occasion) ?>
  </td></tr>


<tr> 
  <td width="40%" align="right" valign="top"><strong>
  <?php echo "<label for=\"subject\">".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</label>" ?>*</strong></td>
  <td width="60%" ><textarea title="<?php echo $VM_LANG->_PHPSHOP_REMINDER_LIST7 ?>" cols="40" rows="4" name="subject" ><?php echo $subject; ?></textarea></td>
  </td></tr>

  <tr> 
  <td colspan="2" align="center">
    <input type="submit" class="button" name="submit" value="<?php echo _E_SAVE ?>" />
    </td></tr></table>
  </form>

<?php
echo "</fieldset>";  




     if ($perm->is_registered_customer($auth['user_id'])) { 
?>

  <!--<strong><?php echo $VM_LANG->_PHPSHOP_REMINDER_ACCOUNT ?></strong>
  <?php  echo $auth["first_name"] . " " . $auth["last_name"] . "<br />";?>-->
  <br />
  <table border="0" cellspacing="0" cellpadding="10" width="100%" align="center">
    <tr>
      <td><strong><?php 
      echo "<img src=\"".IMAGEURL."ps_image/package.png\" align=\"middle\" height=\"32\" width=\"32\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_ACC_ORDER_INFO."\" />&nbsp;&nbsp;&nbsp;";
      echo $VM_LANG->_PHPSHOP_REMINDER_INFO ?></strong>
  
      <br />
      <br />
 <?php


      $ps_reminder->list_reminder("1"); 
     }
?>
   

      </td>
    </tr>
          
</table>
<!-- Body ends here -->
<?php 