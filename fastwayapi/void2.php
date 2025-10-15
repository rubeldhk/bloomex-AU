<?php
require_once 'config.php';
require_once "mysql.php"; require_once "mysql.class.php";
require_once "mysql.php";
$cfg = new DatabaseOptions();
$link = mysql_connect($cfg->host, $cfg->user, $cfg->pw);

function filter_($data) {
    $data = trim(htmlentities(strip_tags($data)));
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
    $data = mysql_real_escape_string($data);
    return $data;
}

$client = new SoapClient($canpar_business_url, $SOAP_OPTIONS);

$db = new db_class;
if (!$db->connect()) {
    $db->print_last_error(false);
}
$timestamp = time() + ( (-1) * 60 * 60 );
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
if (isset($_GET['id'])) {
    $pin = filter_($_GET['id']);
    $request = array();
    $request['id'] = $pin;
    $request['password'] = $password;
    $request['user_id'] = $user_id;
    $rq = array();
    $rq['request'] = $request;
    $rs = $client->VoidShipment($rq);
    if ($rs->return->error) {
        echo "rows:<pre>";
        var_dump($rows);
        echo "</pre>";
        echo "Shipment Void Fail. Server response:";
        echo "<pre>Request:";
        var_dump($rq);
        echo "</pre>";
        echo "<pre>Response:";
        var_dump($rs);
        echo "</pre>";
    } else {
        echo "Shipment Void Success";
    }
} else {
    echo "query:" . $q1;
    echo "rows:<pre>";
    var_dump($rows);
    echo "</pre>";
    echo "Shipment Void Fail. No canpar ID found in history for order #" . filter_($_REQUEST['order_id']);
}
?>
<br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/>

<?php mysql_close();?>