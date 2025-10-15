<?php
/**
* @version $Id: french.php,v 2.0 2005/11/21 17:12:36 lexel Exp $
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// Site page note found
define( '_404', 'Nous sommes d�sol�s mais la page demand�e n\'a pu �tre trouv�e.' );
define( '_404_RTS', 'Retour au site' );

/** common */
DEFINE('_LANGUAGE','fr'); // Param�tre initial 'en'
DEFINE('_NOT_AUTH','Vous n\'�tes pas autoris�(e) � acc�der � cette ressource.<br />Vous devez vous connecter.');
DEFINE('_DO_LOGIN','Vous devez vous identifier.');
DEFINE('_VALID_AZ09','Saisissez un %s valide&nbsp;:  sans espace, au moins %d caract�res, alphanum�riques uniquement (0-9,a-z,A-Z)');
DEFINE('_CMN_YES','Oui');
DEFINE('_CMN_NO','Non');
DEFINE('_CMN_SHOW','Afficher');
DEFINE('_CMN_HIDE','Cacher');

DEFINE('_CMN_NAME','Nom');
DEFINE('_CMN_DESCRIPTION','Description');
DEFINE('_CMN_SAVE','Sauvegarder');
DEFINE('_CMN_CANCEL','Annuler');
DEFINE('_CMN_PRINT','Version imprimable');
DEFINE('_CMN_PDF','Convertir en PDF');
DEFINE('_CMN_EMAIL','Sugg�rer par mail');
DEFINE('_ICON_SEP','|');
DEFINE('_CMN_PARENT','Parent');
DEFINE('_CMN_ORDERING','Trier');
DEFINE('_CMN_ACCESS','Niveau d\'acc�s');
DEFINE('_CMN_SELECT','S�lectionner');

DEFINE('_CMN_NEXT','Suivant');
DEFINE('_CMN_NEXT_ARROW','&gt;&gt;');
DEFINE('_CMN_PREV','Pr�c�dent');
DEFINE('_CMN_PREV_ARROW','&lt;&lt;');

DEFINE('_CMN_SORT_NONE','Aucun Tri');
DEFINE('_CMN_SORT_ASC','Tri Croissant');
DEFINE('_CMN_SORT_DESC','Tri D�croissant');

DEFINE('_CMN_NEW','Nouveau');
DEFINE('_CMN_NONE','Aucun');
DEFINE('_CMN_LEFT','Gauche');
DEFINE('_CMN_RIGHT','Droite');
DEFINE('_CMN_CENTER','Centre');
DEFINE('_CMN_ARCHIVE','Archiver');
DEFINE('_CMN_UNARCHIVE','D�sarchiver');
DEFINE('_CMN_TOP','Haut');
DEFINE('_CMN_BOTTOM','Bas');

DEFINE('_CMN_PUBLISHED','Publi�');
DEFINE('_CMN_UNPUBLISHED','Non publi�');

DEFINE('_CMN_EDIT_HTML','Editer HTML');
DEFINE('_CMN_EDIT_CSS','Editer CSS');

DEFINE('_CMN_DELETE','Effacer');

DEFINE('_CMN_FOLDER','R�pertoire');
DEFINE('_CMN_SUBFOLDER','Sous-r�pertoire');
DEFINE('_CMN_OPTIONAL','Facultatif');
DEFINE('_CMN_REQUIRED','Obligatoire');

DEFINE('_CMN_CONTINUE','Continuer');

DEFINE('_CMN_NEW_ITEM_LAST','Les nouveaux items sont plac�s en derni�re position'); //item au lieu de element
DEFINE('_CMN_NEW_ITEM_FIRST','Les nouveaux items sont plac�s en premi�re position'); //item au lieu de element
DEFINE('_LOGIN_INCOMPLETE','Merci de renseigner votre nom d\'utilisateur et votre mot de passe.');
DEFINE('_LOGIN_BLOCKED','Votre compte a été bloqué. Nous apprécions vos affaires passées mais ne serons pas en mesure de vous servir à l`avenir');
DEFINE('_LOGIN_INCORRECT','Nom d\'utilisateur ou mot de passe incorrect. Merci de r�essayer.');
DEFINE('_LOGIN_NOADMINS','Vous ne pouvez pas vous identifier. Aucun administrateur n\'a �t� d�clar�.');
DEFINE('_CMN_JAVASCRIPT','!Avertissement! Votre navigateur doit autoriser le javascript pour pour b�n�ficier de toutes les fonctions de navigation du site.');

DEFINE('_NEW_MESSAGE','Un nouveau message priv� vient de vous �tre envoy�');
DEFINE('_MESSAGE_FAILED','Cet utilisateur a bloqu� sa bo�te de r�ception. Envoi du message impossible.');

DEFINE('_CMN_IFRAMES', 'Cette option ne fonctionnera pas correctement car votre navigateur ne supporte pas les frames internes (iframes)');

