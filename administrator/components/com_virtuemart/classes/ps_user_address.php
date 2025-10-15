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
		if (!$my->id && !$_SESSION['legacy_id']) {
			$vmLogger->err( "You must not use this function." );
			$valid = false;
			return $valid;
		}
                
		// for security reasons we escape all malicious codes
		$d = $vmInputFilter->process( $d );
		$d = $vmInputFilter->safeSQL( $d );

		/*if (!$d["address_type_name"]) {
			$d['missing'] .= "address_type_name";
			$valid = false;
		}*/
		if (!$d["last_name"]) {
			$d['missing'] .= "last_name";
			$valid = false;
		}
		if (!$d["first_name"]) {
			$d['missing'] .= "first_name";
			$valid = false;
		}
                if (!$d["street_number"]) {
			$d['missing'] .= "street_number";
			$valid = false;
		}
		if (!$d["street_name"]) {
			$d['missing'] .= "street_name";
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

		/*if(!isset($d['user_info_id'])) {
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
		}*/

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
		global $perm, $page, $VM_LANG, $database, $my;
		$hash_secret = "VirtueMartIsCool";
		$db = new ps_DB;
		$timestamp = time();
                
                if (!empty($my->id)) {
                    $d['user_id'] = (int)$my->id;
                }
                $d['user_id'] = (int)$d['user_id'];
                if ($d['user_id'] < 1) {
                    echo 'error[--3--]N/A';
                    exit(0);
                }
                
		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {		
			//#5951: Blocked postal codes need to be checked with or without spaces
			$aFind 		= array("-", " ","*");
			$aReplace  	= array("", "","%");
			$query	= "SELECT postal_code FROM jos_postcode_warehouse WHERE   `deliverable`=0 AND published=1 AND (postal_code = '". str_replace($aFind, $aReplace, $d["zip"])."' or '". str_replace($aFind, $aReplace, $d["zip"])."' like (replace(replace( postal_code, ' ', '' ), '*', '%')) )";
			$database->setQuery($query);
			$row	= $database->loadResult();
			if ( $row ) {
				echo "undeliver[--3--]N/A";
				exit(0);
			}	
		}

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
                $d["address_type_name"]=(isset($d["address_type_name"]))?$d["address_type_name"]:'';
		 $addr1=$d["suite"].' '.$d["street_number"].' '. $d["street_name"];
		$q = "INSERT INTO #__{vm}_user_info (user_info_id, user_id,address_type,address_type_name,";
		$q .= "company,title,last_name,first_name,middle_name,";
		$q .= "phone_1,phone_2,fax,address_1,";
		$q .= "address_2,city,state,country,zip,user_email,extra_field_1,extra_field_2,extra_field_3,extra_field_4,extra_field_5,";
		$q .= "cdate,mdate,suite, street_number,street_name) VALUES ('";
		$q .= md5( uniqid( $hash_secret ))."', '";
		if (!$perm->check("admin,storeadmin")) {
                    if(isset($_SESSION['legacy_id'])){
                     $q .= $_SESSION['legacy_id']."', '";
                    }else{
                     $q .= $_SESSION['auth']['user_id']."', '";
                    }
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
		$q .= $addr1 . "','";
		$q .= " ','";
		$q .= $d["city"] . "','";
		$q .= @$d["state"] . "','";
		$q .= $d["country"] . "','";
		$q .= $d["zip"] . "','";
                $q .= $d["email"] . "','";
		$q .= $d["extra_field_1"] . "','";
		$q .= $d["extra_field_2"] . "','";
		$q .= $d["extra_field_3"] . "','";
		$q .= $d["extra_field_4"] . "','";
		$q .= $d["extra_field_5"] . "','";
		$q .= $timestamp . "','";
		$q .= $timestamp .  "','";
                $q .= $d["suite"] . "','";
                $q .= $d["street_number"] . "','";
                $q .= $d["street_name"] . "') ";
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
			$q .= "jos_vm_state.state_2_code as state,jos_vm_state.state_3_code as state_3, country, zip ";
			$q .= "FROM #__{vm}_user_info left join jos_vm_state on jos_vm_state.state_2_code=#__{vm}_user_info.state ";
			$q .= "WHERE jos_vm_state.country_id='13' and user_id = '" . $d["user_id"] . "' ";
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
						echo "<input type='radio' name='$name' value='" . $db->f("user_info_id") . "' title='" . $db->f("country") ."_". $db->f("state") . "' checked='checked' onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
						echo "<input type='hidden' name='zip_checked' value='' >\n";
						$sCurrentStateTax	= $db->f("country") ."_". $db->f("state");
                                                
                                                $currentZip = trim($db->f("zip"));
					}else {
						echo "<input type='radio' name='$name' value='" . $db->f("user_info_id") . "' title='" . $db->f("country") ."_". $db->f("state") . "' onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
					}
	           /* }*/
				echo "</td><td><div>";
				
//					$sAddress = $db->f("address_1").", ".$db->f("state_3")." ".$db->f("zip");
                	$sAddress = $db->f("address_1").", ".$db->f("city").", ".$db->f("state_3")." ".$db->f("zip");
                                        if(strpos($db->f("company"), 'funeral')){
                                            $sAddress = $db->f("company");
                                        }
				echo "<strong>Name:</strong> ".$db->f("first_name"). " " .$db->f("middle_name"). " " .$db->f("last_name")."<br/>
						<b>".$VM_LANG->_VM_ADDRESS.":</b> ".$sAddress;	
				
				echo "<div></td>
						<td><input class='new_checkout_register_button edit-deliver' style='width:65px !important;' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_EDIT."' type='button'></td>
	 					<td><input class='new_checkout_register_button delete-deliver' style='width:65px !important;' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_DELETE."' type='button'></td>
					</tr>\n";
				if($i == 1) $i++;
				elseif($i == 2) $i--;
			}
	
			echo "</td>\n";
			echo "</tr>\n"; 
			echo "</table>\n[--3--]".$sCurrentStateTax."[--3--]".$currentZip;
			require_once 'end_access_log.php';
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
		global $perm, $page, $my,$VM_LANG, $database,$mosConfig_adm_link,$mosConfig_adm_auth;
		$db = new ps_DB;
		$timestamp = time();
                
                if (!empty($my->id)) {
                    $d['user_id'] = (int)$my->id;
                }
                $d['user_id'] = (int)$d['user_id'];
                if ($d['user_id'] < 1) {
                    echo 'error[--3--]N/A';
                    exit(0);
                }

		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {
			//#5951: Blocked postal codes need to be checked with or without spaces
			$aFind 		= array("-", " ");
			$aReplace  	= array("", "");
			$query	= "SELECT postal_code FROM jos_postcode_warehouse WHERE   `deliverable`=0 AND published=1 AND (postal_code = '". str_replace($aFind, $aReplace, $d["zip"])."' or '". str_replace($aFind, $aReplace, $d["zip"])."' like (replace(replace( postal_code, ' ', '' ), '*', '%')) )";
            $database->setQuery($query);
			$row	= $database->loadResult();

			if ($row ) {
				echo "undeliver[--3--]N/A";
				exit(0);
			}	
		}
                
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

        $d['my_id'] = $my->id;
        $d['key'] = md5($my->id . $d['user_info_id'] . 'blca');

        $d = $GLOBALS['vmInputFilter']->safeSQL($d);

        $service_url = $mosConfig_adm_link . '/scripts/for_blcoma/update_user_info.php';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $curl_response = curl_exec($curl);

        $sCurrentStateTax	= $d["country"] ."_". $d["state"];
        $currentZip = trim($d["zip"]);

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
			$q .= "jos_vm_state.state_2_code as state,jos_vm_state.state_3_code as state_3, country, zip ";
			$q .= "FROM #__{vm}_user_info left join jos_vm_state on jos_vm_state.state_2_code=#__{vm}_user_info.state ";
			$q .= "WHERE  jos_vm_state.country_id='13' and  user_id = '" . $d["user_id"] . "' ";
			$q .= "AND address_type = 'ST' ";
			$q .= "ORDER BY mdate DESC";

			$db->query($q);
			
			$sCurrentZipChecked	= trim(mosGetParam( $_REQUEST, "current_zip_checked" ));
			$sCurrentZipChecked	= ( $sCurrentZipChecked != "" && $sCurrentZipChecked != "undefined" ) ? $sCurrentZipChecked : false;
	
			echo "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"0\">\n";
			$i = 2;
			while($db->next_record()) {	
				$nStep++;

				
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
                if ( $sCurrentZipChecked ) {
                	if ( $sCurrentZipChecked == $db->f("user_info_id") ) {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\" onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
						echo "<input type=\"hidden\" name=\"zip_checked\" value=\"\" >\n";

					}else {
						echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\"  onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
					}
	            }
				echo "</td><td><div>";
				
				if ($db->f("address_2")) {
					$sAddress = $db->f("address_1"). "(Or ".$db->f("address_2").")";
				}else{
					$sAddress = $db->f("address_1");
				}			
				
				echo "<strong>Name:</strong> ".$db->f("first_name"). " " .$db->f("middle_name"). " " .$db->f("last_name")."<br/>
						<b>".$VM_LANG->_VM_ADDRESS.":</b> ".$db->f("address_1").", ".$db->f("state_3")." ".$db->f("zip");	
				
				echo "<div></td>
						<td><input class='new_checkout_register_button edit-deliver' style='width:65px !important;' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_EDIT."' type='button'></td>
						<td><input class='new_checkout_register_button delete-deliver' style='width:65px !important;' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_DELETE."' type='button'></td>
					</tr>\n";
				if($i == 1) $i++;
				elseif($i == 2) $i--;
			}
	
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n[--3--]".$sCurrentStateTax."[--3--]".$currentZip;
			require_once 'end_access_log.php';
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
		global $perm, $VM_LANG,$mosConfig_adm_link,$mosConfig_adm_auth,$my;

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

        $d['my_id'] = $my->id;
        $d['key'] = md5($my->id.$d['user_info_id'].'blca');

        $d = $GLOBALS['vmInputFilter']->safeSQL($d);

        $service_url = $mosConfig_adm_link.'/scripts/for_blcoma/delete_user_info.php';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

        $curl_response = curl_exec($curl);
        $response = json_decode($curl_response);
        curl_close($curl);
        if ($response->result) {
		if( mosGetParam( $_REQUEST, "action", "" ) == "ajax" ) {
			echo "success[--3--]";	
			
			global $database;
                            $nStep=0;
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
			$q .= "jos_vm_state.state_2_code as state,jos_vm_state.state_3_code as state_3, country, zip ";
			$q .= "FROM #__{vm}_user_info left join jos_vm_state on jos_vm_state.state_2_code=#__{vm}_user_info.state ";
			$q .= "WHERE  jos_vm_state.country_id='13' and  user_id = '" . $d["user_id"] . "' ";
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
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\" checked=\"checked\"  onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
							echo "<input type=\"hidden\" name=\"zip_checked\" value=\"\" >\n";
							$sCurrentStateTax	= $db->f("country") ."_". $db->f("state");
                                                        $currentZip = trim($db->f("zip"));
						}else {
							echo "<input type=\"radio\" name=\"$name\" value=\"" . $db->f("user_info_id") . "\"  onclick='changDeliver(\"".$row."\",\"".$db->f("country") ."_". $db->f("state")."\", \"".trim($db->f("zip"))."\");' >\n";
						}
		           /* }*/
					echo "</td><td><div>";
					
					if ($db->f("address_2")) {
						$sAddress = $db->f("address_1"). "(Or ".$db->f("address_2").")";
					}else{
						$sAddress = $db->f("address_1");
					}					
					
					echo "<strong>Name:</strong> ".$db->f("first_name"). " " .$db->f("middle_name"). " " .$db->f("last_name")."<br/>
						<b>".$VM_LANG->_VM_ADDRESS.":</b> ".$db->f("address_1").", ".$db->f("state_3")." ".$db->f("zip");	
					
					echo "<div></td>
							<td><input class='new_checkout_register_button edit-deliver' style='width:65px !important;' onclick='editDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_EDIT."' type='button'></td>
							<td><input class='new_checkout_register_button delete-deliver' style='width:65px !important;' onclick='deleteDeliver(\"".$db->f("user_info_id")."\",\"".$db->f("user_id")."\");' value='".$VM_LANG->_VM_DELETE."' type='button'></td>
						</tr>\n";
					if($i == 1) $i++;
					elseif($i == 2) $i--;
				}
		
				echo "</td>\n";
				echo "</tr>\n";
				echo "</table>\n[--3--]".$sCurrentStateTax."[--3--]".$currentZip;
			}else {
				echo "noshipping";
			}
			require_once 'end_access_log.php';
			die();
		}
    }
return true;
	}

}
?>
