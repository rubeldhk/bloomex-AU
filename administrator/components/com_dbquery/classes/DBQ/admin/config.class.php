
<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_config');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/settings.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');


/**
 * A class to represent configuration information
 *
 * @subpackage DBQ_admin_config DBQ Administrative Configuration Class
 */
class DBQ_admin_config extends DBQ_admin_common {



	/**
	 * Constructor for the DBQ Config class
	 *
	 * @access public
	 * @since 1.0
	 * @param boolean $dummy Indicates if only the object framework should be constructed
	 */
	function DBQ_admin_config() {
		$this->_identifier = $this->_identifiers['config'];
		$this->_search_fields = array ('id', 'name', 'type');
		// Call our parent
		parent :: DBQ_admin_common(_DBQ_CONFIG_TABLE);
	}

	function adminEdit($id = NULL) {
		global $globals, $dbq_xhtml_path;

		// Make an object
		$obj =& $this->getInstance();
		
		// Load and checkout
		if ( $id and ! $this->loadAndCheckOut($id))
			return false;

		$option = $globals->option;
		$act = $globals->act;
		$displaytask = ($obj->id ? _LANG_EDIT : _LANG_ADD).' '._LANG_CONFIGURATION;

		// Get info on the DB table
		$columnInfo = $this->getTableInformation();

		//print_r($dbColumns); echo "<BR><BR>";
		//print_r($dbc); echo "<BR><BR>";

		$visibilityObjects = array();
		$selectBoxes = array ();
		$selectBoxes['type'] = $this->listConfigTypes();
		$colsToDisplay = array ( _LANG_DETAILS_CONFIG => array('name','type','key','value'), _LANG_DETAILS_ATTRIBUTES => array ('ordering', 'comment'), _LANG_DETAILS_TEXT => array('description'));
		$colsToHideInDisplay = array ();
		$colsToSkipInDisplay = array ('id', 'created', 'created_by', 'ordering', 'checked_out', 'checked_out_time');
		// Now display the data

		include_once ($dbq_xhtml_path.'details.html.php');
		return true;
	} // end edit()

	/**
	 * Helper function to print the admin interface
	 *
	 * @param String $field
	 * @param Iterator $i
	 */
	function field($field, $i) {

		
		$obj =& $this->getObject();
		
		switch ($field) {
			case 'type' :
				$string = "\$type = _LANG_$obj->type;";
				eval($string);
				parent::field($field, $i, $type);
				break;
			default :
				parent::field($field, $i);
		}
	} // end field()

	function adminShow($id = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;
		
		$obj =& $this->getInstance();
		
		// Define useful varialbes
		$option = $globals->option;
		$limit = $globals->limit;
		$limitstart = $globals->limitstart;
		$typeIdentifier = $this->getIdentifierForObjectType('type');		
		$search = $mainframe->getUserStateFromRequest("searchc{option}", 'searchc', '');
		$search = trim(strtolower($search));
		$type = $mainframe->getUserStateFromRequest("$typeIdentifier{$option}", $typeIdentifier, 0);
		
		// Get rows and calculte results
		$total = $id ? 1 : $this->getCountOfAllRecords($search, $type);
		if ( $total <= $limitstart) $limitstart = 0;		
		$rows = $this->listAllRecords($limit, $limitstart, $id, $search, $type);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		// Make a list of all search fields
		$lists = array();

		// Search by name
		$lists[_LANG_NAME] =& $this->makeSearchField('searchc',$search);
				
		// Search by configuration type
		$configTypes = $this->listConfigTypes();
		$configs[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_CONFIGURATIONS);
		$configs[] = mosHTML :: makeOption('', _LANG_ALL._LANG_CONFIGURATIONS);
		foreach ($configTypes as $k => $v) {
			$configs[] = mosHTML :: makeOption($k, $v);
		}
		//print_r($configTypes); echo ' are dd<BR>';
		$lists[_LANG_CONFIG] = mosHTML :: selectList($configs, $typeIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $type);

		// Display our rows
		$headers = array('name' => _LANG_NAME, 'type' => _LANG_TYPE, 'key' => _LANG_KEY, 'value' => _LANG_VALUE, 'editor' => _LANG_CHECKED_OUT);
		$screenName = _DBQ_COMPONENT_TITLE.' '._LANG_CONFIGURATION;

		require_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	}
	
	/**
	 * List the types of configurations available via DBQ
	 * @access public
	 * @return array Configuration types
	 */
	function listConfigTypes() {
		return $this->_config_types;
	}
}
?>

