<?php


defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

@define('_LANG_NO_ACCESS','Direct Access to this location is not allowed.');
@define('_LANG_INVALID_INPUT','The data submitted is not valid input');
@define('_LANG_ACCESS_DENIED','Access to this resource is denied');
@define('_LANG_GENERAL_ERROR','A error occured while processing your request');
@define('_LANG_VARIABLE','Variable');
@define('_LANG_VARIABLES','Variables');
@define('_LANG_GENERAL_ERROR', 'A general error occured');
@define('_LANG_EREG','Regular Expressions');
@define('_LANG_INPUT_NOT_IN_LIST','The selected input is not in the list');
@define('_LANG_INPUT_DOESNT_MATCH_REGEX','The input contains characters which are not allowed');
@define('_LANG_MISSING_INPUT','The input is required but missing');
@define('_LANG_SQLPROBLEM_EXECUTE','Could not execute the query ');
@define('_LANG_SQLPROBLEM_PARSE_FROM','Could not find a FROM statement in the query ');
@define('_LANG_DB_CONNECT_FAILED','Could not connect to the database');
@define('_LANG_UNSUPPORTED_DRIVER','Database misconfigured to use an unsupported driver');
@define('_LANG_LANG_FAILED_CUSTOM_QUERY','Unable to execute the custom query for QID ');
@define('_LANG_UNKNOWN_QID','Unknown Query ID');
@define('_LANG_INVALID_VID','Invalid Variable ID');
@define('_LANG_INPUT_RECEIVED','Thank you for your input.');
@define('_LANG_PARAMETER_MISSING','A function was called without a required parameter.');
@define('_LANG_KEYWORD','Keyword');
@define('_LANG_STATEMENT','Statement');
@define('_LANG_NO_TEMPLATE_LANGUAGE_FILE','Unable to retrieve the template language file');
@define('_LANG_COULD_NOT_LOAD_FILE','Could not load the requested file');
@define('_LANG_TOO_LARGE','The input exceeds the specified size requirements');
@define('_LANG_INPUT_REQUIRED','You have not provided the proper input');

// New in 1.1
@define('_LANG_UNKNOWN_REGEX','Unknown regex input');
@define('_LANG_NO_RETRIEVE_SUB','Could not retrieve list substitution for the variable');
@define('_LANG_NO_DATABASE_CONFIG', 'Could not retrieve the database configuration');

// New in 1.2
@define('_LANG_CANNOT_READ_DIRECTORY', 'Cannot read files from the directory ');

// New in 1.3
@define('_LANG_DIRECTORY_NOT_WRITTABLE', 'The destination directory is not writtable');
@define('_LANG_FILE_EXISTS', 'The filename already exists');
@define('_LANG_TEMP_FILE_MISSING', 'The temporary file does not exist');
@define('_LANG_FILE_TOO_LARGE', 'The file exceeds the size limit');
@define('_LANG_FILE_WACKO', 'The tempoary file is not recognized as the file which you uploaded');
@define('_LANG_STMT_INCOMPLETE', 'Additional input is required to complete the query');
@define('_LANG_INPUT_EXCEED_MAX_SIZE', 'The input string exceeds the maximum size of ');
@define('_LANG_CODE', 'Code');

// Required by the DBQBase class when evaling configuration info from the db
@define('_LANG_REQUIRED','Requirement Options');
@define('_LANG_CONFIG','DBQ Configurations');
@define('_LANG_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_INPUT','Input Types');
@define('_LANG_DBTYPE','Database Types');
@define('_LANG_DBDRIVER','Database Drivers');
@define('_LANG_COUNTSQL','Count SQL');
@define('_LANG_DISPLAY_REGEX','Display Regex');
@define('_LANG_USER_CONFIG','User Configurations');
@define('_LANG_FILES','Template Files');

// Misc dded in 1.4
@define('_LANG_ERROR','Error');
@define('_LANG_PROFESSIONAL', 'DBQ Professional');
@define('_LANG_DESCRIPTION', 'Description');
@define('_LANG_MAIL', 'Mail');
?>
