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
  var $AKF_PUBLISHED           = 'Publié';
  var $AKF_PUBLISHING          = 'Publication';
  var $AKF_STARTPUBLISHING     = 'Début de la publication:';
  var $AKF_FINISHPUBLISHING    = 'Fin de la publication:';
  var $AKF_PUBPENDING          = 'Publié, mais suspendu';
  var $AKF_PUBCURRENT          = 'Publié et actuel';
  var $AKF_PUBEXPIRED          = 'Publié, mais expiré';
  var $AKF_UNPUBLISHED         = 'Pas publié';
  var $AKF_REORDER             = 'Réorganisé';
  var $AKF_ORDERING            = 'Ordre:';
  var $AKF_TITLE               = 'Titre:';
  var $AKF_DESCRIPTION         = 'Description:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Edition langue';
  var $AKF_PATH                = 'Chemin:';
  var $AKF_FILEWRITEABLE       = 'Note: de manière à sauver vos modifications, les droits d&acute;ériture sur le ficher doivent être accordés.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Gestionaire de formulaire';
  var $AKF_FORMTITLE           = 'Titre du formulaire';
  var $AKF_SENDMAIL            = 'Envoi d&acute;Email';
  var $AKF_STOREDB             = 'Enregistrement de la base de données';
  var $AKF_FINISHING           = 'Terminé';
  var $AKF_FORMPAGE            = 'Page du formulaire';
  var $AKF_REDIRECTION         = 'Redirection';
  var $AKF_SHOWRESULT          = 'Visualisation du résultat';
  var $AKF_NUMBEROFFIELDS      = 'Nombre de champs';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Ajout d&acute;un formulaire';
  var $AKF_EDITFORM            = 'Edition d&acute;un formulaire';
  var $AKF_HEADER              = 'Entête';
  var $AKF_HANDLING            = 'Traitement';
  var $AKF_SENDBYEMAIL         = 'Envoi par Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Enregistrement dans la base de données:';
  var $AKF_ENDPAGETITLE        = 'Fin du titre de la page:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Fin de la description de la page:';
  var $AKF_FORMTARGET          = 'Cible du formulaire:';
  var $AKF_TARGETURL           = 'Redirection URL:';
  var $AKF_SHOWENDPAGE         = 'Montre le pied de page';
  var $AKF_REDIRECTTOURL       = 'Réacheminer vers l&acute;URL';
  var $AKF_NEWFORMSLAST        = 'Le nouveau formulaire est placé à la fin.';
  var $AKF_SHOWFORMRESULT      = 'Montre le résultat du formulaire:';

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
  var $AKF_EDITSETTINGS        = 'Edition des paramètres';
  var $AKF_MAILSUBJECT         = 'Objet de l&acute;Email:';
  var $AKF_SENDERNAME          = 'Nom de l&acute;expéditeur:';
  var $AKF_SENDEREMAIL         = 'Email de l&acute;expéditeur:';
  var $AKF_SETTINGSSAVED       = 'Les paramètres ont été sauvé.';
  var $AKF_SETTINGSNOTSAVED    = 'Les paramètres ne peuvent être sauvés.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Formulaires stockés';
  var $AKF_NUMBEROFENTRIES     = 'Nombre d&acute;enregistrements';
  var $AKF_STOREDDATA          = 'Données stockées';
  var $AKF_STOREDIP            = 'IP de l&acute;expéditeur';
  var $AKF_STOREDDATE          = 'Date de l&acute;envoi';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Remplir tous les champs obligatoires svp:';
  var $AKF_REQUIREDFIELD       = 'Champs requis';
  var $AKF_BUTTONSEND          = 'Envoi';
  var $AKF_BUTTONCLEAR         = 'Effacer';
  var $AKF_FORMEXPIRED         = 'Ce formulaire n&acute;est plus valable!';
  var $AKF_FORMPENDING         = 'Ce formulaire est actuellement en suspend!';
  var $AKF_MAILERHELLO         = 'Bienvenue';
  var $AKF_MAILERHEADER        = 'Un utilisateur de votre site a utilisé un formulaire pour vous soumettre les données suivantes:';
  var $AKF_MAILERFOOTER        = 'Cordialement';
  var $AKF_MAILERERROR         = 'Une erreur est survenue lors de l&acute;envoi de l&acute;Email à:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Attribuer le champs à un formulaire en utilisant le menu déroulant.';
  var $AKF_HELPTITLE           = 'Entrer un titre court pour votre formulaire/champs dans ce champs.';
  var $AKF_HELPDESCRIPTION     = 'Utiliser ce champs de manière à d&acute;écrire en HTML les caractéristiques du champs ou du formulaire.';
  var $AKF_HELPTYPE            = 'Choisir un champs parmis presque tous les champs standards ou prédéfinis. Pour d&acute;autres sortes de champs, contacter Arthur Konze.';
  var $AKF_HELPVALUE           = 'La valeur du champs peut être utilisé de manière à assigner une valeur prédéfinie à un champs. De manière à créer une liste déroulante, entrer les différentes options de la liste déroulante dans des lignes séparées. Utiliser la même méthode pour les boutons radio et les boîtes de sélection. Pour une case à coché, le texte de description sera placé à coté du champs.';
  var $AKF_HELPSTYLE           = 'Utiliser l&acute;option de style de manière à ajouter une option CSS au champs. Exemple: pour obtenir un champs de 200 pixels de largeur, utiliser: width:200px;';
  var $AKF_HELPREQUIRED        = 'Choisir si le champs doit être rempli par l&acute;utilisateur ou non.';
  var $AKF_HELPORDERING        = 'Utiliser l&acute;option de classification de manière à déterminer la position.';
  var $AKF_HELPSTARTFINISH     = 'Pour la publication, choisir la date de début et de fin.';
  var $AKF_HELPSENDMAIL        = 'Choisir si le résultat du formulaire doit ou non être envoyé par Email.';
  var $AKF_HELPEMAILS          = 'Entrer l&acute;adresse Email ici. Séparer différentes adresse en utilisant la virgule (,).';
  var $AKF_HELPSAVEDB          = 'Choisir si le résultat du formulaire doit ou non être sauvé dans la base de données.';
  var $AKF_HELPTARGET          = 'Choisir si la page suivante doit ou non être affichée ou si l&acute;utilisateur doit être redirigé vers l_URL.';
  var $AKF_HELPTARGETURL       = 'Entrer une adresse de redirection ici. Il peut s&acute;agir de n&acute;importe qu&acute;el type d&acute;URL, y compris l&acute;adresse d&acute;un nouveau formulaire.';
  var $AKF_HELPSUBJECT         = 'Indiquer l&acute;objet des Emails dans ce champs.';
  var $AKF_HELPSENDER          = 'Le nom indiqué dans ce champs sera utilisé comme expéditeur des Emails.';
  var $AKF_HELPEMAIL           = 'Une adresse Email valide pour les courriers sortants.';
  var $AKF_HELPRESULT          = 'Choisir si le résultat du formulaire sera montré ou nom à l&acute;utilisateur.';

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