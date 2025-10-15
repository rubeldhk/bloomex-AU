<?php
require_once 'config.php';
require_once "mysql.class.php";
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
$timestamp = time() /* + ( (-1) * 60 * 60 ) */;
$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
$q1 = "SELECT  `comments` ,  `date_added` 
FROM jos_vm_order_history
WHERE  `order_status_code` =  '" . SENT_ORDER_STATUS . "'
AND  `order_id` =" . filter_($_REQUEST['order_id']) . "
AND `comments` like \"%ID: %\"
ORDER BY  `date_added` DESC";
$r = mysql_query($q1);
if (!$r) {
    echo "query:" . $q1 . "<br>";
    die('mysql error: ' . mysql_error());
}
$rows = array();
while ($row = mysql_fetch_array($r)) {
    $rows[] = $row;
}

if (!empty($rows)) {
    $pin = str_replace("ID: ", "", $rows['0']['0']);
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
        $q = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
								VALUES ('" . filter_($_REQUEST['order_id']) . "', 'K', '$mysqlDatetime', 'Canpar Order Void Confirmed','Canparapi')";
        $db->insert_sql($q);
        echo "Shipment Void Success";
    }
} else {
    echo "query:" . $q1 . "<br/>";
    echo "rows:<pre>";
    var_dump($rows);
    echo "</pre>";
    echo "Shipment Void Fail. No canpar ID found in history for order #" . filter_($_REQUEST['order_id']);
}
?>
<br/><center><input  type='button' name='Close' value=' Close ' onclick='window.close();' /></center><br/>
<?php mysql_close();?>