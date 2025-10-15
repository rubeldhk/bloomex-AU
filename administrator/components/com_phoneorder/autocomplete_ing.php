<?php

if ($_REQUEST) {
    include "../../../configuration.php";

    $link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
    if (!$link) {
        die('Could not connect: ' . mysqli_error());
    }

    if (!mysqli_select_db($link,$mosConfig_db)) {
        die('Could not select database: ' . mysqli_error());
    }

    $q = mysqli_real_escape_string($link,$_REQUEST["term"]);

    $query = "SELECT * FROM `product_ingredient_options` WHERE igo_product_name LIKE '%" . $q . "%'";

    $sql = mysqli_query( $link,$query);

    $i = 0;
    $a = array();

    while ($rows = mysqli_fetch_object($sql))
    {
        $a[$i]['id'] = $rows->igo_id;
        $a[$i]['label'] = addslashes($rows->igo_product_name."  ( $".round($rows->landing_price,2)." )");
        $a[$i]['value'] = addslashes($rows->igo_product_name."  ( $".round($rows->landing_price,2)." )");
        $a[$i]['price'] = addslashes($rows->landing_price);

        $i++;
    }


    echo json_encode($a);
}
?>