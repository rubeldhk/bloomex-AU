<?php

/***************************************
 * $Id: admin.english.php,v 1.9 2005/06/16 21:53:13 tcp Exp $
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.9 $
 **/

defined( '_VALID_MOS' ) or die( 'Eingeschr&auml;nkter Zugang.' );

@define("_LANG_ID","ID");
@define('_LANG_NAME','Name');
@define('_LANG_DESCRIPTION','Beschreibung');
@define('_LANG_ACCESS_DENIED','Sie sind nicht berechtigt, diesen Bereich zu sehen');
@define('_LANG_INPUT_REQUIRED','Sie haben nicht die richtigen Eingaben gemacht');
@define('_LANG_YES','Ja');
@define('_LANG_NO','Nein');
@define('_LANG_COMMENT','Kommentar');
@define('_LANG_LABEL','Label');
@define('_LANG_REQUIRED','Pflichtfeld');
@define('_LANG_SIZE','Gr&ouml;&szlig;e');
@define('_LANG_PUBLISHED','Ver&ouml;ffentlicht');
@define('_LANG_CATEGORY','Kategorie');
@define('_LANG_CATEGORIES','Kategorien');
@define('_LANG_PARSED','Geparst');
@define('_LANG_PARSE','Parse');
@define('_LANG_PREVIEW','Vorschau');
@define('_LANG_ACCESS','Berechtigung');
@define('_LANG_REORDER','Umsortieren');
@define('_LANG_HITS','Treffer');
@define('_LANG_ORDER','Reihenfolge');
@define('_LANG_CHECKED_OUT','Ausgecheckt');
@define('_LANG_HOSTNAME','Hostname');
@define('_LANG_ONLINE','Online');
@define('_LANG_SEARCH','Suche');
@define('_LANG_DISPLAY','Anzeige');
@define('_LANG_SELECT','Auswahl ');
@define('_LANG_GROUP','Gruppe');
@define('_LANG_TYPE','Typ');
@define('_LANG_REGEX','Regex');
@define('_LANG_DB', 'Datenbank');
@define('_LANG_DBS', 'Datenbank');
@define('_LANG_ALL','Alle ');
@define('_LANG_EDIT','Editieren');
@define('_LANG_ADD','Zuf&uuml;gen');
@define('_LANG_EDIT_DATABASE','Datenbank Editieren');
@define('_LANG_ADD_DATABASE','Datenbank hinzuf&uuml;gen');
@define('_LANG_FIELD_REQUIRED','Sie m&uuml;ssen einen Wert angeben f&uuml;r');
@define('_LANG_USES_VARIABLES','Benutze Variablen');
@define('_LANG_DISPLAY_NAME','Titel');
@define('_LANG_STATEMENT','Befehl');
@define('_LANG_LIST','Liste');
@define('_LANG_KEYWORD','Schl&uuml;sselworte');
@define('_LANG_CONFIGURATION','Konfiguration');
@define('_LANG_CONFIGURATIONS','Konfigurationen');
@define('_LANG_QUERY','Query');
@define('_LANG_QUERIES','Queries');
@define('_LANG_STAR_INDICATES','Felder mit Sternen (*) sind Pflichtfelder');
@define('_LANG_REQUIRED_MARK','<SPAN style="color:red">*</SPAN>');
@define('_LANG_BEGIN_RED_SPAN','<SPAN style="color:red">');
@define('_LANG_END_RED_SPAN','</SPAN>');
@define('_LANG_KEY','Schl&uuml;ssel');
@define('_LANG_VALUE','Wert');
@define('_LANG_INPUT','Eingabe');
@define('_LANG_DBTYPE','Datenbank Typ');
@define('_LANG_DBDRIVER','Datenbank Drivers');
@define('_LANG_COUNTSQL','SQL Z&auml;hler');
@define('_LANG_DISPLAY_REGEX','Regex anzeigen');
@define('_LANG_SUBSTITUTION','Substitution');
@define('_LANG_SUBSTITUTIONS','Substitutionen');
@define('_LANG_TEMPLATE','Template');
@define('_LANG_MAX_SIZE','Erlaubte Gr&ouml;&szlig;e ist ');
@define('_LANG_EQWR_NO_RESULTS','Keine Treffer f&uuml;r ihre Auswahl');
@define('_LANG_EQWR_RESULTS',"Erhaltene Resultate: ");
@define('_LANG_PARENT','Eltern');
@define('_LANG_QUERY_NO_GO','Ihr Query konnte nicht ausgef&uuml;hrt werden');
@define('_LANG_INPUT_RECEIVED','Vielen Dank f&uuml;r ihre Eingabe.');
@define('_LANG_YOUR_SQL','Ihr SQL ist: ');
@define('_LANG_REPRESENTED_BY','Vertreten durch : ');
@define('_LANG_IN_STATEMENT','Gefunden im Befehl : ');
@define('_LANG_NO_PASS_REGEX','hat die regex nicht passiert');
@define('_LANG_PAGE_COMPANY','Professional Consulting');
@define('_LANG_PAGE_SUPPORT','Support und FAQ');
@define('_LANG_PAGE_FORUM','Forum');
@define('_LANG_PAGE_DOWNLOAD','Download');
@define('_LANG_DBQ_VERSION','DBQ Version');
@define('_LANG_PHP_VERSION','PHP Version');
@define('_LANG_JOOMLA_VERSION','Joomla Version');
@define('_LANG_WEBSERVER_VERSION','Webserver Version');
@define('_LANG_SUBMISSIONS','Einsendungen');
@define('_LANG_STATS','Statistiken');
@define('_LANG_USER_CONFIG','Benutzerkonfigurationen');

