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

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

@define("_LANG_ID","ID");
@define('_LANG_NAME','Name');
@define('_LANG_DESCRIPTION','Description');
@define('_LANG_ACCESS_DENIED','You do not have access to this resource');
@define('_LANG_INPUT_REQUIRED','You have not provided the proper input');
@define('_LANG_YES','Yes');
@define('_LANG_NO','No');
@define('_LANG_COMMENT','Comment');
@define('_LANG_LABEL','Label');
@define('_LANG_REQUIRED','Required');
@define('_LANG_SIZE','Size');
@define('_LANG_PUBLISHED','Published');
@define('_LANG_CATEGORY','Category');
@define('_LANG_CATEGORIES','Categories');
@define('_LANG_PARSED','Parsed');
@define('_LANG_PARSE','Parse');
@define('_LANG_PREVIEW','Preview');
@define('_LANG_ACCESS','Access');
@define('_LANG_REORDER','Reorder');
@define('_LANG_HITS','Hits');
@define('_LANG_ORDER','Order');
@define('_LANG_CHECKED_OUT','Checked Out');
@define('_LANG_HOSTNAME','Hostname');
@define('_LANG_ONLINE','On Line');
@define('_LANG_SEARCH','Search');
@define('_LANG_DISPLAY','Display');
@define('_LANG_SELECT','Select ');
@define('_LANG_GROUP','Group');
@define('_LANG_TYPE','Type');
@define('_LANG_REGEX','Regex');
@define('_LANG_DB', 'Database');
@define('_LANG_DBS', 'Databases');
@define('_LANG_ALL','All ');
@define('_LANG_EDIT','Edit');
@define('_LANG_ADD','Add');
@define('_LANG_EDIT_DATABASE','Edit Database');
@define('_LANG_ADD_DATABASE','Add Database');
@define('_LANG_FIELD_REQUIRED','You must provided a value for');
@define('_LANG_USES_VARIABLES','Uses Variables');
@define('_LANG_DISPLAY_NAME','Title');
@define('_LANG_STATEMENT','Statement');
@define('_LANG_LIST','List');
@define('_LANG_KEYWORD','Keyword');
@define('_LANG_CONFIGURATION','Configuration');
@define('_LANG_CONFIGURATIONS','Configurations');
@define('_LANG_QUERY','Query');
@define('_LANG_QUERIES','Queries');
@define('_LANG_STAR_INDICATES','An astericks (*) indicates a required field');
@define('_LANG_REQUIRED_MARK','<SPAN style="color:red">*</SPAN>');
@define('_LANG_BEGIN_RED_SPAN','<SPAN style="color:red">');
@define('_LANG_END_RED_SPAN','</SPAN>');
@define('_LANG_KEY','Key');
@define('_LANG_VALUE','Value');
@define('_LANG_INPUT','Input');
@define('_LANG_DBTYPE','Database Types');
@define('_LANG_DBDRIVER','Database Drivers');
@define('_LANG_COUNTSQL','Count SQL');
@define('_LANG_DISPLAY_REGEX','Display Regex');
@define('_LANG_SUBSTITUTION','Substitution');
@define('_LANG_SUBSTITUTIONS','Substitutions');
@define('_LANG_TEMPLATE','Template');
@define('_LANG_MAX_SIZE','Maximum Size is ');
@define('_LANG_EQWR_NO_RESULTS','There is no data matching you selection');
@define('_LANG_EQWR_RESULTS',"Returned results: ");
@define('_LANG_PARENT','Parent');
@define('_LANG_QUERY_NO_GO','Your query could not be executed');
@define('_LANG_INPUT_RECEIVED','Thank you for your input.');
@define('_LANG_YOUR_SQL','Your SQL is: ');
@define('_LANG_REPRESENTED_BY','Represented By : ');
@define('_LANG_IN_STATEMENT','Found in Statement : ');
@define('_LANG_NO_PASS_REGEX','did not pass regex');
@define('_LANG_PAGE_COMPANY','Professional Consulting');
@define('_LANG_PAGE_SUPPORT','Support and FAQ');
@define('_LANG_PAGE_FORUM','Forum');
@define('_LANG_PAGE_DOWNLOAD','Download');
@define('_LANG_DBQ_VERSION','DBQ Version');
@define('_LANG_PHP_VERSION','PHP Version');
@define('_LANG_JOOMLA_VERSION','Joomla Version');
@define('_LANG_WEBSERVER_VERSION','Webserver Version');
@define('_LANG_SUBMISSIONS','Submissions');
@define('_LANG_STATS','Stats');
@define('_LANG_USER_CONFIG','User Configurations');

// admin screens
// Added for 1.1
@define('_LANG_ADMIN_IN_USE','The current record is being edited by another person');
@define('_LANG_SELECT_ITEM', 'Select an item');
@define('_LANG_ITEM_NOT_DELETED', 'The selected item could not be deleted');
@define('_LANG_ERROR', 'Error: ');
@define('_LANG_COULDNOT_PUBLISH', 'The selected item could not be published or unpublished');
@define('_LANG_ACTIVE','Active');
@define('_LANG_TIME_START', 'From');
@define('_LANG_TIME_END', 'Until');
@define('_LANG_INVALID_DATE_RANGE','Invalid Date Range');
@define('_LANG_MANAGER','Manager');
@define('_LANG_DIRECTORY','Directory');
@define('_LANG_AUTHOR','Author');
@define('_LANG_VERSION','Version');
@define('_LANG_DATE','Date');
@define('_LANG_AUTHOR_URL','Author URL');
@define('_LANG_INFORMATION', 'Information');

