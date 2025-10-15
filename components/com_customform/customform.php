<?php
/**
* @version $Id: admin.Category.php 10002 2008-02-08 10:56:57Z willebil $
* @package Joomla
* @subpackage Category
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $mosConfig_absolute_path;

require_once( $mainframe->getPath( 'front_html' ) );

switch ($task) {
	case 'sendQuoteForm':
		sendQuoteForm();
		break;
		
	default:
		showQuoteForm(  );
		break;
}



function showQuoteForm( ) {
	global $database;
	
	$query 	= "SELECT * FROM #__vm_order_occasion ORDER BY order_occasion_name";
	$database->setQuery($query);
	$rows	= $database->loadObjectList();
	
	$aInfo['occasion']	= "";
	if( count($rows) ) {
		foreach($rows as $row) {			
			$aInfo['occasion']	.= '<option value="'.$row->order_occasion_name.'">'.$row->order_occasion_name.'</option>';
		}
	}
	
	
	$query 	= "SELECT S.* FROM #__vm_state AS S 
				INNER JOIN #__vm_country as C ON C.country_id = S.country_id 
				WHERE C.country_3_code = 'AUS' 
				ORDER BY S.state_name";
	$database->setQuery($query);
	$rows	= $database->loadObjectList();
	//ECHO $query;
	
	$aInfo['state']	= "";
	if( count($rows) ) {
		foreach($rows as $row) {			
			$aInfo['state']	.= '<option value="'.$row->state_name.'">'.$row->state_name.'</option>';
		}
	}
	
	HTML_CustomForm::showQuoteForm( $aInfo );
}


function sendQuoteForm( ) {
    if(!strpos($_SERVER['HTTP_USER_AGENT'],'Firefox') && !strpos($_SERVER['HTTP_USER_AGENT'],'Trident')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=6LdJvGgUAAAAAPH2Fo5RBuQy_EIkhm-6wgQuineo&response=" . $_REQUEST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if(curl_error($ch))
        {
            die('error:' . curl_error($ch));
        }
        $capcha_res = json_decode($server_output);
        if (!$capcha_res->success)
        {
            die("Incorrect CAPTCHA");
        }
    }
	global $database, $mosConfig_live_site, $mosConfig_fromname;
	
	$full_name			= trim(mosGetParam( $_REQUEST, "full_name" , "" ));
	$email				= trim(mosGetParam( $_REQUEST, "email" , "" ));
	$phone				= trim(mosGetParam( $_REQUEST, "phone" , "" ));
	$state				= trim(mosGetParam( $_REQUEST, "state" , "" ));
	$product_desc		= trim(mosGetParam( $_REQUEST, "product_desc" , "" ));
	$number_of_gift_basket		= trim(mosGetParam( $_REQUEST, "number_of_gift_basket" , "" ));
	$estimated_budget		= trim(mosGetParam( $_REQUEST, "estimated_budget" , "" ));
	$delivery_date		= trim(mosGetParam( $_REQUEST, "delivery_date" , "" ));

	$subject  	= "(BLMX.COM.AU) A new submit quote  from ". $full_name;
	$body 		=  "A new submit quote  from ". $full_name . " and contains the following data:\r\n\r\n";
	$body 		.= "Full Name: " . $full_name . "\r\n\r\n";
	$body 		.= "Phone: " . $phone . " \r\n\r\n";
	$body 		.= "E-mail: " . $email . "\r\n\r\n";
	$body 		.= "State: " . $state . "\r\n\r\n";
	$body 		.= "Number Of Gift Basket: " . $number_of_gift_basket . "\r\n\r\n";
	$body 		.= "Estimated Budget: " . $estimated_budget . "\r\n\r\n";
	$body 		.= "Delivery Date: " . $delivery_date . "\r\n\r\n";
	$body 		.= "Product Description: " . $product_desc . "\r\n\r\n";
		
	$success = mosMail( $email, $full_name , "corporate@bloomex.com.au", $subject, $body );

	
	$subject  	= "Thanks for your Quote request";
	$body 		= "You have successfully submitted a quote request. A Corporate Account Manager will get back to you shortly.\r\n\r\n";
	$body 		.= "Best Regards,\r\n\r\n";
	$body 		.= "Bloomex Corporate Account Team\r\n";
	$body 		.= "1800 768 357\r\n\r\n";
	
	$success = mosMail( "corporate@bloomex.com.au", $mosConfig_fromname , $email, $subject, $body );
	
	$link = sefRelToAbs( '?success=1' );
	
	mosRedirect( $link);
}


?>

