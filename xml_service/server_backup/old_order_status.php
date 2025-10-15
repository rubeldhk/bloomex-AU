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


$rootPath			=  "$mosConfig_absolute_path/xml_service/xml_processed/";


echo "Generate Order Status XML file:<br/><br/>";

$sql		= " SELECT  P.* FROM jos_users AS U INNER JOIN  tbl_partners AS P ON P.partner_name = U.username WHERE P.published = 1 AND U.block = 0";
$users 		= $db->select($sql);

$i			= 1;
while ( $row = $db->get_row($users, 'MYSQL_ASSOC') ) {

	$sOrder	='<?xml version="1.0" encoding="utf-8"?>
				<orders version="1.0.0">';
	
	$sql = "SELECT * FROM tbl_xmlorder AS PF INNER JOIN jos_vm_orders AS O ON PF.order_id = O.order_id
				  LEFT JOIN jos_vm_order_status AS OS ON OS.order_status_code = O.order_status WHERE partner_name = '".$row['partner_name']."' ORDER BY O.cdate DESC";
	$order 	= $db->select($sql);
	while ( $order_item = $db->get_row($order, 'MYSQL_ASSOC') ) {
		$sOrder	.= '<order>
							<partner_order_id>' . $order_item['partner_order_id'] . '</partner_order_id> 
							<partner_name>' . $order_item['partner_name'] . '</partner_name>
							<order_id>' . $order_item['order_id'] . '</order_id> 
							<delivery_date>'.date( "m-d-Y H:i:s", strtotime($order_item['ddate'])).'</delivery_date>							
							<order_status>' . htmlentities( htmlspecialchars(strip_tags($order_item['order_status_name']), ENT_QUOTES), ENT_QUOTES, 'UTF-8' ) . '</order_status> 	
						</order>';
	}

	$sOrder	.= '</orders>';	
	

	/*if( is_file( $rootPath . "old_order_status/old_order_status.xml" ) ) {
		@unlink( $rootPath . "old_order_status/old_order_status.xml" );
	}
   
	$fh 			= fopen( $rootPath . "old_order_status/old_order_status.xml", 'w' ) or die("The old order status xml file wasn't create!");
	if( fwrite( $fh, $sOrder) ) {
		fclose($fh);	*/	
		
		$ftp = new ClsFTP( $row['ftp_username'], $row['ftp_password'], $row['domain_name'] );
		
		if( $ftp->file_size( "/httpdocs/old_order_status.xml" ) >= 0 ) {
			if( $ftp->chmod( "/httpdocs/old_order_status.xml", 0777 ) !== false ) {
				$ftp->delete( "/httpdocs/old_order_status.xml" );
			}
		}
		
//		if( $ftp->put( "/httpdocs/old_order_status.xml",  $rootPath . "old_order_status/old_order_status.xml" ) ) {	
		if( $ftp->put_string( "/httpdocs/old_order_status.xml",  $sOrder ) ) {	
			echo "$i. <b>" . $row['partner_name'] . "</b>: successful.<br/>";
		}else {
			echo "$i. <b>" . $row['partner_name'] . "</b>: unsuccessful.<br/>";
		}
	
		$ftp->close();
		
	/*}else {
		die("The main product list wasn't create!");
	}*/
	
	
	$query = "	UPDATE tbl_partners SET status_updated_time = '".date( "Y-m-d H:i:s" , time())."' WHERE partner_name = '" . $row['partner_name'] . "'";
	$db->update_sql($query);
	
	$i++;
}



?>