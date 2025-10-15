<?php
ini_set('memory_limit', '-1');
define('_VALID_MOS', 1);
require( '../../../globals.php' );
require_once( '../../../configuration.php' );
require_once( $mosConfig_absolute_path . '/includes/joomla.php' );

loadfunc();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function loadfunc(){
     global $database, $mosConfig_offset,$mosConfig_absolute_path;
     
     $notdoit=true;
     $state=array('AB','BC','MB','NB','NL','NT','NS','NU','ON','PE','QC','SK','YT');
     $occasion=array('ANNIV','BIRTH', 'CONGR','JB','LOVER','00000','think','GET');
    

     $ret_html='<table style=" border: 1px solid black;"><thead>
            <tr >
                <th >Domain</th>
                <th >old id</th>
                <th >new id</th>
                <th >deliver date</th>
                <th >Province</th>
                <th >Postal code</th>  
                 <th >Card</th> 
            </tr>
            </thead><tbody>';
     $start_load=true;
     $ident_uniq=md5(microtime());
     $countOrd=0;
   $q = "SELECT product_sku FROM `jos_vm_product` WHERE product_id in (select product_id from `jos_vm_product_category_xref` where category_id=81 )";
                 $database->setQuery($q);
                 $database->query();
                 $all_product = $database->loadAssocList();
                 $count_product=count($all_product);
     if($start_load){
     $key="LRWvbvldfgERUFG;AekghbnzyfgUKA956";//pass
            $user=array();    
            while ($countOrd < 25) {
                $new_user=rand(0,1);
                if($new_user==1){
                  $user['name']=generateRandomString();
                  $user['username']=generateRandomString();
                 $user['email']=generateRandomString(5).'@'.generateRandomString(4).'.com';
                 $user['password']='test123456';
                 $user['sendEmail']=$user['email'];
                }
                else{     
                    $q = "SELECT * FROM `jos_users` ";
                 $database->setQuery($q);
                 $database->query();
                 $result_u = $database->loadAssocList();
                 $user_count=count($result_u);
                 $user=$result_u[rand(1,$user_count)];   
               
                }
                 $q = "SELECT * FROM `jos_users` WHERE `email`='".$user['email']."'";
                 $database->setQuery($q);
                 $database->query();
                 $result_u = $database->loadAssocList();
                 if(count($result_u)>0){
                   $user_id_new=$result_u[0]['id'];
                 }
                 else{
                     $q = "insert into `jos_users` ( `name`, `username`, `email`, `email2`, `password`, `usertype`, `block`, `sendEmail`, `gid`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES(";
                     $q.="'".$user['name']."','".$user['username']."','".$user['email']."',NULL,'".$user['password']."','Registered',0,'".$user['sendEmail']."',18,NOW(),NOW(),NOW(),'')";
                     $database->setQuery($q);
                     $database->query(); 
                    
                     $user_id_new	= $database->insertid();
                 } 
                 $sql="SELECT * FROM `jos_vm_user_info` WHERE `address_type`='BT' and `user_id`=".$user_id_new."";
                  $database->setQuery($sql);
                 $database->query();
                 $result_ui = $database->loadAssocList();
                  if(!$result_ui){    
                          $user_info=array();//= mysql_fetch_assoc($user_info_res);
                          $user_info['address_type']='BT';
                          $user_info['address_type_name']='-default-';
                          $user_info['company']=(rand(0,1)==0)?generateRandomString():'';
                          $user_info['title']='';
                          $user_info['last_name']=generateRandomString();
                          $user_info['first_name']=generateRandomString();
                          $user_info['middle_name']=(rand(0,1)==0)?generateRandomString():'';
                          $user_info['phone_1']=generateRandomString();
                          $user_info['phone_2']=(rand(0,1)==0)?generateRandomString():'';
                          $user_info['fax']=(rand(0,1)==0)?generateRandomString():'';
                          $user_info_id	= md5($user_id_new.time());
                          $user_info['address_1']=generateRandomString();
                          $user_info['address_2']=(rand(0,1)==0)?generateRandomString():'';
                          $user_info['city']=generateRandomString();
                          $user_info['state']=$state[rand(0,12)];
                          $user_info['country']='CAN';
                          $user_info['zip']=generateRandomString(5);
                          $user_info['user_email']=$user['email'];
                          $user_info['extra_field_1']='';
                          $user_info['extra_field_2']='';
                          $user_info['extra_field_3']='';
                          $user_info['extra_field_4']='';
                          $user_info['extra_field_5']='';
                         
                           $q = "insert into `jos_vm_user_info` (`user_info_id`,`user_id`, 
                                                                 `address_type`, 
                                                                 `address_type_name`, 
                                                                 `company`, 
                                                                 `title`, 
                                                                 `last_name`, 
                                                                 `first_name`,
                                                                 `middle_name`, 
                                                                 `phone_1`, 
                                                                 `phone_2`, 
                                                                 `fax`, 
                                                                 `address_1`, 
                                                                 `address_2`, 
                                                                 `city`, 
                                                                 `state`, 
                                                                 `country`, 
                                                                 `zip`, 
                                                                 `user_email`, 
                                                                 `extra_field_1`, 
                                                                 `extra_field_2`, 
                                                                 `extra_field_3`, 
                                                                 `extra_field_4`, 
                                                                 `extra_field_5`, 
                                                                 `cdate`, 
                                                                 `mdate`, 
                                                                 `perms`, 
                                                                 `bank_account_nr`, 
                                                                 `bank_name`, 
                                                                 `bank_sort_code`, 
                                                                 `bank_iban`, 
                                                                 `bank_account_holder`, 
                                                                 `bank_account_type`, 
                                                                 `address_type2`, 
                                                                 `suite`, 
                                                                 `street_number`, 
                                                                 `street_name`) 
                                                                 VALUES(";
                     $q.="'".$user_info_id."','".$user_id_new."','"
                            .$user_info['address_type']."','"
                            .$user_info['address_type_name']."','"
                             .$user_info['company']."','"
                             .$user_info['title']."','"
                             .$user_info['last_name']."','"
                             .$user_info['first_name']."','"
                             .$user_info['middle_name']."','"
                             .$user_info['phone_1']."','"
                             .$user_info['phone_2']."','"
                             .$user_info['fax']."','"
                             .$user_info['address_1']."','"
                             .$user_info['address_2']."','"
                             .$user_info['city']."','"
                             .$user_info['state']."','"
                             .$user_info['country']."','"
                             .$user_info['zip']."','"
                             .$user_info['user_email']."','"
                             .$user_info['extra_field_1']."','"
                             .$user_info['extra_field_2']."','"
                             .$user_info['extra_field_3']."','"
                             .$user_info['extra_field_4']."','"
                             .$user_info['extra_field_5']."',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()),'','','','','','','','',NULL,'','"
                             ."')";
                    
                     $database->setQuery($q);
                     $database->query(); 
                  }
          
                 $order['order_id']='';
                 $order['ddate']=date("d-m-Y",strtotime(rand(1,27)."-".rand(1,12)."-2013"));
                 $order['customer_occasion']=$occasion[rand(0,7)];
                 $order['coupon_code']='';
                 $order['customer_note']=(rand(0,1)==0)?generateRandomString():'';
                $order['customer_signature']=(rand(0,1)==0)?generateRandomString():'';
                $order['last_name']=generateRandomString();
                $order['middle_name']=(rand(0,1)==0)?generateRandomString():'';
                $order['first_name']=generateRandomString();
                $order['phone_1']=generateRandomString();
                $order['address_1']=generateRandomString();
                $order['address_2'] =(rand(0,1)==0)?generateRandomString():'';
                $order['city']=generateRandomString();
                $order['state'] =$state[rand(0,12)];
                $order['zip']=generateRandomString(5);
                $order['country']='CAN';
                $order['fax']=(rand(0,1)==0)?generateRandomString():'';
                $order['user_email']=$user['email'];
                $order['customer_comments']=(rand(0,1)==0)?generateRandomString():'';
                 $aOrderItem 							= array();
                 $aOrderItem['Mode']						= 1;
		 $aOrderItem['PartnerEmail']				= $user['email'];
               
                 	 $aOrderItem['PartnerName']				='generate';

						$aOrderItem['PartnerOrderID']			= trim($order['order_id']);					
						$aOrderItem['DeliveryDate']				= trim($order['ddate']);
						$aOrderItem['Occasion']					= trim($order['customer_occasion']);
						$aOrderItem['CouponCode']				= trim($order['coupon_code']);
						$aOrderItem['RecipientCardMessage']		= trim($order['customer_note']);	
						$aOrderItem['RecipientSignature']		= trim($order['customer_signature']);				
						
						$aOrderItem['RecipientLastName']		= trim($order['last_name']);
						$aOrderItem['RecipientMiddleName']		= trim($order['middle_name']);
						$aOrderItem['RecipientFirstName']		= trim($order['first_name']);
						$aOrderItem['RecipientPhoneNumber']		= trim($order['phone_1']);
						$aOrderItem['RecipientAddress']			= trim($order['address_1'] );
						$aOrderItem['RecipientAddress2']		= trim($order['address_2'] );
						$aOrderItem['RecipientCity']			= trim($order['city'] );
						$aOrderItem['RecipientState']			= trim($order['state'] );
						$aOrderItem['RecipientZipCode']			= trim($order['zip'] );
						$aOrderItem['RecipientCountry']			= trim($order['country'] );
						$aOrderItem['RecipientFax']				= trim($order['fax'] );
						$aOrderItem['RecipientEmail']			= trim($order['user_email']);
                                                $aOrderItem['RecipientComments']			= trim($order['customer_comments']);                                               
                 
                                                
		                   $aOrderItem['ProductSKU']		= "";
                       $user_info_id	= md5($user_id_new.time());
                       $q = "insert into `jos_vm_user_info` (`user_info_id`,`user_id`, 
                                                                 `address_type`, 
                                                                 `address_type_name`, 
                                                                 `company`, 
                                                                 `title`, 
                                                                 `last_name`, 
                                                                 `first_name`,
                                                                 `middle_name`, 
                                                                 `phone_1`, 
                                                                 `phone_2`, 
                                                                 `fax`, 
                                                                 `address_1`, 
                                                                 `address_2`, 
                                                                 `city`, 
                                                                 `state`, 
                                                                 `country`, 
                                                                 `zip`, 
                                                                 `user_email`, 
                                                                 `extra_field_1`, 
                                                                 `extra_field_2`, 
                                                                 `extra_field_3`, 
                                                                 `extra_field_4`, 
                                                                 `extra_field_5`, 
                                                                 `cdate`, 
                                                                 `mdate`, 
                                                                 `perms`, 
                                                                 `bank_account_nr`, 
                                                                 `bank_name`, 
                                                                 `bank_sort_code`, 
                                                                 `bank_iban`, 
                                                                 `bank_account_holder`, 
                                                                 `bank_account_type`, 
                                                                 `address_type2`, 
                                                                 `suite`, 
                                                                 `street_number`, 
                                                                 `street_name`) 
                                                                 VALUES(";
                     $q.="'".$user_info_id."','".$user_id_new."','ST','','','','" 
                             .$aOrderItem['RecipientLastName']."','"
                             .$aOrderItem['RecipientFirstName']."','"
                             .$aOrderItem['RecipientMiddleName']."','"
                             .$aOrderItem['RecipientPhoneNumber']."','','"   
                             .$aOrderItem['RecipientFax']."','"
                             .$aOrderItem['RecipientAddress']."','"
                             .$aOrderItem['RecipientAddress2']."','"
                             .$aOrderItem['RecipientCity']."','"
                             .$aOrderItem['RecipientState']."','"
                             .$aOrderItem['RecipientCountry']."','"
                             .$aOrderItem['RecipientZipCode']."','"
                             .$aOrderItem['RecipientEmail']."','','','','','',UNIX_TIMESTAMP(NOW()),UNIX_TIMESTAMP(NOW()),'','','','','','','','',NULL,'','"
                             ."')";
                    
                     $database->setQuery($q);
                     $database->query();              
                                   
                                   
                  $aProductItem					= array();
                  $bCart							= false;
                  $sProductQ="";
                  $count_order_item=rand(1,5);
                  $order_item_c=1;
                   while ($order_item_c <=$count_order_item) {
                       $prod=$all_product[rand(0,$count_product)];
                       
                       $quant=rand(1,3);
                      $aProductItem[]				 = trim($prod['product_sku'] );
			$sProductItem			 	.= trim($prod['product_sku'] ). ",";
                        $sProductQ                              .=$prod['product_sku'].",".$quant.";";
                       $aOrderItem['ProductSKU']	.= trim($prod['product_sku']) . '[--1--]' . trim($quant) . '[--2--]';
                       $bCart	= true;
                       $order_item_c++;
                   }
                   $sError		= "";
		   $bCheck		= true;
                  
                 
                 
                  if( $bCheck ) {
                  $sResultProcess		= confirmOrder($aOrderItem);
                   
                  if($sResultProcess){
                  $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                  $query = "INSERT INTO jos_vm_order_history(	order_id,
													order_status_code,
													date_added,
													customer_notified,
													comments) 
					VALUES ('$sResultProcess', 
							'P', 
							'" . $mysqlDatetime . "', 
							1, 
							' order id : ".$aOrderItem['PartnerOrderID']." from ".$row['title']." ')";
                
			$database->setQuery($query);
                     $database->query(); 
                     
                  }       
                  if($last_date<$order['cdate']){
                    $last_date=$order['cdate'];
                  }
                  }
                   else{
                     $sResultProcess=0; 
                  }
                   $mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
                   $query = "INSERT INTO `tbl_load_history`(`old_number`, `new_number`, `from`, `where`,`note`)
					VALUES (".$aOrderItem['PartnerOrderID'].",". $sResultProcess.",'".$row['title']."',UNIX_TIMESTAMP(' ".$mysqlDatetime."'),'".$sError."')";
							
                
			$database->setQuery($query);
                     $database->query(); 
                     
                   if($sResultProcess!=0){
                       $countOrd++;
                   $ret_html.="<tr><td>".$row['title']."</td><td>".$aOrderItem['PartnerOrderID']."</td><td>".$sResultProcess."</td><td>".$aOrderItem['DeliveryDate']."</td><td>".$aOrderItem['RecipientState']."</td><td>".$aOrderItem['RecipientZipCode']."</td><td>".$sProductQ."</td></tr>";  
                   }
              
            }
                   $ret_html.="<tr><td>".$row['title']."</td><td colspan 6>".$countOrd."</td></tr>";  
                   $countOrd=0;
           
       
        
       
     }
     
    
       $ret_html.="</tbody></table>";
       exit($ret_html );  
    
     
}
function encode($String, $Password)
{
    $Salt='BGuxLWQtKweKEMV4';
    $StrLen = strlen($String);
    $Seq = $Password;
    $Gamma = '';
    while (strlen($Gamma)<$StrLen)
    {
        $Seq = pack("H*",sha1($Gamma.$Seq.$Salt));
        $Gamma.=substr($Seq,0,8);
    }
   
    return $String^$Gamma;
}
function confirmOrder( $Info ) {	
	global $mosConfig_offset, $mosConfig_absolute_path, $mosConfig_mailfrom, $mosConfig_live_site, $mosConfig_fromname, $database;
	
	
	$timestamp 			= time() + ($mosConfig_offset*60*60);
	$PaymentVar			= array();
	$isResult			= true;
	$sResultProcess		= "";

	$bTestMode					= $Info['Mode'];
	$nPartnerOrderID			= $Info['PartnerOrderID'];
	$sPartnerEmail				= $Info['PartnerEmail'];
        $sPartnerName                           = $Info['PartnerName'];
	$sDeliveryDate				= $Info['DeliveryDate'];
		
	$sProductSKU				= $Info['ProductSKU']; 
	$aProductItem				= explode( '[--2--]', $sProductSKU );
	$aQuantity					= array();
	$sProductSKUItem			= "";
	
	if( count($aProductItem) ) {
		foreach ($aProductItem as $Item) {
			if( $Item ) {
				$aItem	= explode( '[--1--]', $Item );
				$sProductSKUItem		.= "'".$aItem[0] . "',";
				$aQuantity[$aItem[0]]	 = intval($aItem[1]);
			}
		}
	}	
	$sProductSKUItem			= substr( $sProductSKUItem, 0, strlen($sProductSKUItem) - 1 );
	
	$sOccasion					= $Info['Occasion'];		
	$sCouponCode				= $Info['CouponCode'];	
	$sRecipientCardMessage		= $Info['RecipientCardMessage'];
	$sRecipientSignature		= $Info['RecipientSignature'];		

	
	
	$sRecipientPhoneNumber		= $Info['RecipientPhoneNumber'];
	$sRecipientAddress			= $Info['RecipientAddress'];
	$sRecipientAddress2			= $Info['RecipientAddress2'];
	$sRecipientCity				= $Info['RecipientCity'];
	$sRecipientState			= $Info['RecipientState'];
	$sRecipientZipCode			= $Info['RecipientZipCode'];
	$sRecipientCountry			= $Info['RecipientCountry'];
	$sRecipientFax				= $Info['RecipientFax'];
	$sRecipientEmail			= $Info['RecipientEmail'];

	
	$query 	= "SELECT * FROM jos_users AS U, jos_vm_user_info AS UI WHERE U.id = UI.user_id AND U.email = '{$sPartnerEmail}' LIMIT 1";
	 $database->setQuery($query);
        $database->query();
	$oUser	= $database->loadAssocList();
		
	/*print_r($oUser);
	die("aaaaaaaaaaaaa");*/
        
        $user_info_id		= "";		
	$user_id		= "";
	$bill_company_name 	= ""; 
	$bill_last_name 	= ""; 
	$bill_first_name 	= ""; 
	$bill_middle_name 	= ""; 
	$bill_phone 		= ""; 
	$bill_fax 		= ""; 
	$bill_address_1 	= ""; 
	$bill_address_2 	= ""; 
	$bill_city 		= ""; 
	$bill_state 		= ""; 
	$bill_country 		= ""; 
	$bill_zip_code 		= ""; 
	$bill_email 		= ""; 
	$account_email 		= ""; 	
       
	if( $oUser ) {		
		$user_info_id		= $oUser[0]['user_info_id'];		
		$user_id		= $oUser[0]['id'];
		$bill_company_name 	= $oUser[0]['company']; 
		$bill_last_name 	= $oUser[0]['last_name']; 
		$bill_first_name 	= $oUser[0]['first_name']; 
		$bill_middle_name 	= $oUser[0]['middle_name']; 
		$bill_phone 		= $oUser[0]['phone_1']; 
		$bill_fax 		= $oUser[0]['fax']; 
		$bill_address_1 	= $oUser[0]['address_1']; 
		$bill_address_2 	= $oUser[0]['address_2']; 
		$bill_city 		= $oUser[0]['city']; 
		$bill_state 		= $oUser[0]['state']; 
		$bill_country 		= $oUser[0]['country']; 
		$bill_zip_code 		= $oUser[0]['zip']; 
		$bill_email 		= $oUser[0]['user_email']; 
		$account_email 		= $oUser[0]['email']; 		
	}
	           
	
	$occasion					= "CONGR";
	$card_msg					= $sRecipientCardMessage;
	$signature					= $sRecipientSignature;
        $card_comment				= $Info['RecipientComments'];	
	
	
	$aDeliveryDate				= explode( "-", $sDeliveryDate );
	$deliver_day				= intval(trim($aDeliveryDate[1]));
	$deliver_month				= intval(trim($aDeliveryDate[0]));
	$deliver_year				= intval(trim($aDeliveryDate[2]));
	
	$address_user_name			= "";
	$deliver_company_name 		= ""; 
	
	$deliver_first_name			= $Info['RecipientFirstName'];
	$deliver_last_name			= $Info['RecipientLastName'];
	$deliver_middle_name                    = $Info['RecipientMiddleName'];

	$deliver_phone				= $sRecipientPhoneNumber;
	$deliver_cell_phone			= $sRecipientPhoneNumber;
	$deliver_fax				= $sRecipientFax;	
	$deliver_address_1			= $sRecipientAddress;
	$deliver_address_2			= $sRecipientAddress2;
	$deliver_city				= $sRecipientCity;
	$deliver_state				= $sRecipientState;
	$deliver_country			= $sRecipientCountry;	
	$deliver_zip_code			= $sRecipientZipCode;
	$deliver_recipient_email	= $sRecipientEmail;
	
   			
	$payment_method_state		= "";
	$payment_method				= "";
	$name_on_card				= "";
	$credit_card_number			= "";
	$credit_card_security_code	= "";
	$expire_month				= "";
	$expire_year				= "";
	$find_us					= 1;
	
	
	$query 		= "SELECT * FROM jos_vm_tax_rate WHERE tax_country = '{$deliver_country}' AND tax_state = '{$deliver_state}' LIMIT 1";
         $database->setQuery($query);
        $database->query();
	$oStateTax	= $database->loadResult();
	
	$nStateTax	= $oStateTax['tax_rate'];
	
		
	$query				= " SELECT VM.product_id, VM.product_sku, VM.product_name, VMP.product_price, VTR.tax_rate 
							FROM jos_vm_product AS VM INNER JOIN jos_vm_product_price AS VMP ON VM.product_id = VMP.product_id
							INNER JOIN  jos_vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id AND VM.product_sku IN ({$sProductSKUItem})";
	
                 $database->setQuery($query);
        $database->query();
	$result	= $database->loadAssocList();
        $sProductId="";
         foreach ($result as $row) {                                               
		$sProductId			.=  $row['product_id'] . ",";
		
		$sub_total_price	+= 	round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']];
		if( $nStateTax ) {
			$total_tax		+=	round( $nStateTax, 2 ) * ( round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']] );	
		}else {
			$total_tax		+=	round( floatval($row['tax_rate']), 2 ) * ( round( doubleval($row['product_price']), 2 ) * $aQuantity[$row['product_sku']] );	
		}
	}
	
	 $database->setQuery("SELECT * FROM jos_vm_coupons WHERE coupon_code = '" . $sCouponCode . "'");
        $database->query();
	$oCoupon			= $database->loadAssocList();
	$nDiscountValue		= 0;
	if( $oCoupon[0]['coupon_id'] ){
		if( $oCoupon[0]['percent_or_total'] == 'percent' ){
			$sub_total_price	= floatval($sub_total_price) - ( $sub_total_price * (floatval($oCoupon[0]['coupon_value']) / 100) ) ; 
			$nDiscountValue		= $sub_total_price * (floatval($oCoupon[0]['coupon_value']) / 100);
		}else {
			$sub_total_price	= floatval($sub_total_price) - floatval($oCoupon[0]['coupon_value']);
			$nDiscountValue		= floatval($oCoupon[0]['coupon_value']);
		}
	}
	
	$total_price	= doubleval( $sub_total_price + $total_tax );
	$sProductId		= substr( $sProductId, 0, strlen($sProductId) - 1 );
		
	
	$query 	= "SELECT shipping_rate_id, shipping_rate_value FROM jos_vm_shipping_rate ORDER BY shipping_rate_list_order ASC LIMIT 1";
	 $database->setQuery($query);
        $database->query();
        $row 	= $database->loadAssocList();	
	if( $nStateTax ) {
		$total_deliver_tax_fee		= doubleval( $row[0]['shipping_rate_value'] * $nStateTax );
	}else {
		$total_deliver_tax_fee		= doubleval( $row[0]['shipping_rate_value']  );	
	}	
	
	$deliver_fee				= doubleval( $row[0]['shipping_rate_value'] + $total_deliver_tax_fee );
	$shipping_method			= $row[0]['shipping_rate_id'];
	
	//Canculate Total Price
	$total_price	= doubleval( $deliver_fee + $total_price );
	
	
	//echo "=======================".$deliver_address_item."=======================";	
	$query 				= "SELECT * FROM jos_vm_vendor WHERE vendor_country = '{$bill_country}'";
        	 $database->setQuery($query);
        $database->query();
        $row 	= $database->loadAssocList();	
	$vendor_id			= $row[0]['vendor_id'];
	if( !$vendor_id ) $vendor_id = 1;
	$PaymentVar["vendor_id"]		= $vendor_id;	
	$vendor_currency				= $row['vendor_currency'];
	$PaymentVar["vendor_currency"]	= $vendor_currency;
	//echo "<br/>33. <br/>".$query."<br/>";
	
	
	$query 				= " SELECT VSC.shipping_carrier_name, VSR.shipping_rate_name, VSR.shipping_rate_value, VSR.shipping_rate_id 
							FROM jos_vm_shipping_rate AS VSR
							INNER JOIN jos_vm_shipping_carrier AS VSC
							ON VSC.shipping_carrier_id = VSR.shipping_rate_carrier_id  
							WHERE VSR.shipping_rate_id = {$shipping_method}";
         $database->setQuery($query);
        $database->query();
        $rows 	= $database->loadAssocList();
        foreach ($rows as $row) { 
	$sShippingMethod	= "standard_shipping|". implode( "|", $row );
        }
	
	$order_tax_details 	= array();
	$query				= " SELECT VMP.product_price, VTR.tax_rate 
							FROM jos_vm_product AS VM 
							LEFT JOIN jos_vm_product_price AS VMP 
							ON VM.product_id = VMP.product_id 
							LEFT JOIN  jos_vm_tax_rate AS VTR 
							ON VM.product_tax_id = VTR.tax_rate_id 
							WHERE VM.product_id IN ({$sProductId})";
        $database->setQuery($query);
        $database->query();
        $rows 	= $database->loadAssocList();
	foreach ($rows as $row) { 
		if( $nStateTax ) {
			if( !isset($order_tax_details[$nStateTax]) ) {
				$order_tax_details[$nStateTax] = doubleval($nStateTax) * doubleval($row['product_price']);
			}else {
				$order_tax_details[$nStateTax] = $order_tax_details[$nStateTax] + (doubleval($nStateTax) * doubleval($row['product_price']));
			}	
		}else {
			if( !isset($order_tax_details[$row['tax_rate']]) ) {
				$order_tax_details[$row['tax_rate']] = doubleval($row['tax_rate']) * doubleval($row['product_price']);
			}else {
				$order_tax_details[$row['tax_rate']] = $order_tax_details[$row['tax_rate']] + (doubleval($row['tax_rate']) * doubleval($row['product_price']));
			}	
		}
	}
			
        
	/* Insert the main order information */
	$order_number	= md5( "order".$user_id.time() );
	
	//================================== PAYMENT =========================================
	
	
	
	$aResult["order_payment_log"] 	= ' Payment information was captured for later processing. We may contact you over the phone to verify credit card information.<br />';;
	$order_status					= "P";
	$payment_msg					= " and ". ' Payment information was captured for later processing. We may contact you over the phone to verify credit card information.<br />';;
	
	//====================================================================================
	$phpShopDeliveryDate	=  date( "M d, Y" ,strtotime($deliver_day."-".$deliver_month."-".$deliver_year) + ($mosConfig_offset*60*60));
	
	if( $bTestMode ) { //check test mode 
		$query 			= "INSERT INTO jos_vm_orders( user_id, 
													 vendor_id, 
													 order_number, 
													 user_info_id, 
													 order_total, 
													 order_subtotal, 
													 order_tax, 
													 order_tax_details, 
													 order_shipping, 
													 order_shipping_tax, 
													 coupon_discount, 
													 order_currency, 
													 order_status, 
													 cdate, 
													 mdate, 
													 ddate,
													 ship_method_id, 
													 customer_note, 
													 customer_signature, 
													 customer_occasion, 
													 customer_comments, 
													 find_us,
													 username ) 
					   	   VALUES( 	$user_id, 
					   	   			$vendor_id, 
					   	   			'$order_number', 
					   	   			'$user_info_id', 
					   	   			$total_price, 
					   	   			$sub_total_price, 
					   	   			$total_tax, 
					   	   			'".serialize($order_tax_details)."', 
					   	   			$deliver_fee, 
					   	   		   	$total_deliver_tax_fee, 
					   	   		   	$nDiscountValue, 
					   	   		   	'$vendor_currency', 
					   	   		   	'$order_status', 
					   	   		   	".$timestamp.", 
					   	   		   	".$timestamp.", 
					   	   		   	'".($deliver_day."-".$deliver_month."-".date("Y", time()))."', 
					   	   		   	'".$sShippingMethod."', 
							   	   	'".htmlspecialchars(strip_tags($card_msg))."', 
							   	   	'".htmlspecialchars(strip_tags($signature))."', 
							   	   	'".htmlspecialchars(strip_tags($occasion))."', 
							   	   	'".htmlspecialchars(strip_tags($card_comment))."', 
							   	   	'$find_us', 
							   	   	'".htmlspecialchars(strip_tags($sPartnerName))."' )";
                 $database->setQuery($query);
                     $database->query(); 
                     $order_id	= $database->insertid();	
   
		if( !$order_id ) {
                    
                  
			return  false;
		}
	}
	
	$query="SELECT order_status_name FROM jos_vm_order_status WHERE order_status_code = '" . $order_status . "'";
                $database->setQuery($query);
        $database->query();
        $sOrderStatus 	= $database->loadResult();
	if( $sOrderStatus ){
		$order_status	= $sOrderStatus;
	}	
	$sResultProcess	= $order_id ;
	
	
	/*echo $query;
	echo "<br/>4. <br/>".$database->getErrorMsg()."<br/>";
	die($sResultProcess . "===========" .$query);*/
	
	
	if( $bTestMode ) { //check test mode 
		//Mix Info
			
	
		/*Insert the initial Order History.*/
		$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
			
		$query = "INSERT INTO jos_vm_order_history(	order_id,
													order_status_code,
													date_added,
													customer_notified,
													comments) 
					VALUES ('$order_id', 
							'P', 
							'" . $mysqlDatetime . "', 
							1, 
							'')";
                
			$database->setQuery($query);
                     $database->query(); 
 
		//echo "<br/>5. <br/>".$database->getErrorMsg()."<br/>";
			
	
		/*Insert the Order payment info */
		$payment_number = preg_replace("/ |-/", "", $credit_card_number);	
	
		
		// Payment number is encrypted using mySQL ENCODE function.
		$query 	= "INSERT INTO jos_vm_order_payment(	order_id, 
													order_payment_code, 
													payment_method_id, 
													order_payment_number, 
													order_payment_expire, 
													order_payment_log, 
													order_payment_name, 
													order_payment_trans_id) 
					VALUES ({$order_id}, 
							'{$credit_card_security_code}', 
							3, 
							'{$payment_number}', 
							'".strtotime("{$expire_month}/01/{$expire_year}")."',
							'{$aResult["order_payment_log"]}',
							'{$name_on_card}',			
							'{$aResult["order_payment_trans_id"]}')";
                		 $database->setQuery($query);
                     $database->query(); 
		
		//echo "<br/>6. <br/>".$database->getErrorMsg()."<br/>";
	
	
		/*Insert the User Billto & Shipto Info to Order Information Manager Table*/
		$query 	= "INSERT INTO jos_vm_order_user_info (  order_id, 
														user_id, 
														address_type, 
														address_type_name, 
														company, 
														last_name, 
														first_name, 
														middle_name, 
														phone_1, 
														fax, 
														address_1, 
														address_2, 
														city, 
														state, 
														country, 
														zip,
														user_email ) 
					   	   VALUES(  '".$order_id."', 
					   	   			{$user_id}, 
					   	   			'BT', 
					   	   			'-default-', 
					   	   			'". $bill_company_name."', 
					   	   			'". $bill_last_name."', 
					   	   			'". $bill_first_name."', 
					   	   			'". $bill_middle_name."', 
					   	   			'". $bill_phone."', 
					   	   			'". $bill_fax."', 
					   	   			'". $bill_address_1."', 
					   	   			'". $bill_address_2."', 
					   	   			'". $bill_city."', 
					   	   			'". $bill_state."', 
					   	   			'". $bill_country."', 
					   	   			'". $bill_zip_code."', 
					   	   			'". $account_email."' )";
                  $database->setQuery($query);
                     $database->query(); 
                     $isResult	= $database->insertid();                                                       

		if( !$isResult ) {
                    
			return  false;
		}	
		//echo "<br/>7. <br/>".$database->getErrorMsg()."<br/>";	
			
		$query 	= "INSERT INTO jos_vm_order_user_info (  order_id, 
														user_id, 
														address_type, 
														address_type_name, 
														company, 
														last_name, 
														first_name, 
														middle_name, 
														phone_1, 
														phone_2, 
														fax, 
														address_1, 
														address_2, 
														city, 
														state, 
														country, 
														zip,
														user_email ) 
					   	   VALUES(  '".$order_id."', 
					   	   			{$user_id}, 
					   	   			'ST', 
					   	   			'". $address_user_name."', 
					   	   			'$deliver_company_name', 
					   	   			'". $deliver_last_name."', 
					   	   			'". $deliver_first_name."', 
					   	   			'". $deliver_middle_name."', 
					   	   			'". $deliver_phone."', 
					   	   			'". $deliver_cell_phone."', 
					   	   			'". $deliver_fax."', 
					   	   			'". $deliver_address_1."', 
					   	   			'". $deliver_address_2."', 
					   	   			'". $deliver_city."', 
					   	   			'". $deliver_state."', 
					   	   			'". $deliver_country."', 
					   	   			'". $deliver_zip_code."', 
					   	   			'". $deliver_recipient_email."' )";
                     $database->setQuery($query);
                     $database->query(); 
                     $isResult	= $database->insertid();    
		
		if( !$isResult ) {
			return  false;
		}
		//echo "<br/>8. <br/>".$database->getErrorMsg()."<br/>";
		
	
		/*Insert all Products from the Cart into order line items*/
		$order_items	= '<table width="100%">';
		$order_items   .= '<tr>
								<td width="5%">No</td>
								<td width="5%">SKU Code</td>
								<td width="50%">Product Name</td>
								<td width="10%">Product Price (Net)</td>
								<td width="5%">Tax</td>
								<td width="10%">Product Price (Gross)</td>
								<td width="5%">Quantity</td>
								<td width="10%">SubTotal</td>
							</tr>';
		
		$phpShopOrderSubtotal	= 0;
		$phpShopOrderTax		= 0;
		$query					= " SELECT VM.product_id, VM.product_name, VM.product_sku, VM.product_desc, VMP.product_price, VMP.product_currency, VM.product_in_stock, VTR.tax_rate 
									FROM jos_vm_product AS VM LEFT JOIN jos_vm_product_price AS VMP 
									ON VM.product_id = VMP.product_id 
									LEFT JOIN  jos_vm_tax_rate AS VTR 
									ON VM.product_tax_id = VTR.tax_rate_id 
									WHERE VM.product_id IN ({$sProductId})";
                      $database->setQuery($query);
                     $database->query(); 
                     $rows	= $database->loadAssocList();
		
		
		$j 	= 0;
		foreach ($rows as $row) { 
							
			if( $nStateTax ) {
				$nTaxTemp	= $nStateTax;			
			}else {
				$nTaxTemp	= $row['tax_rate'];
			}
			
			$query 	= "INSERT INTO jos_vm_order_item (   order_id, 
														user_info_id, 
														vendor_id, 
														product_id, 
														order_item_sku, 
														order_item_name, 
														product_quantity, 
														product_item_price, 
														product_final_price, 
														order_item_currency, 
														order_status, 
														product_attribute, 
														cdate, 
														mdate ) 
				   	   VALUES(  {$order_id}, 
				   	   			'{$user_info_id}', 
				   	   			{$vendor_id}, 
				   	   			".$row['product_id'].", 
				   	   			'".addslashes( $row['product_sku'] )."', 
				   	   			'".addslashes( $row['product_name'] )."', 	
				   	   			".intval($aQuantity[$row['product_sku']]).", 
				   	   			".$row['product_price'].", 	
				   	   			".( ($row['product_price'] * $nTaxTemp ) + $row['product_price'] ).", 	
				   	   			'".$row['product_currency']."', 
				   	   			'P', 
				   	   			'".addslashes( $row['product_desc'] )."', 
				   	   			'{$timestamp}', 
				   	   			'{$timestamp}'
				   	   			 )";
                            $database->setQuery($query);
                     $database->query(); 
                    
			//echo "<br/>9-1. <br/>".$database->getErrorMsg()."<br/>";
			
	
			$order_items .= '<tr>
								<td>'.($j+1).'. </td>
								<td>'.addslashes( $row['product_sku'] ).'</td>
								<td>'.addslashes( $row['product_name'] ).'<br/>'.addslashes( $row['product_desc'] ).'</td>
								<td>$'.number_format( $row['product_price'], 2, ".", " " ).'</td>
								<td>'.number_format( $nTaxTemp, 2, ".", " " ).'</td>
								<td>$'.number_format( (($row['product_price'] * $nTaxTemp ) + $row['product_price']), 2, ".", " " ).'</td>
								<td>'.intval($aQuantity[$row['product_sku']]).'</td>
								<td>$'.number_format( (($row['product_price'] * $nTaxTemp) + $row['product_price'])*intval($aQuantity[$row['product_sku']]), 2, ".", " " ).'</td>
							</tr>';
						
			$phpShopOrderSubtotal	+=  $row['product_price'] * intval($aQuantity[$row['product_sku']]);
			//$phpShopOrderTax		+=  ($row['product_price'] * $nTaxTemp )*intval($nQuantityTemp);
			
			
			/* Insert ORDER_PRODUCT_TYPE */
			$query = "SELECT * FROM jos_vm_product_product_type_xref, jos_vm_product_type 
					  WHERE jos_vm_product_product_type_xref.product_id = '".$row['product_id']."' 
					  AND jos_vm_product_product_type_xref.product_type_id = jos_vm_product_type.product_type_id";
                        
			
			 $database->setQuery($query);
                     $database->query(); 
                     $rows2	= $database->loadAssocList();
			foreach ($rows2 as $row2) { 
				$product_type_id = $row2['product_type_id'];
				
				$query = "  SELECT * 
							FROM jos_vm_product_type_$product_type_id 
							WHERE product_id='".$row['product_id']."' ";			
				 $database->setQuery($query);
                                 $database->query(); 
                                $item2 =  $database->loadAssocList();
                                $ItemQuantity=0;
                                if($item2[0]['quantity']){
                                     $ItemQuantity=$item2[0]['quantity'];
                                     
                                }
                                $ItemPrice=0;
	        		 if($item2[0]['price']){
                                     $ItemPrice=$item2[0]['price'];
                                     
                                }
                                $ItemProduct_type_name=' ';
	        		 if($item2[0]['product_type_name']){
                                     $ItemProduct_type_name=$item2[0]['product_type_name']; 
                                     
                                }
				$query = "INSERT INTO jos_vm_order_product_type( order_id, 
															product_id, 
															product_type_name, 
															quantity, price) 															
							VALUES ( {$order_id}, 
									 '".$row['product_id']."', 
									 '" . addslashes($ItemProduct_type_name) . "', 
									 " . $ItemQuantity . ", 
									 ".$ItemPrice. ")";
                        
	           	
                                                       $database->setQuery($query);
                                 $database->query(); 
                        
				//echo "<br/>9-2. <br/>".$database->getErrorMsg()."<br/>";
			}
			
			
			/* Update Stock Level and Product Sales */
			if ( $row['product_in_stock'] ) {
				$query = "	UPDATE jos_vm_product 
							SET product_in_stock = product_in_stock - ".intval($aQuantity[$row['product_sku']])." 
							WHERE product_id = '" . $row['product_id'] . "'";
				 $database->setQuery($query);
                     $database->query(); 
			}
	
			$query = "	UPDATE jos_vm_product 
						SET product_sales= product_sales + ".intval($aQuantity[$row['product_sku']])."  
						WHERE product_id='".$row['product_id'] ."'";
			 $database->setQuery($query);
                     $database->query(); 
			//echo "<br/>9-3. <br/>".$database->getErrorMsg()."<br/>";
			
			
			$j++;
		}
		$order_items	.= '</table>';	
		
		/*===================================== Setup Email =====================================*/
		$aEmail		= array();
		$sendmail 	= new sendMail();
		$sendmail->set(html, true);
		
			
		
		/*===================================== Assign Order To The WareHouse =====================================*/
		$query		= "SELECT WH.warehouse_email, WH.warehouse_code FROM jos_vm_warehouse AS WH, jos_postcode_warehouse AS PWH WHERE WH.warehouse_id = PWH.warehouse_id AND PWH.postal_code = '".substr( $deliver_zip_code, 0, 3 )."'";
                 $database->setQuery($query);
                                 $database->query(); 
                               
                                
		$oWarehouse	= $database->loadAssocList();
		$warehouse_code		= $oWarehouse[0]['warehouse_code'];
		$warehouse_email	= $oWarehouse[0]['warehouse_email'];
		
		$query = "UPDATE jos_vm_orders SET warehouse='" . $warehouse_code . "', mdate='" . $timestamp . "' WHERE order_id='" . $order_id . "'";
		 $database->setQuery($query);
                                 $database->query(); 
                               
			
		if ( $warehouse_code ) {
			$mail_Subject 	= $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY . " of Order ID #".$order_id;
			$mail_Content 	= str_replace('{order_id}', $order_id, $VM_LANG->_PHPSHOP_ORDER_WAREHOUSE_NOTIFY_CONTENT);
			
			
			$aEmail['to']			= $warehouse_email;
			$aEmail['from']			= $mosConfig_mailfrom;
			$aEmail['subject']		= $mail_Subject;
			$aEmail['body']			= $mail_Content;		      
			$sendmail->getParams($aEmail);
			$sendmail->setHeaders();
			$sendmail->send();
		}	
		
			
		$query 	= "SELECT creditcard_name FROM jos_vm_creditcard WHERE creditcard_code = '$payment_method'";
                 $database->setQuery($query);
                                 $database->query(); 
                               
                                
		$payment_info_details	= $database->loadResult();
                
		$payment_info_details  .= '<br />Name On Card: '.$name_on_card.'<br />'
								. 'Credit Card Number: '.$credit_card_number.'<br />'
								. 'Expiration Date: '.$expire_month.' / '.$expire_year.'<br />';
		
		$shopper_header 		= 'Thank you for shopping with us.  Your order information follows.';
		$shopper_order_link 	= $mosConfig_live_site."/index.php?page=account.order_details&order_id=$order_id";					
		$shopper_footer_html 	= "<br /><br />Thank you for your patronage.<br />"
							 	. "<br /><a title=\"View the order by following the link below.\" href=\"$shopper_order_link\">View the order by following the link below.</a>"
								. "<br /><br />Questions? Problems?<br />"
								. "E-mail: <a href=\"mailto:" . $mosConfig_mailfrom."\">".$mosConfig_mailfrom."</a>";
								
		$vendor_header			= "The following order was received.";
		$vendor_order_link 		= $mosConfig_live_site."/index.php?page=order.order_print&order_id=$order_id&pshop_mode=admin" ;
		$vendor_footer_html 	= "<br /><br /><a title=\"View the order by following the link below.\" href=\"$vendor_order_link\">View the order by following the link below.</a>";
		
		$vendor_image = "<img src=\"".$mosConfig_live_site."/components/com_virtuemart/shop_image/vendor/".$aVendor->vendor_full_image."\" alt=\"vendor_image\" border=\"0\" />";
		
		/*===================================== Assign Email Content =====================================*/

    }
	
	return $sResultProcess;
}

