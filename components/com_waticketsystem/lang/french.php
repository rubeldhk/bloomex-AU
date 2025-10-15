<?php
/**
 * FileName: french.php
 * Date: 10/06/2006
 * License: GNU General Public License
 * File Version #: 1
 * WATS Version #: 2.0.0.1
 * Author: Johan Aubry jaubry@a-itservices.com (www.a-itservices.com)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Nouveau ticket");
DEFINE("_WATS_NAV_CATEGORY","Cat�gories");
DEFINE("_WATS_NAV_TICKET","Num�ro du ticket");

// USER
DEFINE("_WATS_USER","Utilisateur");
DEFINE("_WATS_USER_SET","Utilisateurs");
DEFINE("_WATS_USER_NAME","Nom");
DEFINE("_WATS_USER_USERNAME","Nom d'utilisateur");
DEFINE("_WATS_USER_GROUP","Groupe");
DEFINE("_WATS_USER_ORG","Organisation");
DEFINE("_WATS_USER_ORG_SELECT","Entrez l'organisation");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Cr�er un nouvel utilisateur");
DEFINE("_WATS_USER_NEW_SELECT","S�lectionner un utilisateur");
DEFINE("_WATS_USER_NEW_CREATED","Utilisateur cr�e");
DEFINE("_WATS_USER_NEW_FAILED","Cette utilisateur a d�j� un compte");
DEFINE("_WATS_USER_DELETED","Utilisateur effac�");
DEFINE("_WATS_USER_EDIT","Editer l'utilisateur");
DEFINE("_WATS_USER_DELETE_REC","D�truire les tickets (recommand�)");
DEFINE("_WATS_USER_DELETE_NOTREC","D�truire les tickets de l'utilisateurs et r�pondre aux autres tickets (pas recommand�)");
DEFINE("_WATS_USER_DELETE","Effacer l'utilisateur");
DEFINE("_WATS_USER_ADD","Ajouter l'utilisateur");
DEFINE("_WATS_USER_SELECT","S�lectionner l'utilisateur");
DEFINE("_WATS_USER_SET_DESCRIPTION","G�rer les utilisateurs");
DEFINE("_WATS_USER_ADD_LIST","L'utilisateur a �t� ajout�");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","S�lectionnez le groupe");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Cat�gorie");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Mes tickets ouverts");
DEFINE("_WATS_TICKETS_USER_CLOSED","Mes tickets ferm�s");
DEFINE("_WATS_TICKETS_OPEN","Tickets ouverts");
DEFINE("_WATS_TICKETS_CLOSED","Tickets ferm�s");
DEFINE("_WATS_TICKETS_DEAD","Tickers morts");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Voir tous les tickets ouverts");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Voir tous les tickets ferm�s");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Voir tous les tickets morts");
DEFINE("_WATS_TICKETS_NAME","Nom ticket");
DEFINE("_WATS_TICKETS_POSTS","Affichage");
DEFINE("_WATS_TICKETS_DATETIME","Dernier affichage");
DEFINE("_WATS_TICKETS_PAGES","Pages");
DEFINE("_WATS_TICKETS_SUBMIT","Sousmettre un nouveau ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Ticket en sousmission");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket sousmis avec succ�s");
DEFINE("_WATS_TICKETS_DESC","Description");
DEFINE("_WATS_TICKETS_CLOSE","Fermer le ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket ferm�");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket effac�");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket purg�");
DEFINE("_WATS_TICKETS_NONE","pas de tickets trouv�s");
DEFINE("_WATS_TICKETS_FIRSTPOST","date de d�but: ");
DEFINE("_WATS_TICKETS_LASTPOST","post� par: ");
DEFINE("_WATS_TICKETS_REPLY","R�pondre");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","R�pondre et Fermer");
DEFINE("_WATS_TICKETS_ASSIGN","Assigner le ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Assign� �");
DEFINE("_WATS_TICKETS_ID","ID du Ticket");
DEFINE("_WATS_TICKETS_REOPEN","Rouvrir");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Merci de donner une raison pourquoi vous voulez rouvrir le ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Tous");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Personnel");
DEFINE("_WATS_TICKETS_STATE_OPEN","Ouvert");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Ferm�");
DEFINE("_WATS_TICKETS_STATE_DEAD","Mort");
DEFINE("_WATS_TICKETS_PURGE","Purg� les tickets morts dans ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Ticket soumis par: ");
DEFINE("_WATS_MAIL_REPLY","R�ponse soumise par: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Me d�truire ?");
DEFINE("_WATS_MISC_GO","OK");

//ERRORS
DEFINE("_WATS_ERROR","Une erreur est survenue");
DEFINE("_WATS_ERROR_ACCESS","Vous n'avez PAS les droits suffisants pour effectuer cette t�che");
DEFINE("_WATS_ERROR_NOUSER","Vous n'�tes pas autoris� � voir cette ressource.<br>Vous devez vous connecter ou demander un acc�s � l'administrateur.");
DEFINE("_WATS_ERROR_NODATA","Vous n'avez pas rempli correctement le formulaire, merci de recommencer.");
DEFINE("_WATS_ERROR_NOT_FOUND","El�ment introuvable");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Utiliser les 'tags' ci-dessous pour formatter votre texte :</i></p> 
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
