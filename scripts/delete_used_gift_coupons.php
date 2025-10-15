<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$query = "SELECT `c`.`coupon_id`  FROM `jos_vm_orders` AS `o` LEFT JOIN jos_vm_coupons as c on c.coupon_code=o.coupon_code WHERE (LENGTH(o.`coupon_code`) > 0) and `c`.`coupon_type`='gift' and o.coupon_discount>0 and c.create_date<FROM_UNIXTIME(o.cdate) limit 100";
$items_sql = $mysqli->query($query);
$ides = '';
if ($items_sql->num_rows > 0)
{
    while($item_obj = $items_sql->fetch_object()){
        $ides.=$item_obj->coupon_id.',';
    };

    $ides = rtrim($ides,',');
    $q = "DELETE FROM  `jos_vm_coupons`  WHERE `coupon_id` in (".$ides.")";
    $mysqli->query($q);

}
echo $q;
 $items_sql->close();
$mysqli->close();

?>