DEFINE('_INSTALL_WARN','Pour votre s�curit�, merci de supprimer le r�pertoire d\'installation ainsi que tous les fichiers et sous-dossiers qu\'il contient. Ensuite vous pourrez rafra�chir cette page');
DEFINE('_TEMPLATE_WARN','<font color=\"red\"><B>Fichier template non trouv�! Le fichier recherch�&nbsp;:</b></font>');
DEFINE('_NO_PARAMS','Aucun param�tre d�fini pour ce module');
DEFINE('_HANDLER','Aucun gestionnaire n\'a �t� d�fini pour ce type');

/** mambots */
DEFINE('_TOC_JUMPTO','Index de l\'article');

/**  content */
DEFINE('_READ_MORE','Lire la suite...');
DEFINE('_READ_MORE_REGISTER','Identifiez-vous pour lire la suite...');
DEFINE('_MORE','Suite...');
DEFINE('_ON_NEW_CONTENT', 'Un nouveau contenu a �t� soumis par [ %s ] intitul� [ %s ] dans la section [ %s ] et la cat�gorie [ %s ]' );
DEFINE('_SEL_CATEGORY','- S�lectionner une cat�gorie -');
DEFINE('_SEL_SECTION','- S�lectionner une section -');
DEFINE('_SEL_AUTHOR','- S�lectionner un auteur -');
DEFINE('_SEL_POSITION','- S�lectionner une position -');
DEFINE('_SEL_TYPE','- S�lectionner un type -');
DEFINE('_EMPTY_CATEGORY','Cette cat�gorie ne contient aucune publication');
DEFINE('_EMPTY_BLOG','');
DEFINE('_NOT_EXIST','Cette page est indisponible.<br />Veuillez faire un autre choix dans le menu g�n�ral.');

/** classes/html/modules.php */
DEFINE('_BUTTON_VOTE','Voter');
DEFINE('_BUTTON_RESULTS','R�sultats');
DEFINE('_USERNAME','Nom d\'utilisateur');
DEFINE('_LOST_PASSWORD','Perdu votre mot de passe&nbsp;?');
DEFINE('_PASSWORD','Mot de passe');
DEFINE('_BUTTON_LOGIN','Se connecter');
DEFINE('_BUTTON_LOGOUT','Se d�connecter');
DEFINE('_NO_ACCOUNT','Pas encore de compte&nbsp;?');
DEFINE('_CREATE_ACCOUNT','Enregistrez-vous');
DEFINE('_VOTE_POOR','Faible');
DEFINE('_VOTE_BEST','Meilleur');
DEFINE('_USER_RATING','Appr�ciation des utilisateurs');
DEFINE('_RATE_BUTTON','Appr�ciation');
DEFINE('_REMEMBER_ME','Se souvenir de moi');

/** contact.php */
DEFINE('_ENQUIRY','Demande');
DEFINE('_ENQUIRY_TEXT','Une demande de contact a �t� formul�e par e-mail via %s de la part de');
DEFINE('_COPY_TEXT','Ceci est une copie du message que vous avez envoy� � l\'administrateur de %s');
DEFINE('_COPY_SUBJECT','Copie de: ');
DEFINE('_THANK_MESSAGE','Merci pour votre message');
DEFINE('_CLOAKING','Cet e-mail est prot�g� contre les robots collecteurs de mails, votre navigateur doit accepter le Javascript pour le voir');
DEFINE('_CONTACT_HEADER_NAME','Nom');
DEFINE('_CONTACT_HEADER_POS','Titre');
DEFINE('_CONTACT_HEADER_EMAIL','E-mail');
DEFINE('_CONTACT_HEADER_PHONE','T�l�phone');
DEFINE('_CONTACT_HEADER_FAX','Fax');
DEFINE('_CONTACTS_DESC','La liste des contacts du site.');


/** classes/html/contact.php */
DEFINE('_CONTACT_TITLE','Contact');
DEFINE('_EMAIL_DESCRIPTION','Envoyez un e-mail � ce contact&nbsp;:');
DEFINE('_NAME_PROMPT',' Entrez votre nom&nbsp;:');
DEFINE('_EMAIL_PROMPT',' Saisissez votre adresse e-mail&nbsp;:');
DEFINE('_MESSAGE_PROMPT',' Saisissez votre message&nbsp;:');
DEFINE('_SEND_BUTTON','Envoyer');
DEFINE('_CONTACT_FORM_NC','Assurez-vous d\'avoir rempli correctement votre formulaire avant de le valider.');
DEFINE('_CONTACT_TELEPHONE','T�l�phone&nbsp;:');
DEFINE('_CONTACT_MOBILE','Mobile&nbsp;:');
DEFINE('_CONTACT_FAX','T�l�copie&nbsp;:');
DEFINE('_CONTACT_EMAIL','E-mail&nbsp;:');
DEFINE('_CONTACT_NAME','Nom&nbsp;:');
DEFINE('_CONTACT_POSITION','Titre&nbsp;:');
DEFINE('_CONTACT_ADDRESS','Adresse&nbsp;:');
DEFINE('_CONTACT_MISC','Information&nbsp;:');
DEFINE('_CONTACT_SEL','S�lectionnez un contact&nbsp;:');
DEFINE('_CONTACT_NONE','Aucun profil de contact d�fini.');
DEFINE('_EMAIL_A_COPY','Recevoir une copie de cet e-mail');
DEFINE('_CONTACT_DOWNLOAD_AS','T�l�charger les informations comme');
DEFINE('_VCARD','VCard');

