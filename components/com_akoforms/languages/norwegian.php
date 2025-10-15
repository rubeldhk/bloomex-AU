<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : ...
* Homepage   : ...
**/

defined( '_VALID_MOS' ) or die( 'Direkte adgang til denne filen er ikke tillatt.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Ja';
  var $AKF_NO                  = 'Nei';
  var $AKF_PUBLISHED           = 'Publisert';
  var $AKF_PUBLISHING          = 'Publisere';
  var $AKF_STARTPUBLISHING     = 'Start Publisering:';
  var $AKF_FINISHPUBLISHING    = 'Avslutt Publisering:';
  var $AKF_PUBPENDING          = 'Publisert, men Parkert';
  var $AKF_PUBCURRENT          = 'Publisert og Gyldig';
  var $AKF_PUBEXPIRED          = 'Publisert, men er Utgått';
  var $AKF_UNPUBLISHED         = 'Ikke Publisert';
  var $AKF_REORDER             = 'Omsertere';
  var $AKF_ORDERING            = 'Sortere:';
  var $AKF_TITLE               = 'Tittel:';
  var $AKF_DESCRIPTION         = 'Beskrivelse:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Endre Språk';
  var $AKF_PATH                = 'Bane/Path:';
  var $AKF_FILEWRITEABLE       = 'NB!: Filen må være skrivbar til å kunne lagre endringene dine.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Skjema Administrasjon';
  var $AKF_FORMTITLE           = 'Skjema Tittel';
  var $AKF_SENDMAIL            = 'Sende Email';
  var $AKF_STOREDB             = 'Lagre DB';
  var $AKF_FINISHING           = 'Avslutte';
  var $AKF_FORMPAGE            = 'Skjema Side';
  var $AKF_REDIRECTION         = 'Omdirigering';
  var $AKF_SHOWRESULT          = 'Vis Resultat';
  var $AKF_NUMBEROFFIELDS      = 'Ant. Felt';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Legg til Skjema';
  var $AKF_EDITFORM            = 'Redigere Skjema';
  var $AKF_HEADER              = 'Header';
  var $AKF_HANDLING            = 'Handling';
  var $AKF_SENDBYEMAIL         = 'Sende via Email:';
  var $AKF_EMAILS              = 'Emailer:';
  var $AKF_SAVETODATABASE      = 'Lagre i Databasen:';
  var $AKF_ENDPAGETITLE        = 'Tittel for Siste siden:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Beskrivelse for Siste siden:';
  var $AKF_FORMTARGET          = 'Skjema Target:';
  var $AKF_TARGETURL           = 'Omdirigerings URL:';
  var $AKF_SHOWENDPAGE         = 'Vis Siste side';
  var $AKF_REDIRECTTOURL       = 'Omdiriger til URL';
  var $AKF_NEWFORMSLAST        = 'Nye skjemaer kommer til slutt.';
  var $AKF_SHOWFORMRESULT      = 'Vis resultat skjema:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Felt Administrasjon';
  var $AKF_FIELDTITLE          = 'Felt Tittel';
  var $AKF_FIELDTYPE           = 'Felt Type';
  var $AKF_FIELDREQUIRED       = 'Velg Obligatorisk';
  var $AKF_SELECTFORM          = 'Velg Skjema';
  var $AKF_ALLFORMS            = '- Alle Skjema';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Legge til Felt';
  var $AKF_EDITFIELD           = 'Redigere Felt';
  var $AKF_GENERAL             = 'Generellelt';
  var $AKF_FORM                = 'Skjema:';
  var $AKF_TYPE                = 'Type:';
  var $AKF_VALUE               = 'Verdi:';
  var $AKF_STYLE               = 'Stil:';
  var $AKF_REQUIRED            = 'Obligatorisk:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Edit Settings';
  var $AKF_MAILSUBJECT         = 'Email Subject:';
  var $AKF_SENDERNAME          = 'Sender Name:';
  var $AKF_SENDEREMAIL         = 'Sender Email:';
  var $AKF_SETTINGSSAVED       = 'Stillingene har blitt lagret.';
  var $AKF_SETTINGSNOTSAVED    = 'Stillingene kunne ikke lagres.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Lagrete skjemaer';
  var $AKF_NUMBEROFENTRIES     = 'Ant. innslag';
  var $AKF_STOREDDATA          = 'Lagret Data';
  var $AKF_STOREDIP            = 'Avsender IP';
  var $AKF_STOREDDATE          = 'Avsendt Dato';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Vennligst fylle ut alle obligatoriske felt:';
  var $AKF_REQUIREDFIELD       = 'Obligatorisk felt';
  var $AKF_BUTTONSEND          = 'Sende';
  var $AKF_BUTTONCLEAR         = 'Rense';
  var $AKF_FORMEXPIRED         = 'Dette skjemaet er utgått!';
  var $AKF_FORMPENDING         = 'Dette skjemaet er for tiden parkert!';
  var $AKF_MAILERHELLO         = 'Hallo';
  var $AKF_MAILERHEADER        = 'En bruker av hjemmesiden har brukt et skjema og fölgende data er kommet til deg:';
  var $AKF_MAILERFOOTER        = 'Vennlig';
  var $AKF_MAILERERROR         = 'Det oppstod en feil ved sending til:';

  // Help - Forms & Fields
  var $AKF_HELPFORM            = 'Tilknytte feltet til et bestemt skjema ved å bruke rullegardinsmenyen.';
  var $AKF_HELPTITLE           = 'Fyll ut en kort beskrivelse for skjemaet/feltet her.';
  var $AKF_HELPDESCRIPTION     = 'Du kan bruke  dette feltet til å sette inn html kompatibel beskrivelse for feltet/skjemaet.';
  var $AKF_HELPTYPE            = 'Velg mellom nesten alle standard skjema felt og et utvalg av forhåndsdefinerte felt. Dersom du har behov for spesialtilpasset rullegardinsmeny, kontakt Arthur Konze.';
  var $AKF_HELPVALUE           = 'Verdien angitt i et felt kan brukes til å tilknytte en forhåndsdefinert verdi for et felt. For å lage Rullegardinsmeny, trenger du bare å angi de valgmuligheter menyen skal innholde i separate linjer. Det samme gjelder Radioknapper og Avmerkingsbokser. I en Avmerkingsboks vil angitte verdier, vises som en beskrivende tekst bak boksen.';
  var $AKF_HELPSTYLE           = 'Bruk Stilvalget for å legge til CSS definisjoner til et felt. F.eks. for å gi et felt 200 pixler bredde blir det gjordt slik: width:200px;';
  var $AKF_HELPREQUIRED        = 'Angi om feltet er obligatorisk og skal fylles ut eller ikke.';
  var $AKF_HELPORDERING        = 'Bruk sortering til å velge posisjonering.';
  var $AKF_HELPSTARTFINISH     = 'Velg start og slutt dato for publisering ved å bruke disse to valgmulighetene.';
  var $AKF_HELPSENDMAIL        = 'Angi om resultat fra skjemaet skal sendes med email eller ikke.';
  var $AKF_HELPEMAILS          = 'Angi email addresser her. Du kan oppgi flere adresser ved å adskille dem med et komma (,).';
  var $AKF_HELPSAVEDB          = 'Angi om resultat fra skjemaet skal lagres i databasen.';
  var $AKF_HELPTARGET          = 'Angi om siden ovenfor skal vises eller om brukeren skal omdirigeres til URL´en angitt nedenfor.';
  var $AKF_HELPTARGETURL       = 'Angi her URL´en som brukeren omdirigeres til. Det være hvilen som helst URL til og med et annet skjema.';
  var $AKF_HELPSUBJECT         = 'Enter a subject for all outgoing emails into this field.';
  var $AKF_HELPSENDER          = 'Navnet som oppgies her vil bli brukt som avsender adresse for utgående epost.';
  var $AKF_HELPEMAIL           = 'Gyldig emailadresse til avsender av utgående epost.';
  var $AKF_HELPRESULT          = 'Angi om du vil få resultat fra skjemaet vist til brukeren.';

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