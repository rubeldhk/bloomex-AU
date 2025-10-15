<?php
/**
 * FileName: italian.php
 * Date: 06/06/2006
 * License: GNU General Public License
 * File Version #: 5
 * WATS Version #: 2.0.0.1
 * Author: Leonardo Lombardi (www.dimsat.unicas.it)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Nuovo Ticket");
DEFINE("_WATS_NAV_CATEGORY","Supporto Categorie");
DEFINE("_WATS_NAV_TICKET","Numero Ticket");

// USER
DEFINE("_WATS_USER","Utente");
DEFINE("_WATS_USER_SET","Utenti");
DEFINE("_WATS_USER_NAME","Nome");
DEFINE("_WATS_USER_USERNAME","Nome Utente");
DEFINE("_WATS_USER_GROUP","Gruppo");
DEFINE("_WATS_USER_ORG","Organizzazione");
DEFINE("_WATS_USER_ORG_SELECT","Inserisci Organizzazione");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Crea Nuovo Utente");
DEFINE("_WATS_USER_NEW_SELECT","Seleziona un Utente");
DEFINE("_WATS_USER_NEW_CREATED","Utente Creato");
DEFINE("_WATS_USER_NEW_FAILED","Questo utente ha già un Ticket di supporto");
DEFINE("_WATS_USER_DELETED","Utente Cancellato");
DEFINE("_WATS_USER_EDIT","Modifica Utente");
DEFINE("_WATS_USER_DELETE_REC","Rimuovi i Tickets dell'utente (raccomandato)");
DEFINE("_WATS_USER_DELETE_NOTREC","Rimuovi i Tickets dell'utente e sostituiscili con altri Tickets (non raccomandato)");
DEFINE("_WATS_USER_DELETE","Cancella Utente");
DEFINE("_WATS_USER_ADD","Aggiungi Utente");
DEFINE("_WATS_USER_SELECT","Seleziona Utente");
DEFINE("_WATS_USER_SET_DESCRIPTION","Modifica Utenti");
DEFINE("_WATS_USER_ADD_LIST","Gli utenti selezionati sono stati aggiunti");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Seleziona Gruppo");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Categoria");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","I miei Tickets attivi");
DEFINE("_WATS_TICKETS_USER_CLOSED","I miei Tickets chiusi");
DEFINE("_WATS_TICKETS_OPEN","Apri Tickets");
DEFINE("_WATS_TICKETS_CLOSED","Chiudi Tickets");
DEFINE("_WATS_TICKETS_DEAD","Cancella Tickets");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Visualizza tutti i Tickets aperti");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Visualizza tutti i Tickets chiusi");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Visualizza tutti i Tickets cancellati");
DEFINE("_WATS_TICKETS_NAME","Nome Ticket");
DEFINE("_WATS_TICKETS_POSTS","Messaggi");
DEFINE("_WATS_TICKETS_DATETIME","Ultimo Messaggio");
DEFINE("_WATS_TICKETS_PAGES","Pagine");
DEFINE("_WATS_TICKETS_SUBMIT","Registra Nuovo Ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Sto registrando il Ticket");
DEFINE("_WATS_TICKETS_SUBMITTED","Il Ticket è stato registrato con successo");
DEFINE("_WATS_TICKETS_DESC","Descrizione");
DEFINE("_WATS_TICKETS_CLOSE","Chiudi Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket Chiuso");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket Cancellato");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket Spostato");
DEFINE("_WATS_TICKETS_NONE","Nessun Ticket Trovato");
DEFINE("_WATS_TICKETS_FIRSTPOST","Avviato: ");
DEFINE("_WATS_TICKETS_LASTPOST","Postato da: ");
DEFINE("_WATS_TICKETS_REPLY","Risposta");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Risposto e Chiuso");
DEFINE("_WATS_TICKETS_ASSIGN","Assegna Ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Assegnato a");
DEFINE("_WATS_TICKETS_ID","ID Ticket");
DEFINE("_WATS_TICKETS_REOPEN","Riaperto");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Per favore inserisci il motivo per il quale il Ticket deve essere riaperto");
DEFINE("_WATS_TICKETS_STATE_ALL","Tutti");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personali");
DEFINE("_WATS_TICKETS_STATE_OPEN","Aperti");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Chiusi");
DEFINE("_WATS_TICKETS_STATE_DEAD","Cancellati");
DEFINE("_WATS_TICKETS_PURGE","Sposta i cancellati in ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket registrato da: ");
DEFINE("_WATS_MAIL_REPLY","Risposta data da: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Vuoi veramente Cancellare ?");
DEFINE("_WATS_MISC_GO","Go");

//ERRORS
DEFINE("_WATS_ERROR","Si è verificato un errore");
DEFINE("_WATS_ERROR_ACCESS","Non hai i permessi sufficienti per completare l'operazione");
DEFINE("_WATS_ERROR_NOUSER","Non sei autorizzato a visualizzare questa risorsa.<br>Hai bisogno di loggarti o di chiedere l'accesso al WebMaster.");
DEFINE("_WATS_ERROR_NODATA","C'è qualche errore nel form, riprova.");
DEFINE("_WATS_ERROR_NOT_FOUND","Istanza non trovata");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Usa i 'tags' per modificare il tuo testo:</i></p> 
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