// Added for 1.2
@define('_LANG_DEFAULT', 'Default');
@define('_LANG_OPENING_HELP_WINDOW', 'Opening the help window now.');
@define('_LANG_PERCENT', 'Percent');


// details.html.php
// Added for 1.1
@define('_LANG_DETAILS_CONFIG','Configuration');
@define('_LANG_DETAILS_ATTRIBUTES','Attributes');
@define('_LANG_DETAILS_TEXT','Text');
@define('_LANG_DETAILS_PARSE','Parse');
// Added for 1.2
@define('_LANG_DETAILS_PARAMS','Parameters');
@define('_LANG_DETAILS_DEFAULT_VALUE', 'Default Value');
@define('_LANG_DETAILS_LIST', 'List');

// summary.html.php
// Added for 1.1
@define('_LANG_FILTER','Filter: ');
@define('_LANG_UNDER_DEVELOPMENT','This feature is currently under development.');
@define('_LANG_ON_LINE','On Line');
@define('_LANG_COPY_OF','Copy of ');
// Added for 1.4, Dev3
@define('_LANG_NONE', 'None');

// internal in class files but used only by admin screens
// Added for 1.1
@define('_LANG_CHANGES_SAVED', 'Your changes have been saved');
@define('_LANG_CHANGES_NOT_SAVED', 'Your changes could not be saved');
@define('_LANG_UNKNOWN','Unknown');

// support.html.php
// Added for 1.0
@define('_LANG_PEAR_SUPPORT','PEAR Interface Support');
// Added for 1.1
@define('_LANG_JOOMLA_SUPPORT','Joomla Database Interface Support');
@define('_LANG_ADODB_SUPPORT','ADODB Interface Support');
@define('_LANG_MYSQL_SUPPORT','MySQL Interface Support');
@define('_LANG_MYSQLI_SUPPORT','MySQLi Interface Support');
// Added for 1.2
@define('_LANG_INCLUDE_PATH', 'Include Path');

// details.html.php
// Added for 1.3
@define('_LANG_DETAILS_NOTIFICATION', 'Notifications');
@define('_LANG_DETAILS_SPECIAL', 'Special');

// General 1.3 Additions
@define ('_LANG_DETAILS_CONTACT','Notifications');
@define ('_LANG_UPDATE','Update');

// Template Editing Text
// Added for 1.3
@define('_LANG_TEMPLATE_NOT_FOUND', 'The template file is not found');
@define('_LANG_TEMPLATE_NOT_READABLE', 'The template file is not readable');
@define('_LANG_TEMPLATE_NOT_WRITABLE', 'The template file is not writable');
@define('_LANG_DIRECTORY_NOT_FOUND', 'The direcotory is not found');
@define('_LANG_DIRECTORY_NOT_READABLE', 'The direcotory is not readable');
@define('_LANG_DIRECTORY_NOT_WRITABLE', 'The direcotory is not writable');
@define('_LANG_DIRECTORY_COPIED', 'The directory has been successfully copied');
@define('_LANG_DIRECTORY_NOT_COPIED', 'The directory could not be copied');

// File Editor
// Added for 1.3
@define('_LANG_FILE_EDITOR', 'File Editor');
@define('_LANG_FILE_IS', 'File is ');
@define('_LANG_IS_WRITABLE', 'Writable');
@define('_LANG_IS_UNWRITABLE', 'Not Writable');

// Error Reporting
@define('_LANG_ERRORS', 'Errors');
@define('_LANG_SOURCE', 'Source');
@define('_LANG_OBJECT', 'Object');
@define('_LANG_DATE_REPORTED', 'Date Reported');
@define('_LANG_MESSAGE', 'Message');
@define('_LANG_PRIORITY', 'Priority');

// Variable Objects
// Added for 1.4
@define('_LANG_VT_CODE', 'Server Side Code Variable');
@define('_LANG_VT_FILES', 'File Select Variable');
@define('_LANG_VT_KEYWORD', 'Keyword Variable');
@define('_LANG_VT_RESULTS', 'Query Result Variable');
@define('_LANG_VT_STATEMENT', 'Statement Variable');
@define('_LANG_VT_SUBSTITUTIONS', 'Substitution Variable');
@define('_LANG_VT_FILE_UPLOAD', 'File Upload Variable');
@define('_LANG_VT_USER', 'Joomla User Variable');
@define('_LANG_VT_CUSTOM', 'Custom Variable');
@define('_LANG_VT_FIELD', 'Field Variable');

// Adding tags that were previously included in the frontend lang file
@define ('_LANG_CONFIG','Configurations');
@define ('_LANG_EREG','Regular Expressions');
@define ('_LANG_FILES','Files');
@define ('_LANG_VARIABLE', 'Variable');
@define ('_LANG_VARIABLES', 'Variables');

// Misc additions in 1.4
@define('_LANG_TYPES','Types');
@define('_LANG_PROFESSIONAL', 'DBQ Professional');
@define ('_LANG_MAIL','Mail');
@define('_LANG_DBQ_PROFESSIONAL_INSTALLED','Professional Version Installed');
//@define ('_LANG_','');
?>