/** pageNavigation */
DEFINE('_PN_LT','&lt;');
DEFINE('_PN_RT','&gt;');
DEFINE('_PN_PAGE','Page');
DEFINE('_PN_OF','sur');
DEFINE('_PN_START','D�but');
DEFINE('_PN_PREVIOUS','Pr�c�dente');
DEFINE('_PN_NEXT','Suivante');
DEFINE('_PN_END','Fin');
DEFINE('_PN_DISPLAY_NR','Affiche #');
DEFINE('_PN_RESULTS','R�sultats');

/** emailfriend */
DEFINE('_EMAIL_TITLE','Sugg�rer l\'article � un ami');
DEFINE('_EMAIL_FRIEND','Sugg�rer l\'article � un ami');
DEFINE('_EMAIL_FRIEND_ADDR','Son adresse e-mail&nbsp;:');
DEFINE('_EMAIL_YOUR_NAME','Votre nom&nbsp;:');
DEFINE('_EMAIL_YOUR_MAIL','Votre adresse e-mail&nbsp;:');
DEFINE('_SUBJECT_PROMPT','Objet du message&nbsp;:');
DEFINE('_BUTTON_SUBMIT_MAIL','Envoyer');
DEFINE('_BUTTON_CANCEL','Annuler');
DEFINE('_EMAIL_ERR_NOINFO','Vous devez saisir une adresse e-mail valide');
DEFINE('_EMAIL_MSG','Une page du site %s vous est sugg�r�e par %s ( %s ).

Vous pouvez consulter la page en question � l\'adresse suivante:
%s

Cordialement.');
DEFINE('_EMAIL_INFO','Publication envoy�e par');
DEFINE('_EMAIL_SENT','Cette publication a �t� sugg�r�e �');
DEFINE('_PROMPT_CLOSE','Fermer la fen�tre');

/** classes/html/content.php */
DEFINE('_AUTHOR_BY', ' Soumis par'); 
DEFINE('_WRITTEN_BY', ' Ecrit par');
DEFINE('_LAST_UPDATED', ' Derni�re mise � jour&nbsp;:');
DEFINE('_BACK','[&nbsp;Retour&nbsp;]');
DEFINE('_LEGEND','L�gende');
DEFINE('_DATE','Date');
DEFINE('_ORDER_DROPDOWN','Trier');
DEFINE('_HEADER_TITLE','Titre de la publication');
DEFINE('_HEADER_AUTHOR','Auteur');
DEFINE('_HEADER_SUBMITTED','Soumis');
DEFINE('_HEADER_HITS','Clics');
DEFINE('_E_EDIT','Editer');
DEFINE('_E_ADD','Ajouter');
DEFINE('_E_WARNUSER','Veuillez valider ou annuler la modification en cours'); //ajout veuillez
DEFINE('_E_WARNTITLE','Le titre est obligatoire');
DEFINE('_E_WARNTEXT','Le texte d\'introduction est obligatoire');
DEFINE('_E_WARNCAT','Veuillez s�lectionner une cat�gorie'); //ajout veuillez
DEFINE('_E_CONTENT','Contenu');
DEFINE('_E_TITLE','Titre&nbsp;:');
DEFINE('_E_CATEGORY','Cat�gorie&nbsp;:');
DEFINE('_E_INTRO','Texte d\'introduction');
DEFINE('_E_MAIN','Texte principal');
DEFINE('_E_MOSIMAGE','INSERT {mosimage}'); // Ne pas traduire c'est une commande SQL
DEFINE('_E_IMAGES','Images');
DEFINE('_E_GALLERY_IMAGES','Galerie d\'images');
DEFINE('_E_CONTENT_IMAGES','Images s�lectionn�es');
DEFINE('_E_EDIT_IMAGE','Propri�t�s de l\'image');
DEFINE('_E_INSERT','Insertion');
DEFINE('_E_UP','Au dessus');
DEFINE('_E_DOWN','Au dessous');
DEFINE('_E_REMOVE','Suppression');
DEFINE('_E_SOURCE','Source&nbsp;:');
DEFINE('_E_ALIGN','Alignement&nbsp;:');
DEFINE('_E_ALT','Balise alt&nbsp;:');
DEFINE('_E_BORDER','Bordure&nbsp;:');
DEFINE('_E_CAPTION','L�gende&nbsp;:');
DEFINE('_E_APPLY','Appliquer');
DEFINE('_E_PUBLISHING','Publication');
DEFINE('_E_STATE','Etat&nbsp;:');
DEFINE('_E_AUTHOR_ALIAS','Alias de l\'auteur&nbsp;:');
DEFINE('_E_ACCESS_LEVEL','Niveau d\'acc�s&nbsp;:');
DEFINE('_E_ORDERING','Ordre&nbsp;:');
DEFINE('_E_START_PUB','D�but de publication&nbsp;:');
DEFINE('_E_FINISH_PUB','Fin de publication&nbsp;:');
DEFINE('_E_SHOW_FP','Afficher en page d\'accueil&nbsp;:');
DEFINE('_E_HIDE_TITLE','Cacher le titre de l\'item&nbsp;:'); //item au lieu de element
DEFINE('_E_METADATA','M�tadonn�es');
DEFINE('_E_M_DESC','Description&nbsp;:');
DEFINE('_E_M_KEY','Mots-cl�s&nbsp;:');
DEFINE('_E_SUBJECT','Sujet&nbsp;:');
DEFINE('_E_EXPIRES','Date d\'expiration&nbsp;:');
DEFINE('_E_VERSION','Version&nbsp;:');
DEFINE('_E_ABOUT','A propos');
DEFINE('_E_CREATED','Cr��&nbsp;:');
DEFINE('_E_LAST_MOD','Modifi� le&nbsp;:');
DEFINE('_E_HITS','Clics&nbsp;:');
DEFINE('_E_SAVE','Sauvegarder');
DEFINE('_E_CANCEL','Abandonner');
DEFINE('_E_REGISTERED','Utilisateurs enregistr�s seulement');
DEFINE('_E_ITEM_INFO','Info sur l\'article');
DEFINE('_E_ITEM_SAVED','Publication sauvegard�e avec succ�s.');
DEFINE('_ITEM_PREVIOUS','&lt; Pr�c�dent');
DEFINE('_ITEM_NEXT','Suivant &gt;');

/** content.php */
DEFINE('_SECTION_ARCHIVE_EMPTY','Cette section ne contient aucune archive.');	
DEFINE('_CATEGORY_ARCHIVE_EMPTY','Cette cat�gorie ne contient aucune archive.');
DEFINE('_HEADER_SECTION_ARCHIVE','Archives par section');
DEFINE('_HEADER_CATEGORY_ARCHIVE','Archives par cat�gorie');
DEFINE('_ARCHIVE_SEARCH_FAILURE','Il n\'y a pas d\'archives pour %s %s');	// les valeurs %s repr�sentent mois et ann�e
DEFINE('_ARCHIVE_SEARCH_SUCCESS','Voici les Archives de %s %s');	// les valeurs %s repr�sentent mois et ann�e
DEFINE('_FILTER','Filtre');
DEFINE('_ORDER_DROPDOWN_DA','Date asc');
DEFINE('_ORDER_DROPDOWN_DD','Date desc');
DEFINE('_ORDER_DROPDOWN_TA','Titre asc');
DEFINE('_ORDER_DROPDOWN_TD','Titre desc');
DEFINE('_ORDER_DROPDOWN_HA','Clics asc');
DEFINE('_ORDER_DROPDOWN_HD','Clics desc');
DEFINE('_ORDER_DROPDOWN_AUA','Auteur asc');
DEFINE('_ORDER_DROPDOWN_AUD','Auteur desc');
DEFINE('_ORDER_DROPDOWN_O','Ordre');

/** poll.php */
DEFINE('_ALERT_ENABLED','Vous devez autoriser les cookies.'); 
DEFINE('_ALREADY_VOTE','Vous avez d�ja vot� pour ce sondage aujourd\'hui.');
DEFINE('_NO_SELECTION','Vous n\'avez rien s�lectionn�, veuillez recommencer');
DEFINE('_THANKS','Merci d\'avoir vot�. Pour voir les r�sultats, cliquez sur le bouton \'R�sultats\'');
DEFINE('_SELECT_POLL','Veuillez s�lectionner un sondage dans la liste');

/** classes/html/poll.php */
DEFINE('_JAN','Janvier');
DEFINE('_FEB','F�vrier');
DEFINE('_MAR','Mars');
DEFINE('_APR','Avril');
DEFINE('_MAY','Mai');
DEFINE('_JUN','Juin');
DEFINE('_JUL','Juillet');
DEFINE('_AUG','Ao�t');
DEFINE('_SEP','Septembre');
DEFINE('_OCT','Octobre');
DEFINE('_NOV','Novembre');
DEFINE('_DEC','D�cembre');
DEFINE('_POLL_TITLE','R�sultats du sondage');
DEFINE('_SURVEY_TITLE','Titre du sondage');
DEFINE('_NUM_VOTERS','Nombre de votants');
DEFINE('_FIRST_VOTE','Premier vote');
DEFINE('_LAST_VOTE','Dernier vote');
DEFINE('_SEL_POLL','S�lectionner un sondage:');
DEFINE('_NO_RESULTS','Il n\'y a pas encore de r�sultats pour ce sondage.');

/** registration.php */
DEFINE('_ERROR_PASS','Aucun utilisateur correspondant n\'a �t� trouv�');
DEFINE('_NEWPASS_MSG','Le compte utilisateur $checkusername est associ� � cet e-mail.\n'
.' Un utilisateur de $mosConfig_live_site a demand� un nouveau mot de passe.\n\n'
.' Nom d\'utilisateur: $checkusername\n\n'
.' Votre nouveau mot de passe est&nbsp;: $newpass\n\n Vous n\'aviez pas demand� � changer&nbsp;? Ne soyez pas d�rout�(e)&nbsp;!'
.' Vous �tes le(la) seul(e) � voir ce message. Ainsi, si vous pensez que ceci est une erreur, connectez vous juste avec votre'
.' nouveau mot de passe puis changez-le de nouveau dans votre profil.');
DEFINE('_NEWPASS_SUB','$_sitename :: Nouveau mot de passe pour - $checkusername'); 
DEFINE('_NEWPASS_SENT','<span class="componentheading">Un nouveau mot de passe a �t� cr�� et vous a �t� envoy�.</span>');
DEFINE('_REGWARN_NAME','Saisissez votre nom.'); 
DEFINE('_REGWARN_UNAME','Saisissez un nom d\'utilisateur.'); 
DEFINE('_REGWARN_MAIL','Saisissez une adresse e-mail valide.');
DEFINE('_REGWARN_PASS','Saisissez un mot de passe valide&nbsp;: sans espace, d\'au moins 6 caract�res, alphanum�riques uniquement (0-9,a-z,A-Z)'); //
DEFINE('_REGWARN_VPASS1','V�rifiez le mot de passe.');
DEFINE('_REGWARN_VPASS2','Le mot de passe ne correspond pas, veuillez r�essayer.');
DEFINE('_REGWARN_INUSE','Ce nom d\'utilisateur / mot de passe existe d�j�. Veuillez r�essayer.');
DEFINE('_REGWARN_EMAIL_INUSE', 'Cet e-mail est d�j� pr�sent dans notre base de donn�es. Si vous avez perdu votre mot de passe, utilisez la fonction de r�cup�ration et nous vous enverrons un nouveau mot de passe � cette adresse e-mail.');
DEFINE('_SEND_SUB','Profil de %s inscrit � %s');
DEFINE('_USEND_MSG_ACTIVATE', 'Bonjour %s,

Merci de vous �tre enregistr�(e) sur %s. Votre compte a �t� cr�� correctement, il ne vous reste qu\'� l\'activer.
Pour l\'activer vous pouvez cliquer sur le lien ci-dessous ou le copier/coller dans votre navigateur:
%s

Apr�s l\'activation vous pourrez vous connecter � %s en utilisant le nom d\'utilisateur et le mot de passe suivant:

Nom d\'Utilisateur - %s
Mot de passe - %s');
DEFINE('_USEND_MSG', 'Bonjour %s,

Merci de vous �tre enregistr�(e) sur %s.

Vous pouvez maintenant vous connecter � %s en utilisant votre nom d\'utilisateur et mot de passe choisis lors de votre inscription.');
DEFINE('_USEND_MSG_NOPASS','Bonjour $name,\n\nVous avez �t� inscrit(e) comme utilisateur $mosConfig_live_site.\n'
.'Vous pouvez vous connecter au site $mosConfig_live_site avec le nom d\'utilisateur et le mot de passe que vous avez choisi.\n\n'
.'Ne r�pondez pas � cet e-mail. Il a �t� envoy� automatiquement pour votre information\n');
DEFINE('_ASEND_MSG','Bonjour %s,

un nouvel utilisateur s\'est inscrit � %s.
Cet e-mail contient son profil:

Nom - %s
e-mail - %s
Nom d\'Utilisateur - %s

Ne r�pondez pas � ce message, il a �t� g�n�r� automatiquement pour votre information');
DEFINE('_REG_COMPLETE_NOPASS','<div class="componentheading">Inscription compl�te.</div><br />'
.'Vous pouvez vous connecter.<br />');
DEFINE('_REG_COMPLETE', '<div class="componentheading">Inscription compl�te.</div><br />Vous pouvez maintenant vous connecter.');
DEFINE('_REG_COMPLETE_ACTIVATE', '<div class="componentheading">Enregistrement effectu�.</div><br />Votre profil a �t� cr�� correctement pour confirmer et finir votre enregistrement nous vous avons adress� un lien d\'activation par e-mail. Avant de vous connecter sur ce site, il est imp�ratif d\'activer votre compte en utilisant le lien contenu dans cet e-mail d\'activation.');
DEFINE('_REG_ACTIVATE_COMPLETE', '<div class="componentheading">Activation effectu�e.</div><br />Votre profil a �t� correctement activ�. Vous pouvez maintenant vous connecter en utilisant le nom d\'utilisateur et mot de passe choisis lors de votre inscription.');
DEFINE('_REG_ACTIVATE_NOT_FOUND', '<div class="componentheading">Lien d\'activation invalide.</div><br />Le lien fait r�f�rence � un profil inexitsant ou d�j� activ� dans notre base de donn�es.');

/** classes/html/registration.php */
DEFINE('_PROMPT_PASSWORD','Perdu votre mot de passe&nbsp;?'); 
DEFINE('_NEW_PASS_DESC','Entrez votre adresse e-mail, puis cliquez sur le bouton envoyer le mot de passe.<br />'
.'Vous recevrez un nouveau mot de passe rapidement. Utilisez-le pour vous identifier sur le site.'); 
DEFINE('_PROMPT_UNAME','Nom d\'utilisateur&nbsp;:');
DEFINE('_PROMPT_EMAIL','Adresse e-mail&nbsp;:');
DEFINE('_BUTTON_SEND_PASS','Envoyer le mot de passe');
DEFINE('_REGISTER_TITLE','Inscription');
DEFINE('_REGISTER_NAME','Nom&nbsp;:');
DEFINE('_REGISTER_UNAME','Nom d\'utilisateur&nbsp;:');
DEFINE('_REGISTER_EMAIL','e-mail&nbsp;:');
DEFINE('_REGISTER_PASS','Mot de passe&nbsp;:');
DEFINE('_REGISTER_VPASS','V�rification du mot de passe&nbsp;:');
DEFINE('_REGISTER_REQUIRED','Les champs marqu�s avec un ast�risque (*) sont obligatoires.');
DEFINE('_BUTTON_SEND_REG','Terminer l\'inscription'); 
DEFINE('_SENDING_PASSWORD','Votre mot de passe sera envoy� � l\'adresse e-mail ci-dessus.<br />Une fois que vous l\'aurez re�u'
.' vous pourrez vous identifier et le modifier � votre convenance dans votre profil.');

/** classes/html/search.php */
DEFINE('_SEARCH_TITLE','Rechercher');
DEFINE('_PROMPT_KEYWORD','Rechercher les mots-cl�s');
DEFINE('_SEARCH_MATCHES','%d r�sultat(s)');
DEFINE('_CONCLUSION','$totalRows r�sultat(s) trouv�(s) au total.  Rechercher <b>$searchword</b> avec');
DEFINE('_NOKEYWORD','Aucun r�sultats pour cette recherche');
DEFINE('_IGNOREKEYWORD','Un ou plusieurs mots communs ont �t� ignor�s');
DEFINE('_SEARCH_ANYWORDS','Un des termes');
DEFINE('_SEARCH_ALLWORDS','Tous les termes');
DEFINE('_SEARCH_PHRASE','Phrase exacte');
DEFINE('_SEARCH_NEWEST','Plus r�cent en premier');
DEFINE('_SEARCH_OLDEST','Plus ancien en premier');
DEFINE('_SEARCH_POPULAR','Plus populaire');
DEFINE('_SEARCH_ALPHABETICAL','Alphab�tique');
DEFINE('_SEARCH_CATEGORY','Section/Cat�gorie');
DEFINE('_SEARCH_MESSAGE','Recherche mini 3 lettres et maxi 20 lettres');
DEFINE('_SEARCH_ARCHIVED','Archiv�e');
DEFINE('_SEARCH_CATBLOG','Categorie Blog');
DEFINE('_SEARCH_CATLIST','Categorie Liste');
DEFINE('_SEARCH_NEWSFEEDS','Flux RSS');
DEFINE('_SEARCH_SECLIST','Section Liste');
DEFINE('_SEARCH_SECBLOG','Section Blog');

/** templates/*.php */
DEFINE('_ISO','charset=utf-8');
DEFINE('_DATE_FORMAT','l, F d Y');  //Uses PHP's DATE Command Format - Depreciated
/**
* Modifier la ligne en accord avec le format de date que vous souhaitez voir apparaitre sur votre site
*
*e.g. DEFINE('_DATE_FORMAT_LC','%A, %d %B %Y %H:%M'); // R�f�rez-vous � l'utilisation de la commande PHP strftime
*/
DEFINE('_DATE_FORMAT_LC','%d-%m-%Y'); // R�f�rez-vous � l'utilisation de la commande PHP strftime
/** la ligne initiale dans le fichier source en anglais :  DEFINE('_DATE_FORMAT_LC2','%A, %d %B %Y %H:%M'); */
DEFINE('_DATE_FORMAT_LC2','%d-%m-%Y %H:%M');
DEFINE('_SEARCH_BOX','Rechercher...');
DEFINE('_NEWSFLASH_BOX','Annonce');
DEFINE('_MAINMENU_BOX','Menu Principal');

/** classes/html/usermenu.php */
DEFINE('_UMENU_TITLE','Menu Utilisateur');
DEFINE('_HI','Bonjour, ');

/** user.php */
DEFINE('_SAVE_ERR','Veuillez remplir tous les champs du formulaire, merci.');
DEFINE('_THANK_SUB','Merci pour votre proposition. Votre proposition sera v�rifi�e  avant d\'�tre publi�e sur le site.');
DEFINE('_THANK_SUB_PUB','Merci de nous avoir propos� cet article.');
DEFINE('_UP_SIZE','Vous ne pouvez pas transmettre des fichiers de plus de 15ko.');
DEFINE('_UP_EXISTS','Une image portant le nom $userfile_name existe d�j�. Veuillez renommer votre fichier avant de r�essayer.');
DEFINE('_UP_COPY_FAIL','La copie a �chou�');
DEFINE('_UP_TYPE_WARN','Seuls les fichiers gif ou jpg sont autoris�s.');
DEFINE('_MAIL_SUB','Publication soumise par un membre'); 
DEFINE('_MAIL_MSG','Bonjour $adminName,\n\nUn nouveau texte $type, $title, a �t� soumis par $author'
.' pour le site $live_site website. \n' 
.'Rendez-vous sur $mosConfig_live_site/administrator pour v�rifier et valider ce $type.\n\n'
.'Ne r�pondez pas � cet e-mail, il a �t� g�n�r� automatiquement pour votre information\n');
DEFINE('_PASS_VERR1','Si vous modifiez votre mot de passe, retapez-le pour v�rification.');
DEFINE('_PASS_VERR2','Si vous modifiez votre mot de passe, assurez-vous que le mot de passe et sa v�rification concordent.');
DEFINE('_UNAME_INUSE','Ce nom d\'utilisateur est d�j� utilis�.');
DEFINE('_UPDATE','Mise � jour');
DEFINE('_USER_DETAILS_SAVE','Votre profil a �t� sauvegard�.');
DEFINE('_USER_LOGIN','Identification Utilisateur');

/** components/com_user */
DEFINE('_EDIT_TITLE','Editer vos informations personnelles'); 
DEFINE('_YOUR_NAME','Votre nom&nbsp;:');
DEFINE('_EMAIL','E-mail&nbsp;:');
DEFINE('_UNAME','Nom d\'utilisateur&nbsp;:');
DEFINE('_PASS','Mot de passe&nbsp;:');
DEFINE('_VPASS','V�rifiez votre mot de passe&nbsp;:');
DEFINE('_SUBMIT_SUCCESS','Envoi r�ussi');
DEFINE('_SUBMIT_SUCCESS_DESC','Votre article a �t� propos� avec succ�s � nos administrateurs. Il sera v�rifi� et valid� avant d\'�tre plubli� sur le site.');
DEFINE('_WELCOME','Bienvenue');
DEFINE('_WELCOME_DESC','<span class="componentheading">Bienvenue dans la partie utilisateur de notre site</span>');
DEFINE('_CONF_CHECKED_IN','Tous vos �l�ments sont consid�r�s comme v�rifi�s/lib�r�s');
DEFINE('_CHECK_TABLE','Table de V�rification');
DEFINE('_CHECKED_IN','V�rifi� ');
DEFINE('_CHECKED_IN_ITEMS',' items');
DEFINE('_PASS_MATCH','Mots de passe ne correspondent pas');

/** components/com_banners */
DEFINE('_BNR_CLIENT_NAME','Vous devez sp�cifier un nom pour ce client.');
DEFINE('_BNR_CONTACT','Vous devez sp�cifier un contact pour ce client.');
DEFINE('_BNR_VALID_EMAIL','Vous devez sp�cifier un e-mail valide pour ce client.');
DEFINE('_BNR_CLIENT','Vous devez s�lectionner un client,');
DEFINE('_BNR_NAME','Vous devez sp�cifier un nom pour cette banni�re.');
DEFINE('_BNR_IMAGE','Vous devez s�lectionner une image pour cette banni�re.');
DEFINE('_BNR_URL','Vous devez pr�ciser une URL ou un code personnalis� pour cette banni�re.');

/** components/com_login */
DEFINE('_ALREADY_LOGIN','Vous �tes d�j� connect�(e)');
DEFINE('_LOGOUT','Cliquez ici pour vous d�connecter');
DEFINE('_LOGIN_TEXT','Utilisez le formulaire d\'identification ci-contre pour obtenir un acc�s complet');
DEFINE('_LOGIN_SUCCESS','Vous �tes connect�(e)');
DEFINE('_LOGOUT_SUCCESS','Vous �tes d�connect�(e)');
DEFINE('_LOGIN_DESCRIPTION','Pour acc�der � la partie priv�e merci de vous identifier');
DEFINE('_LOGOUT_DESCRIPTION','Vous �tes connect�(e) � la partie priv�e du site');

/** components/com_weblinks */
DEFINE('_WEBLINKS_TITLE','Liens Web');
DEFINE('_WEBLINKS_DESC','Nous surfons souvent sur le Web. D�s que nous rencontrons un site int�ressant, nous le r�pertorions'
.' pour vous.  <br />S�lectionnez dans la liste propos�e un de nos liens Web.');
DEFINE('_HEADER_TITLE_WEBLINKS','Liens Web');
DEFINE('_SECTION','Section&nbsp;:');
DEFINE('_SUBMIT_LINK','Soumettre un Lien Web');
DEFINE('_URL','URL&nbsp;:');
DEFINE('_URL_DESC','Description&nbsp;:');
DEFINE('_NAME','Nom&nbsp;:');
DEFINE('_WEBLINK_EXIST','il existe d�j� un Lien Web qui porte ce nom, merci de r�essayer.');
DEFINE('_WEBLINK_TITLE','Votre Lien Web doit contenir un titre.');

/** components/com_newfeeds */
DEFINE('_FEED_NAME','Nom du Fil d\'actualit�'); 
DEFINE('_FEED_ARTICLES','# Articles');
DEFINE('_FEED_LINK','Lien vers le Fil d\'actualit�'); 

/** whos_online.php */
DEFINE('_WE_HAVE', 'Il y a actuellement ');
DEFINE('_AND', ' et ');
DEFINE('_GUEST_COUNT','$guest_array invit�');
DEFINE('_GUESTS_COUNT','$guest_array invit�s');
DEFINE('_MEMBER_COUNT','$user_array membre');
DEFINE('_MEMBERS_COUNT','$user_array membres');
DEFINE('_ONLINE',' en ligne');
DEFINE('_NONE','Aucun utilisateur enregistr� en ligne');

/** modules/mod_stats.php */
DEFINE('_TIME_STAT','Heure');
DEFINE('_MEMBERS_STAT','Membres');
DEFINE('_HITS_STAT','Clics');
DEFINE('_NEWS_STAT','Publications');
DEFINE('_LINKS_STAT','Liens');
DEFINE('_VISITORS','Visiteurs');

/** /adminstrator/components/com_menus/admin.menus.html.php */
DEFINE('_MAINMENU_HOME','* Ceci est le premier item publi� dans ce menu [mainmenu] c\'est la page d\'accueil du site par d�faut *'); //item au lieu de element
DEFINE('_MAINMENU_DEL','* Vous ne pouvez pas effacer ce menu, car il est n�cessaire au bon fonctionnement de Joomla *');
DEFINE('_MENU_GROUP','* Quelques \'Types de Menu\' existent dans plus d\'un groupe *');

/** administrators/components/com_users */
DEFINE('_NEW_USER_MESSAGE_SUBJECT', 'Votre Profil Utilisateur' );
DEFINE('_NEW_USER_MESSAGE', 'Bonjour %s,


Vous avez �t� inscrit(e) comme membre du site %s par un administrateur.

Cet e-mail contient votre nom d\'utilisateur et mot de passe qui vous permettent de vous identifier sur %s:

Nom d\'Utilisateur - %s
Mot de passe - %s


Merci de ne pas r�pondre � cet e-mail. Il a �t� envoy� automatiquement pour votre information');

/** administrators/components/com_massmail */
DEFINE('_MASSMAIL_MESSAGE', "Ceci est un e-mail de \'%s\'

Message:
" );

/** includes/pdf.php */
DEFINE('_PDF_GENERATED','G�n�r�:');
DEFINE('_PDF_POWERED','Propuls� par Joomla!');


DEFINE('_AJAX_WAITTING','S\'il vous pla&icirc;t patienter pendant que le traitement de votre demande ...');
DEFINE ('_DELIVERY_CALENDAR','Calendrier de livraison');
DEFINE ('_DELIVERY_CALENDAR_NOTE', '<strong> S&eacute;lectionnez une date de livraison ci-dessous </strong> <br/> Cliquez sur la date ci-dessous pour choisir la date de livraison de votre panier.');
DEFINE ('_DELIVERY_CALENDAR_NOTE2', '&Eacute;tape 2: Choisissez votre date de livraison:');
DEFINE ('_DELIVERY_SURCHARGE','&Eacute;tape 1: S&eacute;lectionnez votre option de livraison:');
DEFINE ('_DELIVERY_SPECIAL_DAY', 'Journ&eacute;e sp&eacute;ciale');
DEFINE('_DELIVERY_EXTRA_SURCHARGE','Suppl�ment Exp�dition n�gative');
DEFINE ('_DELIVERY_SAME_DAY','Le m&ecirc;me jour de livraison');
DEFINE ('_DELIVERY_FOLLOW_ADDRESS', 'Votre adresse de livraison');
DEFINE ('_HOLIDAYS','Vacances');
DEFINE ('_CONTACT_BLOG_EMAIL','Envoyer un courriel � ce contact');



DEFINE('_PAGE_TITLE','{city} Fleurs | {city} Fleuriste | {city} Livraison Fleurs | Bloomex {city}, {province}, Canada');
DEFINE('_PAGE_META_DESC','Bloomex {city} offre les fleurs les plus fra�ches pour 50% le prix des autres fleuristes. Commandez enligne ou par telephone pour les livraisons de fleurs le jour meme a {city}.');
DEFINE('_PAGE_META_KEYWORDS','fleurs {city}, fleuriste {city}, fleurs livraison {city}, fleurs fra�ches {city}, fleuristes {city}, fleur {city}, plantes {city}, arrangements de sympathies {city}, {city} st valentin, Bloomex {city} {province}');

DEFINE('_BASKET_PAGE_TITLE','{city} Fleurs | {city} Fleuriste | {city} Livraison Fleurs | Bloomex {city}, {province}, Canada');
DEFINE('_BASKET_PAGE_META_DESC','Bloomex {city} offre les fleurs les plus fra�ches pour 50% le prix des autres fleuristes. Commandez enligne ou par telephone pour les livraisons de fleurs le jour meme a {city}.');
DEFINE('_BASKET_PAGE_META_KEYWORDS','fleurs {city}, fleuriste {city}, fleurs livraison {city}, fleurs fra�ches {city}, fleuristes {city}, fleur {city}, plantes {city}, arrangements de sympathies {city}, {city} st valentin, Bloomex {city} {province}');

?>
