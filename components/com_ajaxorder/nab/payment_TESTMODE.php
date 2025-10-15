<?php
	function processNABpayment( $aReplace = array() ) {
		//=============== CONFIGURATION ===============//
		$merchantID		= "BB80010";
		$merchantPASS	= "abcd1234";
		$mode			= "0"; // Test = 0, Live = 1
		//============= END CONFIGURATION =============//
		
		
		$dataArray	= array();
		
		if( is_array($aReplace) && count($aReplace) ) {
			$xmlString	= '<?xml version="1.0" encoding="UTF-8"?>
							<NABTransactMessage>
							<MessageInfo>
								<messageID>{messageID}</messageID>
								<messageTimestamp>{messageTimestamp}</messageTimestamp>
								<timeoutValue>60</timeoutValue>
								<apiVersion>xml-4.2</apiVersion>
							</MessageInfo>
							<MerchantInfo>
								<merchantID>'.$merchantID.'</merchantID>
								<password>'.$merchantPASS.'</password>
							</MerchantInfo>
							<RequestType>Payment</RequestType>
							<Payment>
								<TxnList count="1">
								<Txn ID="1">
								<txnType>0</txnType>
								<txnSource>23</txnSource>
								<amount>{amount}</amount>
								<currency>AUD</currency>
								<purchaseOrderNo>{purchaseOrderNo}</purchaseOrderNo>
								<CreditCardInfo>
								<cardNumber>{cardNumber}</cardNumber>
								<expiryDate>{expiryDate}</expiryDate> 
								</CreditCardInfo>
								</Txn>
								</TxnList>
							</Payment>
							</NABTransactMessage>';
							
			$aSearch 	= array("{messageID}", "{messageTimestamp}", "{amount}", "{purchaseOrderNo}", "{cardNumber}", "{expiryDate}");
			$xmlString 	= str_replace($aSearch, $aReplace, $xmlString);		
			
//			print_r($aReplace);
//			echo "<br/><br/><br/>";
			//echo $xmlString;
		
			if( $mode == 0 ) {
				$url = "https://transact.nab.com.au/test/xmlapi/payment";
			}else{
				$url = "https://transact.nab.com.au/live/xmlapi/payment";
			}
				
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlString");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			die($output);
						
			if( !empty($output) ) {
				require_once('ParseXml.class.php'); 
				
				$xml = new ParseXml();
				$xml->LoadString($output);
				
				//echo $str;
				$dataArray = $xml->ToArray();
				//print_r($dataArray);	
				if(!count($dataArray)) {
					$dataArray[0]	= $output;
				}		
			}
		}
		
		return $dataArray;
	}
	
	function genRandomString( $length = 30 ) {
		$characters 	= "0123456789abcdefghijklmnopqrstuvwxyz";
		$string 		= "";    
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return $string;
	}
?> 