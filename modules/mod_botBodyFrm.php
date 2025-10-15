<?php

class botBobyFrm {

    private $line = 0;
    private $productsCount = 4;
    private $pages = array('shop.product_details', 'shop.browse');

    function create($line = null) {
        global $my, $database, $mosConfig_footerCategories, $ps_product, $ps_product_category, $cur_template;
        
        if (!is_null($line)) {
            $this->line = $line;
        }
        if(!$ps_product_category){
            require_once(CLASSPATH.'ps_product_category.php');
            $ps_product_category = new ps_product_category;
        }
        if(!$ps_product){
            require_once(CLASSPATH. 'ps_product.php' );
            $ps_product = new ps_product;
        }
        
        if ($this->line >= 0) {
            $category_id = $mosConfig_footerCategories[$this->line];

            $query = "SELECT 
            `p`.`product_id`,`c`.`category_name`,`c`.`category_description` 
            FROM (
                `jos_vm_product` AS `p`,  
                `jos_vm_product_category_xref` AS `cx`,
                `jos_vm_category` AS `c`,
                `jos_vm_product_price` AS `pp`
            ) 
            WHERE 
                `cx`.`category_id`=".$category_id." 
                AND
                `p`.`product_id`=`cx`.`product_id` 
                 AND
                `c`.`category_id`=`cx`.`category_id` 
                AND
                `pp`.`product_id`=`p`.`product_id`
                AND 
                `p`.`product_parent_id`='0' 
                AND 
                `p`.`product_publish`='Y' 
            ORDER BY (`pp`.`product_price`-`pp`.`saving_price`) ASC LIMIT 0, ".$this->productsCount."";

            $database->setQuery($query);
            $products_obj = $database->loadObjectList();

            if ($products_obj) {
                $products = array();

                foreach ($products_obj as $product_obj) {
                    $products[] = $product_obj->product_id;
                }

                ?>
                <div class="container bottom_category">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 title t1">
                                <div class="flower">
                                    <img alt="Flower" src="/templates/<?php echo $cur_template; ?>/images/Flower.svg" />
                                </div>
                                <?php echo strip_tags($products_obj[0]->category_name??''); ?>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 description"> 
                                <?php echo strip_tags($products_obj[0]->category_description??''); ?>
                            </div>
                        </div>
                    </div>
                    <?php echo $ps_product->show_product_list($products, true); ?>
                <?php
            }

            $this->line++;
        }
    }

    function check() {
        global $iso_client_lang, $sef;
        $startPage = str_replace("index.php", "", str_replace("/", "", $_SERVER['REQUEST_URI']));
        $startPage = str_replace("?lang=$iso_client_lang", "", $startPage);
        $check=0;

        if(strlen($startPage) < 1 ||  isset($_REQUEST['option']) && ($_REQUEST['option'] == 'com_frontpage' || $_REQUEST['option'] == 'com_page_not_found') ){
            $check=1;
        }
        elseif ($sef->landing_type == 1) {
            $check=3;
        }
        elseif ($sef->landing_type == 2) {
            $check=2;
        }
        elseif ($sef->landing_type == 3) {
            $check=3;
        }elseif ($sef->landing_type == 4) {
            $check=4;
        }elseif( isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_landingpages' && (isset($_REQUEST['type']) && $_REQUEST['type'] == 'basket') ) {
            $check = 2;
        }elseif( isset($_REQUEST['option']) && $_REQUEST['option'] == 'com_landingpages' ){
            $check=3;
        }else{
            $check=0;
        }

        return $check;

    }

}

global $sef;
if ($sef->homepage) {
    include "mod_middle_content.php";
}
$botBobyFrm = new botBobyFrm();
$check = $botBobyFrm->check();
if($check==1 || $check==4){
    $botBobyFrm->create(0);
    if ($sef->homepage) {
        include "mod_benefits.php";
    }
    $botBobyFrm->create(1);

}else{

    $botBobyFrm->create($check-1);
}





if (isset($sef->description_text_footer) && strstr($sef->description_text_footer, '{readmore}') !== false) {
    $footer_description_a = explode('{readmore}', $sef->description_text_footer);
    $footer_description = $footer_description_a[0];
}
else {
    $footer_description = $sef->description_text_footer??'';
}

if (isset($footer_description_a[1]) AND !empty($footer_description_a[1])) {
    $footer_description .= '<span class="landing_content_more_btn"> more...</span><span class="landing_content_more">'.$footer_description_a[1].'</span>';
}

if (!empty($footer_description) && ($sef->homepage || $sef->landing_type==4)) {
    ?>
    <div class="container bottom_category">
        <div class="row">
            <div class="col-xs-12 description">
                <div class="text">
                    <?php echo $footer_description; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
