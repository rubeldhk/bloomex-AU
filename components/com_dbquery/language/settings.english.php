<?php

/**
 * Language for DBQ Settings
 *
 * @package DBQ
 * @subpackage DBQ_Settings
 *
 * $Rev::                 $
 * $Author::              $
 * $Date::                $
 *
 *
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Display Regex

@define('_LANG_SETTINGS_DISPLAY_ALWAYS_COMMENT', 'Always display regex information.');
@define('_LANG_SETTINGS_DISPLAY_ALWAYS_NAME', 'Always');
@define('_LANG_SETTINGS_DISPLAY_NEVER_COMMENT', 'Never display information about regex datatypes in the user forms');
@define('_LANG_SETTINGS_DISPLAY_NEVER_NAME', 'Never');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_COMMENT', 'Display information about regex when the user has previously submitted an error.');
@define('_LANG_SETTINGS_DISPLAY_ON_ERROR_NAME', 'On Error');
//@define('_LANG_SETTINGS_DISPLAY_','');

// Error Codes
@define('_LANG_SETTINGS_ERROR_UNKNOWN_COMMENT', 'An error from an unknown source');
@define('_LANG_SETTINGS_ERROR_UNKNOWN_NAME', 'Unknown Error');
@define('_LANG_SETTINGS_ERROR_USER_COMMENT', 'An user error that caused unexpected behavior');
@define('_LANG_SETTINGS_ERROR_USER_NAME', 'User Error');
@define('_LANG_SETTINGS_ERROR_APPLICATION_COMMENT', 'An application error that caused an unexpected behavior');
@define('_LANG_SETTINGS_ERROR_APPLICATION_NAME', 'Application Error');
@define('_LANG_SETTINGS_ERROR_CRITICAL_COMMENT', 'An system error that represents missing files or other critical problems');
@define('_LANG_SETTINGS_ERROR_CRITICAL_NAME', 'Critical Error');
@define('_LANG_SETTINGS_ERROR_QUERY_COMMENT', 'An registered query erred and could not be executed');
@define('_LANG_SETTINGS_ERROR_QUERY_NAME', 'Query Error');
//@define('_LANG_SETTINGS_ERROR_','');

// Regular Expressions
@define('_LANG_SETTINGS_REGEX_ALNUM_COMMENT', 'Letters and Numbers, without spaces or punctuation') ;
@define('_LANG_SETTINGS_REGEX_ALNUM_NAME', 'Alphanumeric Characters' ) ;
@define('_LANG_SETTINGS_REGEX_ANY_COMMENT', 'Any Text') ;
@define('_LANG_SETTINGS_REGEX_ANY_NAME', 'Anything' ) ;
@define('_LANG_SETTINGS_REGEX_COMP_COMMENT', 'Comparison') ;
@define('_LANG_SETTINGS_REGEX_COMP_NAME', 'Comparison' ) ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_COMMENT', 'Currency') ;
@define('_LANG_SETTINGS_REGEX_CURRENCY_NAME', 'Currency' ) ;
@define('_LANG_SETTINGS_REGEX_DATETIME_COMMENT', 'Date and time (YYYY-MM-DD HH:MM:SS)');
@define('_LANG_SETTINGS_REGEX_DATETIME_NAME', 'Datetime' );
@define('_LANG_SETTINGS_REGEX_DIGIT_COMMENT', 'Digits') ;
@define('_LANG_SETTINGS_REGEX_DIGIT_NAME', 'Digits' ) ;
@define('_LANG_SETTINGS_REGEX_EMAIL_COMMENT', 'Email Address') ;
@define('_LANG_SETTINGS_REGEX_EMAIL_NAME', 'Email' ) ;
@define('_LANG_SETTINGS_REGEX_EURODATE_COMMENT', 'European Style of Date (DD/MM/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_EURODATE_NAME', 'European Style Date' ) ;
@define('_LANG_SETTINGS_REGEX_FLOAT_COMMENT', 'Numbers with an optional decimal') ;
@define('_LANG_SETTINGS_REGEX_FLOAT_NAME', 'Float' ) ;
@define('_LANG_SETTINGS_REGEX_INT_COMMENT', 'Numbers, but without signs, decimals, or units') ;
@define('_LANG_SETTINGS_REGEX_INT_NAME', 'Integer' ) ;
@define('_LANG_SETTINGS_REGEX_PUNCT_COMMENT', 'Punctuation') ;
@define('_LANG_SETTINGS_REGEX_PUNCT_NAME', 'Punctuation' ) ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_COMMENT', 'Numbers with optional plus or minus signs') ;
@define('_LANG_SETTINGS_REGEX_SIGNEDDIGIT_NAME', 'Signed Digit' ) ;
@define('_LANG_SETTINGS_REGEX_SWITCH_COMMENT', 'Yes or No') ;
@define('_LANG_SETTINGS_REGEX_SWITCH_NAME', 'Switch' ) ;
@define('_LANG_SETTINGS_REGEX_TEXT_COMMENT', 'Normal Text') ;
@define('_LANG_SETTINGS_REGEX_TEXT_NAME', 'Text' ) ;
@define('_LANG_SETTINGS_REGEX_TINYINT_COMMENT', 'Numbers, but without signs, decimals, or units') ;
@define('_LANG_SETTINGS_REGEX_TINYINT_NAME', 'Tiny Integer' ) ;
@define('_LANG_SETTINGS_REGEX_URL_COMMENT', 'Web Address');
@define('_LANG_SETTINGS_REGEX_URL_NAME', 'Web URL');
@define('_LANG_SETTINGS_REGEX_USDATE_COMMENT', 'US Style of Date (MM/DD/YYYY)') ;
@define('_LANG_SETTINGS_REGEX_USDATE_NAME', 'US Style Date' ) ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_COMMENT', 'Normal Text') ;
@define('_LANG_SETTINGS_REGEX_VARCHAR_NAME', 'Varchar' ) ;
@define('_LANG_SETTINGS_REGEX_XDIGIT_COMMENT', 'Hexadecimal Digits (0-9A-F)');
@define('_LANG_SETTINGS_REGEX_XDIGIT_NAME', 'Hexadecimal' );
//@define('_LANG_SETTINGS_REGEX_','');

// Requirement Options
@define('_LANG_SETTINGS_REQUIRE_BLANK', 'Accept Blanks');
@define('_LANG_SETTINGS_REQUIRE_DEFAULT', 'Use Default');
@define('_LANG_SETTINGS_REQUIRE_NO', 'Not Required');
@define('_LANG_SETTINGS_REQUIRE_YES', 'Required');
//@define('_LANG_SETTINGS_REQUIRE_','');

// Input Types
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_COMMENT','Select one of the presented values');
@define('_LANG_SETTINGS_INPUTS_CHECKBOX_NAME','Checkbox');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_COMMENT','Browse Button for file upload');
@define('_LANG_SETTINGS_INPUTS_FILEUPLOAD_NAME','File Upload');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_COMMENT','Hidden field');
@define('_LANG_SETTINGS_INPUTS_HIDDEN_NAME','Hidden Input');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_COMMENT','Use the editor to write the requested information');
@define('_LANG_SETTINGS_INPUTS_HTMLEDITOR_NAME','HTML Editor');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_COMMENT','Select multiple options from the list');
@define('_LANG_SETTINGS_INPUTS_MULTISELECT_NAME','Drop Down List - Multiselect');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_COMMENT','Provide the requested information');
@define('_LANG_SETTINGS_INPUTS_PASSWORD_NAME','Password');
@define('_LANG_SETTINGS_INPUTS_RADIO_COMMENT','Select one of the presented values');
@define('_LANG_SETTINGS_INPUTS_RADIO_NAME','Radio');
@define('_LANG_SETTINGS_INPUTS_SELECT_COMMENT','Select an option from the list');
@define('_LANG_SETTINGS_INPUTS_SELECT_NAME','Drop Down List');
@define('_LANG_SETTINGS_INPUTS_TEXT_COMMENT','Provide the requested information');
@define('_LANG_SETTINGS_INPUTS_TEXT_NAME','Text Box');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_COMMENT','Provide the requested information');
@define('_LANG_SETTINGS_INPUTS_TEXTAREA_NAME','Text Area');
//@define('_LANG_SETTINGS_INPUTS_','');

?>