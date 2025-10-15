
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

// Prohibit direct access
defined('_VALID_MOS') or die('Direct access is not permitted to this file');

class DBQ_Settings {
	

	// Various types of configuration data that we keep in this class
	var $adminclasses = NULL;
	var $adminurls = NULL;
	var $displayRegex = NULL;
	var $errorCodes = NULL;
	var $regularExpressions = NULL;
	var $requiredOptions = NULL;
	var $tables = NULL;
	var $templateFiles = NULL;
	var $userclasses = NULL;
	var $validInputs = NULL;
	
	function DBQ_Settings() {

		// Load the language file for settings
		global $mosConfig_lang;
		$langpath = DBQ_Settings::getPath('user_language');
		
		// Load the english language, default for all strings
		if (file_exists($file = $langpath.'settings.english.php')) 
			require_once ($file);
			
		// Load a local language file
		if ( $mosConfig_lang !== 'english' && file_exists($file = $langpath.'settings.'.$mosConfig_lang.'.php')) 
			require_once ($file);
				
		// Define user classes
		$this->userclasses = array (
			'dbq_config' 			=> 'config.class.php',
			'dbq_common' 			=> 'common.class.php',
			'dbq_database' 			=> 'database.class.php',
			'dbq_error' 			=> 'error.class.php',
			'dbq_frontend' 			=> 'frontend.class.php',
			'dbq_query' 			=> 'query.class.php',
			'dbq_professional' 		=> 'professional.class.php',
			'dbq_variable' 			=> 'variable.class.php',
			'dbq_variable_code' 		=> 'variables/code.class.php',
			'dbq_variable_custom' 		=> 'variables/custom.class.php',
			'dbq_variable_field' 		=> 'variables/field.class.php',
			'dbq_variable_files' 		=> 'variables/files.class.php',
			'dbq_variable_keyword' 		=> 'variables/keyword.class.php',
			'dbq_variable_list' 		=> 'variables/list.class.php',
			'dbq_variable_results' 		=> 'variables/results.class.php',
			'dbq_variable_statement' 	=> 'variables/statement.class.php',
			'dbq_variable_substitutions'	=> 'variables/substitutions.class.php',
			'dbq_variable_upload' 		=> 'variables/upload.class.php',
			'dbq_variable_user' 		=> 'variables/user.class.php',
			'dbq_stats' 			=> 'stats.class.php',
			'dbq_substitution' 		=> 'substitution.class.php',
			'dbq_template' 			=> 'template.class.php'
		);	
		
		// Define admin classes
		$this->adminclasses = array (
			'dbq_admin_common' 			=> 'common.class.php',
			'dbq_admin_config' 			=> 'config.class.php',
			'dbq_admin_database' 		=> 'database.class.php',
			'dbq_admin_error' 			=> 'error.class.php',
			'dbq_admin_query' 			=> 'query.class.php',
			'dbq_admin_stats' 			=> 'stats.class.php',
			'dbq_admin_substitution'	=> 'substitution.class.php',
			'dbq_admin_template' 		=> 'template.class.php',
			'dbq_admin_variable' 		=> 'variable.class.php'
		);


		// Define admin urls
		global $option;
		$this->adminurls = array(
			'COMPANY' 		=> 'http://www.gmitc.biz',
			'DOWNLOAD'		=> 'http://forge.joomla.org/sf/frs/do/viewSummary/projects.dbq/frs',
			'HELP'			=> 'http://www.gmitc.biz',
			'FORUM'			=> 'http://www.gmitc.biz/component/option,com_smf/',
			'SUPPORT' 		=> 'http://www.gmitc.biz',
			'database'		=> "index2.php?option=$option&act=database",
			'query' 		=> "index2.php?option=$option&act=query",
			'professional' 	=> "index2.php?option=$option&act=query",
			'variable' 		=> "index2.php?option=$option&act=variable",
			'config' 		=> "index2.php?option=$option&act=config",
			'substitution' 	=> "index2.php?option=$option&act=substitution",
			'preview' 		=> "index2.php?option=$option&act=preview",
			'stats' 		=> "index2.php?option=$option&act=stats",
			'errors' 		=> "index2.php?option=$option&act=errors",
			'consulting' 	=> "index2.php?option=$option&act=consulting",
			'template' 		=> "index2.php?option=$option&act=template",
			'category' 		=> 'index2.php?option=categories&section=com_dbquery'
		);


		// Constants used by DBQ
		$this->tables = array(
					 'CONFIG' => '#__dbquery_config'
		);

		// Define the different regex requirements for displays
		$this->displayRegex = array(
			'ALWAYS' 	=> array ( 1, _LANG_SETTINGS_DISPLAY_ALWAYS_COMMENT, _LANG_SETTINGS_DISPLAY_ALWAYS_NAME),
			'NEVER' 	=> array ( 0, _LANG_SETTINGS_DISPLAY_NEVER_COMMENT, _LANG_SETTINGS_DISPLAY_NEVER_NAME),
			'ON_ERROR'	=> array ( -1, _LANG_SETTINGS_DISPLAY_ON_ERROR_COMMENT, _LANG_SETTINGS_DISPLAY_ON_ERROR_NAME)
		);
		
		// Error codes used by DBQ
		$this->errorCodes = array(
			1 => array('Unknown Error', _LANG_SETTINGS_ERROR_UNKNOWN_COMMENT, _LANG_SETTINGS_ERROR_UNKNOWN_NAME),
			2 => array('User Error', _LANG_SETTINGS_ERROR_USER_COMMENT, _LANG_SETTINGS_ERROR_USER_NAME),
			3 => array('Application Error', _LANG_SETTINGS_ERROR_APPLICATION_COMMENT, _LANG_SETTINGS_ERROR_APPLICATION_NAME),
			4 => array('Critical Error', _LANG_SETTINGS_ERROR_CRITICAL_COMMENT, _LANG_SETTINGS_ERROR_CRITICAL_NAME),
			5 => array('Query Error', _LANG_SETTINGS_ERROR_QUERY_COMMENT, _LANG_SETTINGS_ERROR_QUERY_NAME)
		);
		
		// Define regular expressions
		$this->regularExpressions = array (
			'ALPHANUM' 		=> Array ( '[[:alnum:]]+', _LANG_SETTINGS_REGEX_ALNUM_COMMENT, _LANG_SETTINGS_REGEX_ALNUM_NAME ) ,
			'ANY' 			=> Array ( '.+', _LANG_SETTINGS_REGEX_ANY_COMMENT, _LANG_SETTINGS_REGEX_ANY_NAME ) ,
			'COMP' 			=> Array ( '[<=>]+', _LANG_SETTINGS_REGEX_COMP_COMMENT, _LANG_SETTINGS_REGEX_COMP_NAME ) ,
			'CURRENCY' 		=> Array ( '[0-9,]+\.?[0-9]{0,2}', _LANG_SETTINGS_REGEX_CURRENCY_COMMENT, _LANG_SETTINGS_REGEX_CURRENCY_NAME ) ,
			//'DATETIME' 		=> Array ( '[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}', _LANG_SETTINGS_REGEX_DATETIME_COMMENT, _LANG_SETTINGS_REGEX_DATETIME_NAME ), 
			'DIGIT' 		=> Array ( '[[:digit:]]+', _LANG_SETTINGS_REGEX_DIGIT_COMMENT, _LANG_SETTINGS_REGEX_DIGIT_NAME ) ,
			'EMAIL' 		=> Array ( '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})', _LANG_SETTINGS_REGEX_EMAIL_COMMENT, _LANG_SETTINGS_REGEX_EMAIL_NAME ) ,
			'EURODATE' 		=> Array ( '[0123]?[0-9]{1}\/[01]?[0-9]{1}\/[0-9]{2,4}', _LANG_SETTINGS_REGEX_EURODATE_COMMENT, _LANG_SETTINGS_REGEX_EURODATE_NAME ) ,
			'FLOAT' 		=> Array ( '[+\-]?[0-9.,]+', _LANG_SETTINGS_REGEX_FLOAT_COMMENT, _LANG_SETTINGS_REGEX_FLOAT_NAME ) ,
			'INTEGER'		=> Array ( '[0-9]+', _LANG_SETTINGS_REGEX_INT_COMMENT, _LANG_SETTINGS_REGEX_INT_NAME ) ,
			'PUNCT' 		=> Array ( '[[:punct:]]+', _LANG_SETTINGS_REGEX_PUNCT_COMMENT, _LANG_SETTINGS_REGEX_PUNCT_NAME ) ,
			//'SIGNEDDIGIT'	=> Array ( '[+\-]?[0-9]+', _LANG_SETTINGS_REGEX_SIGNEDDIGIT_COMMENT, _LANG_SETTINGS_REGEX_SIGNEDDIGIT_NAME ) ,
			//'SWITCH' 		=> Array ( '(on|off)', _LANG_SETTINGS_REGEX_SWITCH_COMMENT, _LANG_SETTINGS_REGEX_SWITCH_NAME ) ,
			//'STRONGPASSWORD'=> Array ( '', _LANG_SETTINGS_REGEX_PASSWORD_COMMENT, _LANG_SETTINGS_REGEX_PASSWORD_NAME ),
			'TEXT' 			=> Array ( '.+', _LANG_SETTINGS_REGEX_TEXT_COMMENT, _LANG_SETTINGS_REGEX_TEXT_NAME ) ,
			//'TINYINT' 		=> Array ( '[0-9]+', _LANG_SETTINGS_REGEX_TINYINT_COMMENT, _LANG_SETTINGS_REGEX_TINYINT_NAME ) ,
			'URL'			=> Array ( '(https?:[0-9]*\/\/)?[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})', _LANG_SETTINGS_REGEX_URL_COMMENT, _LANG_SETTINGS_REGEX_URL_NAME ),
			'USDATE' 		=> Array ( '[01]?[0-9]{1}\/[0123]?[0-9]{1}\/[0-9]{2,4}', _LANG_SETTINGS_REGEX_USDATE_COMMENT, _LANG_SETTINGS_REGEX_USDATE_NAME ) 
			//'VARCHAR' 		=> Array ( '[[:ascii:]]+', _LANG_SETTINGS_REGEX_VARCHAR_COMMENT, _LANG_SETTINGS_REGEX_VARCHAR_NAME) ,
			//'XDIGIT' 		=> Array ( '[[:xdigit:]]+', _LANG_SETTINGS_REGEX_XDIGIT_COMMENT, _LANG_SETTINGS_REGEX_XDIGIT_NAME ) 
		);
		
		// Define various required options for a variable
		$this->requiredOptions = array(
			'BLANK' => _LANG_SETTINGS_REQUIRE_BLANK,
			'DEFAULT' => _LANG_SETTINGS_REQUIRE_DEFAULT,
			'NO' => _LANG_SETTINGS_REQUIRE_NO,
			'YES' => _LANG_SETTINGS_REQUIRE_YES
		);
		
		// Template Files for the frontend
		$this->templateFiles = array(
			'CUSTOM_CODE_FILE' 		=> 'Custom.Code.php',
			'DISPLAY_RESULTS_PREFIX'	=> 'DisplayResults',
			'ERROR_FILE' 			=> 'Error.html.php',
			'EXECUTE_FILE' 			=> 'Execute.html.php',
			'JAVASCRIPT_FILE' 		=> 'javascript.js',
			'PAGE_NAVIGATION_FILE'	=> 'PageNavigation.html.php',
			'PREPARE_FILE' 			=> 'Prepare.html.php',
			'PRINT_PAGE_FILE' 		=> 'Print.Page.html.php',
			'SELECT_FILE' 			=> 'Select.html.php',
			'RETURN_LINK_FILE' 		=> 'Return.html.php',
			'TEMPLATE_CSS_FILE'		=> 'template.css',
		);
		
		// List of standard input types
		$this->validInputs = array(
			'CHECKBOX' 		=> array('checkbox',_LANG_SETTINGS_INPUTS_CHECKBOX_COMMENT,_LANG_SETTINGS_INPUTS_CHECKBOX_NAME),
			'FILEUPLOAD' 	=> array('file',_LANG_SETTINGS_INPUTS_FILEUPLOAD_COMMENT,_LANG_SETTINGS_INPUTS_FILEUPLOAD_NAME),
			'HIDDEN' 		=> array('hidden',_LANG_SETTINGS_INPUTS_HIDDEN_COMMENT,_LANG_SETTINGS_INPUTS_HIDDEN_NAME),
			'HTMLEDITOR' 	=> array('htmleditor',_LANG_SETTINGS_INPUTS_HTMLEDITOR_COMMENT,_LANG_SETTINGS_INPUTS_HTMLEDITOR_NAME),
			'MULTISELECT'	=> array('select',_LANG_SETTINGS_INPUTS_MULTISELECT_COMMENT,_LANG_SETTINGS_INPUTS_MULTISELECT_NAME),
			'PASSWORD' 		=> array('password',_LANG_SETTINGS_INPUTS_PASSWORD_COMMENT,_LANG_SETTINGS_INPUTS_PASSWORD_NAME),
			'RADIO' 		=> array('radio',_LANG_SETTINGS_INPUTS_RADIO_COMMENT,_LANG_SETTINGS_INPUTS_RADIO_NAME),
			'SELECT' 		=> array('select',_LANG_SETTINGS_INPUTS_SELECT_COMMENT,_LANG_SETTINGS_INPUTS_SELECT_NAME),
			'TEXT' 			=> array('text',_LANG_SETTINGS_INPUTS_TEXT_COMMENT,_LANG_SETTINGS_INPUTS_TEXT_NAME),
			'TEXTAREA' 		=> array('textarea',_LANG_SETTINGS_INPUTS_TEXTAREA_COMMENT,_LANG_SETTINGS_INPUTS_TEXTAREA_NAME)
		);
		// Backward compatability
		global $urls;
		$urls = $this->adminurls;
				
	} // end constructor DBQ_Settings;
	
