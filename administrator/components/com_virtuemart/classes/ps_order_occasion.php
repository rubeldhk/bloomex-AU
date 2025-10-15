<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: ps_order_status.php,v 1.4.2.1 2006/03/14 18:42:11 soeren_nb Exp $
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


class ps_order_occasion {
  var $classname = "ps_order_occasion";
  
  /*
  ** VALIDATION FUNCTIONS
  **
  */

  function validate_add(&$d) {
    
    $db = new ps_DB;
   
    if (!$d["order_occasion_code"]) {
      $d["error"] = "ERROR:  You this order occasion type is already defined.";
      return False;
    } 

    return True;    
  }
  
  function validate_delete($d) {
    
    if (!$d["order_occasion_id"]) {
      $d["error"] = "ERROR:  Please select an order occasion type to delete.";
      return False;
    }
    else {
      return True;
    }
  }
  
  function validate_update(&$d) {
    $db = new ps_DB;

    if (!$d["order_occasion_id"]) {
      $d["error"] = "ERROR:  You must select an order occasion to update.";
      return False;
    }
    if (!$d["order_occasion_code"]) {
      $d["error"] = "ERROR:  You must enter a order occasion code.";
      return False;
    }
    return True;
  }
  
  
  /**************************************************************************
   * name: add()
   * created by: pablo
   * description: creates a new tax rate record
   * parameters:
   * returns:
   **************************************************************************/
  function add(&$d) {
    $db = new ps_DB; 
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $timestamp = time();
    
    if (!$this->validate_add($d)) {
      return False;
    }
    $q = "INSERT INTO #__{vm}_order_occasion (order_occasion_code,";
    $q .= "order_occasion_name, order_occasion_desc,list_order,published) ";
    $q .= "VALUES ('";
    $q .= $d["order_occasion_code"] . "','";
    $q .= $d["order_occasion_name"] . "','";
    $q .= $d["order_occasion_desc"] . "','";
    $q .= $d["list_order"] . "','";
    $q .= $d["published"] .      "')";
    $db->query($q);
    $db->next_record();
    return True;

  }
  
  /**************************************************************************
   * name: update()
   * created by: pablo
   * description: updates function information
   * parameters:
   * returns:
   **************************************************************************/
  function update(&$d) {
    $db = new ps_DB; 
    $ps_vendor_id = $_SESSION["ps_vendor_id"];
    $timestamp = time();

    if (!$this->validate_update($d)) {
      return False;	
    }
    $q = "UPDATE #__{vm}_order_occasion SET ";
    $q .= "order_occasion_code='" . $d["order_occasion_code"];
    $q .= "',order_occasion_name='" . $d["order_occasion_name"];
    $q .= "',order_occasion_desc='" . $d["order_occasion_desc"];
    $q .= "',list_order='" . $d["list_order"];
     $q .= "',published='" . $d["published"]; 
    $q .= "' WHERE order_occasion_id='" . $d["order_occasion_id"] . "'";
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
		$record_id = $d["order_occasion_id"];
		
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
	* Deletes one Record.
	*/
	function delete_record( $record_id, &$d ) {
		global $db;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		
		$q = "DELETE from #__{vm}_order_occasion WHERE order_occasion_id='$record_id'";
		$db->query($q);
		return True;
  }

	function list_order_occasion($order_occasion_code, $extra="") {
		echo $this->getOrderOccasion( $order_occasion_code, $extra );
	}


	function getOrderOccasion( $order_occasion_code, $extra="") {
		$db = new ps_DB;
		
		$q = "SELECT order_occasion_id, order_occasion_code, order_occasion_name FROM #__{vm}_order_occasion ORDER BY list_order";
		$db->query($q);
		$html = "<select name=\"order_occasion\" class=\"inputbox\" $extra>\n";
		while ($db->next_record()) {
		  $html .= "<option value=\"" . $db->f("order_occasion_code")."\"";
		  if ($order_occasion_code == $db->f("order_occasion_code")) 
			 $html .= " selected=\"selected\">";
		  else
			 $html .= ">";
		  $html .= $db->f("order_occasion_name") . "</option>\n";
		}
		$html .= "</select>\n";
		
                return $html;
        }

        
        function getOrderOccasionName( $order_occasion_code ) {
                $db = new ps_DB;
                
                $q = "SELECT order_occasion_id, order_occasion_name FROM #__{vm}_order_occasion WHERE `order_occasion_code`='".$order_occasion_code."'";
                $db->query($q);
                $db->next_record();
                return $db->f("order_occasion_name");
        }

}
?>
