<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$md5_salt = '@#%DFG%^Y^ERGU&N^U&^J#$%^&UCFT%G^H&J^&$F$%T*J&*^V$#';
$return = array();
$return['result'] = false;

if ($_POST['key'] == md5($md5_salt.(int)$_POST['user_id'].$md5_salt)) {
    $user_id = (int)$_POST['user_id'];
    $query = "UPDATE `tbl_platinum_club` SET `uses`=`uses`+1 WHERE `user_id`=" . $user_id . " AND `end_datetime` IS NULL";
    $mysqli->query($query);
$return['result'] = "updated_uses_count";

    $query = "SELECT `id`, `uses` FROM `tbl_platinum_club` WHERE `user_id`=" . $user_id . " AND `end_datetime` IS NULL";
    $result = $mysqli->query($query);
    $platinum_result = $result->fetch_object();


    if ($platinum_result->uses == 6) {
        $sql_pc = "UPDATE `tbl_platinum_club` SET `end_datetime`=NOW() WHERE `id`=" . $platinum_result->id . "";
        $mysqli->query($sql_pc);
$return['result'] = "finished_limit";
    }

    $sql_pc = "INSERT INTO `tbl_platinum_club_uses` (`platinum_club_id`, `order_id`) VALUES (" . $platinum_result->id . ", " . $order_id . ")";
    $mysqli->query($sql_pc);

}
echo json_encode($return);
$mysqli->close();
?>

