<?php

include_once '../configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
$mysqli->set_charset('utf8');

$query = "SELECT 
             o.igo_id as old_ing_id,n.igo_id as new_ing_id
             FROM `product_ingredient_options` as o
             join `product_ingredient_options_ca` as n on n.igo_product_name=o.igo_product_name";

$oldIngrediendsList = [];
$result = $mysqli->query($query);
if ($result && $result->num_rows > 0) {
    while ($obj = $result->fetch_object()) {
        $oldIngrediendsList[$obj->old_ing_id] = $obj->new_ing_id;
    }
    $result->close();
}

if ($oldIngrediendsList) {

    foreach ($oldIngrediendsList as $k => $i) {

        $query = "UPDATE product_ingredients_lists SET igo_id = $i WHERE igo_id =" . $k;
        $mysqli->query($query);

    }
}


$mysqli->close();

