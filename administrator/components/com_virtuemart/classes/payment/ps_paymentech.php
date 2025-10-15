<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: ps_paymentech.php V1.0 2008-03-01
* @package VirtueMart
* @subpackage Paymentech
* @author Aloysius Foo <aloysiusf@gmail.com>
* @copyright Copyright (C) 2008 Aloysius Foo. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/


/**
* This class let's you handle transactions with the Paymentech Payment Gateway
*
*/
class ps_paymentech {

    var $classname = "ps_paymentech";
    var $payment_code = "PMT";
    
    
    
    /**
    * Show all configuration parameters for this payment method
    * @returns boolean False when the Payment method has no configration
    */
    function show_configuration() {
        global $VM_LANG;
        $db = new ps_DB();
        
        /** Read current Configuration ***/
        include_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
    ?>
    <table>
    
    	<tr>
            <td><strong>Transaction Mode</strong></td>
            <td>
                <select name="PMT_MODE" class="inputbox" >
                <option <?php if (PMT_MODE == 'Test') echo "selected=\"selected\""; ?> value="Test">Test</option>
                <option <?php if (PMT_MODE == 'Production') echo "selected=\"selected\""; ?> value="Production">Production(LIVE)</option>
                </select>
            </td>
            <td>Selected mode must have appropriate Merchant ID populated.
            </td>
        </tr>
        <tr>
        <td><strong>Test Merchant ID</strong></td>
            <td>
                <input type="text" name="PMT_TEST_MERCHANT_ID" class="inputbox" value="<?php  echo PMT_TEST_MERCHANT_ID ?>" />
            </td>
            <td>The Merchant ID you received from Paymentech.
            </td>
        </tr>
        <tr>
        <td><strong>Live Merchant ID</strong></td>
            <td>
                <input type="text" name="PMT_LIVE_MERCHANT_ID" class="inputbox" value="<?php  echo PMT_LIVE_MERCHANT_ID ?>" />
            </td>
            <td>Live production environment. Usually acquired after declaration of certification.
            </td>
        </tr>
        <tr>
            <td><strong>Paymentech BIN</strong></td>
            <td>
                <select name="PMT_BIN" class="inputbox" >
                <option <?php if (PMT_BIN == '000001') echo "selected=\"selected\""; ?> value="000001">Salem (default)</option>
                <option <?php if (PMT_BIN == '000002') echo "selected=\"selected\""; ?> value="000002">Tampa</option>
                </select>
            </td>
            <td>000001 = Salem if Merchant ID has 6 integer, 000002 = Tampa(default)  if Merchant ID has 12 integer.
            </td>
        </tr>
        <tr>
            <td><strong>Paymentech Trace ID</strong></td>
            <td>
                <input type="text" name="PMT_TERMINAL_ID" class="inputbox" value="<?php  echo PMT_TERMINAL_ID ?>" />
            </td>
            <td>Please check the Terminal ID once you logged in to Paymentech. 001 (default)
            </td>
        </tr>
        <tr>
            <td><strong>Industry Type Request</strong></td>
            <td>
                <select name="PMT_INDUSTRYTYPE" class="inputbox" >
                <option <?php if (PMT_INDUSTRYTYPE == 'EC') echo "selected=\"selected\""; ?> value="EC">EC</option>
                <option <?php if (PMT_INDUSTRYTYPE == 'MO') echo "selected=\"selected\""; ?> value="MO">MO</option>
                </select>
            </td>
            <td>EC(default)
            </td>
        </tr>
        <tr>
            <td><strong>Message Type Request</strong></td>
            <td>
                <select name="PMT_MESSAGETYPE" class="inputbox" >
                <option <?php if (PMT_MESSAGETYPE == 'A') echo "selected=\"selected\""; ?> value="A">A</option>
                <option <?php if (PMT_MESSAGETYPE == 'AC') echo "selected=\"selected\""; ?> value="AC">AC</option>
                </select>
            </td>
            <td>AC(default)
            </td>
        </tr>
        
        
        <tr>
            <td><strong>Currency Code</strong></td>
            <td>
                <input type="text" name="PMT_CURRENCYCODE" class="inputbox" value="<?php  echo PMT_CURRENCYCODE ?>" />
            </td>
            <td>840=US Dollar, 124=Canadian 
            </td>
        </tr>
        
      
        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="PMT_VERIFIED_STATUS" class="inputbox" >
                <?php
                    $q = "SELECT order_status_name,order_status_code FROM #__{vm}_order_status ORDER BY list_order";
                    $db->query($q);
                    $order_status_code = Array();
                    $order_status_name = Array();
                    
                    while ($db->next_record()) {
                      $order_status_code[] = $db->f("order_status_code");
                      $order_status_name[] =  $db->f("order_status_name");
                    }
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PMT_VERIFIED_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select an order status for Successful PMT transactions.</td>
        </tr>
            <tr>
            <td><strong>Order Status for failed transactions</strong></td>
            <td>
                <select name="PMT_INVALID_STATUS" class="inputbox" >
                <?php
                    for ($i = 0; $i < sizeof($order_status_code); $i++) {
                      echo "<option value=\"" . $order_status_code[$i];
                      if (PMT_INVALID_STATUS == $order_status_code[$i]) 
                         echo "\" selected=\"selected\">";
                      else
                         echo "\">";
                      echo $order_status_name[$i] . "</option>\n";
                    } ?>
                    </select>
            </td>
            <td>Select an order status for failed PMT transactions.</td>
        </tr>
        
      </table>
    <?php
    }
    
