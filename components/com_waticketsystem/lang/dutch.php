<?php
/**
 * FileName: english.php
 * Date: 06/06/2006
 * License: GNU General Public License
 * File Version #: 5
 * WATS Version #: 2.0.0.1
 * Author: James Kennard james@webamoeba.com (www.webamoeba.co.uk)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","New Ticket");
DEFINE("_WATS_NAV_CATEGORY","Support Categories");
DEFINE("_WATS_NAV_TICKET","Ticket Number");

// USER
DEFINE("_WATS_USER","Gebruiker");
DEFINE("_WATS_USER_SET","Gebruikers");
DEFINE("_WATS_USER_NAME","Name");
DEFINE("_WATS_USER_USERNAME","Gebruikersnaam");
DEFINE("_WATS_USER_GROUP","Groep");
DEFINE("_WATS_USER_ORG","Organisatie");
DEFINE("_WATS_USER_ORG_SELECT","Voer organisatie in");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Nieuwe gebruiker aanmaken");
DEFINE("_WATS_USER_NEW_SELECT","kies een Gebruiker");
DEFINE("_WATS_USER_NEW_CREATED","Gebruiker aangemaakt");
DEFINE("_WATS_USER_NEW_FAILED","Deze gebruiker bestaat reeds");
DEFINE("_WATS_USER_DELETED","Gebruiker verwijderd");
DEFINE("_WATS_USER_EDIT","Gebruiker wijzigen");
DEFINE("_WATS_USER_DELETE_REC","Verwijder tickets van gebruiker (aanbevolen)");
DEFINE("_WATS_USER_DELETE_NOTREC","Verwijder tickets en antwoorden van deze gebruiker op andere tickets (niet aanbevolen)");
DEFINE("_WATS_USER_DELETE","Verwijder Gebruiker");
DEFINE("_WATS_USER_ADD","Gebruiker toevoegen");
DEFINE("_WATS_USER_SELECT","Selecteer Gebruiker");
DEFINE("_WATS_USER_SET_DESCRIPTION","Onderhoud Gebruikers");
DEFINE("_WATS_USER_ADD_LIST","De volgende gebruikers zijn toegevoegd");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Kies een groep");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Categorie");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Mijn Open Tickets");
DEFINE("_WATS_TICKETS_USER_CLOSED","My Gesloten Tickets");
DEFINE("_WATS_TICKETS_OPEN","Open Tickets");
DEFINE("_WATS_TICKETS_CLOSED","Gesloten Tickets");
DEFINE("_WATS_TICKETS_DEAD","Dode Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Bekijk alle open tickets");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Bekijk alle gesloten tickets");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Bekijk alle dode tickets");
DEFINE("_WATS_TICKETS_NAME","Ticket Naam");
DEFINE("_WATS_TICKETS_POSTS","Antwoorden");
DEFINE("_WATS_TICKETS_DATETIME","Laatste antwoord");
DEFINE("_WATS_TICKETS_PAGES","Pagina's");
DEFINE("_WATS_TICKETS_SUBMIT","Verstuur nieuwe ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Ticket word verzonden");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket verzonden");
DEFINE("_WATS_TICKETS_DESC","Omschrijving");
DEFINE("_WATS_TICKETS_CLOSE","Ticket sluiten");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket gesloten");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket verwijderd");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purged");
DEFINE("_WATS_TICKETS_NONE","Geen tickets gevonden");
DEFINE("_WATS_TICKETS_FIRSTPOST","Gestart: ");
DEFINE("_WATS_TICKETS_LASTPOST","Verzonden door: ");
DEFINE("_WATS_TICKETS_REPLY","Antwoord");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Antwoord en sluiten");
DEFINE("_WATS_TICKETS_ASSIGN","Ticket toewijzen");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Toegewezen aan");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Heropenen");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Geef een reden om deze ticket te heropenen");
DEFINE("_WATS_TICKETS_STATE_ALL","Alle");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Persoonlijk");
DEFINE("_WATS_TICKETS_STATE_OPEN","Open");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Gesloten");
DEFINE("_WATS_TICKETS_STATE_DEAD","Dood");
DEFINE("_WATS_TICKETS_PURGE","Verwijder alle dode tickets in ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket verzonden door: ");
DEFINE("_WATS_MAIL_REPLY","Antwoord verzonden door: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Mij verwijderen?");
DEFINE("_WATS_MISC_GO","Go");

//ERRORS
DEFINE("_WATS_ERROR","Er is een fout opgetreden");
DEFINE("_WATS_ERROR_ACCESS","U heeft niet genoeg rechten om deze actie uit te voeren");
DEFINE("_WATS_ERROR_NOUSER","U bent niet geautoriseerd om dit te benaderen.<br>U moet inloggen, of toegang vragen bij de beheerder.");
DEFINE("_WATS_ERROR_NODATA","Formulier is niet juist ingevoerd, probeer opnieuw.");
DEFINE("_WATS_ERROR_NOT_FOUND","Niet gevonden");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Gebruik onderstaande 'tags' om uw tekst op te maken:</i></p> 
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