	/**
	 * Return the path to the appropriate files
	 *
	 * @param String $type Category of files sought after
	 */
	function getPath($type) {
		global $mosConfig_absolute_path;
		$user = $mosConfig_absolute_path . '/components/com_dbquery/';
		$admin = $mosConfig_absolute_path . '/administrator/components/com_dbquery/';
		$path = NULL;
		switch ($type) {
		case 'admin_class':
			$path = $admin . 'classes/DBQ/admin/';
			break;
		case 'admin_language':
			$path = $admin . 'language/';
			break;
		case 'class':
			$path = $user . 'classes/DBQ/';
			break;
		case 'driver_class':
			$path = $user . 'classes/DBQ/drivers/';
			break;
		case 'themes':
			$path = $user . 'themes/';
			break;
		case 'user_language':
			$path = $user . 'language/';
			break;
		case 'variable_class':
			$path = $user . 'classes/DBQ/variables/';
			break;
		case 'xhtml':
			$path = $admin . 'xhtml/';
			break;
		default:
			//return $this->logApplicationError("Request for an unknown path for the type '$type'");
			break;
		}
		
		return $path;
	} // end getPath()

	function getClassFileForAdminType($type) {

		// Create the path
		$path = $this->getPath('admin_class');
		$file = $this->adminclasses[$type];
		//echo "admin class will be $path$file";
		return $path . $file;
	} // end getClassFileForAdminType()
	
