<?php

/**
 * Language for DBQ Settings
 *
 * @package DBQ
 * @subpackage DBQ_Settings
 *
 * $Rev::                 1.4.1. RC1
 * $Author::              vondeken
 * $Date::                2006-10-08
 *
 *
 */
defined( '_VALID_MOS' ) or die( 'Directe toegang tot deze lokatie is niet toegestaan.' );

// Display Regex

@define('_LANG_SETTINGS_DISPLAY_ALWAYS_COMMENT', 'Altijd regex informatie tonen.');
@define('_LANG_SETTINGS_DISPLAY_ALWAYS_NAME', 'Altijd');
@define('_LANG_SETTINGS_DISPLAY_NEVER_COMMENT', 'Laat nooit informatie over regex datatypes zien in de gebruikers forms');
@define('_LANG_SETTINGS_DISPLAY_NEVER_NAME', 'Nooit');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_COMMENT', 'Toon informatie over regex wanneer de gebruiker voordien een fout heeft gesubmit.');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_NAME', 'Bij een fout');
//@define('_LANG_SETTINGS_DISPLAY_','');

// Error Codes
@define('_LANG_SETTINGS_ERROR_UNKNOWN_COMMENT', 'Een fout met onbekende oorzaak');
@define('_LANG_SETTINGS_ERROR_UNKNOWN_NAME', 'Onbekende fout');
@define('_LANG_SETTINGS_ERROR_USER_COMMENT', 'Een gebruikersfout met onverwacht effect');
@define('_LANG_SETTINGS_ERROR_USER_NAME', 'Gebruikersfout');
@define('_LANG_SETTINGS_ERROR_APPLICATION_COMMENT', 'Een Applicatie Fout met onverwacht gedrag');
@define('_LANG_SETTINGS_ERROR_APPLICATION_NAME', 'Applicatie Fout');
@define('_LANG_SETTINGS_ERROR_CRITICAL_COMMENT', 'Een Systeem Fout door ontbrekende Bestanden of andere ernstige problemen');
@define('_LANG_SETTINGS_ERROR_CRITICAL_NAME', 'Ernstige Fout');
@define('_LANG_SETTINGS_ERROR_QUERY_COMMENT', 'Een geregistreerde query faalde en kon niet worden uitgevoerd');
@define('_LANG_SETTINGS_ERROR_QUERY_NAME', 'Query Fout');
//@define('_LANG_SETTINGS_ERROR_','');

