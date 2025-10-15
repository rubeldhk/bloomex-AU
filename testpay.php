<?php
$order_number = '11111' . "_" . date("YmdHis");
$aData = array();
$aData[0] = $order_number;
$aData[1] = date("YdmHiu") . "000" + 600;
$aData[2] = number_format('0.5', 2, '', '');
$aData[3] = $order_number;
$aData[4] = '4514093604631552';
$aData[5] = '11/20';

$aResult = processNABpayment($aData);


function processNABpayment($aReplace = array()) {
    //=============== CONFIGURATION ===============//
    $merchantID = "BB80010";
    $merchantPASS = "fPRRcES1Pa";
    $mode = "1"; // Test = 0, Live = 1
    //============= END CONFIGURATION =============//


    $dataArray = array();

    if (is_array($aReplace) && count($aReplace)) {
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
							<NABTransactMessage>
							<MessageInfo>
								<messageID>{messageID}</messageID>
								<messageTimestamp>{messageTimestamp}</messageTimestamp>
								<timeoutValue>60</timeoutValue>
								<apiVersion>xml-4.2</apiVersion>
							</MessageInfo>
							<MerchantInfo>
								<merchantID>' . $merchantID . '</merchantID>
								<password>' . $merchantPASS . '</password>
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
                                                                <cvv>910</cvv>
								</CreditCardInfo>
								</Txn>
								</TxnList>
							</Payment>
							</NABTransactMessage>';

        $aSearch = array("{messageID}", "{messageTimestamp}", "{amount}", "{purchaseOrderNo}", "{cardNumber}", "{expiryDate}");
        $xmlString = str_replace($aSearch, $aReplace, $xmlString);

        /* print_r($aReplace);
          echo "<br/><br/><br/>";
          echo $xmlString; */

        if ($mode == 0) {
            $url = "https://transact.nab.com.au/test/xmlapi/payment";
        } else {
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

        //die($output);

        
         var_dump($output);
    }

    return $dataArray;
}

?> 