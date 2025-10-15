<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.11 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : ???
* Homepage   : ???
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Ja';
  var $AKF_NO                  = 'Nee';
  var $AKF_PUBLISHED           = 'Gepubliseerd';
  var $AKF_PUBLISHING          = 'Publiseren';
  var $AKF_STARTPUBLISHING     = 'Start Publicatie:';
  var $AKF_FINISHPUBLISHING    = 'Einde Publicatie:';
  var $AKF_PUBPENDING          = 'Gepubliseerd, maar in de wacht';
  var $AKF_PUBCURRENT          = 'Gepubliseerd en actueel';
  var $AKF_PUBEXPIRED          = 'Gepubliseerd, maar verlopen';
  var $AKF_UNPUBLISHED         = 'Niet gepubliseerd';
  var $AKF_REORDER             = 'Sorteren';
  var $AKF_ORDERING            = 'Sortering:';
  var $AKF_TITLE               = 'Titel:';
  var $AKF_DESCRIPTION         = 'Onderwerp:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Wijzig taal';
  var $AKF_PATH                = 'Pad:';
  var $AKF_FILEWRITEABLE       = 'opmerking: het bestand moet schrijfbaar zijn om wijzigingen te kunnen bewaren.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Formulier Manager';
  var $AKF_FORMTITLE           = 'Formulier Titel';
  var $AKF_SENDMAIL            = 'Verzend Email';
  var $AKF_STOREDB             = 'Opslaan in DB';
  var $AKF_FINISHING           = 'Einde';
  var $AKF_FORMPAGE            = 'Formulier pagina';
  var $AKF_REDIRECTION         = 'Redirect';
  var $AKF_SHOWRESULT          = 'Toon resultaat';
  var $AKF_NUMBEROFFIELDS      = 'Aantal velden';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Nieuw Formulier';
  var $AKF_EDITFORM            = 'Wijzig Formulier';
  var $AKF_HEADER              = 'Kop';
  var $AKF_HANDLING            = 'Actie';
  var $AKF_SENDBYEMAIL         = 'Verzend met Email:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'Opslaan in de database:';
  var $AKF_ENDPAGETITLE        = 'Einde Pagina titel:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Einde Pagina omschrijving:';
  var $AKF_FORMTARGET          = 'Formulier doel:';
  var $AKF_TARGETURL           = 'Redirect URL:';
  var $AKF_SHOWENDPAGE         = 'Toon einde pagina';
  var $AKF_REDIRECTTOURL       = 'Redirect naar URL';
  var $AKF_NEWFORMSLAST        = 'Nieuw Formulier komt op de laatste positie.';
  var $AKF_SHOWFORMRESULT      = 'Toon formulier resultaten:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Veld Manager';
  var $AKF_FIELDTITLE          = 'Veld Titel';
  var $AKF_FIELDTYPE           = 'Veld Type';
  var $AKF_FIELDREQUIRED       = 'Veld verplicht';
  var $AKF_SELECTFORM          = 'Selecteer formulier';
  var $AKF_ALLFORMS            = '- Alle Formulieren';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Nieuw veld';
  var $AKF_EDITFIELD           = 'Wijzig veld';
  var $AKF_GENERAL             = 'General';
  var $AKF_FORM                = 'Formulier:';
  var $AKF_TYPE                = 'Type:';
  var $AKF_VALUE               = 'Waarde:';
  var $AKF_STYLE               = 'Stijl:';
  var $AKF_REQUIRED            = 'Verplicht:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Wijzig instellingen';
  var $AKF_MAILSUBJECT         = 'Email onderwerp:';
  var $AKF_SENDERNAME          = 'Naam afzender:';
  var $AKF_SENDEREMAIL         = 'Email afzender:';
  var $AKF_SETTINGSSAVED       = 'Instellingen zijn opgeslagen.';
  var $AKF_SETTINGSNOTSAVED    = 'Instellingen konden niet worden opgeslagen.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Opgeslagen formulieren';
  var $AKF_NUMBEROFENTRIES     = 'Aantal velden';
  var $AKF_STOREDDATA          = 'Opgeslagen Data';
  var $AKF_STOREDIP            = 'Afzender IP';
  var $AKF_STOREDDATE          = 'Datum verzending';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'A.u.b. alle verplichte velden invullen:';
  var $AKF_REQUIREDFIELD       = 'Verplichte velden';
  var $AKF_BUTTONSEND          = 'Versturen';
  var $AKF_BUTTONCLEAR         = 'Wissen';
  var $AKF_FORMEXPIRED         = 'Dit formulier is verlopen!';
  var $AKF_FORMPENDING         = 'Dit formulier staat in de wacht!';
  var $AKF_MAILERHELLO         = 'Hallo';
  var $AKF_MAILERHEADER        = 'Een bezoeker heeft een formulier verzonden met onderstaande details:';
  var $AKF_MAILERFOOTER        = 'Vriendelijke groet';
  var $AKF_MAILERERROR         = 'Een email fout is opgetreden bij een poging om mail te versturen aan:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'koppel een veld aan een formulier.';
  var $AKF_HELPTITLE           = 'Geef hier een korte titel op voor het formulier/veld.';
  var $AKF_HELPDESCRIPTION     = 'Hier kan je een (HTML)omschrijving opgeven voor het formulier/veld.';
  var $AKF_HELPTYPE            = 'Maak een keuze uit 1 van de standaard type formuliervelden.';
  var $AKF_HELPVALUE           = 'Het waarde veld bevat een standaard ingevulde waarde . Om een dropdown lijst te maken, zet je de waarden elk op een eigen regel. Dit geld ook voor de Keuzebox en selectiebox.';
  var $AKF_HELPSTYLE           = 'Gebruik de stijl optie omm CSS definities aan een veld toe te voegen. Voorbeeld: om een veld 200px breed te maken geef je in : width:200px;';
  var $AKF_HELPREQUIRED        = 'Geef aan of een veld verplicht is.';
  var $AKF_HELPORDERING        = 'Gebruik de sortering om de volgorde van velden te bepalen.';
  var $AKF_HELPSTARTFINISH     = 'Kies een begin en einddatum om je formulier te publiceren.';
  var $AKF_HELPSENDMAIL        = 'Geef aan of de resultaten wel of niet per email verzonden moeten worden.';
  var $AKF_HELPEMAILS          = 'Geef hier een email adres in. Je kunt meerdere adressen opgeven gescheiden door een komma (,).';
  var $AKF_HELPSAVEDB          = 'Formulier resultaten wel of niet opslaan in de databse.';
  var $AKF_HELPTARGET          = 'Moeten de resultaten op het scherm worden getoond? of wordt de bezoeker doorgestuurd naar onderstaande URL.';
  var $AKF_HELPTARGETURL       = 'Geef een URL op waanaar de bezoeker wordt doorgestuurd. Dit kan een willeurige URL of een ander formulier zijn ';
  var $AKF_HELPSUBJECT         = 'Geef hier een onderwerp op voor alle uitgaande email.';
  var $AKF_HELPSENDER          = 'De hier ingevoerde naam wordt als afzender gebruikt in uitgaande email.';
  var $AKF_HELPEMAIL           = 'Een geldig email adres van de afzender voor uitgaande email.';
  var $AKF_HELPRESULT          = 'Geef aan of je de resultaten van het formulier aan de gebruiker wil tonene.';

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

$AKFLANG =new  akfLanguage();

?>