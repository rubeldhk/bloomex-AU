<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
* @version $Id: ps_order.php,v 1.25 2005/06/23 18:59:15 soeren_nb Exp $
* @package mambo-phpShop
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


/****************************************************************************
*
* CLASS DESCRIPTION
*                   
* ps_order
*
* The class handles orders from an adminstrative perspective.  Order
* processing is handled in the ps_process_order.
* 
*************************************************************************/
class ps_reminder {
  var $classname = "ps_reminder";
  var $error;
  
  

   /**************************************************************************
   * name: notify_customer
   * created by: Marina Bilenko
   * returns:
   **************************************************************************/
  function notify_customer(&$d){
   global $mosConfig_live_site, $mosConfig_absolute_path, 
     $VM_LANG, $mosConfig_smtpauth, $mosConfig_mailer,
     $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtphost;

    $timestamp = time();
    $db = new ps_DB;
    $dbv = new ps_DB;
    $dbr = new ps_DB;
    $q = "SELECT vendor_name,contact_email FROM #__pshop_vendor ";
    $q .= "WHERE vendor_id='".$_SESSION['ps_vendor_id']."'";
    $dbv->query($q);
    $dbv->next_record();
     
    require_once( CLASSPATH . 'phpmailer/class.phpmailer.php');
    $mail = new mShop_PHPMailer();
    $mail->PluginDir = CLASSPATH . 'phpmailer/';
    $mail->SetLanguage("en", CLASSPATH . 'phpmailer/language/');
    $mail->From =  $dbv->f("contact_email");
    $mail->FromName = $dbv->f("vendor_name");
    $mail->AddReplyTo($dbv->f("contact_email"), $dbv->f("vendor_name"));
    $mail->ContentType = "text/html";
    $mail->Encoding = "base64";


      /* 
    TEST IF WE ARE RUNNING MAMBO 4.5 1.0.9
    */
    if( defined( '_RELEASE' ) )
      if( _RELEASE == '4.5' ) {
        $mosConfig_mailer = CFG_MAILER;
        $mosConfig_smtphost = CFG_SMTPHOST;
        $mosConfig_smtpauth = CFG_SMTPAUTH;
        $mosConfig_smtpuser = CFG_SMTPUSER;
        $mosConfig_smtppass = CFG_SMTPPASS;
      }

    $q = "SELECT recip_name, recip_email, recip_day,recip_month, subject, occasion  FROM #__{vm}_reminder ";
    $q .= "WHERE reminder_id = '".$d["reminder_id"]."' ";
    $db->query($q);
    $db->next_record();

    $dbr = new ps_DB;
    $q = "SELECT * FROM `mos_content` WHERE title_alias = 'Reminder' ORDER BY `title_alias` DESC LIMIT 0,1";
    $dbr->query($q);
    $dbr->next_record();
 
    $HTML="<BR>".$dbr->f("introtext");

    $HTML = preg_replace("/Customer name/",$db->f("recip_name"),$HTML); 
   
    /* MAIL BODY */
        $message="<html><body>Recipient:  <b>" .$db->f("recip_name"). "</b>         Occasion :<b>".$db->f("occasion")."</b><BR>";
        $message.="Occasion Date:    <b>".$db->f("recip_day")."/".$db->f("recip_month")."</b>             Notes :<b>".$db->f("subject")."</b>";  
        $message.=$HTML."</body></html>";


    $mail->Body = html_entity_decode($message);
    $mail->Subject = html_entity_decode($VM_LANG->_PHPSHOP_REMINDER_TITLE1);


    switch( $mosConfig_mailer ) {
  
      case "mail":  
          $mail->IsMail();
          break;
                          
      /*** tell the mailer objects to use SMTP ***/
      case "smtp":  
          $mail->IsSMTP();
          $mail->Host = $mosConfig_smtphost;
          $mail->SMTPAuth = $mosConfig_smtpauth=='1' ? true : false;
  
          if ($mosConfig_smtpauth=='1') {
              $mail->Username = $mosConfig_smtpuser;
              $mail->Password = $mosConfig_smtppass;     
          }
          break;
                          
      case "sendmail":  
          $mail->IsSendmail();
          break;
                          
      default:        
          $mail->IsMail();
          break;
    }
    $mail->AddAddress($db->f("recip_email"));
       /* Send the email */

   if ($mail->Send()) {
       
          $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_DOWNLOADS_SEND_MSG. " ". $db->f("first_name") . " " . $db->f("last_name") . " ".$db->f("user_email");

    $q = "UPDATE #__{vm}_reminder SET";
    $q .= "  sdate='" . $timestamp . "' ";
    $q .= "WHERE reminder_id='" . $d["reminder_id"] . "'";
     $db->query($q);

       }

     else {
     
          $_REQUEST['mosmsg'] = $VM_LANG->_PHPSHOP_DOWNLOADS_ERR_SEND." ". $db->f("first_name") . " " . $db->f("last_name") . " ".$db->f("user_email")." (". $mail->ErrorInfo.")";
       }

      return True;
  }