    function has_configuration() {
      // return false if there's no configuration
      return true;
   }
   
  /**
	* Returns the "is_writeable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_writeable() {
      return is_writeable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }
   
  /**
	* Returns the "is_readable" status of the configuration file
	* @param void
	* @returns boolean True when the configuration file is writeable, false when not
	*/
   function configfile_readable() {
      return is_readable( CLASSPATH."payment/".$this->classname.".cfg.php" );
   }
   
  /**
	* Writes the configuration file for this payment method
	* @param array An array of objects
	* @returns boolean True when writing was successful
	*/
   function write_configuration( &$d ) {
      
      $my_config_array = array("PMT_MODE" => $d['PMT_MODE'],
      						   "PMT_TEST_MERCHANT_ID" => $d['PMT_TEST_MERCHANT_ID'],
      						   "PMT_LIVE_MERCHANT_ID" => $d['PMT_LIVE_MERCHANT_ID'],
      						   "PMT_BIN" => $d['PMT_BIN'],
      						   "PMT_TERMINAL_ID" => $d['PMT_TERMINAL_ID'],
      						   "PMT_INDUSTRYTYPE" => $d['PMT_INDUSTRYTYPE'],
      						   "PMT_MESSAGETYPE" => $d['PMT_MESSAGETYPE'],
      						   "PMT_CURRENCYCODE" => $d['PMT_CURRENCYCODE'],
                               "PMT_VERIFIED_STATUS" => $d['PMT_VERIFIED_STATUS'],
                               "PMT_INVALID_STATUS" => $d['PMT_INVALID_STATUS']
                                              
                                      );
      $config = "<?php\n";
      $config .= "defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); \n\n";
      foreach( $my_config_array as $key => $value ) {
        $config .= "define ('$key', '$value');\n";
      }
      
      $config .= "?>";
  
      if ($fp = fopen(CLASSPATH ."payment/".$this->classname.".cfg.php", "w")) {
          fputs($fp, $config, strlen($config));
          fclose ($fp);
          return true;
     }
     else
        return false;
   }
   
   
  /**************************************************************************
  ** name: process_payment()
  ** returns: 
  ***************************************************************************/
    function process_payment($order_number, $order_total, &$d) {
        global $vendor_name, $VM_LANG, $vmLogger;
        $auth = $_SESSION['auth'];
        
        // Get the Configuration File for PMT
        require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
        
        // PMT GetPMT Location (URL) 
        if( PMT_MODE == "Test" ){
            define( "PMT_URL", "https://orbitalvar1.paymentech.net"); //Test
            define( "PMT_MERCHANT_ID", PMT_TEST_MERCHANT_ID);
        }
        else{
            define( "PMT_URL", "https://orbital1.paymentech.net");  //Production
            define( "PMT_MERCHANT_ID", PMT_LIVE_MERCHANT_ID);
        }
            
        //Get user billing information
        $dbbt = new ps_DB;
        $qt = "SELECT * FROM `#__{vm}_user_info` WHERE user_id='".$auth["user_id"]."' AND address_type='BT'"; //Billing
        $dbbt->query($qt);
        $dbbt->next_record();
        $user_info_id = $dbbt->f("user_info_id");
        if( $user_info_id != $d["ship_to_info_id"]) {
            $dbst =new  ps_DB;
            $qt = "SELECT * FROM #__{vm}_user_info WHERE user_info_id='".$d["ship_to_info_id"]."' AND address_type='ST'"; //Shipping
            $dbst->query($qt);
            $dbst->next_record();
        }
        else {
            $dbst = $dbbt;
        }
        
        //Get the next Order Number
        $dbord = new ps_DB;
        $qord = "SELECT MAX(order_id)+1 As expected_order_number FROM #__{vm}_orders";
        $dbord->query($qord);
        $dbord->next_record();
        $expected_order_number = sprintf("%08d", $dbord->f("expected_order_number"));
        
        //Convert $order_total in cents!
        $order_total = $order_total * 100;
        
      	
		//Create object
        $PMT = new PMTPayment( PMT_MERCHANT_ID, PMT_URL );
        
        //Configure the object
        $PMT->setCardName( $_SESSION['ccdata']['order_payment_name']);
		$PMT->setCardNumber( $_SESSION['ccdata']['order_payment_number'] );
		$PMT->setCCVerify( $_SESSION['ccdata']['credit_card_code'] );
		$PMT->setCardExpYear(substr( $_SESSION['ccdata']['order_payment_expire_year'], 2, 2 ));
		$PMT->setCardExpMonth( $_SESSION['ccdata']['order_payment_expire_month'] );
		$PMT->setTotalAmount( $order_total );
		$PMT->setOrderNumber( $expected_order_number );
		$PMT->setAddress1 ($dbst->f("address_1") );
		$PMT->setAddress2 ($dbst->f("address_2") );
		$PMT->setCity( $dbst->f("city") );
		$PMT->setState( $dbst->f("state") );
		$PMT->setZip( $dbst->f("zip") );
		$PMT->setPhone( $dbst->f("phone_1") );
		
	   
        if( $PMT->doPayment() == true ) {
			
			$d["order_payment_log"] = $VM_LANG->_PHPSHOP_PAYMENT_TRANSACTION_SUCCESS;
            //Catch Transaction ID
            $d["order_payment_trans_id"] = $PMT->getTrxnNumber();
            //$d["error"] = "";
            return true;
		} 
        else {
			$vmLogger->err( $VM_LANG->_PHPSHOP_PAYMENT_ERROR.": ".$PMT->getErrorMessage() );
            //Catch Transaction ID
            $d["order_payment_trans_id"] = $PMT->getTrxnNumber();
            return false;
		}
		
    }
   
}





