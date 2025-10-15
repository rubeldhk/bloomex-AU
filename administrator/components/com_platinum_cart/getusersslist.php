<?php

include "../../../configuration.php";
$db = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}

if (isset($_POST['user_email'])) {
    $email = trim(strtolower($db->real_escape_string($_POST['user_email'])));
    $query = "Select user_id,first_name,last_name,user_email from jos_vm_user_info where user_email LIKE '%$email%' OR last_name LIKE '%$email%' OR first_name LIKE '%$email%'  AND address_type='BT' order by user_id desc";

    $select = "<select  id='user_list_select'>";
    $select.="<option value='0'>Select User Id</option>";
    if (!$result = $db->query($query)) {
        $thread = $db->thread_id;
        $db->close();
        $db->kill($thread);
        die('There was an error running the select query [' . $db->error . ']');
    }

    while ($res = $result->fetch_assoc()) {
        $select.="<option value=" . $res['user_id'] . ">" . $res['first_name'] . "-" . $res['last_name'] . " - " . $res['user_email'] . "</option>";
    }
    $select.="</select>";
}
$thread = $db->thread_id;
$db->close();
$db->kill($thread);
die($select);

