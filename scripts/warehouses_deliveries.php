<?php

$warehouses = array(30, 28, 32, 33, 35, 36);

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$deliveries_sql = $mysqli->query("SELECT `id`, `name` FROM `jos_vm_deliveries`");

if ($deliveries_sql->num_rows > 0) {
    while($obj = $deliveries_sql->fetch_object()) {
        foreach ($warehouses as $warehouse) {
                $mysqli->query("INSERT INTO `jos_vm_warehouses_deliveries` (`warehouse_id`, `delivery_id`) VALUES (".$warehouse.", ".$obj->id.")");
        }
    }
}
