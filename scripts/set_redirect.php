<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');


//$file = file('./redirect-Categories.csv');
$file = file('./product-redirects.csv');
$inserts = [];

foreach ($file as $line) {
    $line_a = explode(',', $line);
    
    $from = str_replace('https//bloomex.com.au/', '', $line_a[0]).'/';
    $to = str_replace('https//dev.bloomex.com.au/', '', $line_a[1]).'/';
    
    $inserts[] = "('".$mysqli->real_escape_string($from)."', '".$mysqli->real_escape_string($to)."', '1', '2')";
}

$query = "INSERT INTO `jos_aliases`
(
    `from`,
    `to`,
    `status`,
    `type`
)
VALUES
    ".implode(',', $inserts)."
";

$mysqli->query($query);

echo '<pre>';
    print_r($inserts);
echo '</pre>';

?>