	function getClassFileForType($type) {

		// Create the path
		$path = $this->getPath('class');
		$file = $this->userclasses[$type];
		//echo "user class will be $path$file<br/>";
		return $path . $file;
	} // end getClassFileForType()

	function getTable($key) {
	  return $this->tables[$key];
	} // end getConstant()

	/**
	 * Return a value from DBQ's Count SQL configurations
	 *
	 * @access public
	 * @param string $key Key to return
	 * @return string Value of the configuration
	 */
	function getCSSColors() {
		$colors = new stdClass;
		$colors->required = '#f7ff86';
		$colors->disabled = '#b1ae94';
		$colors->normal = 'white';
		$colors->xerror = '#ff766f';
		$colors->error = 'red';
		return $colors;
	} // end getCSSColors()	

	function getDisplayRegex($key=NULL) {
		
		// Return all regex
		if (is_null($key))
			return $this->displayRegex;
		
		// Return a specific regex
		return $this->displayRegex[$key];	
	} // end getDisplayRegex()
	
	function getTemplateFile($key) {
		if ( ! array_key_exists($key, $this->templateFiles) )
			return false; //$this->logApplicationError("The template file '$key' cannot be found but is requested by '$this->name' ($this->id)");
			
		return $this->templateFiles[$key];
	} // end getTemplateFile()
	
