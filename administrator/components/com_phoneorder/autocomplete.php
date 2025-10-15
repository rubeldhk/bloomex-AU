<?php

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        return utf8_encode($d);
    }
    return $d;
}

if ($_REQUEST) {
    include "../../../configuration.php";


    $link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
    if (!$link) {
        die('Could not connect: ' . mysqli_error());
    }

    if (!mysqli_select_db($link, $mosConfig_db)) {
        die('Could not select database: ' . mysqli_error());
    }

    $q = mysqli_real_escape_string($link, $_REQUEST["term"]);


    $products_filter = $_REQUEST['products_filter'] ?: 'au';
    $query = " SELECT VM.product_id,VM.product_thumb_image, VM.product_sku, VM.product_name, `PM`.`discount` as promotion_discount,
        VMP.product_price, VMP.saving_price  , VTR.tax_rate, VO.sub_3,VO.deluxe,VO.supersize,VO.petite,VO.must_be_combined
									FROM jos_vm_product AS VM
                                    LEFT JOIN jos_vm_product_price AS VMP ON VM.product_id = VMP.product_id
									    LEFT JOIN (SELECT 
                                                    CASE 
                                                        WHEN pmp.category_id > 0  THEN x.product_id
                                                        ELSE pmp.product_id
                                                    END AS `product_id`,pmp.discount,pmp.end_promotion
                                                    FROM `jos_vm_products_promotion` as pmp 
                                                    left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
                                                    WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) 
                                                    GROUP by product_id) as PM on PM.product_id = VM.product_id
                                     LEFT JOIN  jos_vm_tax_rate AS VTR ON VM.product_tax_id = VTR.tax_rate_id
                                     LEFT JOIN  jos_vm_product_options AS VO ON VO.product_id = VM.product_id
                                    LEFT JOIN jos_vm_product_category_xref AS X ON X.product_id = VMP.product_id AND X.category_id = 413
									WHERE VM.product_publish = 'Y' 
									  AND VO.product_sold_out!=1 
									  AND VO.product_out_of_season!=1 
									  AND  (VM.product_name LIKE '%" . $q . "%' OR VM.product_sku LIKE '%" . $q . "%') ";
    if($products_filter == 'nz') {
        $query .= " AND X.category_id is not null ";
    }else{
        $query .= " AND X.category_id is null ";
    }
    $query .= " ORDER BY VM.product_sku LIMIT 10";
    $sql = mysqli_query($link, $query);

    if (!$sql) {
        echo "SQL error : " . mysqli_error($link);
        echo "<!--<query> $query </query>" . "error:" . mysqli_error($link) . "-->";
    } else {
        $a = array();
        $i = 0;

        $sAttrubute = "";
        $sAttrubute_default = null;

        $sString = "";

        while ($rows = mysqli_fetch_object($sql)) {
            $products[] =$rows;
        }

        foreach($products as $rows){

            $q = "SELECT 
            `l`.`igl_quantity` as `quantity_normal`, 
            `l`.`igl_quantity_deluxe` as `quantity_deluxe`, 
            `l`.`igl_quantity_supersize` as `quantity_supersize`, 
            `l`.`igl_quantity_petite` as `quantity_petite`, 
            `o`.`igo_product_name` as `name`
            FROM `product_ingredients_lists` as `l`
            LEFT JOIN `product_ingredient_options` as `o` ON `o`.`igo_id`=`l`.`igo_id`
            WHERE `l`.`product_id`=" . $rows->product_id . "";

            $sql_ing = mysqli_query($link, $q);
            $ingredient_list = '';
            $ingredient_list_normal = "<div class='ing_{noItem} ing_0_{noItem}'>";
            $ingredient_list_deluxe = "<div style='display:none' class='ing_{noItem} ing_".$rows->deluxe."_{noItem}'>";
            $ingredient_list_supersize = "<div style='display:none' class='ing_{noItem} ing_".$rows->supersize."_{noItem}'>";
            $ingredient_list_petite = "<div style='display:none' class='ing_{noItem} ing_".$rows->petite."_{noItem}'>";
            while ($ing = mysqli_fetch_object($sql_ing)) {
                $ingredient_list_normal .= $ing->quantity_normal . " x " . $ing->name . "<br/>";
                $ingredient_list_deluxe .= $ing->quantity_deluxe . " x " . $ing->name . "<br/>";
                $ingredient_list_supersize .= $ing->quantity_supersize . " x " . $ing->name . "<br/>";
                $ingredient_list_petite .= $ing->quantity_petite . " x " . $ing->name . "<br/>";
            }
            $ingredient_list_normal .= "</div>";
            $ingredient_list_deluxe .= "</div>";
            $ingredient_list_supersize .= "</div>";
            $ingredient_list_petite .= "</div>";
            $ingredient_list = $ingredient_list_normal.$ingredient_list_deluxe.$ingredient_list_supersize.$ingredient_list_petite;


            $bloomex_reg_price = ($rows->product_price - $rows->saving_price)-(($rows->promotion_discount)?(($rows->product_price - $rows->saving_price)*$rows->promotion_discount/100):0);
            $value_product_price = number_format($bloomex_reg_price, 2, '.', '');
            if ($rows->no_tax) {
                $rows->tax_rate = 0;
            }
            $a[$i]['id'] = $rows->product_id;
            $a[$i]['label'] = '[' . ($rows->product_sku) . '] ' . utf8_encode($rows->product_name) . ' <span style="float: right;">' . $bloomex_reg_price . '</span>';
            $a[$i]['value'] = utf8_encode($rows->product_name);
            $a[$i]['href'] = ($rows->product_thumb_image);
            $a[$i]['productinformation'] = utf8_encode($rows->product_id . "[--1--]" . addslashes($rows->product_sku) . "[--1--]" . addslashes($rows->product_name)  . "[--1--]" . round(doubleval($value_product_price), 2) . "[--1--]" . round(floatval($rows->tax_rate), 2) . "[--1--]" . $rows->deluxe . "[--1--]" . $rows->supersize  . "[--1--]" . $rows->sub_3 . "[--1--]" . $rows->sub_6 . "[--1--]" . $rows->sub_12 . "[--1--]" . $rows->must_be_combined . "[--1--]" . addslashes($ingredient_list) . "[--1--]" . $rows->petite . "[--2--]");


            $tmp = explode("[--1--]", $a[$i]['productinformation']);
            foreach ($tmp as $v) {
                $a[$i]['debug']['productinformationparts'][] = $v;
                $a[$i]['debug']['productinformationpartsraw'][] = urlencode($v);
            }
            $i++;
        }

        die(json_encode(utf8ize($a)));
    }
    mysqli_close($link);
} else {
    die("no request");
}