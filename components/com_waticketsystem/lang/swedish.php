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
DEFINE("_WATS_USER","Anvädare");
DEFINE("_WATS_USER_SET","Anvädare");
DEFINE("_WATS_USER_NAME","Name");
DEFINE("_WATS_USER_USERNAME","Anvädarnamn");
DEFINE("_WATS_USER_GROUP","Grupp");
DEFINE("_WATS_USER_ORG","Organisation");
DEFINE("_WATS_USER_ORG_SELECT","Enter organisation");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Skapa ny anvädare");
DEFINE("_WATS_USER_NEW_SELECT","Välv Anvädare");
DEFINE("_WATS_USER_NEW_CREATED","Skapad Anvädare");
DEFINE("_WATS_USER_NEW_FAILED","Den här anvädaren har redan ett konto");
DEFINE("_WATS_USER_DELETED","User deleted");
DEFINE("_WATS_USER_EDIT","Edit User");
DEFINE("_WATS_USER_DELETE_REC","Ta bort anvädare tickets (recommended)");
DEFINE("_WATS_USER_DELETE_NOTREC","Ta bort anvädarens tickets och svar för andra tickets (not recommended)");
DEFINE("_WATS_USER_DELETE","Ta bort Anvädare");
DEFINE("_WATS_USER_ADD","Lägg till anvädare");
DEFINE("_WATS_USER_SELECT","Välj anvädare");
DEFINE("_WATS_USER_SET_DESCRIPTION","Hantera anvädare");
DEFINE("_WATS_USER_ADD_LIST","Följande anvädare har laggts till");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Välj Grupp");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Kategori");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Mina öppna Tickets");
DEFINE("_WATS_TICKETS_USER_CLOSED","Mina stängda Tickets");
DEFINE("_WATS_TICKETS_OPEN","Öppna Tickets");
DEFINE("_WATS_TICKETS_CLOSED","Stängda Tickets");
DEFINE("_WATS_TICKETS_DEAD","Döda Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Se alla öppna tickets");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Se alla stängda tickets");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Se alla döda tickets");
DEFINE("_WATS_TICKETS_NAME","Ticket namn");
DEFINE("_WATS_TICKETS_POSTS","Posts");
DEFINE("_WATS_TICKETS_DATETIME","Senaste Post");
DEFINE("_WATS_TICKETS_PAGES","Sidor");
DEFINE("_WATS_TICKETS_SUBMIT","Skicka ny ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Skicka ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket skickades");
DEFINE("_WATS_TICKETS_DESC","Beskrivning");
DEFINE("_WATS_TICKETS_CLOSE","Stäng Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket Stängd");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket borttagen");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purged");
DEFINE("_WATS_TICKETS_NONE","no tickets hittad");
DEFINE("_WATS_TICKETS_FIRSTPOST","Startad: ");
DEFINE("_WATS_TICKETS_LASTPOST","Skickad av: ");
DEFINE("_WATS_TICKETS_REPLY","Svara");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Svara och stäng");
DEFINE("_WATS_TICKETS_ASSIGN","Assign ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Assigned till");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Öppna igen");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Varför vill ni öppna denna  ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Alla");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personliga");
DEFINE("_WATS_TICKETS_STATE_OPEN","Öppen");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Stängd");
DEFINE("_WATS_TICKETS_STATE_DEAD","Död");
DEFINE("_WATS_TICKETS_PURGE","Purge döda tickets i ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket skickad av: ");
DEFINE("_WATS_MAIL_REPLY","Svar kommer i från: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Ta bort mej?");
DEFINE("_WATS_MISC_GO","Kör");

//ERRORS
DEFINE("_WATS_ERROR","Ett fel har uppstått");
DEFINE("_WATS_ERROR_ACCESS","Du verkar inte ha dom rättigheter som behövs för detta");
DEFINE("_WATS_ERROR_NOUSER","Du har inte rättighetaer att läsa detta.<br>Diu måste logga in eller be om rättighetr från administratorn.");
DEFINE("_WATS_ERROR_NODATA","Du har inte fyllt i detta rätt, Gör ett nytt försök.");
DEFINE("_WATS_ERROR_NOT_FOUND","Item not found");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Använd dessa'tagar' som visas nedan för att redigera din text:</i></p> 
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