	function includeClassFileForAdminType($type) {
		$settings = (isset($GLOBALS['DBQ_Settings']) ) ? $GLOBALS['DBQ_Settings'] : new DBQ_Settings;
		$file = $settings->getClassFileForAdminType(strtolower($type)) ;
		
		if ( ! $file )
			return false;
		
		// Way of dealing with a horrible PHP bug and a problem with symlinks
		$files = get_included_files();
		if ( ! in_array($file, $files ) ) {
			return require_once($file );
		} else {
			return true;
		}
	} // end includeClassFileForAdminType()
		
	function includeClassFileForType($type) {
		$settings = (isset($GLOBALS['DBQ_Settings']) ) ? $GLOBALS['DBQ_Settings'] : new DBQ_Settings;
		$file = $settings->getClassFileForType(strtolower($type)) ;

		if ( ! $file )
			return false;
			
		// Way of dealing with a horrible PHP bug and a problem with symlinks
		$files = get_included_files();
		if ( ! in_array($file, $files ) ) {
			return require_once($file );
		} else {
			return true;
		}
	} // end includeClassFileForType()
	
	/**
	 * Initialize the DBQ Session
	 *
	 * This function performs basic initialization stuff, like reading language files and create a global settings object
	 *
	 * @access private
	 * @since 1.4
	 */
	function init() {
		// Check for settings object
		if ( array_key_exists('DBQ_Settings', $GLOBALS) )
			return true;

		global $mainframe;
		// Toogle between admin and user files
		global $mosConfig_lang;
		if ( $mainframe->isAdmin() ) {
			// Load User Language File
			$langpath =  DBQ_Settings::getPath('admin_language');
			$prefix = 'admin.';
		} else {
			$langpath =  DBQ_Settings::getPath('user_language');
			$prefix = '';
		}

		// Load the language file 
		$languages = array($mosConfig_lang, 'english');
		foreach ( $languages as $language ) {
			$file = "$langpath{$prefix}$language.php";
			if (file_exists($file)) {
				require_once($file);
			} else {
				echo "WARNING: Could not include language file '$file'!";
			}
		} // end loading language files
				
		// Create global settings object for everyone to share	
		$settings = new DBQ_Settings();
		$GLOBALS['DBQ_Settings'] =& $settings;
		
		// Create global debug setting for everyone to reference
		
		// TODO: Put database configs in the settings object
		global $database;
		$database->SetQuery('SELECT c.value FROM '.$settings->getTable('CONFIG')." c WHERE c.type = 'CONFIG' AND c.key = 'DEBUG' ");
		$GLOBALS['DBQ_Debug'] = $database->loadResult();
		
		// Setup a global for DBQ Output
		$GLOBALS['DBQ_OUTPUT'] = mosGetParam($_GET, 'output_format', 'XHTML');
		//echo "output format is ".$GLOBALS['DBQ_OUTPUT'];
		
	} // end init()
	
	function listErrorCodes($key=NULL) {
		if ( ! is_null($key) )
			return $this->errorCodes[$key];

		return $this->errorCodes;
	} // end listErrorCodes
	
	function listRegularExpressions($key=NULL) {
		if ( ! is_null($key) )
			return $this->regularExpressions[$key];
		
		return $this->regularExpressions;
	} // end listRegularExpressions()
	
	function listRequiredOptions ($key=NULL) {
		if ( ! is_null($key) )
			return $this->requiredOptions[$key];

		return $this->requiredOptions;	
	} // end listRequiredOptions()
	
	function listSupportedInputs ($key=NULL) {
		if ( ! is_null($key) )
			return $this->validInputs[$key];

		return $this->validInputs;
	} // end listSupportedInputs ()
}

?>