<?php

$xmlString	= '<?xml version="1.0" encoding="UTF-8"?>
<NABTransactMessage>
<MessageInfo>
<messageID>8af793f9af34bea0cf40f5fb5c630c</messageID>
<messageTimestamp>20041803161306527000+660</messageTimestamp>
<timeoutValue>60</timeoutValue>
<apiVersion>xml-4.2</apiVersion>
</MessageInfo>
<MerchantInfo>
<merchantID>B0010</merchantID>
<password>abcd1234</password>
</MerchantInfo>
<RequestType>Payment</RequestType>
<Payment>
<TxnList count="1">
<Txn ID="1">
<txnType>0</txnType>
<txnSource>0</txnSource>
<amount>1000</amount>
<purchaseOrderNo>test</purchaseOrderNo>
<CreditCardInfo>
<cardNumber>4444333322221111</cardNumber>
<expiryDate>08/12</expiryDate>
</CreditCardInfo>
</Txn>
</TxnList>
</Payment>
</NABTransactMessage>';
							
			$url = "https://transact.nab.com.au/test/xmlapi/payment";
				
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlString");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			if( !empty($output) ) {
				require_once('ParseXml.class.php'); 
				
				$xml = new ParseXml();
				$xml->LoadString($output);
				
				//echo $str;
				$dataArray = $xml->ToArray();
				print_r($dataArray);			
			}
?> 