<?php

include "../../../configuration.php";
$db = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$result['result']=false;
if ($db->connect_errno > 0) {
    $result['msg']='Unable to connect to database [' . $db->connect_error . ']';
    die(json_encode($result));
}

function csv_to_array($filename = '', $delimiter = ',') {
    global $db;
    $res['result']=false;
    if (!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $emails = [];
    $orders=[];

    if (($handle = fopen($filename, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $orders[]=$row[0];
        }
        fclose($handle);
    }
    if(!$orders){
        $res['msg']='There are not order ides in this list';
        die(json_encode($res));
    }

    $sql="SELECT `order_id`,`user_email` FROM `jos_vm_order_user_info` WHERE `address_type`='BT' and user_email!='' and order_id in (".implode(',',$orders).")";
    if (!$result = $db->query($sql)) {
        $thread = $db->thread_id;
        $db->close();
        $db->kill($thread);
        $res['msg']='There was an error running the select query [' . $db->error . ']';
        die(json_encode($res));
    }


    while ($r = $result->fetch_object()) {
        if(!isset($emails[$r->order_id])){
            $emails[$r->order_id]['order_id']=  $r->order_id;
            $emails[$r->order_id]['email']=  $r->user_email;
            $emails[$r->order_id]['sent_datetime']=  '0000-00-00 00:00:00';
            $emails[$r->order_id]['emailValid']=  true;
            if (!filter_var($r->user_email, FILTER_VALIDATE_EMAIL)) {
                $emails[$r->order_id]['emailValid']=  false;
            }
        }

    }
    $res['result']=true;
    $res['emails']=$emails;
    return $res;
}

if (isset($_FILES['file_emails']) && $_FILES['file_emails']['size']!=0) {
    $emailsArr = csv_to_array($_FILES['file_emails']['tmp_name']);
    die(json_encode($emailsArr));
}

