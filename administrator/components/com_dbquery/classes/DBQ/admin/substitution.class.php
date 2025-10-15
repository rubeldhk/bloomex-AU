<?php

/**
 * @package DBQ
 */

/**
 * Prevent Direct Access
 */
defined('_VALID_MOS') or die('Direct access is not permitted to this file');
// Include the file for the base class

//DBQ_Settings::includeClassFileForType('DBQ_substitution');
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/components/com_dbquery/classes/DBQ/substitution.class.php');
require_once($mosConfig_absolute_path.'/administrator/components/com_dbquery/classes/DBQ/admin/common.class.php');

class DBQ_admin_substitution extends DBQ_admin_common {

		
	function DBQ_admin_substitution() {
		$this->_identifier = $this->_identifiers['substitution'];
		$this->_search_fields = array ('id', 'value', 'variable_id');
		$this->_parent_tbl = _DBQ_VARIABLES_TABLE;
		$this->_parent_tbl_idx = 'variable_id';

		parent::DBQ_admin_common(_DBQ_SUBSTITUTIONS_TABLE);
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

		$displaytask = ($obj->id ? _LANG_EDIT : _LANG_ADD).' '._LANG_SUBSTITUTION;

		// Get info on the DB table
		$columnInfo = $this->getTableInformation();

		$colsToDisplay = array (_LANG_DETAILS_CONFIG => array ('variable_id', 'value', 'label', 'default'));
		$selectBoxes = array ();
		//$selectBoxes['variable_id'] = $this->listAllParentRecordsShort();
		$selectBoxes['variable_id'] = $this->listAllParentRecordsShortList();
		//$selectBoxes['variable_id'] = $this->listAllParentRecordsInUseShort();
		$colsToHideInDisplay = array ();
		$colsToSkipInDisplay = array ('id', 'ordering', 'checked_out', 'checked_out_time');
		// Now display the data

		require_once ($dbq_xhtml_path.'details.html.php');
		return true;
	}

	function field($field, $i) {

		
		$obj =& $this->getObject();
		
		switch ($field) {
			case 'parent' :
				$url = $this->getUrl('variable').'&task=show&clean=1&vid='.$obj->variable_id;
				parent :: fieldLink($url, $field);
				break;
			case 'value' :
				parent :: field('name', $i, NULL, $field);
				break;
			case 'default':
				$text = $obj->default ? _LANG_YES : _LANG_NO;
?><td align="center">
	<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','toggle_default')">
		<img src="images/<?php echo ( $obj->default )? 'tick.png' : 'publish_x.png';?>" width="12" height="12" border="0" alt="<?php echo $text;?>" />
	</a>
</td>	
<?php
				//echo "<td align=\"center\">$text</td>";
				break;
			default :
				parent :: field($field, $i);
		}
	}

	function adminShow($id = NULL) {
		global $mainframe, $globals, $dbq_xhtml_path;

		// Define useful varialbes

		$option = $globals->option;
		//$search = trim(strtolower($search));
		$varIdentifier = $this->getIdentifierForObjectType('variable');
		$vid = $search = NULL;
		$search = $this->getUserStateFromRequest("searchs{$option}", 'searchs');
		$vid = $this->getUserStateFromRequest("$varIdentifier{$option}", $varIdentifier);
		$limit = $globals->limit;
		$limitstart = $globals->limitstart;

		// Create an object to work with
		$obj = new DBQ_substitution();
		$this->setObject($obj);
		
		// Get rows and calculate results	
		$total = $id ? 1 : $this->getCountOfAllRecords($search, $vid);
		if ( $total <= $limitstart) $limitstart = 0;
		$rows = $this->listAllRecords($limit, $limitstart, $id, $search, $vid);
		require_once (_DBQ_ADMIN_PAGENAV_PATH);
		$pageNav = new mosPageNav($total, $limitstart, $limit);

		// Make list of all search fields
		$lists = array ();

		// Search by value
		$lists[_LANG_VALUE] = & $this->makeSearchField('searchs', $search);

		// Search by Variable
		$subs[] = mosHTML :: makeOption('', _LANG_SELECT._LANG_VARIABLES);
		$subs[] = mosHTML :: makeOption('', _LANG_ALL._LANG_VARIABLES);
		foreach ($this->listAllParentRecordsInUseShort() as $k => $v) {
			$subs[] = mosHTML :: makeOption($k, $v);
		}
		$lists[_LANG_VARIABLE] = mosHTML :: selectList($subs, $varIdentifier, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $vid);

		// Display our rows
		$headers = array ('value' => _LANG_VALUE, 'label' => _LANG_LABEL, 'parent' => _LANG_PARENT.' '._LANG_VARIABLE, 'default' => _LANG_DEFAULT, 'editor' => _LANG_CHECKED_OUT);
		$screenName = _LANG_SUBSTITUTIONS.' '._LANG_CONFIGURATIONS;
		include_once ($dbq_xhtml_path.'summary.html.php');
		return true;
	} // end adminShow()
	
	
	/**
	 * Toggle the status of the default value
	 * 
	 * If already selected, the element will no longer be the default value
	 * Otherwise, the default status will be enabled and the default status for 
	 *   all other substitutions belonging to this variable will be disabled
	 * 
	 * @param Integer $sid Substitution ID
	 * @return Boolean True if the operation was successful
	 * @access private
	 * @since 1.2
	 * 
	 */
	function adminToggleDefault($sid) {
		$obj = new DBQ_substitution();
		$obj->load($sid);
		$obj->default = $obj->default ? 0 : 1;
		return $obj->store();
	} // end toggleDefault()
	
	/**
	 * List the id and names of all parent records 
	 * where the parent records use substitutions.
	 *
	 * This is implemented by checking the variable's type.  If it advertises 
	 * itself as a 'list' variable, then we assume that it uses substitutions.
	 *
	 * @access public
	 * @since 1.0
	 * @return array List of variables information
	 *
	 */
	function listAllParentRecordsShortList() {
		return parent :: listAllParentRecordsShort(NULL, " where `type` = 'substitutions' ");
	} // end of listAllParentRecordsShortList()

}
?>