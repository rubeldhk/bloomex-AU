<?php
$discpunt_group = false;
if ($my->id) {
    $query = "SELECT SG.shopper_group_discount
    FROM #__vm_shopper_vendor_xref AS SVX 
    INNER JOIN #__vm_shopper_group AS SG ON SG.shopper_group_id = SVX.shopper_group_id
    WHERE  SVX.user_id = " . $my->id . " LIMIT 1";
    $database->setQuery($query);
    $Discount = $database->loadResult();

    if (round($Discount) > 0) {
        $discpunt_group = true;
    }
}

if (isset($_GET['page']) && $_GET['page'] == 'shop.browse' && isset($_GET['category_id']) && strlen($_GET['category_id']) > 0) {
 
} 
else { 
    if (class_exists('checkStartPage', false) == false) {
        class checkStartPage {
            function check() {
                global $iso_client_lang;
                if (isset($_GET['option']) && ($_GET['option'] == 'com_landingpages' || $_GET['option'] == 'com_landingbasketpages' || $_GET['option'] == 'com_companies'))
                    return true;
                $startPage = str_replace("index.php", "", str_replace("/", "", $_SERVER['REQUEST_URI']));
                $startPage = str_replace("?lang=$iso_client_lang", "", $startPage);
                return ( strlen($startPage) < 1 || ( isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_frontpage' ) ) ? true : false;
            }
        }
    }
        include_once 'breadcrumbs.php';
}
?>
