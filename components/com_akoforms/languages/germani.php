<?php
/**
* AkoForms - A Mambo Form Generator Component
* @version 1.1 final
* @package AkoForms
* @copyright (C) 2004 by Arthur Konze
* @license http://www.konze.de/ Copyrighted Commercial Software
* Translator : Christian Hent
* Homepage   : www.mamboreport.de
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class akfLanguage {

  // Admin - Allgemein
  var $AKF_YES                 = 'Ja';
  var $AKF_NO                  = 'Nein';
  var $AKF_PUBLISHED           = 'Veröffentlicht';
  var $AKF_PUBLISHING          = 'Veröffentlichen';
  var $AKF_STARTPUBLISHING     = 'Freigeben am:';
  var $AKF_FINISHPUBLISHING    = 'Ausblenden am:';
  var $AKF_PUBPENDING          = 'Veröffentlicht, jedoch noch freizugeben';
  var $AKF_PUBCURRENT          = 'Veröffentlich und aktuell';
  var $AKF_PUBEXPIRED          = 'Veröffentlicht, jedoch abgelaufen';
  var $AKF_UNPUBLISHED         = 'Unveröffentlicht';
  var $AKF_REORDER             = 'Reihenfolge';
  var $AKF_ORDERING            = 'Anordnung:';
  var $AKF_TITLE               = 'Titel:';
  var $AKF_DESCRIPTION         = 'Beschreibung:';

  // Admin - Spracheneditor
  var $AKF_EDITLANGUAGE        = 'Spracheneditor';
  var $AKF_PATH                = 'Pfad:';
  var $AKF_FILEWRITEABLE       = 'Bitte beachten: Die Datei muss zum Speichern beschreibbar sein.';

  // Admin - Formulare einsehen
  var $AKF_FORMMANAGER         = 'Formular Manager';
  var $AKF_FORMTITLE           = 'Titel des Formulars';
  var $AKF_SENDMAIL            = 'Email senden';
  var $AKF_STOREDB             = 'in DB sichern';
  var $AKF_FINISHING           = 'Abschluss';
  var $AKF_FORMPAGE            = 'Formular Seite';
  var $AKF_REDIRECTION         = 'Weiterleitung';
  var $AKF_SHOWRESULT          = 'Ergebnise anzeigen';
  var $AKF_NUMBEROFFIELDS      = 'Anzahl der Felder';

  // Admin - Formulare bearbeiten/hinzufügen
  var $AKF_ADDFORM             = 'Formular hinzufügen';
  var $AKF_EDITFORM            = 'Formular editieren';
  var $AKF_HEADER              = 'Header';
  var $AKF_HANDLING            = 'Bedienung';
  var $AKF_SENDBYEMAIL         = 'Per Email senden:';
  var $AKF_EMAILS              = 'Emails:';
  var $AKF_SAVETODATABASE      = 'In Datenbank sichern:';
  var $AKF_ENDPAGETITLE        = 'Antwortseite, Titel:';
  var $AKF_ENDPAGEDESCRIPTION  = 'Antwortseite, Beschreibung:';
  var $AKF_FORMTARGET          = 'Ziel:';
  var $AKF_TARGETURL           = 'URL Weiterleitung:';
  var $AKF_SHOWENDPAGE         = 'Antwortseite ';
  var $AKF_REDIRECTTOURL       = 'URL Weiterleitung';
  var $AKF_NEWFORMSLAST        = 'Neue Formulare an letzter Stelle.';
  var $AKF_SHOWFORMRESULT      = 'Formularergebnis anzeigen:';

  // Admin - Felder einsehen
  var $AKF_FIELDMANAGER        = 'Formularfeld Manager';
  var $AKF_FIELDTITLE          = 'Feldtitel';
  var $AKF_FIELDTYPE           = 'Feldtyp';
  var $AKF_FIELDREQUIRED       = 'Pflichtfeld';
  var $AKF_SELECTFORM          = 'Formular auswählen';
  var $AKF_ALLFORMS            = '- alle Formulare';

  // Admin - Felder bearbeiten/hinzugügen
  var $AKF_ADDFIELD            = 'Feld hinzufügen';
  var $AKF_EDITFIELD           = 'Feld bearbeiten';
  var $AKF_GENERAL             = 'Allgemein';
  var $AKF_FORM                = 'Formular:';
  var $AKF_TYPE                = 'Typ:';
  var $AKF_VALUE               = 'Wert:';
  var $AKF_STYLE               = 'Design:';
  var $AKF_REQUIRED            = 'Erforderlich(*):';

  // Admin - Konfiguration
  var $AKF_EDITSETTINGS        = 'Konfiguration bearbeiten';
  var $AKF_MAILSUBJECT         = 'Email Betreff:';
  var $AKF_SENDERNAME          = 'Absender Name:';
  var $AKF_SENDEREMAIL         = 'Absender Email:';
  var $AKF_SETTINGSSAVED       = 'Konfiguration gesichert.';
  var $AKF_SETTINGSNOTSAVED    = 'Konfiguration konnte nicht gesichert werden.';

  // Admin - Datensicherung
  var $AKF_STOREDFORMS         = 'Gespeicherte Formulare';
  var $AKF_NUMBEROFENTRIES     = 'Anzahl der Einträge';
  var $AKF_STOREDDATA          = 'Gespeicherte Daten';
  var $AKF_STOREDIP            = 'Absender IP';
  var $AKF_STOREDDATE          = 'Absendedatum';

  // Frontend - Allgemein
  var $AKF_PLEASEFILLREQFIELD  = 'Bitte alle mit einem * markierten Felder vollständig ausfüllen:';
  var $AKF_REQUIREDFIELD       = 'Pflichtfeld';
  var $AKF_BUTTONSEND          = 'Senden';
  var $AKF_BUTTONCLEAR         = 'Löschen';
  var $AKF_FORMEXPIRED         = 'Formular ist abgelaufen!';
  var $AKF_FORMPENDING         = 'Formular noch nicht freigegeben!';
  var $AKF_MAILERHELLO         = 'Hallo';
  var $AKF_MAILERHEADER        = 'Ein Benutzer hat ein Formular benutzt, um die folgenden Daten einzureichen:';
  var $AKF_MAILERFOOTER        = 'Mit freundlichen Grüßen,';
  var $AKF_MAILERERROR         = 'Fehler während des Versendens an:';

  // Hilfe - Admin Backend
  var $AKF_HELPFORM            = 'Hier über ein Dropdownmenu, das Feld dem gewünschten Formular zuweisen.';
  var $AKF_HELPTITLE           = 'Hier den Titel eintragen.';
  var $AKF_HELPDESCRIPTION     = 'Hier die passende Beschreibung eintragen.';
  var $AKF_HELPTYPE            = 'Hier aus einer Vielzahl von vordefinierten Feldern das gewünschte Formularfeld auswählen, für ein individuell gestaltetes DropDownMenu bitte mit Arthur Konze in Verbindung setzten.';
  var $AKF_HELPVALUE           = 'Hier dem Formularfeld Werte zuweisen, um ein DROPDOWN Menu zu erstellen, einfach in jede Zeile ein neuer Wert eintragen. Dasselbe gilt auch für RADIOBUTTON -und SELECTBOX Einträge.';
  var $AKF_HELPSTYLE           = 'Hier mittels CSS das Design des jeweiligen Formularfeldes beinflussen. Als Beispiel: Für ein 200 Pixel breites Feld gilt width:200px; ';
  var $AKF_HELPREQUIRED        = 'Hier festlegen ob der Anwender das Formularfeld zwingend ausfüllen muss.';
  var $AKF_HELPORDERING        = 'Hier Reihenfolge festlegen.';
  var $AKF_HELPSTARTFINISH     = 'Hier Gültigkeitsdauer des Formulars festlegen (Start - und Enddatum).';
  var $AKF_HELPSENDMAIL        = 'Hier festlegen ob Formularergebnis per Email gesandt werden soll.';
  var $AKF_HELPEMAILS          = 'Hier Email-Adresse eintragen, mehrere Einträge werden durch ein Komma (,) getrennt.';
  var $AKF_HELPSAVEDB          = 'Hier auswählen ob das Formularergebnis in der Datenbank gesichert werden soll.';
  var $AKF_HELPTARGET          = 'Hier das Formular-Ziel festlegen.';
  var $AKF_HELPTARGETURL       = 'Hier das Ziel der URL Weiterleitung eintragen. Auch die URL eines anderen Formulars ist gültig.';
  var $AKF_HELPSUBJECT         = 'Hier eingetragener Betreff wird als Absender-Betreff für alle abgehenden Emails verwendet.';
  var $AKF_HELPSENDER          = 'Hier eingetragener Name wird als Absender-Name für alle abgehenden Emails verwendet.';
  var $AKF_HELPEMAIL           = 'Hier eingetragene Adresse wird als Absender-Adresse für alle abgehenden Emails verwendet.';
  var $AKF_HELPRESULT          = 'Hier festlegen ob das Formularergebnis für User einsehbar sein soll.';

  // NEU in Version 1.01
  var $AKF_MAILCHARSET         = 'Email Zeichensatz:';
  var $AKF_HELPCHARSET         = 'Hier den zu verwendenden Zeichensatz für abgehende Emails auswählen.';
  var $AKF_MAILTABLEFIELD      = 'Feld';
  var $AKF_MAILTABLEDATA       = 'Daten';
  var $AKF_SELECTFIELD         = 'Feld auswählen';

  // NEW in version 1.1
  var $AKF_LAYOUTSETTINGS      = 'Layout Einstellungen';
  var $AKF_EMAILSETTINGS       = 'Email Einstellungen';
  var $AKF_LAYOUTSTART         = 'Layout Start:';
  var $AKF_LAYOUTROW           = 'Layout Reihe:';
  var $AKF_LAYOUTEND           = 'Layout Ende:';
  var $AKF_EMAILTITLECSS       = 'Email Titel Stil:';
  var $AKF_EMAILROW1CSS        = 'Email Reihe1 Stil:';
  var $AKF_EMAILROW2CSS        = 'Email Reihe2 Stil:';
  var $AKF_HELPLAYOUTSTART     = 'Der HTML Code in diesem Feld wird oberhalb und noch vor den Reihen mit den Feldern angezeigt.';
  var $AKF_HELPLAYOUTROW       = 'Dieser Code wird für jede Reihe von Feldern benutzt. Es gibt die folgenden Platzhalter: ###AFTFIELDTITLE###, ###AFTFIELDREQ###, ###AFTFIELDDESC### und ###AFTFIELD###.';
  var $AKF_HELPLAYOUTEND       = 'Nach den Feldern wird dieser HTML Code am Ende des Formulars angezeigt. Es gibt die folgenden Platzhalter: ###AFTSENDBUTTON### und ###AFTCLEARBUTTON###.';
  var $AKF_HELPEMAILTITLECSS   = 'Hier können CSS Definitionen für die Titelreihe der versendeten Email eingegeben werden.';
  var $AKF_HELPEMAILROW1CSS    = 'Hier können CSS Definitionen für die 1., 3., 5., etc. Datenreihe der versendeten Email eingegeben werden.';
  var $AKF_HELPEMAILROW2CSS    = 'Hier können CSS Definitionen für die 2., 4., 6., etc. Datenreihe der versendeten Email eingegeben werden.';

}

$AKFLANG =& new akfLanguage();

?>