// admin screens
// Added for 1.1
@define('_LANG_ADMIN_IN_USE','Der aktuelle Datensatz wird gerade von einer anderen Person bearbeitet');
@define('_LANG_SELECT_ITEM', 'W&auml;hlen sie ein Element');
@define('_LANG_ITEM_NOT_DELETED', 'Das ausgew&auml;hlte Element konnte nicht gel&ouml;scht werden');
@define('_LANG_ERROR', 'Fehler: ');
@define('_LANG_COULDNOT_PUBLISH', 'Das ausgew&auml;hlte Element konnte nicht ver&ouml;ffentlicht oder unver&ouml;ffentlicht werden');
@define('_LANG_ACTIVE','Aktiv');
@define('_LANG_TIME_START', 'Von');
@define('_LANG_TIME_END', 'Bis');
@define('_LANG_INVALID_DATE_RANGE','Ung&uuml;ltiger Datenintervall');
@define('_LANG_MANAGER','Manager');
@define('_LANG_DIRECTORY','Verzeichnis');
@define('_LANG_AUTHOR','Autor');
@define('_LANG_VERSION','Version');
@define('_LANG_DATE','Datum');
@define('_LANG_AUTHOR_URL','Autor URL');
@define('_LANG_INFORMATION', 'Information');

// Added for 1.2
@define('_LANG_DEFAULT', 'Standard');
@define('_LANG_OPENING_HELP_WINDOW', 'Ein Hilfefenster wird jetzt ge&ouml;ffnet.');
@define('_LANG_PERCENT', 'Prozent');


// details.html.php
// Added for 1.1
@define('_LANG_DETAILS_CONFIG','Einstellungen');
@define('_LANG_DETAILS_ATTRIBUTES','Attribute');
@define('_LANG_DETAILS_TEXT','Text');
@define('_LANG_DETAILS_PARSE','Parse');
// Added for 1.2
@define('_LANG_DETAILS_PARAMS','Parameter');
@define('_LANG_DETAILS_DEFAULT_VALUE', 'Standardwert');
@define('_LANG_DETAILS_LIST', 'Liste');

// summary.html.php
// Added for 1.1
@define('_LANG_FILTER','Filter: ');
@define('_LANG_UNDER_DEVELOPMENT','Dieses Merkmal befindet sich noch in der Entwicklung.');
@define('_LANG_ON_LINE','On Line');
@define('_LANG_COPY_OF','Kopie von ');
// Added for 1.4, Dev3
@define('_LANG_NONE', 'Keine');

// internal in class files but used only by admin screens
// Added for 1.1
@define('_LANG_CHANGES_SAVED', 'Ihre &Auml;nderungen wurden gesichert');
@define('_LANG_CHANGES_NOT_SAVED', 'Ihre &Auml;nderungen konnten nicht gesichert werden');
@define('_LANG_UNKNOWN','Unbekannt');

