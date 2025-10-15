<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class
//DBQ_Settings::includeClassFileForType('DBQ_database');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/database.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');

class DBQ_admin_database extends DBQ_admin_common {
		
	function DBQ_admin_database() {
		$this->_identifier = $this->_identifiers['database'];
		$this->_search_fields = array ('id', 'name', 'on_line');
		parent::DBQ_admin_common(_DBQ_DATABASES_TABLE);
	}

	function adminEdit($id = NULL) {
		global $globals, $my, $dbq_xhtml_path;

		// Make an object
		$obj =& $this->getInstance();

		// Load and checkout
		if ( $id and ! $this->loadAndCheckOut($id))
			return false;

		$option = $globals->option;
		$act = $globals->act;
		$displaytask = ($obj->id ? _LANG_EDIT : _LANG_ADD).' '._LANG_DB;

		// Get info on the DB table
		$columnInfo = $this->getTableInformation();

		$selectBoxes = array ();
		$selectBoxes['type'] = $this->listDatabaseTypes();
		$selectBoxes['driver'] = $this->listDatabaseDrivers();

		$colsToSkipInDisplay = array ('id', 'ordering', 'checked_out', 'checked_out_time');
		$colsToHideInDisplay = array ();
		$colsToDisplay = array (_LANG_DETAILS_CONFIG => array ('name', 'driver', 'type', 'hostname', 'schemaname', 'username', 'password'), _LANG_DETAILS_ATTRIBUTES => array ('on_line', 'debug', 'comment'), _LANG_DETAILS_TEXT => array ('description') );

		// Now display the data
		include_once ($dbq_xhtml_path.'details.html.php');
		return true;
	}

	function field($field, $i) {

		$obj = $this->getObject();
		
		switch ($field) {
			case 'Queries':
				$url = $this->getUrl('query') . '&task=show&clean=1&dbid='. $obj->id;
				parent::fieldLink($url,NULL,$field);
				break;
			case 'on_line':
				$text = $obj->on_line ? _LANG_YES : _LANG_NO;
				parent::field(NULL,$i,$text);
				break;
			default:
			parent::field($field,$i);
		}
	} // end field()

	/**
	 * List the id and name of Database Types available via DBQ
	 * @access public
	 * @return array Database information
	 */
	function listDatabaseTypes() {
		return $this->mashRows2($this->_config['DBTYPE']);
	} // end listDatabaseTypes()

	/**
	 * List the id and name of Database Drivers available via DBQ
	 * @access public
	 * @return array Driver information
	 */
	function listDatabaseDrivers() {
		return $this->mashRows2($this->_config['DBDRIVER']);
	} // end listDatabaseDrivers()
	
	function adminShow($id = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;

		// Define useful varialbes
		$option = $globals->option;
		$search = $this->getUserStateFromRequest("searchd{$option}", 'searchd');
		$limit = $globals->limit;
		$limitstart = $globals->limitstart;
		
		$obj = $this->getInstance();
		
		// Get rows and calculate results
		$total = $id ? 1 : $this->getCountOfAllRecords($search, $id);
		if ( $total <= $limitstart) $limitstart = 0;	
		$rows = $this->listAllRecords($limit, $limitstart, $id, $search, NULL);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		// Make list of all search fields
		$lists = array();
		
		// Search by name
		$lists[_LANG_NAME] =& $this->makeSearchField('searchd',$search);

		// Determine which rows to display
		$headers = array('name' => _LANG_NAME, 'hostname' => _LANG_HOSTNAME,
		'Queries' => _LANG_QUERIES, 'editor' => _LANG_CHECKED_OUT, 'on_line' => _LANG_ON_LINE );
		$screenName = _LANG_DB.' '._LANG_CONFIGURATIONS;
		include_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	}
}
?>