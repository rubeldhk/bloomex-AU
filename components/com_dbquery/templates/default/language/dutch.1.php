<?php

defined( '_VALID_MOS' ) or die( 'Directe Toegang tot deze locatie is niet toegestaan.' );
// Vondeken add

// dbquery.php file
@define('_LANG_INVALID_INPUT','Onjuiste invoer');
@define('_LANG_ACCESS_DENIED','Toegang is geweigerd');
@define('_LANG_COULD_NOT_LOAD','Kon geen gegevens laden uit de database');

// print.page.php
// New in 1.1
@define('_LANG_TEMPLATE_SELECT_QUERY','Selecteer');
@define('_LANG_TEMPLATE_PREPARE_QUERY','Voorbereiden');
@define('_LANG_TEMPLATE_EXECUTE_QUERY','Uitvoeren');
@define('_LANG_TEMPLATE_A_QUERY',' Een Query');
@define('_LANG_TEMPLATE_CLOSE_WINDOW','Venster sluiten');
@define('_LANG_TEMPLATE_BACK','Terug');

// General
@define('_LANG_TEMPLATE_NO_ACCESS', 'Toegang verboden');
@define('_LANG_TEMPLATE_GENERAL_ERROR','Een algemene fout is opgetreden tijdens het uitvoeren van het verzoek');
// Added for 1.4
// You will need to customize this for every query, if you choose to have multiple language defs
//@define('_LANG_TEMPLATE_GENERAL_DESCRIPTION','');

// Select Query
@define('_LANG_TEMPLATE_NAME', 'Naam');
@define('_LANG_TEMPLATE_DESCRIPTION', 'Beschrijving');
@define('_LANG_TEMPLATE_NO_QUERIES_AVAILABLE', ' Er zijn geen Queries beschikbaar');

// PrepareQuery
@define('_LANG_TEMPLATE_FIELD', 'Attribuut');
@define('_LANG_TEMPLATE_DATA', 'Optie');
@define('_LANG_TEMPLATE_DESC', 'Beschrijving');
@define('_LANG_TEMPLATE_ENTER_OPTION', 'Selecteer een optie');
@define('_LANG_TEMPLATE_SUBMIT', 'Verzend');
@define('_LANG_TEMPLATE_MISSING_VARS_FROM_STATEMENT', 'Verstrek a.u.b. de gevraagde invoer');
@define('_LANG_TEMPLATE_GENERAL_ERROR', 'Een algemeen fout');
@define('_LANG_TEMPLATE_STAR_INDICATES','Een asteriks (*) betekent een verplicht veld');
@define('_LANG_TEMPLATE_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_TEMPLATE_BEGIN_RED_SPAN','<span style="color:red">');
@define('_LANG_TEMPLATE_END_RED_SPAN','</span>');
// Added for 1.3
@define('_LANG_TEMPLATE_MAX_FILE_SIZE', 'Maximale bestandsgrootte is ');
// Added for 1.4
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_ABOVE','Vul het volgende formulier in');
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_BELOW','');

// ExecuteQuery.Results
@define('_LANG_TEMPLATE_EQWR_RESULTS', 'Resultaten passend bij de criteria');
@define('_LANG_TEMPLATE_DISPLAY','Display');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ABOVE','Resultaten passend bij de criteria');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_BELOW','');

// ExecuteQuery.no.Results
@define('_LANG_TEMPLATE_INPUT_RECEIVED','Bedankt voor uw invoer');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_NO_RESULTS', 'Er zijn geen gegevens passend bij uw selectie');

// ExecuteQuery.wo.Results
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_WO_RESULTS','Bedankt');

// ExecuteQuery.Failure.html.php
@define('_LANG_TEMPLATE_QUERY_NO_GO','Onmogelijk om uw Query uit voeren');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ERROR','Onmogelijk om uw Query uit voeren');

// Return.html.php
@define('_LANG_TEMPLATE_RETURN', 'Terug naar het vorige scherm');
@define('_LANG_TEMPLATE_CLEAR', 'Velden leegmaken');

?>

