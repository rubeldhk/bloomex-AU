<?php


/***************************************
 * $Id: german.php,v 1.3.2.2 2005/08/09 00:54:39 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.3.2.2 $
 **/

defined( '_VALID_MOS' ) or die( 'Eingeschr&auml;nkter Zugang.' );

// dbquery.php file
@define('_LANG_INVALID_INPUT','Ung&uuml;ltige Eingabe');
@define('_LANG_ACCESS_DENIED','Zugang wurde verweigert');
@define('_LANG_COULD_NOT_LOAD','Daten konnten nicht aus der Datenbank geladen werden');

// print.page.php
// New in 1.1
@define('_LANG_TEMPLATE_SELECT_QUERY','Ausw&auml;hlen');
@define('_LANG_TEMPLATE_EXECUTE_QUERY','Ausf&uuml;hren');
@define('_LANG_TEMPLATE_A_QUERY',' einen Query');
@define('_LANG_TEMPLATE_CLOSE_WINDOW','Schliesse dieses Fenster');
@define('_LANG_TEMPLATE_BACK','Zur&uuml;ck');

// General
@define('_LANG_TEMPLATE_NO_ACCESS', 'Der Zugang wurde verweigert');
@define('_LANG_TEMPLATE_GENERAL_ERROR','Ein allgemeiner Fehler ist w&auml;hrend der Bearbeitung ihrer Anfrage aufgetreten');
// Added for 1.4
// You will need to customize this for every query, if you choose to have multiple language defs
//@define('_LANG_TEMPLATE_GENERAL_DESCRIPTION','');

// Select Query
@define('_LANG_TEMPLATE_NAME', 'Name');
@define('_LANG_TEMPLATE_DESCRIPTION', 'Beschreibung');
@define('_LANG_TEMPLATE_NO_QUERIES_AVAILABLE', 'Es sind keine Queries verf&uuml;gbar');

// PrepareQuery
@define('_LANG_TEMPLATE_FIELD', 'Atribut');
@define('_LANG_TEMPLATE_DATA', 'Option');
@define('_LANG_TEMPLATE_DESC', 'Beschreibung');
@define('_LANG_TEMPLATE_ENTER_OPTION', 'Bitte w&auml;hlen');
@define('_LANG_TEMPLATE_SUBMIT', 'Absenden');
@define('_LANG_TEMPLATE_MISSING_VARS_FROM_STATEMENT', 'Bitte machen sie die erforderliche Eingabe');
@define('_LANG_TEMPLATE_GENERAL_ERROR', 'Ein allgemeiner Fehler ist aufgetreten');
@define('_LANG_TEMPLATE_STAR_INDICATES','Pflichtfelder sind durch einen Stern (*) markiert');
@define('_LANG_TEMPLATE_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_TEMPLATE_BEGIN_RED_SPAN','<span style="color:red">');
@define('_LANG_TEMPLATE_END_RED_SPAN','</span>');
// Added for 1.3
@define('_LANG_TEMPLATE_MAX_FILE_SIZE', 'Die erlaubte Dateigr&ouml;sse betr&auml;gt ');
// Added for 1.4
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_ABOVE','Bitte f&uuml;llen sie das folgende Formular aus');
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_BELOW','');

// ExecuteQuery.Results
//@define('_LANG_TEMPLATE_EQWR_RESULTS', '&Uuml;bereinstimmende Resultate');
@define('_LANG_TEMPLATE_DISPLAY','Anzeigen');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ABOVE','&Uuml;bereinstimmende Resultate');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_BELOW','');

// ExecuteQuery.no.Results
//@define('_LANG_TEMPLATE_INPUT_RECEIVED','Vielen Dank f&uuml;r ihre Eingabe');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_NO_RESULTS','Keine &uuml;bereinstimmenden Daten f&uuml;r ihre Auswahl gefunden');

// ExecuteQuery.wo.Results
//@define('_LANG_TEMPLATE_EQWR_NO_RESULTS', 'Keine &uuml;bereinstimmenden Daten f&uuml;r ihre Auswahl gefunden');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_WO_RESULTS','Danke');

// ExecuteQuery.Failure.html.php
//@define('_LANG_TEMPLATE_QUERY_NO_GO','Es war nicht m&ouml;glich ihren Query auszuf&uuml;hren');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ERROR','Es war nicht m&ouml;glich ihren Query auszuf&uuml;hren');

// Return.html.php
@define('_LANG_TEMPLATE_RETURN', 'Zur&uuml;ck zum vorherigen Bildschirm');
@define('_LANG_TEMPLATE_CLEAR', 'L&ouml;sche das Formular');

// Input language codes
// You will need to customize these for each variable
// You should also change the language code in the Edit Variable screen
// Added for 1.4
//@define('_LANG_TEMPLATE_INPUT_DESCRIPTION_VARNAME','');
//@define('_LANG_TEMPLATE_INPUT_COMMENT_VARNAME','');
//@define('_LANG_TEMPLATE_INPUT_INVALID_VARNAME','');
?>