class PMTPayment{
		
	var $my_merchantID;
	var $my_paymentURL;
	
	var $my_card_number;
	var $my_cc_verify;
	var $my_card_exp_year;
	var $my_card_exp_month;
	
	var $my_total_amount;
	var $my_order_number;
	
	var $my_card_name;
	var $my_address;
	var $my_city;
	var $my_state;
	var $my_zip;
	var $my_phone;
	
	var $my_AppError;
    var $my_ProcError;
    var $my_ErrorMessage;
    
    var $my_ResultTrxnNumber;
    
    
 
	
	/***********************************************************************
     *** Class Constructor                                               ***
     ***********************************************************************/
    function PMTPayment( $merchantID = PMT_MERCHANT_ID, $payment_url = PMT_URL ) {
        $this->my_merchantID = $merchantID;
        $this->my_paymentURL = $payment_url;
    }
    
     /***********************************************************************
     *** SET values to send to PMT                                      ***
     ***********************************************************************/
     function setMerchantID( $my_merchantID ) {
        $this->my_merchantID = $my_merchantID;
    }
     function setPaymentURL( $payment_url ) {
        $this->my_paymentURL = $payment_url;
    }
     function setCardNumber( $card_number ) {
        $this->my_card_number = $card_number;
    }
     function setCCVerify( $cc_verify ) {
        $this->my_cc_verify = $cc_verify;
    }
     function setCardExpYear( $card_exp_year ) {
        $this->my_card_exp_year = $card_exp_year;
    }
     function setCardExpMonth( $card_exp_month ) {
        $this->my_card_exp_month = $card_exp_month;
    }
     function setTotalAmount( $total_amount ) {
        $this->my_total_amount = $total_amount;
    }
     function setOrderNumber( $order_number ) {
        $this->my_order_number = $order_number;
    }
    
