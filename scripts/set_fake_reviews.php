<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

set_time_limit(600);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$result = $mysqli->query("SELECT `p`.`product_id`
FROM `jos_vm_product` AS `p`
    LEFT JOIN `tbl_product_fake_reviews` AS `r` ON `r`.`product_id`=`p`.`product_id`
WHERE `r`.`product_id` IS NULL");

if ($result->num_rows > 0) {
    $inserts = array();
    
    while($result_obj = $result->fetch_object()) {
        $rating = mt_rand(43, 48) / 10;
        $reviewCount = mt_rand(1000, 3000);
        
        $inserts[] = "(".$result_obj->product_id.", '".$rating."', '".$reviewCount."')";
    }
    
    if (sizeof($inserts) > 0) {
        $mysqli->query("INSERT INTO `tbl_product_fake_reviews` 
        (
            `product_id`, 
            `rating`,
            `review_count`
        )
        VALUES 
            ".implode(',', $inserts)."
        ");
    }
}

$mysqli->close();

?>
