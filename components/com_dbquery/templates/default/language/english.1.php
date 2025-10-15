<?php


/***************************************
 * $Id: english.php,v 1.3.2.2 2005/08/09 00:54:39 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.3.2.2 $
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// dbquery.php file
@define('_LANG_INVALID_INPUT','Invalid Input');
@define('_LANG_ACCESS_DENIED','Access is denied');
@define('_LANG_COULD_NOT_LOAD','Could not load data from the database');

// print.page.php
// New in 1.1
@define('_LANG_TEMPLATE_SELECT_QUERY','Select');
@define('_LANG_TEMPLATE_PREPARE_QUERY','Prepare');
@define('_LANG_TEMPLATE_EXECUTE_QUERY','Execute');
@define('_LANG_TEMPLATE_A_QUERY',' A Query');
@define('_LANG_TEMPLATE_CLOSE_WINDOW','Close This Window');
@define('_LANG_TEMPLATE_BACK','Back');

// General
@define('_LANG_TEMPLATE_NO_ACCESS', 'Access is not permitted');
@define('_LANG_TEMPLATE_GENERAL_ERROR','A general error occured while processing your request');
// Added for 1.4
// You will need to customize this for every query, if you choose to have multiple language defs
//@define('_LANG_TEMPLATE_GENERAL_DESCRIPTION','');

// Select Query
@define('_LANG_TEMPLATE_NAME', 'Name');
@define('_LANG_TEMPLATE_DESCRIPTION', 'Description');
@define('_LANG_TEMPLATE_NO_QUERIES_AVAILABLE', 'There are no queries available');

// PrepareQuery
@define('_LANG_TEMPLATE_FIELD', 'Attribute');
@define('_LANG_TEMPLATE_DATA', 'Option');
@define('_LANG_TEMPLATE_DESC', 'Description');
@define('_LANG_TEMPLATE_ENTER_OPTION', 'Select an option');
@define('_LANG_TEMPLATE_SUBMIT', 'Submit');
@define('_LANG_TEMPLATE_MISSING_VARS_FROM_STATEMENT', 'Please provide the requested input');
@define('_LANG_TEMPLATE_GENERAL_ERROR', 'A general error occured');
@define('_LANG_TEMPLATE_STAR_INDICATES','An astericks (*) indicates a required field');
@define('_LANG_TEMPLATE_REQUIRED_MARK','<span style="color:red">*</span>');
@define('_LANG_TEMPLATE_BEGIN_RED_SPAN','<span style="color:red">');
@define('_LANG_TEMPLATE_END_RED_SPAN','</span>');
// Added for 1.3
@define('_LANG_TEMPLATE_MAX_FILE_SIZE', 'Maximum file size is ');
// Added for 1.4
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_ABOVE','Please complete the following form');
@define('_LANG_TEMPLATE_PREPARE_DESCRIPTION_BELOW','');

// ExecuteQuery.Results
//@define('_LANG_TEMPLATE_EQWR_RESULTS', 'Results matching your criteria');
@define('_LANG_TEMPLATE_DISPLAY','Display');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ABOVE','Results matching your criteria');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_BELOW','');

// ExecuteQuery.no.Results
//@define('_LANG_TEMPLATE_INPUT_RECEIVED','Thank you for your input');
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_NO_RESULTS','There is no data matching your selection');

// ExecuteQuery.wo.Results
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_WO_RESULTS','Thank you');

// ExecuteQuery.Failure.html.php
//@define('_LANG_TEMPLATE_QUERY_NO_GO','Unable to execute your query');
// Added for 1.4
@define('_LANG_TEMPLATE_EXECUTE_DESCRIPTION_ERROR','Unable to execute your query');

// Return.html.php
@define('_LANG_TEMPLATE_RETURN', 'Return to the previous screen');
@define('_LANG_TEMPLATE_CLEAR', 'Clear the form');

// Input language codes
// You will need to customize these for each variable
// You should also change the language code in the Edit Variable screen
// Added for 1.4
//@define('_LANG_TEMPLATE_VARIABLE_NAME_DESCRIPTION','');
//@define('_LANG_TEMPLATE_VARIABLE_NAME_COMMENT','');
//@define('_LANG_TEMPLATE_VAIRALBE_NAME_INVALID','');
//@define('_LANG_TEMPLATE_VAIRALBE_NAME_DISPLAY','');
//@define('_LANG_TEMPLATE_NAME_DISPLAY','');

?>
