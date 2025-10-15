<?php

# insert callback number via link, ?tel=22233344412
# script answer: 0 - ext is busy, 1 - ext is free and make call,

include ('../configuration.php');
if (!empty($_GET['tel'])) {
	$tel = $_GET['tel'];
	$date = date('Y-m-d H:i:s');
	if (is_numeric($tel)) {
		mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
		mysql_select_db($mosConfig_db);
		$insert_query = "insert into calls_callback_tmp (`tel`, `date`) values ('$tel', '$date')";
		mysql_query($insert_query);
	}
}
?>