// support.html.php
// Added for 1.0
@define('_LANG_PEAR_SUPPORT','PEAR Interface Unterst&uuml;tzung');
// Added for 1.1
@define('_LANG_JOOMLA_SUPPORT','Joomla Datenbank Interface Unterst&uuml;tzung');
@define('_LANG_ADODB_SUPPORT','ADODB Interface Unterst&uuml;tzung');
@define('_LANG_MYSQL_SUPPORT','MySQL Interface Unterst&uuml;tzung');
@define('_LANG_MYSQLI_SUPPORT','MySQLi Interface Unterst&uuml;tzung');
// Added for 1.2
@define('_LANG_INCLUDE_PATH', 'Include Pfad');

// details.html.php
// Added for 1.3
@define('_LANG_DETAILS_NOTIFICATION', 'Benachrichtigungen');
@define('_LANG_DETAILS_SPECIAL', 'Spezial');

// General 1.3 Additions
@define ('_LANG_DETAILS_CONTACT','Benachrichtigungen');
@define ('_LANG_UPDATE','Update');

// Template Editing Text
// Added for 1.3
@define('_LANG_TEMPLATE_NOT_FOUND', 'Das Templatefile wurde nicht gefunden');
@define('_LANG_TEMPLATE_NOT_READABLE', 'Das Templatefile ist nicht lesbar');
@define('_LANG_TEMPLATE_NOT_WRITABLE', 'Das Templatefile ist nicht schreibbar');
@define('_LANG_DIRECTORY_NOT_FOUND', 'Das Verzeichnis wurde nicht gefunden');
@define('_LANG_DIRECTORY_NOT_READABLE', 'Das Verzeichnis ist nicht lesbar');
@define('_LANG_DIRECTORY_NOT_WRITABLE', 'Das Verzeichnis ist nicht schreibbar');
@define('_LANG_DIRECTORY_COPIED', 'Das Verzeichnis wurde erfolgreich kopiert');
@define('_LANG_DIRECTORY_NOT_COPIED', 'Das Verzeichnis konnte nicht kopiert werden');

// File Editor
// Added for 1.3
@define('_LANG_FILE_EDITOR', 'Datei Editor');
@define('_LANG_FILE_IS', 'Die Datei ist ');
@define('_LANG_IS_WRITABLE', 'schreibbar');
@define('_LANG_IS_UNWRITABLE', 'nicht schreibbar');

// Error Reporting
@define('_LANG_ERRORS', 'Fehler');
@define('_LANG_SOURCE', 'Quelle');
@define('_LANG_OBJECT', 'Objekt');
@define('_LANG_DATE_REPORTED', 'Gemeldetes Datum');
@define('_LANG_MESSAGE', 'Nachricht');
@define('_LANG_PRIORITY', 'Priorit&auml;t');

// Variable Objects
// Added for 1.4
@define('_LANG_VT_CODE', 'Serverseitige Codevariable');
@define('_LANG_VT_FILES', 'File Select Variable');
@define('_LANG_VT_KEYWORD', 'Schl&uuml;sselwortvariable');
@define('_LANG_VT_RESULTS', 'Queryresultatvariable');
@define('_LANG_VT_STATEMENT', 'Befehlsvariable');
@define('_LANG_VT_SUBSTITUTIONS', 'Ersetzungsvariable');
@define('_LANG_VT_FILE_UPLOAD', 'File Upload Variable');
@define('_LANG_VT_CUSTOM', 'Benutzerdefinierte Variable');
@define('_LANG_VT_FIELD', 'Feldvariable');

// Adding tags that were previously included in the frontend lang file
@define ('_LANG_CONFIG','Konfigurationen');
@define ('_LANG_EREG','Regular Expressions');
@define ('_LANG_FILES','Files');
@define ('_LANG_VARIABLE', 'Variable');
@define ('_LANG_VARIABLES', 'Variablen');

// Misc additions in 1.4
@define('_LANG_TYPES','Typen');
@define('_LANG_PROFESSIONAL', 'DBQ Professional');
//@define ('_LANG_','');
?>