class sendMail
{
    var $to;
    var $cc;
    var $bcc;
    var $subject;
    var $from;
    var $headers;
    var $html;

    function sendMail() 
    {
        $this->to       = NULL;
        $this->cc       = NULL;
        $this->bcc      = NULL;
        $this->subject  = NULL;
        $this->from     = NULL;
        $this->headers  = NULL;  
        $this->html     = FALSE;
    }

    function getParams($params) 
    {
        $i = 0;
        foreach ($params as $key => $value) {
            switch($key) {
                case 'to':
                    $this->to       = $value;
                break;
                case 'cc':
                    $this->cc       = $value;
                break;
                case 'bcc':
                    $this->bcc       = $value;
                break;
                case 'subject':
                    $this->subject  = $value;
                break;
                case 'from':
                    $this->from     = $value;
                break;
                case 'submitted':
                    NULL;
                break;
                default:
                    $this->body = $value;
            }
        }
    }

    function setHeaders() 
    {
        $this->headers = "From: $this->from\r\n";
        if($this->html === TRUE) {
            $this->headers.= "MIME-Version: 1.0\r\n";
            $this->headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
        }
        if(!empty($this->cc))  $this->headers.= "Cc: $this->cc\r\n";
        if(!empty($this->bcc)) $this->headers.= "Bcc: $this->bcc\r\n";
    }

    function send() 
    {
        if(mail($this->to, $this->subject, $this->body, $this->headers)) return TRUE;
        else return FALSE;
    }

    function set($key, $value) 
    {
        if($value) $this->$key = $value;
        else unset($this->$key);
    }
}

 function generateRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}
?>


