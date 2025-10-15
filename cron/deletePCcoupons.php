<?php


function sendabandom() { 
    include_once '../configuration.php';

    $mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
    $mysqli->set_charset('utf8');
    
    require_once 'MAIL5.php';
    $sendto = ($sendto_V) ? $sendto_V : "mercury.sw@gmail.com";
    $from = $mosConfig_mailfrom;
    
    $query = "SELECT 
        `u`.`user_email` 
    FROM `tbl_platinum_club` AS `p`
    LEFT JOIN `jos_vm_user_info` AS `u` 
        ON 
            `u`.`user_id`=`p`.`user_id`
    WHERE 
        `p`.`cdate`<=UNIX_TIMESTAMP(DATE_SUB(NOW(),INTERVAL 361 DAY )) 
        AND 
        `u`.`address_type`='BT'
    ";
    
    $result = $mysqli->query($query);
    
    if (!$result) {
        echo 'No result';
        return false;
    }

    while ($obj = $result->fetch_object()) {
        $subject = 'Your Bloomex Platinum Club Membership';
        $html = file_get_contents($mosConfig_absolute_path . '/deletePCcoupons/email_template.html');

        if ($obj->user_email) {
            $sendto = $obj->user_email;
        }

        $m = new MAIL5;
        $m->from($from);
        $m->AddTo($sendto);
        $m->Subject($subject);
        $m->Html($html);
        $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);// or die(print_r($m->Result));

        if ($m->Send($c)) {
            echo '<br/>Mail sent to ' . $obj->user_email;
        } 
        else {
            '<br /><pre>';
            print_r($m->History);
            list($tm1, $ar1) = each($m->History[0]);
            list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
            echo 'The process took: ' . (floatval($tm2) - floatval($tm1)) . ' seconds.</pre>';
        }

        $m->Disconnect();
    }
    
    $result->close();
    $mysqli->close();
}

sendabandom();

?>
