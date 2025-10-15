<?php

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$query = "SELECT 
    `id`
FROM `tbl_calls_christmas2019`
WHERE `id`=".$id."
";
    
$database->setQuery($query);
$call_obj = false;
$database->loadObject($call_obj);

if ($call_obj) {
    date_default_timezone_set('Australia/Sydney');
    
    $query = "INSERT INTO `tbl_calls_history`
    (
        `id_number`,
        `comment`,
        `datetime_add`
    )
    VALUES (
        ".$id.",
        '".$database->getEscaped('Looked message.')."',
        '".date('Y-m-d H:i:s')."'
    )";
    
    $database->setQuery($query);
    $database->query();
}

$im = imagecreate(1,1);

$white = imagecolorallocate($im,255,255,255);
imagesetpixel($im,1,1,$white);
header("content-type:image/jpg");
imagejpeg($im);
imagedestroy($im);

die;

?>
