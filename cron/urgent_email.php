<?php

$email_text = '<table cellpadding="0" cellspacing="1" style="background-color: #f5f7fa; padding: 10px; width: 100%;">
<tbody>
<tr>
<td>
<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #efefef; padding-bottom: 15px; padding-top: 15px; width: 700px;">
<tbody>
<tr>
<td>
<table align="center" cellpadding="0" cellspacing="0" style="background-color: #ffffff; width: 650px;">
<tbody>
<tr>
<td style="height: 130px; width: 650px;"><a href="https://bloomex.com.au?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" target="_blank" rel="noopener"><img alt="Bloomex" src="https://bloomex.com.au/images/email-confirmation/Australia_Logo.png" style="border-width: 0px; height: 127px; width: 650px;" /></a></td>
</tr>
<tr>
<td style="border-top: 1px solid #EFEFEF; border-bottom: 1px solid #EFEFEF; padding: 10px 5px 10px 5px; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 15px;"><a href="https://bloomex.com.au/specials/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;">Specials</a> | <a href="https://bloomex.com.au/occasions/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">Occasions</a> | <a href="https://bloomex.com.au/flowers/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">Flowers</a> |&nbsp;<a href="https://bloomex.com.au/extra-touches-extra-touches/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">Extra Touches</a> | <a href="https://bloomex.com.au/gift-baskets/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">Gift Baskets</a> | <a href="https://bloomex.com.au/by-price/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">By Price</a> | <a href="https://bloomex.com.au/corporate-gift-hampers/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="font-family: Arial, Helvetica, sans-serif; color: #333333; text-decoration: none;" target="_blank" rel="noopener">Corporate</a></td>
</tr>
<tr>
<td style="padding: 15px 10px 15px 10px; width: 583px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 20px;">
<p><strong>Thank you for being patient, we apologize for the inconvenience.</strong><br />
<br />We believe in transparency and honesty - that includes when we&#8217;ve messed up. Your item has been delayed due to the recent flooding, but rest assured we will be providing you with all the support you need to keep track of your package as it is moved along.<br/><br/>
<strong>What&#8217;s next?</strong> <br/><br/>You&#8217;ll receive an email confirmation as soon as your package is on its way! The quickest way to contact our customer support is at <a href="mailto:wecare@bloomex.com.au">wecare@bloomex.com.au</a> should you have any questions or concerns.
<br><br>
Thank you,<br>
Customer Care Team<br>
Bloomex Australia</p>
</td>
</tr>


<tr>
<td style="padding: 10px; background-color: #c5a1c6; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #ffffff; line-height: 14px;"><a href="https://bloomex.com.au/contact/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="text-decoration: none; color: #ffffff;" target="_blank" rel="noopener">Contact Us</a>&nbsp;&nbsp; |&nbsp;&nbsp; <a href="https://bloomex.com.au/account-details/?utm_source=email&amp;utm_medium=email-confirmation&amp;utm_campaign=other-url" style="text-decoration: none; color: #ffffff;" target="_blank" rel="noopener">My Account</a></td>
</tr>

</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>';

$time = time();
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/MAIL5.php';

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
global $mysqli;

$query = "SELECT
    *
FROM `tbl_urgent_email`
WHERE  
    `send` = '0000-00-00 00:00:00'
LIMIT 10";

$result = $mysqli->query($query);

if (!$result) {
    die('Select error: ' . $mysqli->error);
}

while ($obj = $result->fetch_object()) {
    $m = new MAIL5;
    $m->From($mosConfig_mailfrom, 'Bloomex Australia');
    $addto = $m->AddTo($obj->email);
    $addto = $m->addbcc('kzrajevsky@bloomex.ca');
    $addto = $m->addbcc('robert@bloomex.ca');
    if ($addto) {
        $m->Subject("Update about your order");
        $m->Html($email_text);
        $c = $m->Connect($mosConfig_smtphost, (int) $mosConfig_smtpport, $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtpprotocol, 20);
        if ($c) {
            if (!$m->Send($c)) {
                echo "<pre>";
                var_dump($m->History);
                print_r($m->History);
                list($tm1, $ar1) = each($m->History[0]);
                list($tm2, $ar2) = each($m->History[count($m->History) - 1]);
            } else {

                echo 'Mail sent to  ' . $obj->email . "<br>";
            }
            $m->Disconnect();
        }
    }

    $query = "UPDATE `tbl_urgent_email`
                SET 
                    `send`=NOW()
                                    WHERE  
                    `id`=" . $obj->id . "
                ";
    $mysqli->query($query);
}
