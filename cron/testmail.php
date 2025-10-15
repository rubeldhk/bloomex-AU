<?php

include (__DIR__ . "/../configuration.php");
include "MAIL5.php";

$email_to = "kzrajevsky@bloomex.ca";

$subject = 'test';
$html_send = '<table border="1" cellpadding="10"><tr><td>Order Id</td><td>Full Name</td><td>Address</td><td>Delivery Date</td><td>Order Total</td><td>DHL Airway bill number</td><td>Operator</td></tr>';
var_dump($mosConfig_smtphost);
var_dump($mosConfig_smtpuser);
var_dump($mosConfig_smtppass);
var_dump($mosConfig_smtpprotocol);
$html_send .= '</table>';
$m = new MAIL5;
$m->From($mosConfig_mailfrom);
$addto = $m->AddTo($email_to);

if ($addto) {
    $m->Subject($subject);
    $m->Html($html_send);
    $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
    if ($c) {
        if (!$m->Send($c)) {

            echo "<pre>";
            var_dump($m->History);
            print_r($m->History);
            list($tm1, $ar1) = each($m->History[0]);
            list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
        } else {

            echo 'Mail sent to  ' . $email_to . "<br>";
        }
        $m->Disconnect();
    } else {
        echo "<pre>";
        var_dump($m->History);
        print_r($m->History);
        list($tm1, $ar1) = each($m->History[0]);
        list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
    }
} else {
    echo "Wrong email address    " . $email_to;
}
