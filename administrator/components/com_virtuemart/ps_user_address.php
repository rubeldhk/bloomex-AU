<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_user_address.php,v 1.3 2007/01/24 06:18:32 paul Exp $
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

/**
* This class is used for managing Shipping Addresses
*
* @author Edikon Corp., pablo
* @package
* @package classes
*/
class ps_user_address {
	var $classname = "ps_user_address";

	/**************************************************************************
	** name: validate_add()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_add(&$d) {
		global $my, $VM_LANG, $vmLogger, $vmInputFilter;
		$valid = true;

		$d['missing'] = "";

		if (!$my->id) {
			$vmLogger->err( "You must not use this function." );
			$valid = false;
			return $valid;
		}

		// for security reasons we escape all malicious codes
		$d = $vmInputFilter->process( $d );
		$d = $vmInputFilter->safeSQL( $d );

		if (!$d["address_type_name"]) {
			$d['missing'] .= "address_type_name";
			$valid = false;
		}
		if (!$d["last_name"]) {
			$d['missing'] .= "last_name";
			$valid = false;
		}
		if (!$d["first_name"]) {
			$d['missing'] .= "first_name";
			$valid = false;
		}
		if (!$d["address_1"]) {
			$d['missing'] .= "address_1";
			$valid = false;
		}
		if (!$d["city"]) {
			$d['missing'] .= "city";
			$valid = false;
		}
		if (CAN_SELECT_STATES == '1') {
			if (!$d["state"]) {
				$d['missing'] .= "state";
				$valid = false;
			}
		}
		if (!$d["zip"]) {
			$d['missing'] .= "zip";
			$valid = false;
		}

		if (!$d["phone_1"]) {
			$d['missing'] .= "phone_1";
			$valid = false;
		}

		if(!isset($d['user_info_id'])) {
			$db = new ps_DB;
			$q  = "SELECT user_id from #__{vm}_user_info ";
			$q .= "WHERE address_type_name='" . $d["address_type_name"] . "' ";
			$q .= "AND address_type='" . $d["address_type"] . "' ";
			$q .= "AND user_id = '" . $d["user_id"] . "'";
			$db->query($q);
	
			if ($db->next_record()) {
				$d['missing'] .= "address_type_name";
				$vmLogger->warning( "The given address label already exists." );
				$valid = false;
			}
		}

		return $valid;
	}

	/**************************************************************************
	** name: validate_update()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_update(&$d) {

		return $this->validate_add( $d );
	}

	/**************************************************************************
	** name: validate_delete()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function validate_delete(&$d) {
		global $vmLogger;
		if (!$d["user_info_id"]) {
			$vmLogger->err( "Please select a user address to delete." );
			return false;
		}
		else {
			return true;
		}
	}

	/**************************************************************************
	** name: add()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function add(&$d) {
		global $perm, $page;
		$hash_secret = "VirtueMartIsCool";
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_add($d)) {
			return false;
		}

		if (empty($d["extra_field_1"])) {
			$d["extra_field_1"] = "";
		}
		if (empty($d["extra_field_2"])) {
			$d["extra_field_2"] = "";
		}
		if (empty($d["extra_field_3"])) {
			$d["extra_field_3"] = "";
		}
		if (empty($d["extra_field_4"])) {
			$d["extra_field_4"] = "N";
		}
		if (empty($d["extra_field_5"])) {
			$d["extra_field_5"] = "N";
		}

		$q = "INSERT INTO #__{vm}_user_info (user_info_id, user_id,address_type,address_type_name,";
		$q .= "company,title,last_name,first_name,middle_name,";
		$q .= "phone_1,phone_2,fax,address_1,";
		$q .= "address_2,city,state,country,zip,extra_field_1,extra_field_2,extra_field_3,extra_field_4,extra_field_5,";
		$q .= "cdate,mdate) VALUES ('";
		$q .= md5( uniqid( $hash_secret ))."', '";
		if (!$perm->check("admin,storeadmin")) {
			$q .= $_SESSION['auth']['user_id']."', '";
		}
		else {
			$q .= $d["user_id"] . "','";
		}
		$q .= $d["address_type"] . "','";
		$q .= $d["address_type_name"] . "','";
		$q .= $d["company"] . "','";
		$q .= @$d["title"] . "','";
		$q .= $d["last_name"] . "','";
		$q .= $d["first_name"] . "','";
		$q .= $d["middle_name"] . "','";
		$q .= $d["phone_1"] . "','";
		$q .= $d["phone_2"] . "','";
		$q .= $d["fax"] . "','";
		$q .= $d["address_1"] . "','";
		$q .= $d["address_2"] . "','";
		$q .= $d["city"] . "','";
		$q .= @$d["state"] . "','";
		$q .= $d["country"] . "','";
		$q .= $d["zip"] . "','";
		$q .= $d["extra_field_1"] . "','";
		$q .= $d["extra_field_2"] . "','";
		$q .= $d["extra_field_3"] . "','";
		$q .= $d["extra_field_4"] . "','";
		$q .= $d["extra_field_5"] . "','";
		$q .= $timestamp . "','";
		$q .= $timestamp . "') ";
		$db->query($q);

		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {
			echo "success[--3--]";	
			
			global $database;
			$name	= "ship_to_info_id";
			/* Select all the ship to information for this user id and
			* order by modification date; most recently changed to oldest
			*/
			$q  = "SELECT * from #__{vm}_user_info WHERE ";
			$q .= "user_id='" . $d["user_id"] . "' ";
			$q .= "AND address_type='BT'";
			$db->query($q);
			$db->next_record();
	
