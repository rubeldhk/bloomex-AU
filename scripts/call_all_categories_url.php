<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

include '../cron/MAIL5.php';

$query = "SELECT 
    CONCAT('http://test:ahs0hij3Ah@stage1.amazon.bloomex.com.au/index.php?option=com_virtuemart&Itemid=1&category_id=',`category_id`,'&lang=en&page=shop.browse') AS `url`
FROM `jos_vm_category`  
WHERE `category_publish`='Y'";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $i = 0;
    while ($obj = $result->fetch_object()) {
        $i++;
        
	$curl = curl_init($obj->url);
	curl_setopt($curl, CURLOPT_URL, $obj->url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

	$content = curl_exec($curl);
	curl_close($curl);
        
	if ($content) {
            echo "<pre>";
            print_r($i.' '.$url);
	}
        else {	
            echo "<pre>------------Cant open ";
            print_r($obj->url);
	}
    }
}

$result->close();
$mysqli->close();

?>
