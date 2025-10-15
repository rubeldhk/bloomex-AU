<?php

include (__DIR__."/../configuration.php");

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

$sql = mysql_query("SELECT `email` FROM `jos_users` WHERE `email`!='' ORDER BY `id` DESC LIMIT 84000");

while ($out = mysql_fetch_array($sql))
{
    echo $out['email'].'<br/>';
}
?>
