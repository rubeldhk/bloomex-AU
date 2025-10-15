<?php

/***************************************
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @Translated by Henk van Cann, vondeken
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

@define("_LANG_ID","ID");
@define('_LANG_NAME','Naam');
@define('_LANG_DESCRIPTION','Beschrijving');
@define('_LANG_ACCESS_DENIED','U heeft geen toegang tot deze bron');
@define('_LANG_INPUT_REQUIRED','U heeft niet de juiste invoer verstrekt');
@define('_LANG_YES','Ja');
@define('_LANG_NO','Nee');
@define('_LANG_COMMENT','Toelichting');
@define('_LANG_LABEL','Label');
@define('_LANG_REQUIRED','Verplicht');
@define('_LANG_SIZE','Grootte');
@define('_LANG_PUBLISHED','Gepubliceerd');
@define('_LANG_CATEGORY','Categorie');
@define('_LANG_CATEGORIES','Categorieën');
@define('_LANG_PARSED','Geparsed');
@define('_LANG_PARSE','Parse');
@define('_LANG_PREVIEW','Voorbeeld');
@define('_LANG_ACCESS','Toegang');
@define('_LANG_REORDER','Sorteer');
@define('_LANG_HITS','Hits');
@define('_LANG_ORDER','Volgorde');
@define('_LANG_CHECKED_OUT','Uitgecheckt');
@define('_LANG_HOSTNAME','Hostnaam');
@define('_LANG_ONLINE','On Line');
@define('_LANG_SEARCH','Zoeken');
@define('_LANG_DISPLAY','Toon');
@define('_LANG_SELECT','Selecteer ');
@define('_LANG_GROUP','Groep');
@define('_LANG_TYPE','Type');
@define('_LANG_REGEX','Regex');
@define('_LANG_DB', 'Database');
@define('_LANG_DBS', 'Databases');
@define('_LANG_ALL','Alle');
@define('_LANG_EDIT','Wijzig');
@define('_LANG_ADD','Voeg toe');
@define('_LANG_EDIT_DATABASE','Wijzig Database');
@define('_LANG_ADD_DATABASE','Toevoegen Database');
@define('_LANG_FIELD_REQUIRED','U dient een waarde te verstrekken voor');
@define('_LANG_USES_VARIABLES','Gebruikers Variabelen');
@define('_LANG_DISPLAY_NAME','Titel');
@define('_LANG_STATEMENT','Statement');
@define('_LANG_LIST','Lijst');
@define('_LANG_KEYWORD','Sleutelwoord');
@define('_LANG_CONFIGURATION','Configuratie');
@define('_LANG_CONFIGURATIONS','Configuraties');
@define('_LANG_QUERY','Query');
@define('_LANG_QUERIES','Queries');
@define('_LANG_STAR_INDICATES','An asteriks (*) duidt op een verplicht veld');
@define('_LANG_REQUIRED_MARK','<SPAN style="color:red">*</SPAN>');
@define('_LANG_BEGIN_RED_SPAN','<SPAN style="color:red">');
@define('_LANG_END_RED_SPAN','</SPAN>');
@define('_LANG_KEY','Sleutel');
@define('_LANG_VALUE','Waarde');
@define('_LANG_INPUT','Invoer Typen');
@define('_LANG_DBTYPE','Database Typen');
@define('_LANG_DBDRIVER','Database Drivers');
@define('_LANG_COUNTSQL','Count SQL');
@define('_LANG_DISPLAY_REGEX','Toon Regex');
@define('_LANG_SUBSTITUTION','Substitutie');
@define('_LANG_SUBSTITUTIONS','Substituties');
@define('_LANG_TEMPLATE','Template');
@define('_LANG_MAX_SIZE','Maximale Grootte is ');
@define('_LANG_EQWR_NO_RESULTS',' Er zijn geen gegevens die passen bij uw selectie');
@define('_LANG_EQWR_RESULTS',"Terugegeven resultaten: ");
@define('_LANG_PARENT','Ouder');
@define('_LANG_QUERY_NO_GO','Uw Query kon niet worden uitgevoerd');
@define('_LANG_INPUT_RECEIVED','Bedankt voor uw invoer.');
@define('_LANG_YOUR_SQL','Uw SQL is: ');
@define('_LANG_REPRESENTED_BY','Vertegenwoordigd Door : ');
@define('_LANG_IN_STATEMENT','Gevonden in Statement : ');
@define('_LANG_NO_PASS_REGEX','Voldeed niet aan regex');
@define('_LANG_PAGE_COMPANY','Professionele Consultancy');
@define('_LANG_PAGE_SUPPORT','Ondersteuning and Veel gestelde vragen (FAQ)');
@define('_LANG_PAGE_FORUM','Forum');
@define('_LANG_PAGE_DOWNLOAD','Download');
@define('_LANG_DBQ_VERSION','DBQ Versie');
@define('_LANG_PHP_VERSION','PHP Versie');
@define('_LANG_MAMBO_VERSION','Mambo Versie');
@define('_LANG_WEBSERVER_VERSION','Webserver Versie');
@define('_LANG_SUBMISSIONS','Submissies');
@define('_LANG_STATS','Statistieken');
@define('_LANG_USER_CONFIG','Gebruikers Configuraties');

// admin screens
// Added for 1.1
@define('_LANG_ADMIN_IN_USE','De huidige regel (record) wordt gewijzigd door een andere persoon');
@define('_LANG_SELECT_ITEM', 'Selecteer een item');
@define('_LANG_ITEM_NOT_DELETED', 'Het geselecteerde item kon niet worden verwijderd');
@define('_LANG_ERROR', 'Error: ');
@define('_LANG_COULDNOT_PUBLISH', 'Het geselecteerde item kon niet worden gepubliceerd of niet-gepubliceerd');
@define('_LANG_ACTIVE','Actief');
@define('_LANG_TIME_START', 'Van');
@define('_LANG_TIME_END', 'Tot');
@define('_LANG_INVALID_DATE_RANGE','Invalide Periode');
@define('_LANG_MANAGER','Manager');
@define('_LANG_DIRECTORY','Map');
@define('_LANG_AUTHOR','Bouwer');
@define('_LANG_VERSION','Versie');
@define('_LANG_DATE','Datum');
@define('_LANG_AUTHOR_URL','Bouwer URL');
@define('_LANG_INFORMATION', 'Informatie');

// Added for 1.2
@define('_LANG_DEFAULT', 'Standaard');
@define('_LANG_OPENING_HELP_WINDOW', 'Opent het help venster nu.');
@define('_LANG_PERCENT', 'Procent');


// details.html.php
// Added for 1.1
@define('_LANG_DETAILS_CONFIG','Configuratie');
@define('_LANG_DETAILS_ATTRIBUTES','Attributen');
@define('_LANG_DETAILS_TEXT','Tekst');
@define('_LANG_DETAILS_PARSE','Parse');
// Added for 1.2
@define('_LANG_DETAILS_PARAMS','Parameters');
@define('_LANG_DETAILS_DEFAULT_VALUE', 'Standaard waarde');
@define('_LANG_DETAILS_LIST', 'Lijst');

// summary.html.php
// Added for 1.1
@define('_LANG_FILTER','Filter: ');
@define('_LANG_UNDER_DEVELOPMENT','Deze functiewordt momenteel ontwikkeld.');
@define('_LANG_ON_LINE','On Line');
@define('_LANG_COPY_OF','Copie van ');

// internal in class files but used only by admin screens
// Added for 1.1
@define('_LANG_CHANGES_SAVED', ' Uw wijzigingen zijn opgeslagen');
@define('_LANG_CHANGES_NOT_SAVED', 'Uw wijzigingen konden niet worden opgeslagen');
@define('_LANG_UNKNOWN','Onbekend');

// support.html.php
// Added for 1.0
@define('_LANG_PEAR_SUPPORT','PEAR Interface Ondersteuning');
// Added for 1.1
@define('_LANG_MAMBO_SUPPORT','Mambo Database Interface Ondersteuning');
@define('_LANG_ADODB_SUPPORT','ADODB Interface Ondersteuning');
@define('_LANG_MYSQL_SUPPORT','MySQL Interface Ondersteuning');
@define('_LANG_MYSQLI_SUPPORT','MySQLi Interface Ondersteuning');
// Added for 1.2
@define('_LANG_INCLUDE_PATH', 'Include Pad');

// details.html.php
// Added for 1.3
@define('_LANG_DETAILS_NOTIFICATION', 'Kennisgevingen');
@define('_LANG_DETAILS_SPECIAL', 'Speciaal');

// General 1.3 Additions
@define ('_LANG_DETAILS_CONTACT','Kennisgevingen');
@define ('_LANG_UPDATE','Vernieuw');

// Template Editing Text
// Added for 1.3
@define('_LANG_TEMPLATE_NOT_FOUND', 'Het sjabloon- (Template) bestand is niet gevonden');
@define('_LANG_TEMPLATE_NOT_READABLE', 'Het sjabloon- (Template) bestand is niet leesbaar');
@define('_LANG_TEMPLATE_NOT_WRITABLE', 'Het sjabloon- (Template) bestand is niet beschrijfbaar');
@define('_LANG_DIRECTORY_NOT_FOUND', 'De Map is niet gevonden');
@define('_LANG_DIRECTORY_NOT_READABLE', 'De Map is niet leebaar');
@define('_LANG_DIRECTORY_NOT_WRITABLE', 'De Map is niet beschrijfbaar');
@define('_LANG_DIRECTORY_COPIED', 'De Map is succesvol gecopieerd');
@define('_LANG_DIRECTORY_NOT_COPIED', 'De Map kon niet worden gecopieerd');

// File Editor
// Added for 1.3
@define('_LANG_FILE_EDITOR', ' Bestand Editor');
@define('_LANG_FILE_IS', 'Bestand is ');
@define('_LANG_IS_WRITABLE', 'Beschrijfbaar');
@define('_LANG_IS_UNWRITABLE', 'Niet beschrijfbaar');

// Error Reporting
@define('_LANG_ERRORS', 'Fouten');
@define('_LANG_SOURCE', 'Bron');
@define('_LANG_OBJECT', 'Object');
@define('_LANG_DATE_REPORTED', 'Datum Gerapporteerd');
@define('_LANG_MESSAGE', 'Bericht');
@define('_LANG_PRIORITY', 'Prioriteit');

// Variable Objects
// Added for 1.4
@define('_LANG_VT_CODE', 'Serverzijde Code Variabele');
@define('_LANG_VT_FILES', 'Bestand Selectie Variabele');
@define('_LANG_VT_KEYWORD', 'Sleutelwoord Variabele');
@define('_LANG_VT_RESULTS', 'Query Resultaat Variabele');
@define('_LANG_VT_STATEMENT', 'Statement Variabele');
@define('_LANG_VT_SUBSTITUTIONS', 'Substitutie Variabele');
@define('_LANG_VT_FILE_UPLOAD', 'Bestand Upload Variabele');
@define('_LANG_VT_CUSTOM', 'Custom Variabele');
@define('_LANG_VT_FIELD', 'Veld Variabele');

// Adding tags that were previously included in the frontend lang file
@define ('_LANG_CONFIG','Configuraties');
@define ('_LANG_EREG','Reguliere Expressies');
@define ('_LANG_FILES','Bestanden');
@define ('_LANG_VARIABLE', 'Variabele');
@define ('_LANG_VARIABLES', 'Variabelen');

// Misc additions in 1.4
@define('_LANG_TYPES','Typen');
@define('_LANG_PROFESSIONAL', 'DBQ Professional');
@define ('_LANG_MAIL','Mail');
@define('_LANG_DBQ_PROFESSIONAL_INSTALLED','Professionele Versie Geïnstalleerd');
//@define ('_LANG_','');
?>