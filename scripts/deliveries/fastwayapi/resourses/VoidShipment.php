<?php

require_once 'config.php';
require_once "mysql.class.php";
$dbrs = new db_class;
if (!$dbrs->connect()) {
    $dbrs->print_last_error(false);
}



require_once('bloomexorder.php');
$order = new BloomexOrder(intval($_REQUEST['id']));

try {

    $client = new SoapClient($canpar_business_url, $SOAP_OPTIONS);

    //Complex Type: VoidShipmentRq
    $request = array();
    $request['password'] = $password;
    $request['id'] = intval($_REQUEST['id']);
    $request['user_id'] = $user_id;

    //Method: processShipment
    $rq = array();
    $rq['request'] = $request;
    echo "<pre>Request:";
    var_dump($rq);
    echo "</pre>";
    $rs = $client->voidShipment($rq);
    echo "<pre>Response:";
    var_dump($rs);
    echo "</pre>";
} catch (SoapFault $fault) {
    print_r($fault);
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}
?>
