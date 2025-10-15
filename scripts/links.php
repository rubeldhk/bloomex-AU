<?php

include ("../configuration.php");

if (!$mosConfig_host)
    die('no config');
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) {
    die('Could not select database: ' . mysql_error());
}

$a = array(); 
$fp = fopen('./links2.csv', "r"); 
while (!feof($fp)) 
{ 
     $a[] = fgetcsv($fp, 1024, ";"); 
} 

foreach ($a as $one)
{
    if (!empty($one[0]))
    {
        $old_arr = explode('/', $one[0]);

        $old = $old_arr[sizeof($old_arr)-1];

        $new_arr = explode('/', $one[1]);

        $new = $new_arr[sizeof($new_arr)-1];

        $q = "SELECT * FROM `jos_redirection` WHERE `oldurl` LIKE '%/".$old."'";
        $sql = mysql_query($q);

        while ($out = mysql_fetch_array($sql))
        {
            $new_url = str_replace($old, $new, $out['oldurl']);
            
            echo $out['id'].'||'.$out['oldurl'].'|||||||'.$new_url.'<br/>';
            
            mysql_query("UPDATE `jos_redirection` SET `oldurl`='".mysql_real_escape_string($new_url)."' WHERE `id`=".$out['id']."");
        }

        echo '<==================================================================><br/>';
    }
}