<?php 
      include "minixml.inc.php";
            

      //
      //you can configure your details here
      //
	    $GATEWAY_URL = 'https://orbitalvar2.paymentech.net/authorize'; //TEST
      	$GATEWAY_URL = 'https://orbital2.paymentech.net/authorize'; //LIVE
	    
      	$my_card_number ='4012888888881'; 
		$my_order_number = '99999';
		$my_total_amount = '20';
		$my_date = '';
		$my_cc_verify = '111';
		$my_card_name = 'testing name';
		$my_address = 'my address';
		$my_city = 'FLA';
		$my_state = 'FL';
		$my_phone = '123412';
		$my_zip = '99880';
		$my_card_exp_month ="08";
		$my_card_exp_year="08";
		define ('PMT_MERCHANT_ID', '800000075863');
		define ('PMT_BIN', '000002');
		define ('PMT_TERMINAL_ID', '001');
		define ('PMT_INDUSTRYTYPE', 'EC');
		define ('PMT_MESSAGETYPE', 'AC');
		
		
		define ('PMT_CURRENCYCODE', '840');
		define ('PMT_VERIFIED_STATUS', 'P');
		define ('PMT_INVALID_STATUS', 'P');
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', TRUE);
		
		
		//
		//Try not to edit anything below here
		//


	   function strip_specialchars($string){
	   $string=str_replace('>', '', $string);
	   $string=str_replace('<', '', $string);
	   $string=str_replace("\'", '', $string);
	   $string=str_replace("'", '', $string);
	   $string=str_replace('\"', '', $string);
	   $string=str_replace('"', '', $string);	
	   $string=str_replace('&', '', $string);
	   $string=str_replace('`', '', $string);
	   $string=str_replace('.', '', $string);
	   $string=str_replace(',', '', $string);
	   $string=str_replace('!', '', $string);
	   $string=str_replace('/', '', $string);
	   $string=str_replace('#', '', $string);	
	   return $string;
		}

	  $xmlDoc  = new MiniXMLDoc();
      $xmlRoot =& $xmlDoc->getRoot();
  
    $Request =& $xmlRoot->createChild('Request');
	$NewOrder =& $Request->createChild('NewOrder');
	$IndustryType =& $NewOrder->createChild('IndustryType');
	$IndustryType->text(PMT_INDUSTRYTYPE); 
	$MessageType =& $NewOrder->createChild('MessageType');
	$MessageType->text(PMT_MESSAGETYPE);
	$BIN =& $NewOrder->createChild('BIN');
	$BIN->text(PMT_BIN);
	$MerchantID =& $NewOrder->createChild('MerchantID');
	$MerchantID->text(PMT_MERCHANT_ID);
	$TerminalID =& $NewOrder->createChild('TerminalID');
	$TerminalID->text(PMT_TERMINAL_ID);
	$CardBrand =& $NewOrder->createChild('CardBrand');
	$AccountNum =& $NewOrder->createChild('AccountNum');
	$AccountNum->text($my_card_number);
	$Exp =& $NewOrder->createChild('Exp');
	$Exp->text($my_card_exp_month.$my_card_exp_year);
	$CurrencyCode =& $NewOrder->createChild('CurrencyCode'); 
	$CurrencyCode->text(PMT_CURRENCYCODE);
	$CurrencyExponent =& $NewOrder->createChild('CurrencyExponent');
	$CurrencyExponent->text('2');
	$CardSecValInd =& $NewOrder->createChild('CardSecValInd');
	$CardSecValInd->text('1');
	$CardSecVal =& $NewOrder->createChild('CardSecVal');
	$CardSecVal->text($my_cc_verify);
	$DebitCardIssueNum =& $NewOrder->createChild('DebitCardIssueNum');
	$DebitCardStartDate =& $NewOrder->createChild('DebitCardStartDate');
	$BCRtNum =& $NewOrder->createChild('BCRtNum');
	$CheckDDA =& $NewOrder->createChild('CheckDDA');
	$BankAccountType =& $NewOrder->createChild('BankAccountType');
	$ECPAuthMethod =& $NewOrder->createChild('ECPAuthMethod');
	$BankPmtDelv =& $NewOrder->createChild('BankPmtDelv');
	$AVSzip =& $NewOrder->createChild('AVSzip');
	$AVSzip->text($my_zip);
	$AVSaddress1 =& $NewOrder->createChild('AVSaddress1');
	$AVSaddress1->text(htmlentities(strip_specialchars($my_address)));
	$AVSaddress2 =& $NewOrder->createChild('AVSaddress2');
	$AVScity =& $NewOrder->createChild('AVScity');
	$AVScity->text(htmlentities(strip_specialchars($my_city)));
	$AVSstate =& $NewOrder->createChild('AVSstate');
	$AVSstate->text(htmlentities(strip_specialchars($my_state)));
	$AVSphoneNum =& $NewOrder->createChild('AVSphoneNum');
	$AVSphoneNum->text(htmlentities(strip_specialchars($my_phone)));
	$AVSname =& $NewOrder->createChild('AVSname');
	$AVSname->text($my_card_name);
	$AVScountryCode =& $NewOrder->createChild('AVScountryCode');
	$CustomerProfileFromOrderInd =& $NewOrder->createChild('CustomerProfileFromOrderInd');
	$CustomerProfileFromOrderInd->text('EMPTY');
	$CustomerRefNum =& $NewOrder->createChild('CustomerRefNum');
	$CustomerProfileOrderOverrideInd =& $NewOrder->createChild('CustomerProfileOrderOverrideInd');
	$AuthenticationECIInd =& $NewOrder->createChild('AuthenticationECIInd');
	$CAVV =& $NewOrder->createChild('CAVV');
	$XID =& $NewOrder->createChild('XID');
	$OrderID =& $NewOrder->createChild('OrderID');
	$OrderID->text($my_order_number);
	$Amount =& $NewOrder->createChild('Amount');
	$Amount->text($my_total_amount);
	$Comments =& $NewOrder->createChild('Comments');
	$ShippingRef =& $NewOrder->createChild('ShippingRef');
	$TaxInd =& $NewOrder->createChild('TaxInd');
	$Tax =& $NewOrder->createChild('Tax');
	$AMEXTranAdvAddn1 =& $NewOrder->createChild('AMEXTranAdvAddn1');
	$AMEXTranAdvAddn2 =& $NewOrder->createChild('AMEXTranAdvAddn2');
	$AMEXTranAdvAddn3 =& $NewOrder->createChild('AMEXTranAdvAddn3');
	$AMEXTranAdvAddn4 =& $NewOrder->createChild('AMEXTranAdvAddn4');
	$AAV =& $NewOrder->createChild('AAV');
	$SDMerchantName =& $NewOrder->createChild('SDMerchantName');
	$SDProductDescription =& $NewOrder->createChild('SDProductDescription');
	$SDMerchantCity =& $NewOrder->createChild('SDMerchantCity');
	$SDMerchantPhone =& $NewOrder->createChild('SDMerchantPhone');
	$SDMerchantURL =& $NewOrder->createChild('SDMerchantURL');
	$SDMerchantEmail =& $NewOrder->createChild('SDMerchantEmail');
	$RecurringInd =& $NewOrder->createChild('RecurringInd');
	$EUDDCountryCode =& $NewOrder->createChild('EUDDCountryCode');
	$EUDDBankSortCode =& $NewOrder->createChild('EUDDBankSortCode');
	$EUDDRibCode =& $NewOrder->createChild('EUDDRibCode');
	$PCOrderNum =& $NewOrder->createChild('PCOrderNum');
	$PCDestZip =& $NewOrder->createChild('PCDestZip');
	$PCDestName =& $NewOrder->createChild('PCDestName');
	$PCDestAddress1 =& $NewOrder->createChild('PCDestAddress1');
	$PCDestAddress2 =& $NewOrder->createChild('PCDestAddress2');
	$PCDestCity =& $NewOrder->createChild('PCDestCity');
	$PCDestState =& $NewOrder->createChild('PCDestState');
    $haystack = $xmlDoc->toString();
      

          
      
      $ch = curl_init();
      $haystack = str_replace("> ", ">", "$haystack");
	  $haystack = str_replace(" <", "<", "$haystack");
	  $haystack = str_replace(" />", "/>", "$haystack");  
	  $xmlRequest = $haystack;

	
	  
	  
      echo $xmlRequest;
      
      if(!curl_setopt($ch, CURLOPT_URL, $GATEWAY_URL)) {echo 'CURLOPT URL Error<p>';}
      if(!curl_setopt($ch, CURLOPT_HEADER, 1)) {echo 'CURLOPT Header Error<p>';}
      if(!curl_setopt($ch, CURLOPT_HTTPHEADER, array('POST /AUTHORIZE HTTP/1.0', 'MIME-Version: 1.0', 'Content-type: application/PTI43','Content-transfer-encoding: text', 'Request-number: '.$my_order_number.'', 'Document-type: Request','Merchant-id: '.PMT_MERCHANT_ID,'Trace-number: '.$my_order_number,'Interface-Version: testing code php'))) {echo 'CURLOPT HTTPHEADER Error<p>';}
      if(!curl_setopt($ch, CURLOPT_POST, 1)) {echo 'CURLOPT POST Error<p>';}
      if(!curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest)) {echo 'CURLOPT POSTFIELDS Error<p>';}
      if(!curl_setopt($ch, CURLOPT_TIMEOUT, 90)) {echo 'CURLOPT TIMEOUT Error<p>';} 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // gives error, but keeps xml formatting
	  
	  
	  
      
      $result=curl_exec ($ch);
      curl_close ($ch);
      
      
     
        if($result)
	    {
		    echo "\n\n\n$result\n\n\n\n\n\n\n";
		    
	    }
	    else
	    {
		    echo "<br><br><br><b>no result</b><br>";
	    }
	    
	    
   
      $needle = '<Response>';
      $pos = strpos($result, $needle);
      $ReqLen = strlen($result)-$pos;
	  $resultclean = substr($result, $pos, $ReqLen);   
	  
	  $returnedXMLDoc = new MiniXMLDoc();
      $returnedXMLDoc->fromString($resultclean);
	   
	
		echo "<b>";
		$statusCode = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/ProcStatus');
		print "<br>Status Code is " . $statusCode->getValue();
		$RespCode= $returnedXMLDoc->getElementByPath('Response/NewOrderResp/RespCode');
		print "<br>RespCode is " . $RespCode->getValue();
		$StatusMsg= $returnedXMLDoc->getElementByPath('Response/NewOrderResp/StatusMsg');
		print "<br>StatusMsg is " . $StatusMsg->getValue();
		$CVV2RespCode= $returnedXMLDoc->getElementByPath('Response/NewOrderResp/CVV2RespCode');
		print "<br>CVV2RespCode is " . $CVV2RespCode->getValue();
		$AVSRespCode= $returnedXMLDoc->getElementByPath('Response/NewOrderResp/AVSRespCode');
		print "<br>AVSRespCode is " . $AVSRespCode->getValue();
		$txRefNum= $returnedXMLDoc->getElementByPath('Response/NewOrderResp/txRefNum');
		print "<br>txRefNum is " . $txRefNum->getValue();
		echo "</b>";
	
      
         

?>

