<?php

/**
 * Language for DBQ Settings
 *
 * @package DBQ
 * @subpackage DBQ_Settings
 *
 * $Rev:: 1.0              $
 * $Author:: Nikolai Plath $
 * $Date:: 21.06.2006      $
 *
 * German
 */
defined( '_VALID_MOS' ) or die( 'Eingeschr&auml;nkter Zugang.' );

// Display Regex

@define('_LANG_SETTINGS_DISPLAY_ALWAYS_COMMENT', 'Immer die regex Informationen anzeigen.');
@define('_LANG_SETTINGS_DISPLAY_ALWAYS_NAME', 'Immer');
@define('_LANG_SETTINGS_DISPLAY_NEVER_COMMENT', 'Nie die informationen &uuml;ber regex datentypen in den Benutzerformularen anzeigen');
@define('_LANG_SETTINGS_DISPLAY_NEVER_NAME', 'Nie');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_COMMENT', 'Information &uuml;ber regex anzeigen wenn der Benutzer vorher einen Fehler &uuml;bergeben hat.');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_NAME', 'On Error');
//@define('_LANG_SETTINGS_DISPLAY_','');

// Error Codes
@define('_LANG_SETTINGS_ERROR_UNKNOWN_COMMENT', 'Ein Fehler aus einer unbekannten Quelle');
@define('_LANG_SETTINGS_ERROR_UNKNOWN_NAME', 'Unbekannter Fehler');
@define('_LANG_SETTINGS_ERROR_USER_COMMENT', 'Ein Benutzerfehler, der ein unerwartetes Verhalten hervorgerufen hat.');
@define('_LANG_SETTINGS_ERROR_USER_NAME', 'Benutzerfehler');
@define('_LANG_SETTINGS_ERROR_APPLICATION_COMMENT', 'Ein Programmfehler der ein unerwartetes Verhalten hervorgerufen hat.');
@define('_LANG_SETTINGS_ERROR_APPLICATION_NAME', 'Programmfehler');
@define('_LANG_SETTINGS_ERROR_CRITICAL_COMMENT', 'Ein Systemfehler der eine fehlende Datei oder ein anderes kritisches Problem signalisiert.');
@define('_LANG_SETTINGS_ERROR_CRITICAL_NAME', 'Kritischer Fehler');
@define('_LANG_SETTINGS_ERROR_QUERY_COMMENT', 'Ein registrierter Query hat einen Fehler verursacht und konnte nicht ausgef&uuml;hrt werden');
@define('_LANG_SETTINGS_ERROR_QUERY_NAME', 'Queryfehler');
//@define('_LANG_SETTINGS_ERROR_','');

