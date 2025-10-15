<?php
/**
 * FileName: germanf.php
 * Date: 24/05/2006
 * License: GNU General Public License
 * File Version #: 2
 * WATS Version #: 2.0.0
 * Author: Chr.Gärtner
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Neues Ticket");
DEFINE("_WATS_NAV_CATEGORY","Support Kategorie");
DEFINE("_WATS_NAV_TICKET","Ticket Nummer");

// USER
DEFINE("_WATS_USER","Berater");
DEFINE("_WATS_USER_SET","Berater");
DEFINE("_WATS_USER_NAME","Name");
DEFINE("_WATS_USER_USERNAME","Beratername");
DEFINE("_WATS_USER_GROUP","Gruppe");
DEFINE("_WATS_USER_ORG","Organisation");
DEFINE("_WATS_USER_ORG_SELECT","Eingabe Organisation");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Erfassen eines neuen Berater");
DEFINE("_WATS_USER_NEW_SELECT","Wählen Sie einen neuen Berater");
DEFINE("_WATS_USER_NEW_CREATED","Erfassen eines Beraters");
DEFINE("_WATS_USER_NEW_FAILED","Dieser Berater hat bereits einen Ticker Account");
DEFINE("_WATS_USER_DELETED","Berater gelöscht");
DEFINE("_WATS_USER_EDIT","Bearbeite Berater");
DEFINE("_WATS_USER_DELETE_REC","Entferne Beratertickets (empfohlen)");
DEFINE("_WATS_USER_DELETE_NOTREC","Entferne Beraterticket und Antworten auf andere Tickets (nicht empfohlen)");
DEFINE("_WATS_USER_DELETE","Lösche Berater");
DEFINE("_WATS_USER_ADD","Füge Berater hinzu");
DEFINE("_WATS_USER_SELECT","Wähle Berater");
DEFINE("_WATS_USER_SET_DESCRIPTION","Verwalte Berater");
DEFINE("_WATS_USER_ADD_LIST","Die folgenden Berater wurden hinzugefügt");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Wähle Gruppe");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Kategorie");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Meine offenen Tickets");
DEFINE("_WATS_TICKETS_USER_CLOSED","Meine geschlossenen Tickets");
DEFINE("_WATS_TICKETS_OPEN","Offene Tickets");
DEFINE("_WATS_TICKETS_CLOSED","Geschlossene Tickets");
DEFINE("_WATS_TICKETS_DEAD","Verlorene Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Zeige alle offenen Tickets");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Zeige alle geschlossenen Tickets");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Zeige alle verlorenen Tickets");
DEFINE("_WATS_TICKETS_NAME","Ticket Name");
DEFINE("_WATS_TICKETS_POSTS","Anfragen");
DEFINE("_WATS_TICKETS_DATETIME","Letzte Anfragen");
DEFINE("_WATS_TICKETS_PAGES","Seiten");
DEFINE("_WATS_TICKETS_SUBMIT","Erstelle ein neues Ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Erstelle Ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticketerstellung erfolgreich");
DEFINE("_WATS_TICKETS_DESC","Beschreibung");
DEFINE("_WATS_TICKETS_CLOSE","Schließe Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket geschlossen");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket gelöscht");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket bereinigt");
DEFINE("_WATS_TICKETS_NONE","keine Tickets gefunden");
DEFINE("_WATS_TICKETS_FIRSTPOST","Gestartet: ");
DEFINE("_WATS_TICKETS_LASTPOST","Versendet von: ");
DEFINE("_WATS_TICKETS_REPLY","Antwort");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Antworten und schließen");
DEFINE("_WATS_TICKETS_ASSIGN","Ticket anweisen");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","zugewiesen an");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Wieder eröffnen");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Ursache, warum das Ticket wieder eröffnet werden soll");
DEFINE("_WATS_TICKETS_STATE_ALL","Alle");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Eigene");
DEFINE("_WATS_TICKETS_STATE_OPEN","Offen");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Geschlossen");
DEFINE("_WATS_TICKETS_STATE_DEAD","Gelöscht");
DEFINE("_WATS_TICKETS_PURGE","Bereinigen von geschlossenen Tickets ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket eröffnet von: ");
DEFINE("_WATS_MAIL_REPLY","Antwort geschrieben von: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Löschen?");
DEFINE("_WATS_MISC_GO","Start");

//ERRORS
DEFINE("_WATS_ERROR","Ein Fehler ist aufgetreten");
DEFINE("_WATS_ERROR_ACCESS","Sie haben keine ausreichenden Rechte, um diese Aufgabe auszuführen");
DEFINE("_WATS_ERROR_NOUSER","Sie sind nicht berechtigt, diese Information zu sehen.<br>Sie müssen sich einloggen oder Zugriff beim Administrator beantragen.");
DEFINE("_WATS_ERROR_NODATA","Sie haben das Formular nicht vollständig ausgefüllt, bitte ergänzen");
DEFINE("_WATS_ERROR_NOT_FOUND","Information nicht gefunden");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Benutzen Sie die 'tags' um Ihren Text zu formatieren:</i></p> 
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
    <td><b>[code]</b>value='123';<b>[/code]
</b></td> 
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
    <td style='cursor: pointer; color: #0000FF;'><u>Link </u></td> 
    <td><b>[url=http://www.armed-assault.eu]ArmedAssault.eu[/url]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td style='cursor: pointer; color: #0000FF;'><u>fred@bloggs.com</u></td> 
    <td><b>[email=info@armed-assault.eu]Email[/email]</b></td> 
  </tr> 
</table> ");
?>