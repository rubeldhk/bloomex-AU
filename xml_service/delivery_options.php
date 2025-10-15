<?php

/*  Micah's MySQL Database Class - Sample Usage
*  4.17.2005 - Micah Carrick, email@micahcarrick.com
*
*  This is a sample file on using my database class.  Hopefully it will provide
*  you with enough information to use the class.  You should also look through
*  the comments in the db.class.php file to get additional information about
*  any specific function.
*/

require_once('../configuration.php');
require_once('db.class.php');
require_once("ftp.class.php");

$db = new db_class;
if (!$db->connect( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, true)) $db->print_last_error(false);

echo "Generate Delivery Options file:<br/><br/>";

$sql		= " SELECT  P.* FROM jos_users AS U INNER JOIN  tbl_partners AS P ON P.partner_name = U.username WHERE P.published = 1 AND U.block = 0";
$users 		= $db->select($sql);

$i			= 1;
while ( $row = $db->get_row($users, 'MYSQL_ASSOC') ) {	
	$ftp = new ClsFTP( $row['ftp_username'], $row['ftp_password'], $row['domain_name'] );
	
	$tempFile	= getDeliveryOptions($row['delivery_updated_time'], $db);
	
	if( $ftp->put( "/httpdocs/".date( "Ymd" , time())."_delivery_options.xml", $tempFile ) && $tempFile ) {	
		echo "$i. <b>" . $row['partner_name'] . "</b>: successful.<br/>";
		
		$query = "	UPDATE tbl_partners SET delivery_updated_time = '".date( "Y-m-d H:i:s" , time())."' WHERE partner_name = '" . $row['partner_name'] . "'";
		$db->update_sql($query);
	}else {
		echo "$i. <b>" . $row['partner_name'] . "</b>: unsuccessful.<br/>";
	}
	
	$query = "	UPDATE tbl_options SET modified_date = '".date( "Y-m-d H:i:s" , time() - (20*60) )."'";
	$db->update_sql($query);
	
	$ftp->close();	
	$i++;
}




function getDeliveryOptions( $lastUpdatedDate, $db ) {
	$sOrder	='<?xml version="1.0" encoding="utf-8"?>
				<delivery version="1.0.0">';
	
	$query 			= "SELECT * FROM tbl_options WHERE type='cut_off_time'";
	$cut_off_time	= $db->select($query);
	$oCutOffTime	= $db->get_row( $cut_off_time, 'MYSQL_ASSOC');
	
	if( (trim($oCutOffTime['options']) && strtotime($lastUpdatedDate) < strtotime($oCutOffTime['modified_date'])) || $lastUpdatedDate == "0000-00-00 00:00:00" ) {	
		$aOptionParam = explode( "[--1--]", $oCutOffTime['options'] );		
		
		$sOrder	.= "<cut_off_time>
						<time>".$aOptionParam[0].":".$aOptionParam[1]."</time>
						<surcharge>".LangNumberFormat::number_format($aOptionParam[2], 2, ".", "")."</surcharge>
					</cut_off_time>";
	}
	
	$sql 			= " SELECT * FROM tbl_options WHERE TYPE = 'deliver_option' ORDER BY name";		
	$nonDelivery 	= $db->select($sql);
	$sNonDelivery	= "";
	while ( $nonDeliveryItem = $db->get_row($nonDelivery, 'MYSQL_ASSOC') ) {
		if( (strtotime($lastUpdatedDate) < strtotime($nonDeliveryItem['modified_date'])) || $lastUpdatedDate == "0000-00-00 00:00:00" ) {
			$sNonDelivery	.= "<non_delivery_item>
									<date>".$nonDeliveryItem['name']."</date>
									<note>".$nonDeliveryItem['options']."</note>
								</non_delivery_item>";
		}
	}
	if( $sNonDelivery ) $sNonDelivery = "<non_delivery>$sNonDelivery</non_delivery>";
	
	
	$sql 				= " SELECT * FROM tbl_options WHERE TYPE = 'special_deliver' ORDER BY name";		
	$specialDelivery 	= $db->select($sql);
	$sSpecialDelivery	= "";
	while ( $specialDeliveryItem = $db->get_row($specialDelivery, 'MYSQL_ASSOC') ) {
		if( (strtotime($lastUpdatedDate) < strtotime($specialDeliveryItem['modified_date'])) || $lastUpdatedDate == "0000-00-00 00:00:00" ) {
			$sSpecialDelivery	.= "<special_delivery_item>
										<date>".$specialDeliveryItem['name']."</date>
										<surcharge>".LangNumberFormat::number_format($specialDeliveryItem['options'], 2, ".", "")."</surcharge>
									</special_delivery_item>";
		}
	}
	if( $sSpecialDelivery ) $sSpecialDelivery = "<special_delivery>$sSpecialDelivery</special_delivery>";
	
	
	$sql 				= " SELECT * FROM tbl_options WHERE TYPE = 'postal_code' ORDER BY name";		
	$postalcodeDelivery 	= $db->select($sql);
	$sPostalcodeDelivery	= "";
	while ( $postalcodeDeliveryItem = $db->get_row($postalcodeDelivery, 'MYSQL_ASSOC') ) {
		if( (strtotime($lastUpdatedDate) < strtotime($postalcodeDeliveryItem['modified_date'])) || $lastUpdatedDate == "0000-00-00 00:00:00" ) {
			$aTemp	= explode( "[--1--]", $postalcodeDeliveryItem['options'] );
			
			$sPostalcodeDelivery	.= "<postal_code_delivery_item>
											<postal_code>".$postalcodeDeliveryItem['name']."</postal_code>
											<location_name>".$aTemp[0]."</location_name>
											<extra_days>".$aTemp[1]."</extra_days>
											<surcharge>".LangNumberFormat::number_format($aTemp[2], 2, ".", "")."</surcharge>
										</postal_code_delivery_item>";
		}
	}
	if( $sPostalcodeDelivery ) $sPostalcodeDelivery = "<postal_code_delivery>$sPostalcodeDelivery</postal_code_delivery>";
	
	
	$sOrder	.= " $sPostalcodeDelivery $sSpecialDelivery $sNonDelivery</delivery>";	
	
	$tempFile = "tmp/delivery_options.xml";
	$fh = fopen($tempFile, 'w') or die("can't open file");
	fwrite($fh, $sOrder );
	fclose($fh);
	
	return $tempFile;
}

?>