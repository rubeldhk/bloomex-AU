<?php
require_once 'config.php';
require_once "mysql.class.php";
$cfg = new DatabaseOptions();

$mysqli = new mysqli($cfg->host, $cfg->user, $cfg->pw, $cfg->db);
$mysqli->set_charset('utf8');

function filter_($data) {
    global $mysqli;
    
    $data = trim(htmlentities(strip_tags($data)));
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
    $data = $mysqli->real_escape_string($data);
    return $data;
}
$sender_object = new SenderOptions(filter_($_REQUEST['sender']));

$shipper_num = $sender_object->shipper_num;



$endofday = new SoapClient($canpar_business_url, $SOAP_OPTIONS);

$payment_info['type'] = 'С';
$request['date'] = date("Y-m-d") . "T" . "20:00:00"/*date('H:i:s')*/;
$request['password'] = $password;
$request['payment_info'] = $payment_info;
$request['shipper_num'] = $shipper_num;
$request['user_id'] = $user_id;

$rq = array();
$rq['request'] = $request;

$rs = $endofday->endOfDay($rq);
if ($rs->return->error) {
    echo "Error while processing end of the day:" . $rs->return->error;
    echo "<br/>RQ/RS:";
    echo "<pre>Request:";
    var_dump($rq);
    echo "</pre>";
    echo "<pre>Response:";
    var_dump($rs);
    echo "</pre>";
} else {
    $manifest = new SoapClient($canpar_business_url, $SOAP_OPTIONS);
    $request = array();
    $request['manifest_num'] = $rs->return->manifest_num;
    $request['password'] = $password;
    $request['shipper_num'] = $shipper_num;
    $request['type'] = 'F';
    $request['user_id'] = $user_id;
    $rq = array();
    $rq['request'] = $request;
    $rs = $manifest->getManifest($rq);
    if ($rs->return->error) {
        echo "Error processing manifest<br/>RQ/RS:";
        echo "<pre>Request:";
        var_dump($rq);
        echo "</pre>";
        echo "<pre>Response:";
        var_dump($rs);
        echo "</pre>";
    } else {
        Echo "<h2>Manifest saved</h2>";
        $manifest_str = $rs->return->manifest;
        $fp = fopen('manifest/manifest_' . date("Y-m-d") . '_'. $sender_object->City.'.pdf', 'wb');
        fwrite($fp, base64_decode($manifest_str));
        fclose($fp);
        Echo '<a href="'.'manifest/manifest_' . date("Y-m-d") . '_'. $sender_object->City.'.pdf">>>>PDF FILE<<</a>';
    }
}
 ?>
 <br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/>
<?php 
$mysqli->close();
?>