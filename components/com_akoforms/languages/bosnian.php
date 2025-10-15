<?php
/**
* Translation provided by Bosnian Mambo Community (BMC)
* Translated by: Emir Sefiæ
* Homepage: http://mambo.bhtechnet.com
* Encoding: ISO-8859-2
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Homepage   : www.konze.de
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Da';
  var $AKF_NO                  = 'Ne';
  var $AKF_PUBLISHED           = 'Objavljeno';
  var $AKF_PUBLISHING          = 'Objavljivanje';
  var $AKF_STARTPUBLISHING     = 'Objavi:';
  var $AKF_FINISHPUBLISHING    = 'Zavr¹i:';
  var $AKF_PUBPENDING          = 'Objavljeno ali na èekanju';
  var $AKF_PUBCURRENT          = 'Objavljeno';
  var $AKF_PUBEXPIRED          = 'Objavljeno, ali isteklo';
  var $AKF_UNPUBLISHED         = 'Nije objavljeno';
  var $AKF_REORDER             = 'Uredi redoslijed';
  var $AKF_ORDERING            = 'Redoslijed:';
  var $AKF_TITLE               = 'Naslov:';
  var $AKF_DESCRIPTION         = 'Opis:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Uredi prevod';
  var $AKF_PATH                = 'Path:';
  var $AKF_FILEWRITEABLE       = 'Napomena: Datoteka mora biti writable da bi ste saèuvali promjene.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Menad¾er formulara';
  var $AKF_FORMTITLE           = 'Naslov formulara';
  var $AKF_SENDMAIL            = 'Po¹alji email';
  var $AKF_STOREDB             = 'Saèuvaj u DB';
  var $AKF_FINISHING           = 'Zavr¹avanje';
  var $AKF_FORMPAGE            = 'Strana';
  var $AKF_REDIRECTION         = 'Preusmjerenje';
  var $AKF_SHOWRESULT          = 'Prika¾i rezultat';
  var $AKF_NUMBEROFFIELDS      = 'Broj polja';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Dodaj formular';
  var $AKF_EDITFORM            = 'Uredi formular';
  var $AKF_HEADER              = 'Header';
  var $AKF_HANDLING            = 'Rukovanje';
  var $AKF_SENDBYEMAIL         = 'Po¹alji email:';
  var $AKF_EMAILS              = 'Email:';
  var $AKF_SAVETODATABASE      = 'Saèuvaj u DB:';
  var $AKF_ENDPAGETITLE        = 'Naslov kraja stranice:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Opis kraja stranice:';
  var $AKF_FORMTARGET          = 'Target formulara:';
  var $AKF_TARGETURL           = 'Redirekcijski URL:';
  var $AKF_SHOWENDPAGE         = 'Prika¾i kraj strane';
  var $AKF_REDIRECTTOURL       = 'Usmjeri prema URL';
  var $AKF_NEWFORMSLAST        = 'Novi formulari idu na zadnje mjesto.';
  var $AKF_SHOWFORMRESULT      = 'Prika¾i iz rezultata:';

  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Menad¾er polja';
  var $AKF_FIELDTITLE          = 'Naslov polja';
  var $AKF_FIELDTYPE           = 'Tip polja';
  var $AKF_FIELDREQUIRED       = 'Polje obavezno';
  var $AKF_SELECTFORM          = 'Izaberi formular';
  var $AKF_ALLFORMS            = '- Svi formulari';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Dodaj polje';
  var $AKF_EDITFIELD           = 'Uredi polje';
  var $AKF_GENERAL             = 'Op¹te';
  var $AKF_FORM                = 'Formuar:';
  var $AKF_TYPE                = 'Tip:';
  var $AKF_VALUE               = 'Vrijednost:';
  var $AKF_STYLE               = 'Stil:';
  var $AKF_REQUIRED            = 'Obavezno:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Uredi postavke';
  var $AKF_MAILSUBJECT         = 'Naslov email poruke:';
  var $AKF_SENDERNAME          = 'Ime po¹iljaoca:';
  var $AKF_SENDEREMAIL         = 'Email po¹iljaoca:';
  var $AKF_SETTINGSSAVED       = 'Postavke su saèuvane.';
  var $AKF_SETTINGSNOTSAVED    = 'Postavke nisu saèuvane.';

  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Saèuvani formulari';
  var $AKF_NUMBEROFENTRIES     = 'Broj unosa';
  var $AKF_STOREDDATA          = 'Saèuvani podaci';
  var $AKF_STOREDIP            = 'IP adresa po¹iljaoca';
  var $AKF_STOREDDATE          = 'Datum slanja';

  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Molimo Vas popunite sva obavezna polja:';
  var $AKF_REQUIREDFIELD       = 'Obavezno polje';
  var $AKF_BUTTONSEND          = 'Po¹alji';
  var $AKF_BUTTONCLEAR         = 'Oèisti';
  var $AKF_FORMEXPIRED         = 'Ovaj formular je istekao!';
  var $AKF_FORMPENDING         = 'Ovaj formular je trenutno na èekanju!';
  var $AKF_MAILERHELLO         = 'Po¹tovanje';
  var $AKF_MAILERHEADER        = 'Posjetilac sa Va¹e stranice je poslao slijedeæe podatke:';
  var $AKF_MAILERFOOTER        = 'S po¹tovanjem,';
  var $AKF_MAILERERROR         = 'Do¹lo je do gre¹ke tokom slanja poruke za:';

  // Help - Forms & Fields
  var $AKF_HELPFORM            = 'Dodijeli polje odreðenom formularu koristeæi dropdown izbor.';
  var $AKF_HELPTITLE           = 'Unesi kratki naslov Va¹eg formulara/polja.';
  var $AKF_HELPDESCRIPTION     = 'Ovo polje mo¾ete koristiti kako bi ste postavili opsi Va¹eg formulara/polja.';
  var $AKF_HELPTYPE            = 'Izaberite izmeðu standardnih i predefinisanih polja. Ukoliko Vam treba poseban dropdown, kontaktirajte Arthur Konze.';
  var $AKF_HELPVALUE           = 'Vrijednost polja (value) mo¾e biti kori¹teno kako bi unijeli predefinisanu vrijednost. Da bi ste kreirali DROPDOWN meni svaku vrijednost morate unijeti u novoj liniji. Isto se odnosi na RADIOBUTTON i SELECTBOXES. U CHECKBOX formatu æe biti prikazano kao opisni tekst iza polja.';
  var $AKF_HELPSTYLE           = 'Koristite style opciju kako bi ste dodali CSS vrijednosti za polje. Na primjer ako ¾elite da je polje 200 px ¹irine unesite: width:200px;';
  var $AKF_HELPREQUIRED        = 'Izaberite da li polje mora biti popunjeno ili ne.';
  var $AKF_HELPORDERING        = 'Koristite redoslijed kako bi ste odabrali poziciju.';
  var $AKF_HELPSTARTFINISH     = 'Izaberite datum poèetka i zav¹etka vrijednosti formulara koristeæi ove dvije opcije.';
  var $AKF_HELPSENDMAIL        = 'Izaberite da li rezultate treba slati email porukom ili ne.';
  var $AKF_HELPEMAILS          = 'Unesite email adresu ovdje. Mo¾ete unijeti i vi¹e adresa odvajajuæi ih zarezom (,).';
  var $AKF_HELPSAVEDB          = 'Izaberite da li rezultati trebaju biti saèuvani u databazi.';
  var $AKF_HELPTARGET          = 'Izaberite da li gornja stranica treba biti prikazana ili æe posjetilac biti redirektovan na donji URL.';
  var $AKF_HELPTARGETURL       = 'Ovdje unesite URL za redirekciju. Mo¾e biti bilo koji URL, èak i drugi formular.';
  var $AKF_HELPSUBJECT         = 'Unesite naslov za sve odlazeæe emial poruke.';
  var $AKF_HELPSENDER          = 'Ime koje ovdje unesete bit æe kori¹teno kao ime po¹iljaoca.';
  var $AKF_HELPEMAIL           = 'Validna email adresa za odlazeæe email poruke.';
  var $AKF_HELPRESULT          = 'Da li da prika¾em rezultat posjetiocu?.';

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