// Regular Expressions
@define('_LANG_SETTINGS_REGEX_ALNUM_COMMENT', 'Buchstaben und Nummern, ohne Leerzeichen oder punkte.') ;
@define('_LANG_SETTINGS_REGEX_ALNUM_NAME', 'Alfanumerische Zeichen' ) ;
@define('_LANG_SETTINGS_REGEX_ANY_COMMENT', 'Jeder Text') ;
@define('_LANG_SETTINGS_REGEX_ANY_NAME', 'Alles' ) ;
@define('_LANG_SETTINGS_REGEX_COMP_COMMENT', 'Vergleich') ;
@define('_LANG_SETTINGS_REGEX_COMP_NAME', 'Vergleich' ) ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_COMMENT', 'W&auml;hrung') ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_NAME', 'W&auml;hrung' ) ;
@define('_LANG_SETTINGS_REGEX_DATETIME_COMMENT', 'Datum und Uhrzeit (YYYY-MM-DD HH:MM:SS)');
@define('_LANG_SETTINGS_REGEX_DATETIME_NAME', 'Datetime' );
@define('_LANG_SETTINGS_REGEX_DIGIT_COMMENT', 'Digits') ;
@define('_LANG_SETTINGS_REGEX_DIGIT_NAME', 'Digits' ) ;
@define('_LANG_SETTINGS_REGEX_EMAIL_COMMENT', 'Email Adresse') ;
@define('_LANG_SETTINGS_REGEX_EMAIL_NAME', 'Email' ) ;
@define('_LANG_SETTINGS_REGEX_EURODATE_COMMENT', 'Europ&auml;isches Datum (DD/MM/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_EURODATE_NAME', 'Europ&auml;isches Datum' ) ;
@define('_LANG_SETTINGS_REGEX_FLOAT_COMMENT', 'Nummern mit optionalem Dezimalpunkt') ;
@define('_LANG_SETTINGS_REGEX_FLOAT_NAME', 'Fliesspunkt' ) ;
@define('_LANG_SETTINGS_REGEX_INT_COMMENT', 'Nummern, aber ohne Vorzeichen, Dezimalpunkt, oder Einheiten') ;
@define('_LANG_SETTINGS_REGEX_INT_NAME', 'Integer' ) ;
@define('_LANG_SETTINGS_REGEX_PUNCT_COMMENT', 'Punctuation') ;
@define('_LANG_SETTINGS_REGEX_PUNCT_NAME', 'Punctuation' ) ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_COMMENT', 'Nummern mit optionalem Plus- oder Minuszeichen') ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_NAME', 'Zahl mit Vorzeichen' ) ;
@define('_LANG_SETTINGS_REGEX_SWITCH_COMMENT', 'Ja oder Nein') ;
@define('_LANG_SETTINGS_REGEX_SWITCH_NAME', 'Wechsel' ) ;
@define('_LANG_SETTINGS_REGEX_TEXT_COMMENT', 'Normaler Text') ;
@define('_LANG_SETTINGS_REGEX_TEXT_NAME', 'Text' ) ;
@define('_LANG_SETTINGS_REGEX_TINYINT_COMMENT', 'Nummern, aber ohne Vorzeichen, Dezimalpunkt, oder Einheiten') ;
@define('_LANG_SETTINGS_REGEX_TINYINT_NAME', 'Tiny Integer' ) ;
@define('_LANG_SETTINGS_REGEX_USDATE_COMMENT', 'US Datum (MM/DD/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_USDATE_NAME', 'US Datum' ) ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_COMMENT', 'Normaler Text') ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_NAME', 'Varchar' ) ;
@define('_LANG_SETTINGS_REGEX_XDIGIT_COMMENT', 'Hexadezimalzahl (0-9A-F)');
@define('_LANG_SETTINGS_REGEX_XDIGIT_NAME', 'Hexadezimal' );
//@define('_LANG_SETTINGS_REGEX_','');

// Requirement Options
@define('_LANG_SETTINGS_REQUIRE_BLANK', 'Akzeptiere Leerzeichen');
@define('_LANG_SETTINGS_REQUIRE_DEFAULT', 'Benutze Voreinstellungen');
@define('_LANG_SETTINGS_REQUIRE_NO', 'Kein Pflichtfeld');
@define('_LANG_SETTINGS_REQUIRE_YES', 'Pflichtfeld');
//@define('_LANG_SETTINGS_REQUIRE_','');

// Input Types
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_COMMENT','W&auml;hlen sie einen der folgenden Werte');
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_NAME','Checkbox');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_COMMENT','Browse Button zum Fileupload');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_NAME','File Upload');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_COMMENT','Verstecktes Feld');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_NAME','Verstecktes Eingabefeld');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_COMMENT','Benutzen sie den Editor um die ben&ouml;tigten Informationen zu schreiben');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_NAME','HTML Editor');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_COMMENT','W&auml;hlen sie mehrere Optionen aus der Liste');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_NAME','Drop-Down Liste Multiselect');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_COMMENT','Stellen sie die geforderten Informationen zur Verf&uuml;gung');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_NAME','Passwort');
@define('_LANG_SETTINGS_INPUTS_RADIO_COMMENT','W&auml;hlen sie eine der folgenden Optionen');
@define('_LANG_SETTINGS_INPUTS_RADIO_NAME','Radio');
@define('_LANG_SETTINGS_INPUTS_SELECT_COMMENT','W&auml;hlen sie eine Option aus der Liste');
@define('_LANG_SETTINGS_INPUTS_SELECT_NAME','Drop Down Liste');
@define('_LANG_SETTINGS_INPUTS_TEXT_COMMENT','Stellen sie die geforderten Informationen zur Verf&uuml;gung');
@define('_LANG_SETTINGS_INPUTS_TEXT_NAME','Text Box');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_COMMENT','Stellen sie die geforderten Informationen zur Verf&uuml;gung');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_NAME','Textfeld');
//@define('_LANG_SETTINGS_INPUTS_','');

?>
