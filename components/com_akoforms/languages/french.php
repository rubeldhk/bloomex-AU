<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Vincent Ackermann
* Homepage   : jumping-net.com
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Oui';
  var $AKF_NO                  = 'Non';
  var $AKF_PUBLISHED           = 'Publi�';
  var $AKF_PUBLISHING          = 'Publication';
  var $AKF_STARTPUBLISHING     = 'D�but de la publication:';
  var $AKF_FINISHPUBLISHING    = 'Fin de la publication:';
  var $AKF_PUBPENDING          = 'Publi�, mais suspendu';
  var $AKF_PUBCURRENT          = 'Publi� et actuel';
  var $AKF_PUBEXPIRED          = 'Publi�, mais expir�';
  var $AKF_UNPUBLISHED         = 'Pas publi�';
  var $AKF_REORDER             = 'R�organis�';
  var $AKF_ORDERING            = 'Ordre:';
  var $AKF_TITLE               = 'Titre:';
  var $AKF_DESCRIPTION         = 'Description:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Edition langue';
  var $AKF_PATH                = 'Chemin:';
  var $AKF_FILEWRITEABLE       = 'Note: de mani�re � sauver vos modifications, les droits d&acute;�riture sur le ficher doivent �tre accord�s.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Gestionaire de formulaire';
  var $AKF_FORMTITLE           = 'Titre du formulaire';
  var $AKF_SENDMAIL            = 'Envoi d&acute;Email';
  var $AKF_STOREDB             = 'Enregistrement de la base de donn�es';
  var $AKF_FINISHING           = 'Termin�';
  var $AKF_FORMPAGE            = 'Page du formulaire';
  var $AKF_REDIRECTION         = 'Redirection';
  var $AKF_SHOWRESULT          = 'Visualisation du r�sultat';
  var $AKF_NUMBEROFFIELDS      = 'Nombre de champs';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Ajout d&acute;un formulaire';
  var $AKF_EDITFORM            = 'Edition d&acute;un formulaire';
  var $AKF_HEADER              = 'Ent�te';
  var $AKF_HANDLING            = 'Traitement';
  var $AKF_SENDBYEMAIL         = 'Envoi par Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Enregistrement dans la base de donn�es:';
  var $AKF_ENDPAGETITLE        = 'Fin du titre de la page:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Fin de la description de la page:';
  var $AKF_FORMTARGET          = 'Cible du formulaire:';
  var $AKF_TARGETURL           = 'Redirection URL:';
  var $AKF_SHOWENDPAGE         = 'Montre le pied de page';
  var $AKF_REDIRECTTOURL       = 'R�acheminer vers l&acute;URL';
  var $AKF_NEWFORMSLAST        = 'Le nouveau formulaire est plac� � la fin.';
  var $AKF_SHOWFORMRESULT      = 'Montre le r�sultat du formulaire:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Gestionaire des champs';
  var $AKF_FIELDTITLE          = 'Titre du champs';
  var $AKF_FIELDTYPE           = 'Type du champs';
  var $AKF_FIELDREQUIRED       = 'Champs requis';
  var $AKF_SELECTFORM          = 'Selectioner le formulaire';
  var $AKF_ALLFORMS            = '- Tous les formulaires';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Ajout d&acute;un champs';
  var $AKF_EDITFIELD           = 'Edition d&acute;un champs';
  var $AKF_GENERAL             = 'General';
  var $AKF_FORM                = 'Formulaire:';
  var $AKF_TYPE                = 'Type:';
  var $AKF_VALUE               = 'Valeur:';
  var $AKF_STYLE               = 'Style:';
  var $AKF_REQUIRED            = 'Requis:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Edition des param�tres';
  var $AKF_MAILSUBJECT         = 'Objet de l&acute;Email:';
  var $AKF_SENDERNAME          = 'Nom de l&acute;exp�diteur:';
  var $AKF_SENDEREMAIL         = 'Email de l&acute;exp�diteur:';
  var $AKF_SETTINGSSAVED       = 'Les param�tres ont �t� sauv�.';
  var $AKF_SETTINGSNOTSAVED    = 'Les param�tres ne peuvent �tre sauv�s.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Formulaires stock�s';
  var $AKF_NUMBEROFENTRIES     = 'Nombre d&acute;enregistrements';
  var $AKF_STOREDDATA          = 'Donn�es stock�es';
  var $AKF_STOREDIP            = 'IP de l&acute;exp�diteur';
  var $AKF_STOREDDATE          = 'Date de l&acute;envoi';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Remplir tous les champs obligatoires svp:';
  var $AKF_REQUIREDFIELD       = 'Champs requis';
  var $AKF_BUTTONSEND          = 'Envoi';
  var $AKF_BUTTONCLEAR         = 'Effacer';
  var $AKF_FORMEXPIRED         = 'Ce formulaire n&acute;est plus valable!';
  var $AKF_FORMPENDING         = 'Ce formulaire est actuellement en suspend!';
  var $AKF_MAILERHELLO         = 'Bienvenue';
  var $AKF_MAILERHEADER        = 'Un utilisateur de votre site a utilis� un formulaire pour vous soumettre les donn�es suivantes:';
  var $AKF_MAILERFOOTER        = 'Cordialement';
  var $AKF_MAILERERROR         = 'Une erreur est survenue lors de l&acute;envoi de l&acute;Email �:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Attribuer le champs � un formulaire en utilisant le menu d�roulant.';
  var $AKF_HELPTITLE           = 'Entrer un titre court pour votre formulaire/champs dans ce champs.';
  var $AKF_HELPDESCRIPTION     = 'Utiliser ce champs de mani�re � d&acute;�crire en HTML les caract�ristiques du champs ou du formulaire.';
  var $AKF_HELPTYPE            = 'Choisir un champs parmis presque tous les champs standards ou pr�d�finis. Pour d&acute;autres sortes de champs, contacter Arthur Konze.';
  var $AKF_HELPVALUE           = 'La valeur du champs peut �tre utilis� de mani�re � assigner une valeur pr�d�finie � un champs. De mani�re � cr�er une liste d�roulante, entrer les diff�rentes options de la liste d�roulante dans des lignes s�par�es. Utiliser la m�me m�thode pour les boutons radio et les bo�tes de s�lection. Pour une case � coch�, le texte de description sera plac� � cot� du champs.';
  var $AKF_HELPSTYLE           = 'Utiliser l&acute;option de style de mani�re � ajouter une option CSS au champs. Exemple: pour obtenir un champs de 200 pixels de largeur, utiliser: width:200px;';
  var $AKF_HELPREQUIRED        = 'Choisir si le champs doit �tre rempli par l&acute;utilisateur ou non.';
  var $AKF_HELPORDERING        = 'Utiliser l&acute;option de classification de mani�re � d�terminer la position.';
  var $AKF_HELPSTARTFINISH     = 'Pour la publication, choisir la date de d�but et de fin.';
  var $AKF_HELPSENDMAIL        = 'Choisir si le r�sultat du formulaire doit ou non �tre envoy� par Email.';
  var $AKF_HELPEMAILS          = 'Entrer l&acute;adresse Email ici. S�parer diff�rentes adresse en utilisant la virgule (,).';
  var $AKF_HELPSAVEDB          = 'Choisir si le r�sultat du formulaire doit ou non �tre sauv� dans la base de donn�es.';
  var $AKF_HELPTARGET          = 'Choisir si la page suivante doit ou non �tre affich�e ou si l&acute;utilisateur doit �tre redirig� vers l_URL.';
  var $AKF_HELPTARGETURL       = 'Entrer une adresse de redirection ici. Il peut s&acute;agir de n&acute;importe qu&acute;el type d&acute;URL, y compris l&acute;adresse d&acute;un nouveau formulaire.';
  var $AKF_HELPSUBJECT         = 'Indiquer l&acute;objet des Emails dans ce champs.';
  var $AKF_HELPSENDER          = 'Le nom indiqu� dans ce champs sera utilis� comme exp�diteur des Emails.';
  var $AKF_HELPEMAIL           = 'Une adresse Email valide pour les courriers sortants.';
  var $AKF_HELPRESULT          = 'Choisir si le r�sultat du formulaire sera montr� ou nom � l&acute;utilisateur.';

  // NEW in version 1.01
  var $AKF_MAILCHARSET         = 'Email Charset:';
  var $AKF_HELPCHARSET         = 'Choose a charset for your outgoing emails from the dropdown menu.';
  var $AKF_MAILTABLEFIELD      = 'Field';
  var $AKF_MAILTABLEDATA       = 'Data';
  var $AKF_SELECTFIELD         = 'Select Field';

  // NEW in version 1.1
  var $AKF_LAYOUTSETTINGS      = 'Layout Settings';
  var $AKF_EMAILSETTINGS       = 'Email Settings';
  var $AKF_LAYOUTSTART         = 'Layout Start:';
  var $AKF_LAYOUTROW           = 'Layout Row:';
  var $AKF_LAYOUTEND           = 'Layout End:';
  var $AKF_EMAILTITLECSS       = 'Email Title Style:';
  var $AKF_EMAILROW1CSS        = 'Email Row1 Style:';
  var $AKF_EMAILROW2CSS        = 'Email Row2 Style:';
  var $AKF_HELPLAYOUTSTART     = 'The HTML code inside this field will be displayed on top of the form before the rows with the fields start.';
  var $AKF_HELPLAYOUTROW       = 'This code will be used for every field row. You can use the following substitutes: ###AFTFIELDTITLE###, ###AFTFIELDREQ###, ###AFTFIELDDESC### and ###AFTFIELD###.';
  var $AKF_HELPLAYOUTEND       = 'After the rows this html code will display at the end of the form. You can use the following substitutes: ###AFTSENDBUTTON### and ###AFTCLEARBUTTON###.';
  var $AKF_HELPEMAILTITLECSS   = 'Enter CSS definitions for the title row of the submitted form data.';
  var $AKF_HELPEMAILROW1CSS    = 'Enter CSS definitions for the 1st, 3rd, 5th, ... data row of the submitted form data.';
  var $AKF_HELPEMAILROW2CSS    = 'Enter CSS definitions for the 2nd, 4th, 6th, ... data row of the submitted form data.';

}

$AKFLANG =& new akfLanguage();

?>