     function setCardName( $card_name ) {
        $this->my_card_name = $card_name;
    }
     function setAddress1( $address1 ) {
        $this->my_address1 = $address1;
    }
    function setAddress2( $address2 ) {
        $this->my_address2 = $address2;
    }
     function setCity( $city) {
        $this->my_city = $city;
    }
     function setState( $state ) {
        $this->my_state = $state;
    }
     function setZip( $zip ) {
        $this->my_zip = $zip;
    }
    function setPhone( $phone ) {
        $this->my_phone = $phone;
    }
    
    
    function setErrorMessage( $errormessage) {
        $this->my_ErrorMessage = $errormessage;
    }
    function setAppError($error) {
        $this->my_AppError = $error;
    }
    function setProcError($error) {
        $this->my_ProcError = $error;
    }
    
    
    
      
    function getTrxnNumber() {
        return $this->my_ResultTrxnNumber;
    }
    
 
    
    
     function getError()
    {
	      
        if( $this->my_ProcError == 0) //Connection Success
        {
	        if($this->my_AppError == 1){
		    return true; //Approval Success
	        }
	        else{
		    return false;
	        }
        }else{
	        return false;
        }
    }

    function getErrorMessage()
    {
	   return $this->my_ErrorMessage;

    }
    
    /***********************************************************************
     *** Remove Special Characters                                       ***
     ***********************************************************************/
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
    
    
    /***********************************************************************
     *** Business Logic                                                  ***
     ***********************************************************************/
    function doPayment() {
	    
	    include_once(CLASSPATH ."minixml/minixml.inc.php");
	    
	    //Generating the XML 
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
		$AccountNum->text($this->my_card_number);
		$Exp =& $NewOrder->createChild('Exp');
		$Exp->text(htmlentities($this->my_card_exp_month).htmlentities($this->my_card_exp_year));
		$CurrencyCode =& $NewOrder->createChild('CurrencyCode'); 
		$CurrencyCode->text(PMT_CURRENCYCODE);
		$CurrencyExponent =& $NewOrder->createChild('CurrencyExponent');
		$CurrencyExponent->text('2');
		$CardSecValInd =& $NewOrder->createChild('CardSecValInd');
		$CardSecVal =& $NewOrder->createChild('CardSecVal');
		$CardSecVal->text($this->my_cc_verify);
		$DebitCardIssueNum =& $NewOrder->createChild('DebitCardIssueNum');
		$DebitCardStartDate =& $NewOrder->createChild('DebitCardStartDate');
		$BCRtNum =& $NewOrder->createChild('BCRtNum');
		$CheckDDA =& $NewOrder->createChild('CheckDDA');
		$BankAccountType =& $NewOrder->createChild('BankAccountType');
		$ECPAuthMethod =& $NewOrder->createChild('ECPAuthMethod');
		$BankPmtDelv =& $NewOrder->createChild('BankPmtDelv');
		$AVSzip =& $NewOrder->createChild('AVSzip');
		$AVSzip->text(htmlentities($this->strip_specialchars($this->my_zip)));
		$AVSaddress1 =& $NewOrder->createChild('AVSaddress1');
		$AVSaddress1->text(htmlentities($this->strip_specialchars($this->my_address1)));
		$AVSaddress2 =& $NewOrder->createChild('AVSaddress2');
		$AVSaddress2->text(htmlentities($this->strip_specialchars($this->my_address2)));
		$AVScity =& $NewOrder->createChild('AVScity');
		$AVScity->text(htmlentities($this->strip_specialchars($this->my_city)));
		$AVSstate =& $NewOrder->createChild('AVSstate');
		$AVSstate->text(htmlentities($this->strip_specialchars($this->my_state)));
		$AVSphoneNum =& $NewOrder->createChild('AVSphoneNum');
		$AVSphoneNum->text(htmlentities($this->strip_specialchars($this->my_phone)));
		$AVSname =& $NewOrder->createChild('AVSname');
		$AVSname->text($this->my_card_name);
		$AVScountryCode =& $NewOrder->createChild('AVScountryCode');
		$CustomerProfileFromOrderInd =& $NewOrder->createChild('CustomerProfileFromOrderInd');
		$CustomerProfileFromOrderInd->text('EMPTY');
		$CustomerRefNum =& $NewOrder->createChild('CustomerRefNum');
		$CustomerProfileOrderOverrideInd =& $NewOrder->createChild('CustomerProfileOrderOverrideInd');
		$AuthenticationECIInd =& $NewOrder->createChild('AuthenticationECIInd');
		$CAVV =& $NewOrder->createChild('CAVV');
		$XID =& $NewOrder->createChild('XID');
		$OrderID =& $NewOrder->createChild('OrderID');
		$OrderID->text($this->my_order_number);
		$Amount =& $NewOrder->createChild('Amount');
		$Amount->text($this->my_total_amount);
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
  					
  		//To remove spaces in the XML
	    $haystack = str_replace("> ", ">", "$haystack");
	  	$haystack = str_replace(" <", "<", "$haystack");
	  	$haystack = str_replace(" />", "/>", "$haystack");  
	  	$xmlRequest = $haystack;
	  	
	  		  	     
       	//Ready to sent the XML to server
  		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_VERBOSE, 1); // comment once in production
  		if(!curl_setopt($ch, CURLOPT_URL, $this->my_paymentURL)) {echo 'CURLOPT URL Error<p>';}
        if(!curl_setopt($ch, CURLOPT_HEADER, 1)) {echo 'CURLOPT Header Error<p>';}
        if(!curl_setopt($ch, CURLOPT_HTTPHEADER, array('POST /AUTHORIZE HTTP/1.0', 'MIME-Version: 1.0', 'Content-type: application/PTI42','Content-transfer-encoding: text', 'Request-number: 1', 'Document-type: Request','Merchant-id: '.PMT_MERCHANT_ID,'Trace-number: '.$this->my_order_number,'Interface-Version: 1.0'))) {echo 'CURLOPT HTTPHEADER Error<p>';}
        if(!curl_setopt($ch, CURLOPT_POST, 1)) {echo 'CURLOPT POST Error<p>';}
        if(!curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest)) {echo 'CURLOPT POSTFIELDS Error<p>';}
        if(!curl_setopt($ch, CURLOPT_TIMEOUT, 90)) {echo 'CURLOPT TIMEOUT Error<p>';} 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // gives error, but keeps xml formatting
		$result=curl_exec ($ch);
  		curl_close ($ch); 
  		
  	
  		
  		
  		
  		//To Remove MIME headers
  		$needle = '<Response>';
        $pos = strpos($result, $needle);
        $ReqLen = strlen($result)-$pos;
	    $result = substr($result, $pos, $ReqLen);   
	  
	  
	    //To read the Response XML
  		$returnedXMLDoc = new MiniXMLDoc();
  		$returnedXMLDoc->fromString($result);
		$procstatus = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/ProcStatus');
		$sorry = '<br>Sorry for this inconvenience. You may callus for further assistance.';
		$this->my_ProcError = $procstatus ->getValue();
		
		if ($procstatus->getValue() === '0') 
		{
		    $response = '';
		    $approvalstatus = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/ApprovalStatus');
		    $txRefNum = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/txRefNum');
			$CVVrespcode = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/CVV2RespCode');
			$AVSrespcode = $returnedXMLDoc->getElementByPath('Response/NewOrderResp/AVSRespCode');
	   		if ($approvalstatus->getValue() === '1') 
	   		{
			//  Success!
			$this->my_AppError = $approvalstatus->getValue();
			$this->my_ResultTrxnNumber = $txRefNum->getValue();
			} 
			elseif ($approvalstatus->getValue() === '0') 
			{
			  	if (($CVVrespcode->getValue()!='N')&&($CVVrespcode->getValue()!='I')&&($CVVrespcode->getValue()!='Y')&&($AVSrespcode->getValue()!='2')&&($AVSrespcode->getValue()!='G'))
			  	{ 
			  	$response='<br>Possible that card issuer does not participate with address OR card verification.'.$sorry;
		  	    }
 			    if ($AVSrespcode->getValue()=='6') 
 			    {
 			    $response='<br>System unavailable or timed-out.'.$sorry;
			    }
			    $this->my_ErrorMessage = "Credit Card Declined :".$AVSrespcode->getValue().$CVVrespcode->getValue().$response;
		        $this->my_AppError = $approvalstatus->getValue();
		        $this->my_ResultTrxnNumber = $txRefNum->getValue();
			} 
			elseif ($approvalstatus->getValue() === '2') 
			{
			    $this->my_ErrorMessage = "Server Error :".$sorry;
		        $this->my_AppError = $approvalstatus->getValue();
	   		}
	    } 
	    else 
	    {
	   	   $this->my_ErrorMessage = 'System Error '.$procstatus->getValue().$sorry;
    	}
        curl_close ($ch); 
       
              
        return $this->getError();
	    
    }
	
}
















