<?php

include_once '../configuration.php';

if (!$mosConfig_host)
{
    die('no config');
}

$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

if (!$link) 
{
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) 
{
    die('Could not select database: ' . mysql_error());
}

$file = file('./postcodes.txt');
$inserts = array();

foreach ($file as $line)
{
   $postcode = (int)$line;
   
   $inserts[] = "(".$postcode.", 33)";
}

mysql_query("INSERT INTO `jos_postcode_warehouse` (`postal_code`, `warehouse_id`) VALUES ".implode(',', $inserts)."");

?>