			$bt_user_info_id = $db->f("user_info_id");
	
			$q  = "SELECT user_id, user_info_id, address_type_name, company, title, ";
			$q .= "last_name, first_name, middle_name, phone_1, phone_2, ";
			$q .= "fax, address_1, address_2, city, ";
			$q .= "state, country, zip ";
			$q .= "FROM #__{vm}_user_info ";
			$q .= "WHERE user_id = '" . $d["user_id"] . "' ";
			$q .= "AND address_type = 'ST' ";
			$q .= "ORDER BY mdate DESC";
	
			$db->query($q);
			
			$sCurrentZipChecked	= trim(mosGetParam( $_REQUEST, "current_zip_checked" ));
			$sCurrentZipChecked	= ( $sCurrentZipChecked != "" && $sCurrentZipChecked != "undefined" ) ? $sCurrentZipChecked : false;			
			
			
			echo "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">\n";
			$i 		= 2;
			$nStep	= 0;
			while($db->next_record()) {	
				$nStep++;
						
				$query	= "SELECT options FROM tbl_options WHERE type='postal_code' AND name = '".trim($db->f("zip"))."'";
				$database->setQuery($query);
				$row	= $database->loadResult();
				
				echo "<tr class=\"sectiontableentry$i\">\n";
				echo "<td>\n";	            
	            /*if ( $sCurrentZipChecked ) {
	            	if ( $sCurrentZipChecked == $db->f("user_info_id") ) {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick=\"changDeliver('".$row."');\" >\n";
						echo "<input type=\"hidden\" name=\"zip_checked\" value=\"" . $row . "\" >\n";
					}else {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" onclick=\"changDeliver('".$row."');\" >\n";
					}
	            }else {*/
		            if ( $nStep == 1 ) {
						echo "<input type='radio' name='$name' value='" . $db->f("user_info_id") . "' title='" . $db->f("country") ."_". $db->f("state") . "' checked='checked' onclick='changDeliver('".$row."');' >\n";
						echo "<input type='hidden' name='zip_checked' value='" . $row . "' >\n";
						$sCurrentStateTax	= $db->f("country") ."_". $db->f("state");
					}else {
						echo "<input type='radio' name='$name' value='" . $db->f("user_info_id") . "' title='" . $db->f("country") ."_". $db->f("state") . "' onclick='changDeliver('".$row."');' >\n";
					}
	           /* }*/
				echo "</td><td><div>";
				
				if ($db->f("address_2")) {
					$sAddress = $db->f("address_1"). "(Or ".$db->f("address_2").")";
				}else{
					$sAddress = $db->f("address_1");
				}
				
				echo "<strong>".$db->f("address_type_name")."</strong>(".$db->f("first_name"). " " .$db->f("middle_name"). " " .$db->f("last_name").").&nbsp;
						<b>Zip Code:</b> ".$db->f("zip").".&nbsp;
						<b>Address:</b> ".$sAddress.", ".$db->f("city").", ".$db->f("state").", ".$db->f("").".&nbsp;
						<b>Phone:</b> ".$db->f("phone_1").", <b>Fax:</b> ".$db->f("fax");
				
				echo "<div></td>
						<td><input class='button edit-deliver' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Edit' type='button'></td>
						<td><input class='button delete-deliver' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Delete' type='button'></td>
					</tr>\n";
				if($i == 1) $i++;
				elseif($i == 2) $i--;
			}
	
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n[--3--]$sCurrentStateTax";
			die();
		}
		
		//mosRedirect($_SERVER['PHP_SELF']."?option=com_virtuemart&page=$page&task=edit&cid[0]=".$_REQUEST['cid'][0]."&Itemid=".$_REQUEST['Itemid'], "" );
		return true;
	}

	/**************************************************************************
	* name: update()
	* created by:
	* description:
	* parameters:
	* returns:
	**************************************************************************/
	function update(&$d) {
		global $perm, $page;
		$db = new ps_DB;
		$timestamp = time();

		if (!$this->validate_update($d)) {
			return false;
		}

		if (empty($d["extra_field_1"])) {
			$d["extra_field_1"] = "";
		}
		if (empty($d["extra_field_2"])) {
			$d["extra_field_2"] = "";
		}
		if (empty($d["extra_field_3"])) {
			$d["extra_field_3"] = "";
		}
		if (empty($d["extra_field_4"])) {
			$d["extra_field_4"] = "N";
		}
		if (empty($d["extra_field_5"])) {
			$d["extra_field_5"] = "N";
		}

		$q  = "UPDATE #__{vm}_user_info set company='" . $d["company"] . "', ";
		$q .= "address_type='" . $d["address_type"] . "', ";
		$q .= "address_type_name='" . $d["address_type_name"] . "', ";
		$q .= "title='" . @$d["title"] . "', ";
		$q .= "last_name='" . $d["last_name"] . "', ";
		$q .= "first_name='" . $d["first_name"] . "', ";
		$q .= "middle_name='" . $d["middle_name"] . "', ";
		$q .= "phone_1='" . $d["phone_1"] . "', ";
		$q .= "phone_2='" . $d["phone_2"] . "', ";
		$q .= "fax='" . $d["fax"] . "', ";
		$q .= "address_1='" . $d["address_1"] . "', ";
		$q .= "address_2='" . $d["address_2"] . "', ";
		$q .= "city='" . $d["city"] . "', ";
		$q .= "state='" . @$d["state"] . "', ";
		$q .= "country='" . $d["country"] . "', ";
		$q .= "zip='" . $d["zip"] . "', ";
		$q .= "extra_field_1='" . $d["extra_field_1"] . "', ";
		$q .= "extra_field_2='" . $d["extra_field_2"] . "', ";
		$q .= "extra_field_3='" . $d["extra_field_3"] . "', ";
		$q .= "extra_field_4='" . $d["extra_field_4"] . "', ";
		$q .= "extra_field_5='" . $d["extra_field_5"] . "', ";
		$q .= "mdate='" . $timestamp . "' ";
		$q .= "WHERE user_info_id='" . $d["user_info_id"] . "'";
		if (!$perm->check("admin,storeadmin")) {
			$q .= " AND user_id='".$_SESSION['auth']['user_id']."'";
		}
		$db->query($q);
		
		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {
			echo "success[--3--]";	
			
			global $database;
			$name	= "ship_to_info_id";
			/* Select all the ship to information for this user id and
			* order by modification date; most recently changed to oldest
			*/
			$q  = "SELECT * from #__{vm}_user_info WHERE ";
			$q .= "user_id='" . $d["user_id"] . "' ";
			$q .= "AND address_type='BT'";
			$db->query($q);
			$db->next_record();
	
			$bt_user_info_id = $db->f("user_info_id");
	
			$q  = "SELECT user_id, user_info_id, address_type_name, company, title, ";
			$q .= "last_name, first_name, middle_name, phone_1, phone_2, ";
			$q .= "fax, address_1, address_2, city, ";
			$q .= "state, country, zip ";
			$q .= "FROM #__{vm}_user_info ";
			$q .= "WHERE user_id = '" . $d["user_id"] . "' ";
			$q .= "AND address_type = 'ST' ";
			$q .= "ORDER BY mdate DESC";
	
			$db->query($q);
			
			$sCurrentZipChecked	= trim(mosGetParam( $_REQUEST, "current_zip_checked" ));
			$sCurrentZipChecked	= ( $sCurrentZipChecked != "" && $sCurrentZipChecked != "undefined" ) ? $sCurrentZipChecked : false;
	
			echo "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">\n";
			$i = 2;
			while($db->next_record()) {	
				$nStep++;
						
				$query	= "SELECT options FROM tbl_options WHERE type='postal_code' AND name = '".trim($db->f("zip"))."'";
				$database->setQuery($query);
				$row	= $database->loadResult();
				
				echo "<tr class=\"sectiontableentry$i\">\n";
				echo "<td>\n";
				
	            /*if ( $sCurrentZipChecked ) {
	            	 if ( $sCurrentZipChecked == $db->f("user_info_id") ) {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick=\"changDeliver('".$row."');\" >\n";
						echo "<input type=\"hidden\" name=\"zip_checked\" value=\"" . $row . "\" >\n";
					}else {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" onclick=\"changDeliver('".$row."');\" >\n";
					}
	            }else {*/
		            if ( $nStep == 1 ) {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick=\"changDeliver('".$row."');\" >\n";
						echo "<input type=\"hidden\" name=\"zip_checked\" value=\"" . $row . "\" >\n";
						$sCurrentStateTax	= $db->f("country") ."_". $db->f("state");
					}else {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" onclick=\"changDeliver('".$row."');\" >\n";
					}
	            //}
				echo "</td><td><div>";
				
				if ($db->f("address_2")) {
					$sAddress = $db->f("address_1"). "(Or ".$db->f("address_2").")";
				}else{
					$sAddress = $db->f("address_1");
				}
				
				echo "<strong>".$db->f("address_type_name")."</strong>(".$db->f("first_name")." ".$db->f("middle_name")." ".$db->f("last_name").").&nbsp;
						<b>Zip Code:</b> ".$db->f("zip").".&nbsp;
						<b>Address:</b> ".$sAddress.", ".$db->f("city").", ".$db->f("state").", ".$db->f("country").".&nbsp;
						<b>Phone:</b> ".$db->f("phone_1").", <b>Fax:</b> ".$db->f("fax");
				
				echo "<div></td>
						<td><input class='button edit-deliver' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Edit' type='button'></td>
						<td><input class='button delete-deliver' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Delete' type='button'></td>
					</tr>\n";
				if($i == 1) $i++;
				elseif($i == 2) $i--;
			}
	
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n[--3--]$sCurrentStateTax";
			die();
		}
		//mosRedirect($_SERVER['PHP_SELF']."?option=com_virtuemart&page=$page&task=edit&cid[0]=".$_REQUEST['cid'][0]."&Itemid=".$_REQUEST['Itemid'], "" );
		return true;
	}

	/**************************************************************************
	** name: delete()
	** created by:
	** description:
	** parameters:
	** returns:
	***************************************************************************/
	function delete(&$d) {
		global $perm;

		$db = new ps_DB;

		if (!$this->validate_delete($d)) {
			return false;
		}

		$q  = "DELETE FROM #__{vm}_user_info ";
		$q .= "WHERE user_info_id='" . $d["user_info_id"] . "'";
		if (!$perm->check("admin,storeadmin")) {
			$q .= " AND user_id='".$_SESSION['auth']['user_id']."'";
		}
		$db->query($q);
		
		
		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {
			echo "success[--3--]";	
			
			global $database;
			$name	= "ship_to_info_id";
			/* Select all the ship to information for this user id and
			* order by modification date; most recently changed to oldest
			*/
			$q  = "SELECT * from #__{vm}_user_info WHERE ";
			$q .= "user_id='" . $d["user_id"] . "' ";
			$q .= "AND address_type='BT'";
			$db->query($q);
			$db->next_record();
	
			$bt_user_info_id = $db->f("user_info_id");
	
			$q  = "SELECT user_id, user_info_id, address_type_name, company, title, ";
			$q .= "last_name, first_name, middle_name, phone_1, phone_2, ";
			$q .= "fax, address_1, address_2, city, ";
			$q .= "state, country, zip ";
			$q .= "FROM #__{vm}_user_info ";
			$q .= "WHERE user_id = '" . $d["user_id"] . "' ";
			$q .= "AND address_type = 'ST' ";
			$q .= "ORDER BY mdate DESC";
	
			$db->query($q);
			
			$sCurrentZipChecked	= trim(mosGetParam( $_REQUEST, "current_zip_checked" ));
			$sCurrentZipChecked	= ( $sCurrentZipChecked != "" && $sCurrentZipChecked != "undefined" ) ? $sCurrentZipChecked : false;
			
			if( $db->num_rows() ) {
				echo "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">\n";
				$i = 2;
				while($db->next_record()) {	
					$nStep++;
							
					$query	= "SELECT options FROM tbl_options WHERE type='postal_code' AND name = '".trim($db->f("zip"))."'";
					$database->setQuery($query);
					$row	= $database->loadResult();
					
					echo "<tr class=\"sectiontableentry$i\">\n";
					echo "<td>\n";
	            	/*if ( $sCurrentZipChecked ) {
		            	 if ( $sCurrentZipChecked == $db->f("user_info_id") ) {
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick=\"changDeliver('".$row."');\" >\n";
							echo "<input type=\"hidden\" name=\"zip_checked\" value=\"" . $row . "\" >\n";
						}else {
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" onclick=\"changDeliver('".$row."');\" >\n";
						}
		            }else {*/
			            if ( $nStep == 1 ) {
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick=\"changDeliver('".$row."');\" >\n";
							echo "<input type=\"hidden\" name=\"zip_checked\" value=\"" . $row . "\" >\n";
							$sCurrentStateTax	= $db->f("country") ."_". $db->f("state");
						}else {
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" onclick=\"changDeliver('".$row."');\" >\n";
						}
		           /* }*/
					echo "</td><td><div>";
					
					if ($db->f("address_2")) {
						$sAddress = $db->f("address_1"). "(Or ".$db->f("address_2").")";
					}else{
						$sAddress = $db->f("address_1");
					}
					
					echo "<strong>".$db->f("address_type_name")."</strong>(".$db->f("first_name")." ".$db->f("middle_name")." ".$db->f("last_name").").&nbsp;
							<b>Zip Code:</b> ".$db->f("zip").".&nbsp;
							<b>Address:</b> ".$sAddress.", ".$db->f("city").", ".$db->f("state").", ".$db->f("country").".&nbsp;
							<b>Phone:</b> ".$db->f("phone_1").", <b>Fax:</b> ".$db->f("fax");
					
					echo "<div></td>
							<td><input class='button edit-deliver' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Edit' type='button'></td>
							<td><input class='button delete-deliver' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='Delete' type='button'></td>
						</tr>\n";
					if($i == 1) $i++;
					elseif($i == 2) $i--;
				}
		
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n[--3--]$sCurrentStateTax";
			}else {
				echo "noshipping";
			}
			die();
		}
		return true;
	}

}
?>