   /**************************************************************************
   * name: list_reminder
   * created by: maryna
   * description: shows a listbox of reminders which can be used in a form
   * @param string order_status (A = All)
   * @param int secure (0 = Show orders of all users, 1 = Show only orders of the user)
   * returns:
   **************************************************************************/
  function list_reminder($secure=0) {
    global $VM_LANG, $CURRENCY_DISPLAY, $sess;
    
    $auth = $_SESSION['auth'];
        
    $db = new ps_DB;
    $dbs = new ps_DB;
    $i = 0;

    $q = "SELECT * FROM #__{vm}_reminder ";
    $q .= "WHERE ";
    if ($secure) {
      $q .= "user_id='" . $auth["user_id"] . "' "; 
    }
    $q .= "ORDER BY recip_day,recip_month DESC";


    $db->query($q);
    if( $db->num_rows() ) {
      echo "<table width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\">\n";
      
      while ($db->next_record()) {
        if ($i++ % 2) 
           $bgcolor=SEARCH_COLOR_1;
        else
           $bgcolor=SEARCH_COLOR_2;
           
        echo "<tr style=\"background-color:$bgcolor;\"';\">\n<td width=\"25%\" valign=\"top\">";
         echo "<strong>".$VM_LANG->_PHPSHOP_REMINDER_LIST1."</strong>";
         echo " ". $db->f("recip_name");
         echo "</br><strong>".$VM_LANG->_PHPSHOP_REMINDER_LIST2."</strong>";
         echo " ".$db->f("recip_email")."</td>";

        echo "<td width=\"25%\" valign=\"top\">";
        echo "<strong>".$VM_LANG->_PHPSHOP_REMINDER_LIST4."</strong>";
        echo " ". $db->f("recip_day") ."/". $db->f("recip_month");
        echo "<br />";
        echo "<strong>".$VM_LANG->_PHPSHOP_REMINDER_LIST6."</strong> " . $db->f("occasion");
        echo "</td>\n";
        echo "<td width=\"30%\" valign=\"top\">";
        echo "<strong>".$VM_LANG->_PHPSHOP_REMINDER_LIST7."</strong> " . $db->f("subject");
        echo "</td><td width=\"20%\" valign=\"top\">";
        echo "<a href=\"index.php?option=com_virtuemart&page=reminder.reminder_details&reminder_id=".$db->f("reminder_id")."&Itemid=".@$_REQUEST['Itemid']."\">".$VM_LANG->_PHPSHOP_UPDATE."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<a href=\"index.php?option=com_virtuemart&page=reminder.index&reminder_id=".$db->f("reminder_id")."&Itemid=".@$_REQUEST['Itemid']."&submit=Delete\">".$VM_LANG->_PHPSHOP_DELETE."</a>";
        echo "</td>\n</tr>";
      }
      if (!$i) {
        echo "<span style=\"font-style:italic;\">".$VM_LANG->_PHPSHOP_REMINDER_NO."</span>\n";
      }
      echo "</table>\n";
    }    
  }
   
  /********************************************************************
  ** name: validate_delete()
  ** created by: gday
  ** description:  Validate form values prior to delete
  ** parameters: $d
  ** returns:  True - validation passed
  **          False - validation failed
  ********************************************************************/
  function validate_delete($d) {
    
    $db = new ps_DB;
    
    if(!$d["reminder_id"]) {
       $this->error = "Unable to delete without the reminder id .";
       return False;
    }
    return True;
  }

 /***********************************************************************
  ** name: delete()
  ** created by: gday
  ** description:  Delete the order in the database
  ** parameters: $d
  ** returns:  True - delete succeeded
  **          False - delete failed
  **********************************************************************/
  function delete(&$d) {
    $db = new ps_DB;
  
    if ($this->validate_delete($d)) {
      $q = "DELETE from #__{vm}_reminder where reminder_id=" . $d["reminder_id"];
      $db->query($q);
      $db->next_record();

      return True;
    }
    else {
      return False;
    }
  }
 /**************************************************************************
   * name: update()
   * created by: marina
   * description: updates country information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB;

    $q = "UPDATE #__{vm}_reminder SET";
    $q .= " recip_name='".$d["recip_name"]."' ";
    $q .= ", recip_email='".$d["recip_email"]."' ";
    $q .= ", recip_day='".$d["recip_day"]."' ";
    $q .= ", recip_month='".$d["recip_month"]."' ";
    $q .= ", subject='".$d["subject"]."' ";
    $q .= ", occasion='".$d["occasion"]."' ";
    $q .= "WHERE reminder_id='" . $d["reminder_id"] . "'";
    $db->setQuery($q);
    $db->query();
    $db->next_record();
    return True;
  }
  
  
}

?>
