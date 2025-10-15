<?php
include ("../configuration.php");

if (!$mosConfig_host)
{
    die('no config');
}

$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);

if (!$link) 
{
    die('Could not connect: ' . mysql_error());
}

if (!mysql_select_db($mosConfig_db)) 
{
    die('Could not select database: ' . mysql_error());
}

$domains_a = array();
$id_a = array();

$sql_company = mysql_query("SELECT `company_domain` FROM `company_groups`");

while ($out_company = mysql_fetch_array($sql_company))
{
    $domains_a[] = strtolower($out_company['company_domain']);
}

$sql_user = mysql_query("SELECT `u`.`id`, `u`.`email`
FROM `jos_vm_shopper_vendor_xref` AS `x`  
    INNER JOIN `jos_users` AS `u` ON `u`.`id`=`x`.`user_id`
WHERE `x`.`shopper_group_id`='16'");

while ($out_user = mysql_fetch_array($sql_user))
{
    $out_user['email'] = strtolower($out_user['email']);
    $user_domain = substr($out_user['email'], strrpos($out_user['email'], '@')+1);
    
    if (!in_array($user_domain, $domains_a))
    {
        $id_a[] = $out_user['id'];
        
        echo $out_user['id'].' '.$out_user['email'].'<br/>';
    }
}
echo "<hr/>";
$q="UPDATE `jos_vm_shopper_vendor_xref` SET `shopper_group_id`='5' WHERE `user_id` IN (".implode(',', $id_a).")";
var_dump($q);
echo "<hr/>";
$result = mysql_query($q);
if (!$result) {
    die('error: ' . mysql_error());
}
echo "<hr/>";var_dump(mysql_affected_rows());
