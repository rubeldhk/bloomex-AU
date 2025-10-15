<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.0 beta 3, Finnish language file version 1.0
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Markku Suominen (info@antamis.com)
* Homepage   : www.konze.de
**/

defined( '_VALID_MOS' ) or die( 'P��sy estetty.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Kyll�';
  var $AKF_NO                  = 'Ei';
  var $AKF_PUBLISHED           = 'Julkaistu';
  var $AKF_PUBLISHING          = 'Julkaiseminen';
  var $AKF_STARTPUBLISHING     = 'Aloita julkaiseminen:';
  var $AKF_FINISHPUBLISHING    = 'Lopeta julkaiseminen:';
  var $AKF_PUBPENDING          = 'Julkaistu, odottaa';
  var $AKF_PUBCURRENT          = 'Julkaistu';
  var $AKF_PUBEXPIRED          = 'Julkaistu,  p��ttymisp�iv� ohitettu';
  var $AKF_UNPUBLISHED         = 'Ei julkaistu';
  var $AKF_REORDER             = 'J�rjest�';
  var $AKF_ORDERING            = 'J�rjestys:';
  var $AKF_TITLE               = 'Otsikko:';
  var $AKF_DESCRIPTION         = 'Kuvaus:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Muokkaa kielitiedostoa';
  var $AKF_PATH                = 'Polku:';
  var $AKF_FILEWRITEABLE       = 'Huomaa: Tiedosto ei saa olla kirjoitussuojattu.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Lomakkeiden hallinta';
  var $AKF_FORMTITLE           = 'Lomakkeen otsikko';
  var $AKF_SENDMAIL            = 'L�het� s�hk�posti';
  var $AKF_STOREDB             = 'Tallenna tietokantaan';
  var $AKF_FINISHING           = 'Lopputiedot';
  var $AKF_FORMPAGE            = 'Lomakesivu';
  var $AKF_REDIRECTION         = 'Uudelleenohjaus';
  var $AKF_SHOWRESULT          = 'N�yt� tulokset';
  var $AKF_NUMBEROFFIELDS      = 'Kenttien lukum��r�';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Lis�� lomake';
  var $AKF_EDITFORM            = 'Muokkaa lomaketta';
  var $AKF_HEADER              = 'Yl�tunniste';
  var $AKF_HANDLING            = 'K�sittely';
  var $AKF_SENDBYEMAIL         = 'L�het� s�hk�postitse:';
  var $AKF_EMAILS              = 'S�hk�postiosoitteet:';
  var $AKF_SAVETODATABASE      = 'Tallenna tietokantaan:';
  var $AKF_ENDPAGETITLE        = 'Loppusivun otsikko:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Loppusivun kuvaus:';
  var $AKF_FORMTARGET          = 'Lomakkeen kohde:';
  var $AKF_TARGETURL           = 'Uudelleeohjaus URL:';
  var $AKF_SHOWENDPAGE         = 'N�yt� loppusivu';
  var $AKF_REDIRECTTOURL       = 'Ohjaa sivulle (URL)';
  var $AKF_NEWFORMSLAST        = 'Uudet lomakkeet lis�t��n listan loppuun.';


  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Kenttien hallinta';
  var $AKF_FIELDTITLE          = 'Kent�n otsikko';
  var $AKF_FIELDTYPE           = 'Kent�n tyyppi';
  var $AKF_FIELDREQUIRED       = 'Kentt� vaaditaan';
  var $AKF_SELECTFORM          = 'Valitse lomake';
  var $AKF_ALLFORMS            = '- Kaikki lomakkeet';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Lis�� kentt�';
  var $AKF_EDITFIELD           = 'Muokkaa kentt��';
  var $AKF_GENERAL             = 'Yleinen';
  var $AKF_FORM                = 'Lomake:';
  var $AKF_TYPE                = 'Tyyppi:';
  var $AKF_VALUE               = 'Arvo:';
  var $AKF_STYLE               = 'Tyyli:';
  var $AKF_REQUIRED            = 'Vaaditaan:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Muokkaa asetuksia';
  var $AKF_MAILSUBJECT         = 'S�hk�postin aihe:';
  var $AKF_SENDERNAME          = 'L�hett�j�n nimi:';
  var $AKF_SENDEREMAIL         = 'L�hett�j�n s�hk�posti:';
  var $AKF_SETTINGSSAVED       = 'Asetukset on tallennettu.';
  var $AKF_SETTINGSNOTSAVED    = 'Asetuksia ei voitu tallentaa.';
  
  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Tallennetut lomakkeet';
  var $AKF_NUMBEROFENTRIES     = 'Rivej�';
  var $AKF_STOREDDATA          = 'Tallennetut tiedot';
  var $AKF_STOREDIP            = 'L�hett�j�n IP-osoite';
  var $AKF_STOREDDATE          = 'L�hetysp�iv�';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'T�yt� kaikki vaaditut kent�t:';
  var $AKF_REQUIREDFIELD       = 'Pakollinen kentt�';
  var $AKF_BUTTONSEND          = 'L�het�';
  var $AKF_BUTTONCLEAR         = 'Tyhjenn�';
  var $AKF_FORMEXPIRED         = 'Lomakkeen viimeinen julkaisup�iv� on mennyt.';
  var $AKF_FORMPENDING         = 'Lomake odottaa julkaisua.';
  var $AKF_MAILERHELLO         = 'Hei';
  var $AKF_MAILERHEADER        = 'Sivuston k�ytt�j� on l�hett�nyt seuraavat tiedot:';
  var $AKF_MAILERFOOTER        = 'Parhain terveisin';
  var $AKF_MAILERERROR         = 'Virhe s�hk�postin l�hett�misess�:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Valitse lomake.';
  var $AKF_HELPTITLE           = 'Lomakkeen/kent�n otsikko.';
  var $AKF_HELPDESCRIPTION     = 'Lomakkeen/kent�n html-muotoinen kuvaus.';
  var $AKF_HELPTYPE            = 'Valitse kent�n tyyppi. Jos tarvitset yksil�idyn Tyyppi-valikon, ota yhteytt� Arthur Konzeen.';
  var $AKF_HELPVALUE           = 'T�m�n kent�n avulla voit asettaa oletusarvon. Jos haluat luoda DROPDOWN-tyyppisen kent�n, kirjoita jokainen valinta omalle rivilleen. Sama koskee my�s RADIOBUTTON - ja SELECTBOX -tyyppej�. Jos k�yt�t CHECKBOX-tyyppist� kentt��, teksti n�kyy selitteen� kent�n vieress�.';
  var $AKF_HELPSTYLE           = 'Voit asettaa kent�lle CSS-tyylim��rittelyn. Jos haluat kent�n leveydeksi 200 pikseli�, kirjoita: width:200px;';
  var $AKF_HELPREQUIRED        = 'Onko kentt� pakollinen.';
  var $AKF_HELPORDERING        = 'Valitse j�rjestys.';
  var $AKF_HELPSTARTFINISH     = 'Valitse julkaisemisen alku- ja loppup�iv�.';
  var $AKF_HELPSENDMAIL        = 'L�hetet��nk� lomakkeen tiedot s�hk�postitse.';
  var $AKF_HELPEMAILS          = 'S�hk�postiosoite johon lomakkeen tiedot l�hetet��n. Jos useampi kuin yksi osoite, erota osoitteet pilkulla (,).';
  var $AKF_HELPSAVEDB          = 'Tallennetaanko lomakkeen tiedot tietokantaan.';
  var $AKF_HELPTARGET          = 'N�ytet��nk� k�ytt�j�lle loppusivu vai ohjataanko k�ytt�j� toiselle sivulle (URL).';
  var $AKF_HELPTARGETURL       = 'Anna osoite (URL) johon k�ytt�j� ohjataan. Osoite voi olla mik� tahansa URL tai jopa toinen lomake.';
  var $AKF_HELPSUBJECT         = 'L�htevien viestien aihe.';
  var $AKF_HELPSENDER          = 'L�hteviss� viesteiss� n�kyv� l�hett�j�n nimi.';
  var $AKF_HELPEMAIL           = 'L�hteviss� viesteiss� n�kyv� l�hett�j�n s�hk�posti.';
  var $AKF_HELPRESULT          = 'N�ytet��nk� lomakkeen tiedot k�ytt�j�lle.';

}

$AKFLANG =& new akfLanguage();

?>