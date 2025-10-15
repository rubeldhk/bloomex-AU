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

defined( '_VALID_MOS' ) or die( 'Pääsy estetty.' );

class akfLanguage {

  // Admin - Misc
  var $AKF_YES                 = 'Kyllä';
  var $AKF_NO                  = 'Ei';
  var $AKF_PUBLISHED           = 'Julkaistu';
  var $AKF_PUBLISHING          = 'Julkaiseminen';
  var $AKF_STARTPUBLISHING     = 'Aloita julkaiseminen:';
  var $AKF_FINISHPUBLISHING    = 'Lopeta julkaiseminen:';
  var $AKF_PUBPENDING          = 'Julkaistu, odottaa';
  var $AKF_PUBCURRENT          = 'Julkaistu';
  var $AKF_PUBEXPIRED          = 'Julkaistu,  päättymispäivä ohitettu';
  var $AKF_UNPUBLISHED         = 'Ei julkaistu';
  var $AKF_REORDER             = 'Järjestä';
  var $AKF_ORDERING            = 'Järjestys:';
  var $AKF_TITLE               = 'Otsikko:';
  var $AKF_DESCRIPTION         = 'Kuvaus:';

  // Admin - Editing Language
  var $AKF_EDITLANGUAGE        = 'Muokkaa kielitiedostoa';
  var $AKF_PATH                = 'Polku:';
  var $AKF_FILEWRITEABLE       = 'Huomaa: Tiedosto ei saa olla kirjoitussuojattu.';

  // Admin - View Forms
  var $AKF_FORMMANAGER         = 'Lomakkeiden hallinta';
  var $AKF_FORMTITLE           = 'Lomakkeen otsikko';
  var $AKF_SENDMAIL            = 'Lähetä sähköposti';
  var $AKF_STOREDB             = 'Tallenna tietokantaan';
  var $AKF_FINISHING           = 'Lopputiedot';
  var $AKF_FORMPAGE            = 'Lomakesivu';
  var $AKF_REDIRECTION         = 'Uudelleenohjaus';
  var $AKF_SHOWRESULT          = 'Näytä tulokset';
  var $AKF_NUMBEROFFIELDS      = 'Kenttien lukumäärä';

  // Admin - Add/Edit Form
  var $AKF_ADDFORM             = 'Lisää lomake';
  var $AKF_EDITFORM            = 'Muokkaa lomaketta';
  var $AKF_HEADER              = 'Ylätunniste';
  var $AKF_HANDLING            = 'Käsittely';
  var $AKF_SENDBYEMAIL         = 'Lähetä sähköpostitse:';
  var $AKF_EMAILS              = 'Sähköpostiosoitteet:';
  var $AKF_SAVETODATABASE      = 'Tallenna tietokantaan:';
  var $AKF_ENDPAGETITLE        = 'Loppusivun otsikko:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Loppusivun kuvaus:';
  var $AKF_FORMTARGET          = 'Lomakkeen kohde:';
  var $AKF_TARGETURL           = 'Uudelleeohjaus URL:';
  var $AKF_SHOWENDPAGE         = 'Näytä loppusivu';
  var $AKF_REDIRECTTOURL       = 'Ohjaa sivulle (URL)';
  var $AKF_NEWFORMSLAST        = 'Uudet lomakkeet lisätään listan loppuun.';


  // Admin - View Fields
  var $AKF_FIELDMANAGER        = 'Kenttien hallinta';
  var $AKF_FIELDTITLE          = 'Kentän otsikko';
  var $AKF_FIELDTYPE           = 'Kentän tyyppi';
  var $AKF_FIELDREQUIRED       = 'Kenttä vaaditaan';
  var $AKF_SELECTFORM          = 'Valitse lomake';
  var $AKF_ALLFORMS            = '- Kaikki lomakkeet';

  // Admin - Add/Edit Fields
  var $AKF_ADDFIELD            = 'Lisää kenttä';
  var $AKF_EDITFIELD           = 'Muokkaa kenttää';
  var $AKF_GENERAL             = 'Yleinen';
  var $AKF_FORM                = 'Lomake:';
  var $AKF_TYPE                = 'Tyyppi:';
  var $AKF_VALUE               = 'Arvo:';
  var $AKF_STYLE               = 'Tyyli:';
  var $AKF_REQUIRED            = 'Vaaditaan:';

