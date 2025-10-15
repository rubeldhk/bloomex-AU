<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_tax.php,v 1.9.2.3 2006/03/14 18:42:11 soeren_nb Exp $
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

class ps_priority {
	var $classname = "ps_priority";

	/**
	 * Validates the input values before adding an item
	 *
	 * @param arry $d The _REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function validate_add(&$d) {
                global $vmLogger;
                $valid = true;
                $db = new ps_DB;
		if (!$d["priority_name"]) {
			$vmLogger->err( 'You must enter a priority name. ' );
			$valid = False;
		}
		if (!$d["priority_code"]) {
                        $vmLogger->err( 'You must enter a proirity code.' );
                        $valid = False;
                }
                //$d["priority_rate"] = str_replace( ',', '.', $d['tax_rate']);

                return $valid;
        }
	/**
	 * Validates the input values before updating an item
	 *
	 * @param arry $d The _REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function validate_update(&$d) {
		global $vmLogger;
		
		$db = new ps_DB;


		if (!$d["priority_name"]) {
                        $vmLogger->err( 'You must enter a priority name.' );
                        return False;
                }
		if (!$d["priority_code"]) {
                        $vmLogger->err( 'You must enter a priority code.' );
                        return False;
                }

                
                return True;
        }
        /**
	 * Validates the input values before deleting an item
	 *
	 * @param arry $d The _REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function validate_delete($d) {
		global $vmLogger;
		
		if (!$d["priority_id"]) {
			$vmLogger->err( 'Please select a priority to delete.' );
			return False;
		}
		
		return True;
	
	}

	/**
	 * Creates a new tax record
	 * @author pablo
	 *
	 * @param arry $d The _REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function add(&$d) {
		$db = new ps_DB;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$timestamp = time();

		if (!$this->validate_add($d)) {
			return False;
		}
		$q = "INSERT INTO #__{vm}_priority (vendor_id, priority_name, priority_code, list_priority) VALUES (";
		$q .= "'$ps_vendor_id','";
		$q .= $d["priority_name"] . "','";
		$q .= $d["priority_code"] . "','";
		$q .= $d["list_priority"] . "')";
		$db->query($q);
		$db->next_record();
		return True;

	}

	/**
	 * Updates a tax record
	 * @author pablo
	 *
	 * @param arry $d The _REQUEST array
	 * @return boolean True on success, false on failure
	 */
	function update(&$d) {
		$db = new ps_DB;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$timestamp = time();

		if (!$this->validate_update($d)) {
			return False;
		}
		$q = "UPDATE #__{vm}_priority SET ";
		$q .= "priority_name='" . $d["priority_name"];
		$q .= "',priority_code='" . $d["priority_code"];
		$q .= "',list_priority='" . $d["list_priority"];
		$q .= "' WHERE priority_id='" . $d["priority_id"] . "'";
		$q .= " AND vendor_id='$ps_vendor_id'";
		$db->query($q);
		$db->next_record();
		return True;
	}

	/**
	* Controller for Deleting Records.
	*/
	function delete(&$d) {

		if (!$this->validate_delete($d)) {
			return False;
		}

		$record_id = $d["priority_id"];

		if( is_array( $record_id)) {
			foreach( $record_id as $record) {
				if( !$this->delete_record( $record, $d ))
				return false;
			}
			return true;
		}
		else {
			return $this->delete_record( $record_id, $d );
		}
	}
	/**
	* Deletes one tax record.
	*/
	function delete_record( $record_id, &$d ) {
		global $db;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];

		$q = "DELETE from #__{vm}_priority where priority_id='$record_id'";
		$q .= " AND vendor_id='$ps_vendor_id'";
		$db->query($q);
		$db->next_record();
		return True;
	}

  function list_priority($priority_code, $extra="") {
		echo $this->getPriority( $priority_code, $extra );
	}


  function getPriority($priority_code, $extra="") {
        $db = new ps_DB;

    $q = "SELECT * from #__{vm}_priority ORDER BY list_priority";
    $db->query($q);
    $html = "<select name=\"priority\" class=\"inputbox form-control nopadding\"  size='1' $extra>\n";
    while ($db->next_record()) {

      $html .= "<option value=\"" . $db->f("priority_code")."\"";
      if ($priority_code == $db->f("priority_code")) 
         $html .= " selected=\"selected\">";
      else
         $html .= ">";
      $html .= $db->f("priority_name") . "</option>\n";
    }
    $html .= "</select>\n";
           return $html;
  }


}
?>