// Regular Expressions
@define('_LANG_SETTINGS_REGEX_ALNUM_COMMENT', 'Letters and Numbers, without spaces or punctuation') ;
@define('_LANG_SETTINGS_REGEX_ALNUM_NAME', 'Alphanumeric Characters' ) ;
@define('_LANG_SETTINGS_REGEX_ANY_COMMENT', 'Any Text') ;
@define('_LANG_SETTINGS_REGEX_ANY_NAME', 'Alles' ) ;
@define('_LANG_SETTINGS_REGEX_COMP_COMMENT', 'Vergelijking') ;
@define('_LANG_SETTINGS_REGEX_COMP_NAME', 'Vergelijking' ) ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_COMMENT', 'Munteenheid') ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_NAME', 'Munteenheid') ;
@define('_LANG_SETTINGS_REGEX_DATETIME_COMMENT', 'Datum en Tijd (YYYY-MM-DD HH:MM:SS)');
@define('_LANG_SETTINGS_REGEX_DATETIME_NAME', 'Datetime' );
@define('_LANG_SETTINGS_REGEX_DIGIT_COMMENT', 'Cijfers') ;
@define('_LANG_SETTINGS_REGEX_DIGIT_NAME', 'Cijfers' ) ;
@define('_LANG_SETTINGS_REGEX_EMAIL_COMMENT', 'Email Adres') ;
@define('_LANG_SETTINGS_REGEX_EMAIL_NAME', 'Email' ) ;
@define('_LANG_SETTINGS_REGEX_EURODATE_COMMENT', 'Europese Datumstijl (DD/MM/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_EURODATE_NAME', 'Europese Datum Stijl') ;
@define('_LANG_SETTINGS_REGEX_FLOAT_COMMENT', 'Getallen met een optionele decimaal') ;
@define('_LANG_SETTINGS_REGEX_FLOAT_NAME', 'Float' ) ;
@define('_LANG_SETTINGS_REGEX_INT_COMMENT', 'Getallen, maar zonder tekens, decimalen of eenheden') ;
@define('_LANG_SETTINGS_REGEX_INT_NAME', 'Integer' ) ;
@define('_LANG_SETTINGS_REGEX_PUNCT_COMMENT', 'Punctuation') ;
@define('_LANG_SETTINGS_REGEX_PUNCT_NAME', 'Punctuation' ) ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_COMMENT', 'Getallen met een optioneel plus- of minteken') ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_NAME', 'Cijfers met een teken' ) ;
@define('_LANG_SETTINGS_REGEX_SWITCH_COMMENT', 'Ja of Nee') ;
@define('_LANG_SETTINGS_REGEX_SWITCH_NAME', 'Verwissel' ) ;
@define('_LANG_SETTINGS_REGEX_TEXT_COMMENT', 'Normale Tekst') ;
@define('_LANG_SETTINGS_REGEX_TEXT_NAME', 'Text' ) ;
@define('_LANG_SETTINGS_REGEX_TINYINT_COMMENT', 'Getallen, maar zonder tekens, decimalen of eenheden') ;
@define('_LANG_SETTINGS_REGEX_TINYINT_NAME', 'Tiny Integer' ) ;
@define('_LANG_SETTINGS_REGEX_URL_COMMENT', 'Web Adres');
@define('_LANG_SETTINGS_REGEX_URL_NAME', 'Web URL');
@define('_LANG_SETTINGS_REGEX_USDATE_COMMENT', 'VS Datumstijl(MM/DD/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_USDATE_NAME', 'VS Datumstijl' ) ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_COMMENT', 'Normale Tekst') ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_NAME', 'Varchar' ) ;
@define('_LANG_SETTINGS_REGEX_XDIGIT_COMMENT', 'Hexadecimale Getal (0-9A-F)');
@define('_LANG_SETTINGS_REGEX_XDIGIT_NAME', 'Hexadecimaal' );
//@define('_LANG_SETTINGS_REGEX_','');

// Requirement Options
@define('_LANG_SETTINGS_REQUIRE_BLANK', 'Accepteer Spaties');
@define('_LANG_SETTINGS_REQUIRE_DEFAULT', 'Gebruik Default');
@define('_LANG_SETTINGS_REQUIRE_NO', 'Niet verplicht');
@define('_LANG_SETTINGS_REQUIRE_YES', 'Verplicht');
//@define('_LANG_SETTINGS_REQUIRE_','');

// Input Types
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_COMMENT','Selecteer gepresenteerde waarden');
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_NAME','Vinkhokje');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_COMMENT','Blader Knop voor Bestand Upload');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_NAME','Bestand Upload');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_COMMENT','Verborgen Veld');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_NAME','Verborgen Invoer');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_COMMENT','Gebruik de editor om de gewenste informatie te schrijven');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_NAME','HTML Editor');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_COMMENT','Selecteer meerdere opties uit de lijst');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_NAME','Uitklap-Lijst - Meervoudige Selectie');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_COMMENT','Geef de gewenste informatie');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_NAME','Password');
@define('_LANG_SETTINGS_INPUTS_RADIO_COMMENT','Selecteer één van de gepresenteerde waarden');
@define('_LANG_SETTINGS_INPUTS_RADIO_NAME','Radio');
@define('_LANG_SETTINGS_INPUTS_SELECT_COMMENT','Selecteer een optie uit de lijst');
@define('_LANG_SETTINGS_INPUTS_SELECT_NAME','Uitklap-Lijst');
@define('_LANG_SETTINGS_INPUTS_TEXT_COMMENT','Geef de gewenste informatie');
@define('_LANG_SETTINGS_INPUTS_TEXT_NAME','Text Box');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_COMMENT','Geef de gewenste informatie');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_NAME','Tekst Gebied');
//@define('_LANG_SETTINGS_INPUTS_','');

?>