  // Admin - Settings
  var $AKF_EDITSETTINGS        = 'Muokkaa asetuksia';
  var $AKF_MAILSUBJECT         = 'Sähköpostin aihe:';
  var $AKF_SENDERNAME          = 'Lähettäjän nimi:';
  var $AKF_SENDEREMAIL         = 'Lähettäjän sähköposti:';
  var $AKF_SETTINGSSAVED       = 'Asetukset on tallennettu.';
  var $AKF_SETTINGSNOTSAVED    = 'Asetuksia ei voitu tallentaa.';
  
  // Admin - Stored Data
  var $AKF_STOREDFORMS         = 'Tallennetut lomakkeet';
  var $AKF_NUMBEROFENTRIES     = 'Rivejä';
  var $AKF_STOREDDATA          = 'Tallennetut tiedot';
  var $AKF_STOREDIP            = 'Lähettäjän IP-osoite';
  var $AKF_STOREDDATE          = 'Lähetyspäivä';


  // Frontend - Misc
  var $AKF_PLEASEFILLREQFIELD  = 'Täytä kaikki vaaditut kentät:';
  var $AKF_REQUIREDFIELD       = 'Pakollinen kenttä';
  var $AKF_BUTTONSEND          = 'Lähetä';
  var $AKF_BUTTONCLEAR         = 'Tyhjennä';
  var $AKF_FORMEXPIRED         = 'Lomakkeen viimeinen julkaisupäivä on mennyt.';
  var $AKF_FORMPENDING         = 'Lomake odottaa julkaisua.';
  var $AKF_MAILERHELLO         = 'Hei';
  var $AKF_MAILERHEADER        = 'Sivuston käyttäjä on lähettänyt seuraavat tiedot:';
  var $AKF_MAILERFOOTER        = 'Parhain terveisin';
  var $AKF_MAILERERROR         = 'Virhe sähköpostin lähettämisessä:';

  // Help - Admin Backend
  var $AKF_HELPFORM            = 'Valitse lomake.';
  var $AKF_HELPTITLE           = 'Lomakkeen/kentän otsikko.';
  var $AKF_HELPDESCRIPTION     = 'Lomakkeen/kentän html-muotoinen kuvaus.';
  var $AKF_HELPTYPE            = 'Valitse kentän tyyppi. Jos tarvitset yksilöidyn Tyyppi-valikon, ota yhteyttä Arthur Konzeen.';
  var $AKF_HELPVALUE           = 'Tämän kentän avulla voit asettaa oletusarvon. Jos haluat luoda DROPDOWN-tyyppisen kentän, kirjoita jokainen valinta omalle rivilleen. Sama koskee myös RADIOBUTTON - ja SELECTBOX -tyyppejä. Jos käytät CHECKBOX-tyyppistä kenttää, teksti näkyy selitteenä kentän vieressä.';
  var $AKF_HELPSTYLE           = 'Voit asettaa kentälle CSS-tyylimäärittelyn. Jos haluat kentän leveydeksi 200 pikseliä, kirjoita: width:200px;';
  var $AKF_HELPREQUIRED        = 'Onko kenttä pakollinen.';
  var $AKF_HELPORDERING        = 'Valitse järjestys.';
  var $AKF_HELPSTARTFINISH     = 'Valitse julkaisemisen alku- ja loppupäivä.';
  var $AKF_HELPSENDMAIL        = 'Lähetetäänkö lomakkeen tiedot sähköpostitse.';
  var $AKF_HELPEMAILS          = 'Sähköpostiosoite johon lomakkeen tiedot lähetetään. Jos useampi kuin yksi osoite, erota osoitteet pilkulla (,).';
  var $AKF_HELPSAVEDB          = 'Tallennetaanko lomakkeen tiedot tietokantaan.';
  var $AKF_HELPTARGET          = 'Näytetäänkö käyttäjälle loppusivu vai ohjataanko käyttäjä toiselle sivulle (URL).';
  var $AKF_HELPTARGETURL       = 'Anna osoite (URL) johon käyttäjä ohjataan. Osoite voi olla mikä tahansa URL tai jopa toinen lomake.';
  var $AKF_HELPSUBJECT         = 'Lähtevien viestien aihe.';
  var $AKF_HELPSENDER          = 'Lähtevissä viesteissä näkyvä lähettäjän nimi.';
  var $AKF_HELPEMAIL           = 'Lähtevissä viesteissä näkyvä lähettäjän sähköposti.';
  var $AKF_HELPRESULT          = 'Näytetäänkö lomakkeen tiedot käyttäjälle.';

}

$AKFLANG =& new akfLanguage();

?>