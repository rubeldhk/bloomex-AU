<?php defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); ?>
<?php 

// error_reporting(E_ALL &~ E_NOTICE);
// ini_set("display_errors", 1);
// ini_set("allow_call_time_pass_reference", true);

// require_once(CLASSPATH ."payment/ps_moneris.helper.php");

class ps_moneris
{

	var $payment_code = "MN";
	var $classname = "ps_moneris";

	function show_configuration()
	{
	
		global $VM_LANG, $sess;
		$db =new  ps_DB;
		$payment_method_id = mosGetParam( $_REQUEST, 'payment_method_id', null );

		/** Read current Configuration ***/
		require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
		
    ?>
      <table align="left">
        <tr>
            <td><strong>Test Mode<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_MONERIS_TESTMODE ?></strong></td>
            <td>
                <select name="MN_TEST_REQUEST" class="inputbox" >
                <option <?php if (MN_TEST_REQUEST == 'TRUE') echo "selected=\"selected\""; ?> value="TRUE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (MN_TEST_REQUEST == 'FALSE') echo "selected=\"selected\""; ?> value="FALSE"><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_ENABLE_MONERIS_TESTMODE_EXPLAIN ?>            </td>
        </tr>
        <tr>
          <td><strong>STORE ID:<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_STOREID ?></strong></td>
          <td><input type="text" name="MN_STOREID" class="inputbox" size="40" value="<?php echo MN_STOREID ?>" />          </td>
          <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_STOREID_EXPLAIN ?> </td>
        </tr>
		<tr>
          <td><strong>API TOKEN:<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_APITOKEN ?></strong></td>
          <td><input type="text" name="MN_APITOKEN" class="inputbox" size="40" value="<?php echo MN_APITOKEN ?>" />          </td>
          <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_APITOKEN_EXPLAIN ?> </td>
        </tr>
        <tr>
          <td><strong>TEST STORE ID:<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_TEST_STOREID ?></strong></td>
          <td><input type="text" name="MN_TEST_STOREID" class="inputbox" size="40" value="<?php echo MN_TEST_STOREID ?>" />          </td>
          <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_TEST_STOREID_EXPLAIN ?> </td>
        </tr>
		<tr>
          <td><strong>TEST API TOKEN:<?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_TEST_APITOKEN ?></strong></td>
          <td><input type="text" name="MN_TEST_APITOKEN" class="inputbox" size="40" value="<?php echo MN_TEST_APITOKEN ?>" />          </td>
          <td><?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_MONERIS_TEST_APITOKEN_EXPLAIN ?> </td>
        </tr>
		<tr>
            <td><strong><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2 ?></strong></td>
            <td>
                <select name="MN_CHECK_CARD_CODE" class="inputbox">
                <option <?php if (MN_CHECK_CARD_CODE == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (MN_CHECK_CARD_CODE == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_CVV2_TOOLTIP ?></td>
        </tr>
		<tr>
            <td><strong>Use Address Verification Service (AVS)</strong></td>
            <td>
                <select name="MN_USE_AVS_CHECK" class="inputbox">
                <option <?php if (MN_USE_AVS_CHECK == 'YES') echo "selected=\"selected\""; ?> value="YES">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_YES ?></option>
                <option <?php if (MN_USE_AVS_CHECK == 'NO') echo "selected=\"selected\""; ?> value="NO">
                <?php echo $VM_LANG->_PHPSHOP_ADMIN_CFG_NO ?></option>
                </select>
            </td>
            <td><?php echo $VM_LANG->_PHPSHOP_PAYMENT_AVS_TOOLTIP ?></td>
        </tr>
		        <tr>
            <td><strong>Order Status for successful transactions</strong></td>
            <td>
                <select name="MN_VERIFIED_STATUS" class="inputbox" >
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
                	if (MN_VERIFIED_STATUS == $order_status_code[$i])
                	echo "\" selected=\"selected\">";
                	else
                	echo "\">";
                	echo $order_status_name[$i] . "</option>\n";
                    }?>
                    </select>
            </td>
            <td>Select the order status to which the actual order is set, if the authorize.net Transaction was successful. 
            If using download selling options: select the status which enables the download (then the customer is instantly notified about the download via e-mail).
            </td>
        </tr>
        <tr><td colspan="3"><hr/></td></tr>
      </table>
   <?php
   // return false if there's no configuration
   return true;
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

		$my_config_array = array("MN_TEST_REQUEST" => $d['MN_TEST_REQUEST'],
		"MN_STOREID" => $d['MN_STOREID'],
		"MN_APITOKEN" => $d['MN_APITOKEN'],
		"MN_TEST_STOREID" => $d['MN_TEST_STOREID'],
		"MN_TEST_APITOKEN" => $d['MN_TEST_APITOKEN'],
		"MN_CHECK_CARD_CODE" => $d['MN_CHECK_CARD_CODE'],
		"MN_USE_AVS_CHECK" => $d['MN_USE_AVS_CHECK'],
		"MN_VERIFIED_STATUS" => $d['MN_VERIFIED_STATUS']
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
                else {
                        return false;
                }
        }

        /**************************************************************************
        ** name: process_payment()
	** created by: Soeren
	** description: process transaction with authorize.net
	** parameters: $order_number, the number of the order, we're processing here
	**            $order_total, the total $ of the order
	** returns:
	***************************************************************************/
	function process_payment($order_number, $order_total, &$d) {
	
		global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
		$database = new ps_DB;
		$ps_vendor_id = $_SESSION["ps_vendor_id"];
		$auth = $_SESSION['auth'];
		$ps_checkout = new ps_checkout;

		/*** Get the Configuration File for authorize.net ***/
		require_once(CLASSPATH ."payment/".$this->classname.".cfg.php");
		
		// Get user billing information
		$dbbt = new ps_DB;

		$qt = "SELECT * FROM #__{vm}_user_info WHERE user_id=".$auth["user_id"]." AND address_type='BT'";

		$dbbt->query($qt);
		$dbbt->next_record();
		$user_info_id = $dbbt->f("user_info_id");
		if( $user_info_id != $d["ship_to_info_id"]) {
			// Get user billing information
			$dbst =new  ps_DB;
			$qt = "SELECT * FROM #__{vm}_user_info WHERE user_info_id='".$d["ship_to_info_id"]."' AND address_type='ST'";
			$dbst->query($qt);
			$dbst->next_record();
		}
		else {
			$dbst = $dbbt;
		}

		require_once(CLASSPATH ."payment/ps_moneris.helper.php");

		if(MN_TEST_REQUEST == "TRUE")
		{
			$storeid = MN_TEST_STOREID;
			$apitoken = MN_TEST_APITOKEN;
			$d["order_payment_log"] .= "Test Mode\n";

			$ptoken = rand(1, 10);			
			$ptoken = number_format($ptoken, 0, "", "");
			
			if(($ptoken % 2) == 0)
			{
				$order_total = "10.10";
			}
			else
			{
				$order_total = "10.24";
			}
			
		}
		else
		{
			$storeid = MN_STOREID;
			$apitoken = MN_APITOKEN;
		}

		$order_id = $order_number;
		$cust_id = $dbbt->f("user_id");
		$amount = $order_total;

		$expiration .= substr($_SESSION['ccdata']['order_payment_expire_year'], -2, 2);
		$expiration .= substr($_SESSION['ccdata']['order_payment_expire_month'], 0, 2);
		
		$pan = $_SESSION['ccdata']['order_payment_number'];
		$crypt = 7;
		
		$cvd_indicator = 0;
		$cvd_value = $_SESSION['ccdata']['credit_card_code'];
		
		$avs_street_number = $dbbt->f("address_1");
		$avs_street_name = $dbbt->f("address_2");
		$avs_zipcode = $dbbt->f("zip");
		
		$mpgConfig = new mpgGlobals();

		$txnArray = array('type'=>'purchase',
						'order_id'=>$order_id,
						'cust_id'=>$cust_id,
						'amount'=>$amount,
						'pan'=>$pan,
						'expdate'=>$expiration,
						'crypt_type'=>$crypt);
		
		$cvdTemplate = array('cvd_indicator' => $cvd_indicator,
							'cvd_value' => $cvd_value);
							
		$avsTemplate = array('avs_street_number'=> $avs_street_number,
							'avs_street_name' => $avs_street_name,
							'avs_zipcode' => $avs_zipcode);
													
		$mpgAvsInfo = new mpgAvsInfo ($avsTemplate);
		$mpgCvdInfo = new mpgCvdInfo ($cvdTemplate);
		
		$mpgTxn = new mpgTransaction($txnArray);
		
		if(MN_USE_AVS_CHECK == "YES")
		{
			$mpgTxn->setAvsInfo($mpgAvsInfo);
		}
		
		if(MN_CHECK_CARD_CODE == "YES")
		{
			$mpgTxn->setCvdInfo($mpgCvdInfo);
		}
		
		$mpgRequest = new mpgRequest($mpgTxn);
		$mpgHttpPost = new mpgHttpsPost($storeid,$apitoken,$mpgRequest);		
		$mpgResponse = $mpgHttpPost->getMpgResponse();

		if(MN_TEST_REQUEST == "TRUE"  && false)
		{		
			echo "<pre>";
			echo "Raw Data<br /><br />";
			echo "Globals: <br />";
			var_dump($mpgConfig->getGlobals());
			echo "<br />";
			echo "Request: <br />";
			var_dump($mpgHttpPost);
			echo "<br />";
			echo "Response: <br />";
			var_dump($mpgResponse);
			echo "</pre>";
		}
		
		$mpgRCode = $mpgResponse->getResponseCode();
		$mpgMessage = $mpgResponse->getMessage();
		$mpgTxnNumber = $mpgResponse->getTxnNumber();
		$mpgAvsCode = $mpgResponse->getAvsResultCode();
		$mpgCvdCode = $mpgResponse->getCvdResultCode();
		
		// print_r($mpgTxnNumber);
		
		$d["order_payment_trans_id"] = $mpgTxnNumber;
		$d["order_payment_log"] .= "(" . $VM_LANG->_PHPSHOP_ERROR_CODE . ": " . $mpgRCode . ")";
		
		if(stristr($mpgRCode, "null") == FALSE && $mpgRCode !== null)
		{	
			if(intval($mpgRCode) < 50)
			{
				$d["order_payment_log"] = $VM_LANG->_PHPSHOP_APPROVED;
				$d["order_payment_log"] .= "\nTransaction ID: " . $mpgTxnNumber;
				$d["order_payment_trans_id"] = $mpgTxnNumber;
				// $vmLogger->err($d["order_payment_log"]);
				return true;
			}
			else
			if(intval($mpgRCode) >= 50)
			{
				$d["order_payment_log"] = $d["order_payment_log"] . $VM_LANG->_PHPSHOP_DECLINE;
				$vmLogger->err($d["order_payment_log"]);	
				return false;
			}
			
		}
		else
		{
			$d["order_payment_log"] = str_replace("null", "N/A", $d["order_payment_log"]);
			$d["order_payment_log"] = $d["order_payment_log"] . $VM_LANG->_PHPSHOP_UNKNOWN_ERROR;
			$vmLogger->err($d["order_payment_log"]);
			return false;
		}

		$d["order_payment_log"] = $d["order_payment_log"] . $VM_LANG->_PHPSHOP_UNKNOWN_ERROR;
		$vmLogger->err($d["order_payment_log"]);
		return false;
	}

	/**************************************************************************
	** name: capture_payment()
	** created by: Soeren
	** description: Process a previous transaction with authorize.net, Capture the Payment
	** parameters: $order_number, the number of the order, we're processing here
	** returns:
	***************************************************************************/
	function capture_payment( &$d ) {

		global $vendor_mail, $vendor_currency, $VM_LANG, $vmLogger;
		$database = new ps_DB();
		
		// print_r($_POST);
		
		return false;

	}

}

?>