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


class ps_order_mark {
  var $classname = "ps_order_mark";
  
  /*
  ** VALIDATION FUNCTIONS
  **
  */

  function validate_add(&$d) {
    
    $db = new ps_DB;
   
    if (!$d["order_mark_code"]) {
      $d["error"] = "ERROR:  You this order mark is already defined.";
      return False;
    } 

    return True;    
  }
  
  function validate_delete($d) {
      global $my;

    if($my->gid !=25){
          mosRedirect('/administrator/index2.php?pshop_mode=admin&page=order.order_mark_list&option=com_virtuemart','You are not allowed to to delete this item');
    }

    if (!$d["order_mark_id"]) {
      $d["error"] = "ERROR:  Please select an order mark to delete.";
      return False;
    }
    else {
      return True;
    }
  }
  
  function validate_update(&$d) {
    $db = new ps_DB;

    if (!$d["order_mark_id"]) {
      $d["error"] = "ERROR:  You must select an order mark to update.";
      return False;
    }
    if (!$d["order_mark_code"]) {
      $d["error"] = "ERROR:  You must enter a order mark code.";
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
    $timestamp = time();

    if (!$this->validate_add($d)) {
      return False;
    }
      $q = "INSERT INTO #__{vm}_order_mark ( order_mark_code,";
      $q .= "order_mark_name) ";
      $q .= "VALUES ('".$db->getEscaped($d["order_mark_code"]) ."','";
      $q .= $db->getEscaped($d["order_mark_name"]) . "')";
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
    $timestamp = time();

    if (!$this->validate_update($d)) {
      return False;	
    }
    $q = "UPDATE #__{vm}_order_mark SET ";
    $q .= "order_mark_code='" . $db->getEscaped($d["order_mark_code"]);
    $q .= "',order_mark_name='" . $db->getEscaped($d["order_mark_name"]);
    $q .= "' WHERE order_mark_id='" . $db->getEscaped($d["order_mark_id"]) . "'";

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
		$record_id = $d["order_mark_id"];
		
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

		
		$q = "DELETE from #__{vm}_order_mark WHERE order_mark_id='$record_id'";
		$db->query($q);
		return True;
  }

	function list_order_order($order_mark_code, $extra="") {
                global $my;
		echo $this->getOrdermark( $order_mark_code, $extra );
	}

	function getOrdermark( $order_mark_code, $extra="") {
                global $my,$database;
                

		
		$q = "SELECT order_mark_id, order_mark_code, order_mark_name FROM jos_vm_order_mark";
		$html = "<select  $extra>\n";

                $database->setQuery($q);
                $order_marks = $database->loadObjectList();

                if($order_marks){

                foreach ($order_marks as $order_mark)
                {

                        $html .= "<option value=\"" . $order_mark->order_mark_code."\"";
                        if ($order_mark_code == $order_mark->order_mark_code)
                        {
                            $html .= " selected=\"selected\">";
                        }
                        else
                        {
                            $html .= ">";
                        }
                        
                        $html .= $order_mark->order_mark_name . "</option>\n";

                }

                }
		$html .= "</select>\n";

                return $html;
        }
        function getOrdermarkName( $order_mark_code ) {
                $db = new ps_DB;
                
                $q = "SELECT order_mark_id, order_mark_name FROM #__{vm}_order_mark WHERE `order_mark_code`='".$order_mark_code."'";
                $db->query($q);
                $db->next_record();
                return $db->f("order_mark_name");
        }


}
?>
