<?php
/**
 * FileName: english.php
 * Date: 27/05/2006
 * License: GNU General Public License
 * File Version #: 3
 * WATS Version #: 2.0.0.1
 * Author: Thomas Westman info@backupnow.se(www.backupnow.se)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Ny Ticket");
DEFINE("_WATS_NAV_CATEGORY","Kategori Jump");
DEFINE("_WATS_NAV_TICKET","Ticket Jump");

// USER
DEFINE("_WATS_USER","Anv�dare");
DEFINE("_WATS_USER_SET","Anv�dare");
DEFINE("_WATS_USER_NAME","Name");
DEFINE("_WATS_USER_USERNAME","Anv�darnamn");
DEFINE("_WATS_USER_GROUP","Grupp");
DEFINE("_WATS_USER_ORG","Organisation");
DEFINE("_WATS_USER_ORG_SELECT","Enter organisation");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Skapa ny anv�dare");
DEFINE("_WATS_USER_NEW_SELECT","V�lv Anv�dare");
DEFINE("_WATS_USER_NEW_CREATED","Skapad Anv�dare");
DEFINE("_WATS_USER_NEW_FAILED","Den h�r anv�daren har redan ett konto");
DEFINE("_WATS_USER_DELETED","User deleted");
DEFINE("_WATS_USER_EDIT","Edit User");
DEFINE("_WATS_USER_DELETE_REC","Ta bort anv�dare tickets (recommended)");
DEFINE("_WATS_USER_DELETE_NOTREC","Ta bort anv�darens tickets och svar f�r andra tickets (not recommended)");
DEFINE("_WATS_USER_DELETE","Ta bort Anv�dare");
DEFINE("_WATS_USER_ADD","L�gg till anv�dare");
DEFINE("_WATS_USER_SELECT","V�lj anv�dare");
DEFINE("_WATS_USER_SET_DESCRIPTION","Hantera anv�dare");
DEFINE("_WATS_USER_ADD_LIST","F�ljande anv�dare har laggts till");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","V�lj Grupp");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Kategori");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Mina �ppna Tickets");
DEFINE("_WATS_TICKETS_USER_CLOSED","Mina st�ngda Tickets");
DEFINE("_WATS_TICKETS_OPEN","�ppna Tickets");
DEFINE("_WATS_TICKETS_CLOSED","St�ngda Tickets");
DEFINE("_WATS_TICKETS_DEAD","D�da Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Se alla �ppna tickets");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Se alla st�ngda tickets");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Se alla d�da tickets");
DEFINE("_WATS_TICKETS_NAME","Ticket namn");
DEFINE("_WATS_TICKETS_POSTS","Posts");
DEFINE("_WATS_TICKETS_DATETIME","Senaste Post");
DEFINE("_WATS_TICKETS_PAGES","Sidor");
DEFINE("_WATS_TICKETS_SUBMIT","Skicka ny ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Skicka ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket skickades");
DEFINE("_WATS_TICKETS_DESC","Beskrivning");
DEFINE("_WATS_TICKETS_CLOSE","St�ng Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket St�ngd");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket borttagen");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purged");
DEFINE("_WATS_TICKETS_NONE","no tickets hittad");
DEFINE("_WATS_TICKETS_FIRSTPOST","Startad: ");
DEFINE("_WATS_TICKETS_LASTPOST","Skickad av: ");
DEFINE("_WATS_TICKETS_REPLY","Svara");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Svara och st�ng");
DEFINE("_WATS_TICKETS_ASSIGN","Assign ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Assigned till");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","�ppna igen");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Varf�r vill ni �ppna denna  ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Alla");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personliga");
DEFINE("_WATS_TICKETS_STATE_OPEN","�ppen");
DEFINE("_WATS_TICKETS_STATE_CLOSED","St�ngd");
DEFINE("_WATS_TICKETS_STATE_DEAD","D�d");
DEFINE("_WATS_TICKETS_PURGE","Purge d�da tickets i ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket skickad av: ");
DEFINE("_WATS_MAIL_REPLY","Svar kommer i fr�n: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Ta bort mej?");
DEFINE("_WATS_MISC_GO","K�r");

//ERRORS
DEFINE("_WATS_ERROR","Ett fel har uppst�tt");
DEFINE("_WATS_ERROR_ACCESS","Du verkar inte ha dom r�ttigheter som beh�vs f�r detta");
DEFINE("_WATS_ERROR_NOUSER","Du har inte r�ttighetaer att l�sa detta.<br>Diu m�ste logga in eller be om r�ttighetr fr�n administratorn.");
DEFINE("_WATS_ERROR_NODATA","Du har inte fyllt i detta r�tt, G�r ett nytt f�rs�k.");
DEFINE("_WATS_ERROR_NOT_FOUND","Item not found");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Anv�nd dessa'tagar' som visas nedan f�r att redigera din text:</i></p> 
<table width='100%'border='0'cellspacing='5'cellpadding='0'> 
  <tr valign='top'> 
    <td><b>bold</b></td> 
    <td><b>[b]</b>bold<b>[/b]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><i>italic</i> </td> 
    <td><b>[i]</b>italic<b>[/i]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td> <u>underline</u></td> 
    <td><b>[u]</b>underline<b>[/u]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td>code</td> 
    <td><b>[code]</b>value='123';<b>[/code] </b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font size='+2'>SIZE</font></td> 
    <td><b>[size=25]</b>HUGE<b>[/size]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font color='#FF0000'>RED</font></td> 
    <td><b>[color=red]</b>RED<b> [/color]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td style='cursor: pointer; color: #0000FF;'><u>weblink </u></td> 
    <td><b>[url=http://webamoeba.co.uk]webamoeba[/url]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td style='cursor: pointer; color: #0000FF;'><u>fred@bloggs.com</u></td> 
    <td><b>[email=bbcode@webamoeba.co.uk]mail[/email]</b></td> 
  </tr> 
</table> ");
?>
