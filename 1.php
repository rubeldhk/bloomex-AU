<?php
$myFile = __DIR__ . "/error_log.txt";
echo $myFile;
$theData = file_get_contents($myFile);
echo '<pre style="cssText:overflow-wrap: break-word; white-space: pre-wrap;">';
echo $theData;
echo "</pre>";
file_put_contents($myFile, "");
