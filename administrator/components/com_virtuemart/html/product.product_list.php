<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: product.product_list.php,v 1.9.2.3 2006/04/05 18:16:54 soeren_nb Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );
global $ps_product, $ps_product_category;
global $mainframe;
$limit = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', 20));
$limitstart = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));

$keyword = mosgetparam($_REQUEST, 'keyword' );
$vendor = mosgetparam($_REQUEST, 'vendor', '');
$product_publish3 = mosgetparam($_REQUEST, 'product_publish3', '');
$product_publish33 = mosgetparam($_REQUEST, 'product_publish33', '');
if( empty($product_publish3) && !empty($product_publish33) ) {
	$product_publish3	= $product_publish33;
}

$product_parent_id = mosgetparam($_REQUEST, 'product_parent_id', null);
$category_id = mosgetparam($_REQUEST, 'category_id', null);
$product_type_id = mosgetparam($_REQUEST, 'product_type_id', null); // Changed Product Type
$search_date = mosgetparam($_REQUEST, 'search_date', null); // Changed search by date


$now = getdate();
$nowstring = $now["hours"].":".$now["minutes"]." ".$now["mday"].".".$now["mon"].".".$now["year"];
$search_order = @$_REQUEST["search_order"] ? $_REQUEST["search_order"] : "<";
$search_type = @$_REQUEST["search_type"] ? $_REQUEST["search_type"] : "product";
?>
<script src="<?php echo $mosConfig_live_site ?>/templates/bloomex7/js/jquery-2.2.3.min.js"></script>
<script src="<?php echo $mosConfig_live_site ?>/templates/bloomex7/js/jquery-ui-1.10.3.custom.min.js"></script>
<link rel="stylesheet" href="<?php echo $mosConfig_live_site ?>/templates/bloomex7/css/smoothness/jquery-ui-1.10.3.custom.css">
<link rel=stylesheet href="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/css/ui-lightness/jquery-ui-1.8.23.custom.css">
<script src="<?php echo $mosConfig_live_site; ?>/administrator/components/com_phoneorder/jquery.autocomplete_1.js"></script>

<?php
require_once( CLASSPATH . "pageNavigation.class.php" );
require_once( CLASSPATH . "htmlTools.class.php" );

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'sold_products') {
    global $database;

    $product_id = mosGetParam($_REQUEST, "product_id", "");
    $query_update = "UPDATE jos_vm_product_options SET product_sold_out = '1' WHERE product_id IN (" . implode(",", $product_id) .")";
    $database->setQuery($query_update);
    $database->query();

    $url = '/administrator/index2.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart';
    $pos = strpos($url, "&task=");
    if ($pos !== false) {
        $url = substr($url, 0, $pos);
    }

    header('Location: ' . $url);
    exit;
}
if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'in_stock') {
    global $database;

    $product_id = mosGetParam($_REQUEST, "product_id", "");
    $query_update = "UPDATE jos_vm_product_options SET product_sold_out = '0' WHERE product_id IN (" . implode(",", $product_id) .")";
    $database->setQuery($query_update);
    $database->query();

    $url = '/administrator/index2.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart';
    $pos = strpos($url, "&task=");
    if ($pos !== false) {
        $url = substr($url, 0, $pos);
    }

    header('Location: ' . $url);
    exit;
}


if(isset($_REQUEST['task']) && $_REQUEST['task']=='setCanonicalForCheckedProducts'){
    ob_end_clean();
    global $database;

    if(!$_REQUEST['selectedProducts'])
        exit(json_encode(['result'=>false,'msg'=>'no selected products']));
    if(!$_REQUEST['categoryId'])
        exit(json_encode(['result'=>false,'msg'=>'no selected category']));

    foreach($_REQUEST['selectedProducts'] as $selectedProductId){
        $query="SELECT * FROM jos_vm_product_category_xref WHERE category_id={$database->getEscaped($_REQUEST['categoryId'])} AND product_id=".$database->getEscaped($selectedProductId);
        $database->setQuery($query);
        $checkCategoryExist = $database->loadRow();
        if(!$checkCategoryExist){
            $query="INSERT INTO jos_vm_product_category_xref (category_id,product_id) VALUES ({$database->getEscaped($_REQUEST['categoryId'])},{$database->getEscaped($selectedProductId)})";
            $database->setQuery($query);
            $database->query();
        }
        $query="UPDATE jos_vm_product_options SET canonical_category_id='".$database->getEscaped($_REQUEST['categoryId'])."' WHERE product_id = ".$database->getEscaped($selectedProductId);
        $database->setQuery($query);
        $database->query();
    }
    exit(json_encode(['result'=>true,'msg'=>'products update success']));
}
if($_REQUEST['task']=='update_products_list'){
    ob_end_clean();
    global $database;
    if($_SESSION['parsed_from_file'] && count($_SESSION['parsed_from_file'])>0){
        $update_products_list= array();
        foreach($_SESSION['parsed_from_file'] as $s){
            if($s['diff']){
                $update_products_list[]=$s;
            }
        }
        if($update_products_list){
            $updated=false;
            foreach($update_products_list as $p){
                $query_update_name_sku="UPDATE jos_vm_product SET product_sku='".$database->getEscaped($p['product_sku'])."',product_name='".$database->getEscaped($p['product_name'])."' WHERE product_id=".$p['product_id'];
                $database->setQuery($query_update_name_sku);
                $database->query();


                $query_update_price="UPDATE jos_vm_product_price SET product_price='".$database->getEscaped($p['product_price'])."',saving_price='".$database->getEscaped($p['saving_price'])."' WHERE product_id=".$p['product_id'];
                $database->setQuery($query_update_price);
                $database->query();

                $query_update_deluxe_supersize_petite="UPDATE jos_vm_product_options SET 
                                  petite='".$database->getEscaped($p['petite'])."',
                                  deluxe='".$database->getEscaped($p['deluxe'])."',
                                  supersize='".$database->getEscaped($p['supersize'])."',
                                  no_delivery_order='".$database->getEscaped($p['no_delivery_order'])."'
                                   WHERE product_id=".$p['product_id'];
                $database->setQuery($query_update_deluxe_supersize_petite);
                $database->query();


                $deleteProductIngredients = "DELETE FROM `product_ingredients_lists` WHERE `product_id`=".$p['product_id']."";
                $database->setQuery($deleteProductIngredients);
                $database->query();

                $ingList = [];
                $ingNames = [];
                $ingredientsArr = explode(',',$p['ingredients']);
                if($ingredientsArr){
                    foreach($ingredientsArr as $i){
                        $a = explode(' x ',trim($i));
                        $ingList[strtolower($a[0])]=$a;
                        $ingNames[]=$database->getEscaped(strtolower($a[0]));
                    }
                }


                $queryGetProductIngredients="select igo_id,LOWER(igo_product_name) as igo_product_name from  product_ingredient_options where  LOWER(igo_product_name) in ('".implode("','",$ingNames)."')";
                $database->setQuery($queryGetProductIngredients);
                $ingredients = $database->loadAssocList();
                if($ingredients) {
                    foreach($ingredients as $ing){
                        $database->setQuery("insert into product_ingredients_lists (
                            `igo_id`,
                            `product_id`,
                            `igl_quantity`,
                            `igl_quantity_deluxe`,
                            `igl_quantity_supersize`,
                            `igl_quantity_petite`
                        ) VALUES (
                            '".$ing['igo_id']."',
                            '".$p['product_id']."',
                            '".$ingList[$ing['igo_product_name']][1]."',
                            '".$ingList[$ing['igo_product_name']][2]."',
                            '".$ingList[$ing['igo_product_name']][3]."',
                            '".$ingList[$ing['igo_product_name']][4]."'
                        )");
                        $database->query();
                    }
                }

                $deleteProductCategories = "DELETE FROM `jos_vm_product_category_xref` WHERE `product_id`=".$p['product_id']."";
                $database->setQuery($deleteProductCategories);
                $database->query();


                $catNames = [];
                $catArr = explode(',',$p['categories']);
                if($catArr){
                    foreach($catArr as $c){
                        $catNames[]=$database->getEscaped($c);
                    }
                }

                $queryGetProductCategories="select category_id from  jos_vm_category where  category_name in ('".implode("','",$catNames)."')";
                $database->setQuery($queryGetProductCategories);
                $categories = $database->loadAssocList();
                if($categories) {
                    foreach($categories as $cat){
                        $database->setQuery("insert into jos_vm_product_category_xref (
                            `category_id`, 
                            `product_id`        
                        ) VALUES (
                            '".$cat['category_id']."',
                            '".$p['product_id']."'
                        )");
                        $database->query();
                    }
                }

                if(in_array($p['canonical_category_name'],$catNames)){
                    $queryGetProductCannicalCategory="select category_id from  jos_vm_category where  category_name = '".$database->getEscaped($p['canonical_category_name'])."'";
                    $database->setQuery($queryGetProductCannicalCategory);
                    $CanonicalCategories = $database->loadAssocList();
                    if($CanonicalCategories) {
                        foreach($CanonicalCategories as $can) {
                            $database->setQuery("UPDATE jos_vm_product_options SET canonical_category_id='" . $database->getEscaped($can['category_id']) . "' WHERE product_id='" . $database->getEscaped($p['product_id'])."'");
                            $database->query();
                        }
                    }
                }

                $updated=true;
            }

            if($updated){
                $text='Products updated successfully';
                $out = array('result'=>'ok','msg'=>$text);
            }else{
                $text='No data .Please check file and re-upload';
                $out = array('result'=>'error','msg'=>$text);

            }
        }else{
            $text='No data To Update.Please check file and re-upload';
            $out = array('result'=>'error','msg'=>$text);
        }
    }else{
        $text='No data .Please check file and re-upload';
        $out = array('result'=>'error','msg'=>$text);
    }
    exit(json_encode($out));
}
if($_REQUEST['task']=='parse_xlsx'){
    ob_end_clean();
    global $database;
    require_once $_SERVER['DOCUMENT_ROOT']."/scripts/simplexlsx.class.php";
    $xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];
    $order_row = array();
    $sheet_header=array(
        'product_id',
        'product_name',
        'product_sku',
        'product_price',
        'saving_price',
        'deluxe',
        'supersize',
        'petite',
        'categories',
        'canonical_category_name',
        'ingredients',
        'no_delivery_order',
    );
    if ( 0 < $_FILES['file']['error'] ) {
        unset($_SESSION['parsed_from_file']);
        $res = array('invalid file');
    }else {
        $parsed_result_arr = $xlsx->rowsEx($sheet_num);

        if ($parsed_result_arr) {
            $query = "SELECT p.`product_id`,p.`product_name`,p.`product_sku`,
                        c.`product_price`,c.`saving_price`,
                        o.`deluxe`,o.`supersize`,o.`petite`,o.`no_delivery_order`,
                        i.ingredients,
                        m1.`category_name` as canonical_category_name, 
                        GROUP_CONCAT(m.category_name) as categories
                        FROM `jos_vm_product` as p
                        left join jos_vm_product_price as c on c.product_id=p.product_id
                        left join jos_vm_product_options as o on o.product_id=p.product_id
                        left join (SELECT 
                        GROUP_CONCAT(io.igo_product_name,' x ',l.igl_quantity,' x ',l.igl_quantity_deluxe,' x ',l.igl_quantity_supersize, ' x ',l.igl_quantity_petite) as ingredients,
                        l.product_id
                        from product_ingredients_lists as l
                        left join product_ingredient_options as io on io.igo_id = l.igo_id 
                        group by l.product_id) as i on i.product_id = p.product_id
                        left join jos_vm_product_category_xref as cx on cx.product_id = p.product_id 
                        left join jos_vm_category as m on m.category_id = cx.category_id
                        left join jos_vm_category as m1 on m1.category_id = o.canonical_category_id
                        WHERE p.`product_publish`='Y' group by p.product_id";
            $database->setQuery($query);
            $products_obj = $database->loadAssocList();
            $correct_product_list=array();
            foreach($products_obj as $p){
                $correct_product_list[$p['product_id']]=$p;
            }


            $res=array();
            foreach ($parsed_result_arr as $k=>$a){
                if($k==0)
                    continue;

                foreach($a as $m=>$b){
                    if($sheet_header[$m]){
                        $res[$a[0]['value']][$sheet_header[$m]] = $b['value'];
                    }
                }
            }
            $res = compare_two_arrays($res,$correct_product_list,$sheet_header);

            $_SESSION['parsed_from_file']=$res;
        }
        else{
            $res = array('invalid file');
            unset($_SESSION['parsed_from_file']);
        }
    }
    exit(json_encode($res));

}
if($_REQUEST['task']=='update_products_desc'){
    ob_end_clean();
    global $database;
    require_once $_SERVER['DOCUMENT_ROOT']."/scripts/simplexlsx.class.php";
    $xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
    $sheetNames = array_keys($xlsx->sheetNames());
    $sheet_num = $sheetNames[0];


    if ( 0 < $_FILES['file']['error'] ) {
        $text='No data .Please check file and re-upload';
        $out = array('result'=>'error','msg'=>$text);
    }else {
        $parsed_result_arr = $xlsx->rowsEx($sheet_num);

        if ($parsed_result_arr) {
            $updated=false;
            foreach ($parsed_result_arr as $k=>$a){

                if($k==0)
                    continue;
                $res=[];
                foreach($a as $m=>$b){
                    $res[$parsed_result_arr[0][$m]['value']] = $b['value'];
                }
                if($res && isset($res['Product Description'])){
                    $query_update="UPDATE jos_vm_product SET 
                          product_desc='".$database->getEscaped($res['Product Description']??'')."',
                          product_desc_city='".$database->getEscaped($res['Product Description For City']??'')."' ,
                          meta_info='" . $database->getEscaped(($res["Page Title"]??'') . "[--2010--]" . ($res["Meta Description"]??'') . "[--2010--]" . ($res["Meta Keywords"]??'')) . "' ,
                          meta_info_fr='" . $database->getEscaped(($res["Page Title For City"]??'') . "[--2010--]" . ($res["Meta Description For City"]??'') . "[--2010--]" . ($res["Meta Keywords For City"]??'')) . "'
                          WHERE product_id=".$res['Id'];
                    $database->setQuery($query_update);
                    $database->query();
                    $updated=true;
                }

            }
            if($updated){
                $text='Products Desc updated successfully';
                $out = array('result'=>'ok','msg'=>$text);
            }else{
                $text='No data .Please check file and re-upload';
                $out = array('result'=>'error','msg'=>$text);
            }
        }
        else{
            $text='No data .Please check file and re-upload';
            $out = array('result'=>'error','msg'=>$text);
        }
    }

exit(json_encode($out));
}

function compare_two_arrays($arr1,$arr2,$keys){
    $array_keys = array_keys($arr1);
    foreach ($array_keys as $k1){
        foreach($keys as $m){
            if(trim($arr1[$k1][$m])!=trim($arr2[$k1][$m])){
                $arr1[$k1]['diff']=1;
                break;
            }else{
                $arr1[$k1]['diff']=0;
            }
        }
    }
    return $arr1;
}
if($_REQUEST['task']=='get_current_products_list'){
    global $database;

    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    $phpexcel = new PHPExcel();
    $page = $phpexcel->setActiveSheetIndex(0);

    $page->setCellValue('A1', 'Id');
    $page->setCellValue('B1', 'Name');
    $page->setCellValue('C1', 'Sku');
    $page->setCellValue('D1', 'Price');
    $page->setCellValue('E1', 'Discounted Price');
    $page->setCellValue('F1', 'Deluxe Price');
    $page->setCellValue('G1', 'Supersize Price');
    $page->setCellValue('H1', 'Petite Price');
    $page->setCellValue('I1', 'Categories');
    $page->setCellValue('J1', 'Canonical Category Name');
    $page->setCellValue('K1', 'Ingredients');
    $page->setCellValue('L1', 'Free Shipping');
    $page->setCellValue('M1', 'Bloomex Price');
    $page->setCellValue('N1', 'Ingredient price');
    $page->setCellValue('O1', 'Ingredient price deluxe');
    $page->setCellValue('P1', 'Ingredient price supersize');
    $page->setCellValue('Q1', 'Ingredient price petite');


    $i = 2;


    $query = "SELECT p.`product_id`,p.`product_name`,p.`product_sku`,
                        c.`product_price`,c.`saving_price`,
                        o.`deluxe`,o.`supersize`,o.`petite`,o.`no_delivery_order`,
                        i.`ingredient_price`, 
                        m1.`category_name` as canonical_category_name, 
                        i.`ingredient_price_deluxe`, 
                        i.`ingredient_price_supersize`,
                        i.`ingredient_price_petite`,
                        i.ingredients,
                        GROUP_CONCAT(m.category_name) as categories
                        FROM `jos_vm_product` as p
                        left join jos_vm_product_price as c on c.product_id=p.product_id
                        left join jos_vm_product_options as o on o.product_id=p.product_id
                        left join (SELECT 
                        SUM(l.igl_quantity * io.landing_price) as `ingredient_price`, 
                        SUM(l.igl_quantity_deluxe * io.landing_price) as `ingredient_price_deluxe`, 
                        SUM(l.igl_quantity_supersize * io.landing_price) as `ingredient_price_supersize`,
                        SUM(l.igl_quantity_petite * io.landing_price) as `ingredient_price_petite`,
                        GROUP_CONCAT(io.igo_product_name,' x ',l.igl_quantity,' x ',l.igl_quantity_deluxe,' x ',l.igl_quantity_supersize, ' x ',l.igl_quantity_petite) as ingredients,
                        l.product_id
                        from product_ingredients_lists as l
                        left join product_ingredient_options as io on io.igo_id = l.igo_id 
                        group by l.product_id) as i on i.product_id = p.product_id
                        left join jos_vm_product_category_xref as cx on cx.product_id = p.product_id 
                        left join jos_vm_category as m on m.category_id = cx.category_id
                        left join jos_vm_category as m1 on m1.category_id = o.canonical_category_id
                        WHERE p.`product_publish`='Y' group by p.product_id";
    $database->setQuery($query);
    $products_obj = $database->loadObjectList();

    foreach ($products_obj as $product_obj) {
        $page->setCellValue('A'.$i, $product_obj->product_id);
        $page->setCellValue('B'.$i, $product_obj->product_name);
        $page->setCellValue('C'.$i, $product_obj->product_sku);
        $page->setCellValue('D'.$i, $product_obj->product_price);
        $page->setCellValue('E'.$i, $product_obj->saving_price);
        $page->setCellValue('F'.$i, $product_obj->deluxe);
        $page->setCellValue('G'.$i, $product_obj->supersize);
        $page->setCellValue('H'.$i, $product_obj->petite);
        $page->setCellValue('I'.$i, $product_obj->categories);
        $page->setCellValue('J'.$i, $product_obj->canonical_category_name);
        $page->setCellValue('K'.$i, $product_obj->ingredients);
        $page->setCellValue('L'.$i, $product_obj->no_delivery_order);
        $page->setCellValue('M'.$i, $product_obj->product_price - $product_obj->saving_price);
        $page->setCellValue('N'.$i, $product_obj->ingredient_price);
        $page->setCellValue('O'.$i, $product_obj->ingredient_price_deluxe);
        $page->setCellValue('P'.$i, $product_obj->ingredient_price_supersize);
        $page->setCellValue('Q'.$i, $product_obj->ingredient_price_petite);


        $i++;
    }

    $page->setTitle('Products List');

    ob_end_clean();

    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=products_list.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');
    die;
}
if($_REQUEST['task']=='get_current_products_desc_list'){
    global $database;

    include_once $_SERVER['DOCUMENT_ROOT'].'/scripts/Classes/PHPExcel.php';
    $phpexcel = new PHPExcel();
    $page = $phpexcel->setActiveSheetIndex(0);

    $page->setCellValue('A1', 'Id');
    $page->setCellValue('B1', 'Name');
    $page->setCellValue('C1', 'Product Description');
    $page->setCellValue('D1', 'Product Description For City');
    $page->setCellValue('E1', 'Page Title');
    $page->setCellValue('F1', 'Meta Description');
    $page->setCellValue('G1', 'Meta Keywords');
    $page->setCellValue('H1', 'Page Title For City');
    $page->setCellValue('I1', 'Meta Description For City');
    $page->setCellValue('J1', 'Meta Keywords For City');

    $i = 2;

    $query = "SELECT p.`product_id`,p.`product_name`,p.`product_desc`,
                        p.`product_desc_city`,p.`meta_info`,p.`meta_info_fr`
                        FROM `jos_vm_product` as p
                        WHERE p.`product_publish`='Y'";
    $database->setQuery($query);
    $products_obj = $database->loadObjectList();

    foreach ($products_obj as $product_obj) {
        $aMetaInfo = explode("[--2010--]", trim($product_obj->meta_info));
        $aMetaInfoCity = explode("[--2010--]", trim($product_obj->meta_info_fr));

        $page->setCellValue('A'.$i, $product_obj->product_id);
        $page->setCellValue('B'.$i, $product_obj->product_name);
        $page->setCellValue('C'.$i, trim($product_obj->product_desc));
        $page->setCellValue('D'.$i, trim($product_obj->product_desc_city));
        $page->setCellValue('E'.$i, $aMetaInfo[0]??'');
        $page->setCellValue('F'.$i, $aMetaInfo[1]??'');
        $page->setCellValue('G'.$i, $aMetaInfo[2]??'');
        $page->setCellValue('H'.$i, $aMetaInfoCity[0]??'');
        $page->setCellValue('I'.$i, $aMetaInfoCity[1]??'');
        $page->setCellValue('J'.$i, $aMetaInfoCity[2]??'');
        $i++;
    }

    $page->setTitle('Products List');

    ob_end_clean();

    $objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=products_list.xlsx');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $objWriter->save('php://output');
    die;
}
if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'Get_html_i_l')

{
    ob_end_clean();

    ?>
    <script type="text/javascript">

        function set_cookie ( name, value, exp_y, exp_m, exp_d, path, domain, secure )
        {
            var cookie_string = name + "=" + escape ( value );

            if ( exp_y )
            {
                var expires = new Date ( exp_y, exp_m, exp_d );
                cookie_string += "; expires=" + expires.toGMTString();
            }

            if ( path )
                cookie_string += "; path=" + escape ( path );

            if ( domain )
                cookie_string += "; domain=" + escape ( domain );

            if ( secure )
                cookie_string += "; secure";

            document.cookie = cookie_string;
        }

        function get_cookie ( cookie_name )
        {
            var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );

            if ( results )
                return ( unescape ( results[2] ) );
            else
                return null;
        }

        cookie_i_l = get_cookie("cookie_i_l");

        if (cookie_i_l == '1')
        {
            $('#copy_i_l').hide();
            $('#past_i_l').show();
        }
        else
        {
            $('#copy_i_l').show();
            $('#past_i_l').hide();
        }

        $("#copy_i_l").click(function( event )
        {
            event.preventDefault();

            ing_to_cookie = '';
            q_to_cookie = '';
            q_d_to_cookie = '';
            q_s_to_cookie = '';
            n_to_cookie = '';

            $('input[name="ing[]"]').each(function(indx){
                ing_to_cookie = ing_to_cookie+$(this).val()+'|';
            });

            $('input[name="q_d[]"]').each(function(indx){
                q_d_to_cookie = q_d_to_cookie+$(this).val()+'|';
            });

            $('input[name="q_s[]"]').each(function(indx){
                q_s_to_cookie = q_s_to_cookie+$(this).val()+'|';
            });

            $('input[name="q[]"]').each(function(indx){
                q_to_cookie = q_to_cookie+$(this).val()+'|';
            });

            $('input[name="names[]"]').each(function(indx){
                n_to_cookie = n_to_cookie+$(this).val()+'|';
            });

            set_cookie("cookie_i_l", '1');
            set_cookie("cookie_i_l_ing", ing_to_cookie);
            set_cookie("cookie_i_l_q", q_to_cookie);
            set_cookie("cookie_i_l_q_d", q_d_to_cookie);
            set_cookie("cookie_i_l_q_s", q_s_to_cookie);
            set_cookie("cookie_i_l_n", n_to_cookie);

            $(this).hide();
            $('#past_i_l').show();

        });

        $("#past_i_l").click(function( event )
        {
            event.preventDefault();

            cookie_i_l_ing = get_cookie("cookie_i_l_ing");
            cookie_i_l_q = get_cookie("cookie_i_l_q");
            cookie_i_l_q_d = get_cookie("cookie_i_l_q_d");
            cookie_i_l_q_s = get_cookie("cookie_i_l_q_s");
            cookie_i_l_n = get_cookie("cookie_i_l_n");

            cookie_i_l_ing_a = cookie_i_l_ing.split('|');
            cookie_i_l_q_a = cookie_i_l_q.split('|');
            cookie_i_l_q_d_a = cookie_i_l_q_d.split('|');
            cookie_i_l_q_s_a = cookie_i_l_q_s.split('|');
            cookie_i_l_n_a = cookie_i_l_n.split('|');

            for (var i = 0; i <= cookie_i_l_ing_a.length; i++)
            {
                if (parseInt(cookie_i_l_ing_a[i]) > 0)
                {
                    console.log(cookie_i_l_ing_a[i]+' | '+cookie_i_l_q_a[i]);

                    /*
                    $('#select_custom_id_1').val(parseInt(cookie_i_l_ing_a[i]));
                    $('#addCustomItem').trigger('click');
                    $('#select_custom_id_1').val();
                    */

                    var ing_html = '<tr class="send_info">';
                    ing_html += '<td>';
                    ing_html += '<input type="text" value="'+parseInt(cookie_i_l_q_a[i])+'" min="0" name="q[]">';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="text" value="'+parseInt(cookie_i_l_q_s_a[i])+'" min="0" name="q_d[]">';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="text" value="'+parseInt(cookie_i_l_q_s_a[i])+'" min="0" name="q_s[]">';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="hidden" name="ing[]" value="'+parseInt(cookie_i_l_ing_a[i])+'">';
                    ing_html += '<input type="hidden" name="names[]" value="'+cookie_i_l_n_a[i]+'">';
                    ing_html += cookie_i_l_n_a[i];
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="button" class="rmBtnIng" value="remove">';
                    ing_html += '</td>';

                    $('#products_table > tbody:last').append(ing_html);
                }
            }

            $(this).hide();
            $('#copy_i_l').show();

            set_cookie("cookie_i_l", '0');
            set_cookie("cookie_i_l_ing", '');
            set_cookie("cookie_i_l_q", '');
            set_cookie("ccookie_i_l_q_d", '');
            set_cookie("ccookie_i_l_q_s", '');
            set_cookie("cookie_i_l_n", '');
        });
    </script>
    <?php
    global $database, $my, $mosConfig_absolute_path;


    $product_id = trim(mosGetParam($_POST, "product_id", ""));

    $qInput = "SELECT `ingredient_list` FROM `jos_vm_product` WHERE `product_id`=" . $product_id . "";
    $database->setQuery($qInput);

    $product = $database->loadObjectList();
    $product = $product[0];

    $qInput = "SELECT * FROM `product_ingredients_lists`,`product_ingredient_options` WHERE product_ingredients_lists.product_id='" . $product_id . "' AND product_ingredients_lists.igo_id=product_ingredient_options.igo_id";
    $database->setQuery($qInput);

    $rows = $database->loadObjectList();

    echo '<br/><h2>New Ingredient</h2>
          <input type="text" class="ui-autocomplete-input" id="selectCustomId1" size="50" autocomplete="off" />
          <input type="hidden" id="select_custom_id_1" value="" />
          <input type="hidden" id="select_custom_price_1" value="" />
          <input type="button" value="Add" id="addCustomItem" class="btn" /> ';

    echo '<h2>Current Ingredients</h2>
    <table id="products_table">
    <thead>
        <tr>
            <th>
                Petite QTY
            </th>
            <th>
                Standard QTY
            </th>
            <th>
                Deluxe QTY
            </th>
            <th>
                Supersize QTY
            </th>
            <th>
                Name
            </th>
            <th>
                Action
            </th>
        </tr>
    </thead>
    <tbody>';
    foreach ($rows as $row)
    {
        echo '<tr class="send_info">
        <td><input type="text" name="q_p[]"  class="ing_input petite_size" price="'.$row->landing_price.'" value="'.$row->igl_quantity_petite.'" min="0"><span class="petite_size_price"></span></td>
        <td><input type="text" name="q[]" class="ing_input normal_size" price="'.$row->landing_price.'" value="'.$row->igl_quantity.'" min="0"><span class="normal_size_price"></span></td>
        <td><input type="text" name="q_d[]"  class="ing_input deluxe_size" price="'.$row->landing_price.'" value="'.$row->igl_quantity_deluxe.'" min="0"><span class="deluxe_size_price"></span></td>
        <td><input type="text" name="q_s[]"  class="ing_input supersize_size" price="'.$row->landing_price.'" value="'.$row->igl_quantity_supersize.'" min="0"><span class="supersize_size_price"></span></td>
        <td><input type="hidden" name="ing[]" value="'.$row->igo_id.'"><input type="hidden" name="names[]" value="'.htmlspecialchars($row->igo_product_name).'">'.htmlspecialchars($row->igo_product_name).'  ( $'.round($row->landing_price,2).' )</td>
            
        <td><input type="button" class="rmBtnIng" value="remove"></td></tr>';
    }

    echo '</tbody></table>
    <input style="float: right; margin-right: 150px;" type="button" value="Copy" id="copy_i_l">
    <input style="float: right; margin-right: 150px;" type="button" value="Past" id="past_i_l">
    <br/>';
    /*<h2>Recipe</h2>
    <textarea name="ingredient_list" id="ingredient_list" cols="50" rows="10">'.$product->ingredient_list.'</textarea>*/
    echo '<br/><br/>
        <table>
            <tr>
                <td>Total Petite : <b>$'.'<span class="petite_price_total">0</span></b></td>
                <td>Total Normal : <b>$'.'<span class="normal_price_total">0</span></b></td>
                <td>Total Deluxe : <b>$'.'<span class="deluxe_price_total">0</span></b></td>
                <td>Total Supersize: <b>$'.'<span class="supersize_price_total">0</span></b></td>
            </tr>
        </table>
    <input type="button" value="Save product" id="saveCustom" class="btn" />';
    ?>
    <script type="text/javascript">


        function logcustom2(message,price) {
            $("#select_custom_id_1").val(message);
            $("#select_custom_price_1").val(price);
        }

        $("#selectCustomId1").autocomplete({
            source: "<?php echo $mosConfig_live_site; ?>/administrator/components/com_phoneorder/autocomplete_ing.php",
            autoFocus: true,
            delay: 200,
            minLength: 3,
            select: function(event, ui) {
                logcustom2(
                    ui.item ? ui.item.id : this.value,
                    ui.item ? ui.item.price :0
                );
            }
        });

        $(".rmBtnIng").on('click',function()
        {
            $(this).parent().parent().remove();
            calculate_ingredient_price()
        });

        function calculate_ingredient_price(){
            $('.normal_price_total').text('0')
            $('.petite_price_total').text('0')
            $('.deluxe_price_total').text('0')
            $('.supersize_price_total').text('0')
            $.each($('.send_info'), function( index, value ) {
                var petite_size_price = Number((parseFloat($(this).find('.petite_size').attr('price')) * parseInt($(this).find('.petite_size').val())).toFixed(2));
                var normal_size_price = Number((parseFloat($(this).find('.normal_size').attr('price')) * parseInt($(this).find('.normal_size').val())).toFixed(2));
                var deluxe_size = Number((parseFloat($(this).find('.deluxe_size').attr('price')) * parseInt($(this).find('.deluxe_size').val())).toFixed(2));
                var supersize_size = Number((parseFloat($(this).find('.supersize_size').attr('price')) * parseInt($(this).find('.supersize_size').val())).toFixed(2));
                $(this).find('.petite_size_price').text('$'+petite_size_price);
                $(this).find('.normal_size_price').text('$'+normal_size_price);
                $(this).find('.deluxe_size_price').text('$'+deluxe_size);
                $(this).find('.supersize_size_price').text('$'+supersize_size);

                $('.petite_price_total').text(Number((parseFloat($('.petite_price_total').text())+parseFloat(petite_size_price)).toFixed(2)));
                $('.normal_price_total').text(Number((parseFloat($('.normal_price_total').text())+parseFloat(normal_size_price)).toFixed(2)));
                $('.deluxe_price_total').text(Number((parseFloat($('.deluxe_price_total').text())+parseFloat(deluxe_size)).toFixed(2)));
                $('.supersize_price_total').text(Number((parseFloat($('.supersize_price_total').text())+parseFloat(supersize_size)).toFixed(2)));

            })

        }

        $('.ing_input').on('focusout',function() {
            calculate_ingredient_price()
        })

        $('#addCustomItem').click(function(){
            var f = true;
            var product_id = $('#select_custom_id_1').val();
            var price = parseFloat($('#select_custom_price_1').val());
            if (product_id > 0)
            {
                $('.send_info').each(function(){
                    if ($(this).find('input[type=hidden]').val()==product_id){
                        f = false;
                    }
                });

                if (($('#selectCustomId1').val()!='') & f){

                    var ing_html = '<tr class="send_info">';
                    ing_html += '<td>';
                    ing_html += '<input type="text"  class="ing_input petite_size" price="'+price+'" value="1" min="0" name="q_p[]"><span class="petite_size_price"></span>';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="text"  class="ing_input normal_size" price="'+price+'" value="1" min="0" name="q[]"><span class="normal_size_price"></span>';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="text" class="ing_input deluxe_size" price="'+price+'" value="1" min="0" name="q_d[]"><span class="deluxe_size_price"></span>';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="text" class="ing_input supersize_size" price="'+price+'" value="1" min="0" name="q_s[]"><span class="supersize_size_price"></span>';
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="hidden" name="ing[]" value="'+product_id+'">';
                    ing_html += '<input type="hidden" name="names[]" value="'+$('#selectCustomId1').val()+'">';
                    ing_html += $('#selectCustomId1').val();
                    ing_html += '</td>';
                    ing_html += '<td>';
                    ing_html += '<input type="button" class="rmBtnIng" value="remove">';
                    ing_html += '</td>';

                    $('#products_table > tbody:last').append(ing_html);
                    calculate_ingredient_price()
                }
            }
        });

        $('#saveCustom').click(function(){
            var product_id = <?php echo $product_id; ?>;
            /*var ingredient_list = $("#ingredient_list").val();*/
            var q = $('input[name="q[]"]');
            var q_p = $('input[name="q_p[]"]');
            var q_s = $('input[name="q_s[]"]');
            var q_d = $('input[name="q_d[]"]');
            var ing = $('input[name="ing[]"]');

            $("#dialog-modal_new").html('Loading...');
            $("#i_l_"+product_id).html('Loading...');

            $.ajax({
                data: {
                    option: 'com_virtuemart',
                    task: 'Set_html_i_l',
                    page: 'product.product_list',
                    product_id: product_id,
                    q: q.serialize(),
                    q_d: q_d.serialize(),
                    q_s: q_s.serialize(),
                    q_p: q_p.serialize(),
                    ing: ing.serialize(),
                    /*ingredient_list: ingredient_list*/
                },
                type: "POST",
                dataType: "html",
                url: "index3.php",
                success: function(data)
                {
                    $("#i_l_"+product_id).html(data);
                    $("#dialog-modal_new").html('Product was saved successfully');
                }
            });
        });
    </script>

    <?php
    exit(0);
}

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'Set_html_i_l')
{
    ob_end_clean();

    global $ps_product;

    $product_id = trim(mosGetParam($_POST, "product_id", ""));
    //$ingredient_list = trim(mosGetParam($_POST, "ingredient_list", ""));

    parse_str($_POST['q'], $a_q);
    parse_str($_POST['q_s'], $a_q_s);
    parse_str($_POST['q_d'], $a_q_d);
    parse_str($_POST['q_p'], $a_q_p);
    parse_str($_POST['ing'], $a_ing);

    $a_q = $a_q['q'];
    $a_q_s = $a_q_s['q_s'];
    $a_q_d = $a_q_d['q_d'];
    $a_q_p = $a_q_p['q_p'];
    $a_ing = $a_ing['ing'];

    /*
    $qInput = "UPDATE `jos_vm_product` SET `ingredient_list`='".$ingredient_list."' WHERE `product_id`=".$product_id."";
    $database->setQuery($qInput);
    $database->query();
    */
    $qInput = "DELETE FROM `product_ingredients_lists` WHERE `product_id`=".$product_id."";
    $database->setQuery($qInput);
    $database->query();

    $qInput = "INSERT INTO `product_ingredients_lists`
    (
        `igo_id`, 
        `product_id`, 
        `igl_quantity`, 
        `igl_quantity_petite`, 
        `igl_quantity_deluxe`, 
        `igl_quantity_supersize`         
    ) 
    VALUES ";

    $a_queries = array();

    for ($i = 0; $i < sizeof($a_ing); $i++) {
        $a_queries[] = "(
            '".$a_ing[$i]."', 
            '$product_id', 
            '".$a_q[$i]."', 
            '".$a_q_p[$i]."', 
            '".$a_q_d[$i]."', 
            '".$a_q_s[$i]."'
        )";
    }

    $qInput .= implode(',', $a_queries);
//    
//    echo $qInput;
//    
//    die;

    $database->setQuery($qInput);
    $database->query();

    echo $ps_product->get_il($product_id);

    exit(0);
}
?>

<script type="text/javascript">

    function edit_i_l(product_id)
    {
        $.ajax({
            data:
                {
                    option: 'com_virtuemart',
                    task: 'Get_html_i_l',
                    page: 'product.product_list',
                    product_id: product_id
                },
            type: "POST",
            dataType: "html",
            url: "index3.php",
            success: function(data)
            {
                $("#dialog-modal_new").html(data);


                $("#dialog-modal_new").dialog({
                    width: 700,
                    modal: true,
                    title: 'Ingredient lists for product id: '+product_id,
                    close: function()
                    {
                        $("#dialog-modal_new").hide();
                    }
                });
                calculate_ingredient_price()
            }
        });
    }
</script>

<script type="text/javascript" language="javascript">
                    window.onload = function() {
                    $("#update-links-button").click(function() {
                        $("#update-links-loader").html('loading, please wait');
                        $.post("/review_1.php",{ }, function(data) {
                            if (data == 'success') {
                                $("#update-links-loader").html('sucessfully updated');
                                 location.reload();
                                 console.log("'"+data+"'");
                            } else {
                                $("#update-links-loader").html('Error');
                                console.log("'"+data+"'");
                            }
                        })
                     })
                        $('.close_alert').on('click',function () {
                            $('.products_with_unpublished_canonical_alert').hide()
                        })

                        $('.upload_list').click(function(){
                            $( "#dialog" ).dialog({
                                width: 1200,
                                left:50,
                                modal:true,
                                position: 'top',
                                close: function()
                                {
                                    $("#dialog").hide();
                                }
                            });
                            $( "#fileToUploadform" ).show();
                        })
                        $('.upload_desc_list').click(function(){
                            $( "#dialog_desc" ).dialog({
                                width: 1200,
                                left:50,
                                modal:true,
                                position: 'top',
                                close: function()
                                {
                                    $("#dialog_desc").hide();
                                }
                            });
                            $( "#fileToUploadDescform" ).show();
                        })
                        $('.current_products_list').on('click',function(){
                            $('#fileToUploadform').hide();
                            $('.assignorder_loader').show();
                            $.ajax({
                                data:
                                    {
                                        option: 'com_virtuemart',
                                        task: 'get_current_products_list',
                                        page: 'product.product_list'
                                    },
                                url: "index2.php",
                                async: true,
                                cache: false,
                                method: 'GET',
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function (data) {
                                    $('#fileToUploadform').show();
                                    $('.assignorder_loader').hide();
                                    var a = document.createElement('a');
                                    var url = window.URL.createObjectURL(data);
                                    a.href = url;
                                    a.download = 'products_list.xlsx';
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                },
                                error:function(request, error) {
                                    $('#fileToUploadform').show();
                                    $('.assignorder_loader').hide();
                                }
                            });

                        })
                        $('.current_products_desc_list').on('click',function(){
                            $('#fileToUploadDescform').hide();
                            $('.assignorder_loader').show();
                            $.ajax({
                                data:
                                    {
                                        option: 'com_virtuemart',
                                        task: 'get_current_products_desc_list',
                                        page: 'product.product_list'
                                    },
                                url: "index2.php",
                                async: true,
                                cache: false,
                                method: 'GET',
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function (data) {
                                    $('#fileToUploadDescform').show();
                                    $('.assignorder_loader').hide();
                                    var a = document.createElement('a');
                                    var url = window.URL.createObjectURL(data);
                                    a.href = url;
                                    a.download = 'products_desc_list.xlsx';
                                    a.click();
                                    window.URL.revokeObjectURL(url);
                                },
                                error:function(request, error) {
                                    $('#fileToUploadDescform').show();
                                    $('.assignorder_loader').hide();
                                }
                            });

                        })
                        $('.btnSetCatonicalCategory').on('click',function(){
                            let el=$(this);
                            el.val('please wait...').attr('disabled','true');
                            let selectedProducts = [];
                            $('.order_checkbox:checked').each(function() {
                                selectedProducts.push($(this).attr('value'));
                            });
                            let PromiseAjax = new Promise((resolve, reject) =>  {
                                $.ajax({
                                    data:
                                        {
                                            option: 'com_virtuemart',
                                            task: 'setCanonicalForCheckedProducts',
                                            page: 'product.product_list',
                                            categoryId:$('#setCatonicalCategory').val(),
                                            selectedProducts:(selectedProducts.length>0)?selectedProducts:[]
                                        },
                                    url: "index3.php",
                                    async: true,
                                    cache: false,
                                    method: 'POST',
                                    success: function (data) {
                                        let obj = JSON.parse(data);
                                        (obj.result)?resolve(obj.msg):reject(obj.msg);
                                    },
                                    error:function(request, error) {
                                        reject('ajax error');
                                    }
                                });
                            });
                            PromiseAjax
                                .then(result => {
                                    $('.canonicalUpdateMsg').text(`${result}`).show().css({'color':'green'});
                                })
                                .catch(error =>{
                                    $('.canonicalUpdateMsg').text(`${error}`).show().css({'color':'red'});
                                })
                                .finally(()=>{
                                    el.val('Set Canonical').removeAttr('disabled');
                                })
                        });
                        $('.parse_file').click(function(){
                            $('#fileToUploadform').hide();
                            $('.assignorder_loader').show();
                            var file_data = $('#xlsxfileform').prop('files')[0];
                            var form_data = new FormData();
                            form_data.append('file', file_data);
                            form_data.append('option', "<?php echo $option; ?>");
                            form_data.append('page', "product.product_list");
                            form_data.append('task', "parse_xlsx");
                            $.ajax({
                                url: 'index2.php',
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                dataType: 'json',
                                cache: false,
                                async: true,
                                success: function(data){
                                    {
                                        if(data){
                                            if(data[0]!='invalid file') {
                                                var table = "<p><span style='background:yellow;width: 20px;height: 13px;display: block;float: left;margin-right: 10px;'></span>Marked changed products</p>" +
                                                    "<table border='1' class='adminlist' style='width: 100%'><tr>" +
                                                    "<th>Id</th>" +
                                                    "<th>Name</th>" +
                                                    "<th>Sku</th>" +
                                                    "<th>Price</th>" +
                                                    "<th>Discount Price</th>" +
                                                    "<th>Deluxe Price</th>" +
                                                    "<th>Supersize Price</th>" +
                                                    "<th>Petite Price</th>" +
                                                    "<th>Categories</th>" +
                                                    "<th>Canonical Category Name</th>" +
                                                    "<th>ingredients</th>" +
                                                    "<th>Free Shipping</th>" +
                                                    "</tr>";
                                                $.each(data, function (key, value) {
                                                    if(value.diff){
                                                        table = table + "<tr style='background-color:yellow'>";
                                                    }else{
                                                        table = table + "<tr>";
                                                    }
                                                    $.each(value, function (k, v) {
                                                        if(k!='diff'){
                                                            table = table + "<td>" + v + "</td>";
                                                        }
                                                    })
                                                    table = table + "</tr>";
                                                });
                                                table = table + "</table>";



                                                $('.parse_file').val('Upload').removeAttr('disabled');
                                                $('.parsing_result').html(table);
                                                $('.save_parsed_file').show().val('Update').removeAttr('disabled');

                                            }else{
                                                $('.parsing_result').html(data[0]+" Please change file");
                                                $('.parse_file').val('Upload').removeAttr('disabled');
                                                $('.save_parsed_file').hide();
                                            }

                                        }else{
                                            $('.parsing_result').html("Please change file");
                                            $('.parse_file').val('Upload').removeAttr('disabled');
                                            $('.save_parsed_file').hide();
                                        }
                                        $('#fileToUploadform').show();
                                        $('.assignorder_loader').hide();
                                    }
                                }
                            });

                        })
                        $('.save_parsed_file').click(function(){
                            $('#fileToUploadform').hide();
                            $('.assignorder_loader').show();
                            $.ajax({
                                data:
                                    {
                                        option: 'com_virtuemart',
                                        task: 'update_products_list',
                                        page: 'product.product_list'
                                    },
                                url: "index2.php",
                                async: true,
                                cache: false,
                                method: 'POST',
                                success: function(data){
                                    {
                                        if(data){
                                            var  obj = JSON.parse(data);
                                            if($.trim(obj.result)!='error'){
                                                $('.parsing_result').html(obj.msg);
                                            }else{
                                                $('.parsing_result').html(obj.msg);
                                            }
                                        }else{
                                            $('.parsing_result').html("No data.Please check file and re-upload");
                                        }
                                        $('.save_parsed_file').hide();
                                        $('#fileToUploadform').show();
                                        $('.assignorder_loader').hide();
                                    }
                                }
                            })

                        });
                        $('.save_parsed_desc_file').click(function(){
                            $('#fileToUploadDescform').hide();
                            $('.assignorder_loader').show();
                            $('.parsing_result_desc').html('');

                            var file_data = $('#xlsxdescfileform').prop('files')[0];
                            var form_data = new FormData();
                            form_data.append('file', file_data);
                            form_data.append('option', "<?php echo $option; ?>");
                            form_data.append('page', "product.product_list");
                            form_data.append('task', "update_products_desc");
                            $.ajax({
                                url: 'index2.php',
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                dataType: 'json',
                                cache: false,
                                async: true,
                                success: function(data){
                                    {
                                        if(data){

                                                $('.parsing_result_desc').html(data.msg);
                                        }else{
                                            $('.parsing_result_desc').html("No data.Please check file and re-upload");
                                        }
                                        $('#fileToUploadDescform').show();
                                        $('.assignorder_loader').hide();
                                    }
                                }
                            })

                        });

                    }; </script>

<style>
    .ing_input{
        width: 60%;
    }
.products_with_unpublished_canonical_alert{
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
    clear: both;
    position: relative;
    padding: .75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: .25rem;
}
    .close_alert{
        position: absolute;
        right: .75rem;
        cursor: pointer;
        font-weight: bold;
        font-size: 20px;
    }
</style>
    <div id="dialog" title="Parse Xlsx File">
        <form style="display: none;" id="fileToUploadform">
            <input type="file" name="fileToUpload" id="xlsxfileform">
            <input type="button" class="parse_file" value="Upload" name="submit">
            <input type="button" class="current_products_list" value="Download Current Products List" name="download">
            <input style="display: none;" type="button" class="save_parsed_file" value="Update" name="save_parsed_file">
        </form><br>
        <div class="assignorder_loader" style="display: none;"></div>

        <div class="parsing_result"></div>
        <div style="color:red" class="parsing_error"></div>
    </div>
    <div id="dialog_desc" title="Parse Xlsx File">
        <form style="display: none;" id="fileToUploadDescform">
            <input type="file" name="fileToUpload" id="xlsxdescfileform">
            <input type="button" class="save_parsed_desc_file" value="Update Desc From File " name="save_parsed_desc_file">
            <input type="button" class="current_products_desc_list" value="Download Current Products Desc List" name="download">

        </form><br>
        <div class="assignorder_loader" style="display: none;"></div>

        <div class="parsing_result_desc"></div>
        <div style="color:red" class="parsing_error_desc"></div>
    </div>
<div id="dialog-modal_new"></div>
<div style="text-align:right;display:block;">
        <a href="#" id="update-links-button" style="float:left;margin-left:50px;color:#0C00CA;font-size:14px;">Update Reviews</a><div style="float:left;margin-left:150px;color:#0C00CA;font-size:14px;" id="update-links-loader"></div>
	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" name="adminFormSearch">
		<table border="0" cellpadding="5" cellspacing="0" width="40%" align="right">
			<tr>
				<td align="right" width="30%">
					<b><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE ?></b>&nbsp;
				</td>
				<td align="left" width="70%">
				       <select class="inputbox" name="search_type">
				              <option value="product"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRODUCT ?></option>
				              <option value="price" <?php echo $search_type == "price" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRICE ?></option>
				              <option value="withoutprice" <?php echo $search_type == "withoutprice" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_WITHOUTPRICE ?></option>
				       </select>
				       <select class="inputbox" name="search_order">
				              <option value="<"><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_BEFORE ?></option>
				              <option value=">" <?php echo $search_order == ">" ? 'selected="selected"' : ''; ?>><?php echo $VM_LANG->_PHPSHOP_PRODUCT_LIST_SEARCH_BY_DATE_AFTER ?></option>
				       </select>
				       <input type="hidden" name="option" value="com_virtuemart" />
				       <input class="inputbox" type="text" size="15" name="search_date" value="<?php echo mosgetparam($_REQUEST, 'search_date', $nowstring) ?>" />
				       <input type="hidden" name="page" value="product.product_list" />
				       <input class="button" type="submit" name="search" value="<?php echo $VM_LANG->_PHPSHOP_SEARCH_TITLE?>" />
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">
					<b>Category:</b>
				</td>
				<td align="left" width="70%">
					<select class="inputbox" id="category_id" name="category_id" onchange="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?option=com_virtuemart&page=product.product_list&category_id='+document.getElementById('category_id').options[selectedIndex].value;">
						<option value=""><?php echo _SEL_CATEGORY ?></option>
						<?php $ps_product_category->list_tree_new( $category_id );  ?>
			         	</select>
				</td>
			</tr>
			<tr>
				<td align="right" width="30%">
					<b>Product State:</b>
				</td>
				<td align="left" width="70%">
					<select class="inputbox" id="product_publish3" name="product_publish3" onchange="document.adminFormSearch.submit();">
						<option value="">Select Product Status</option>
						<option value="Y" <?php if( !empty($product_publish3) && $product_publish3 == "Y" ) echo "selected='selected'"; ?>>Published</option>
						<option value="N" <?php if( !empty($product_publish3) && $product_publish3 == "N" ) echo "selected='selected'"; ?>>UnPublished</option>
			         	</select>
				</td>
			</tr>
		</table>
	</form>
	<br/>
</div>
<?php

if (!$perm->check("admin")) {
    $q = "SELECT vendor_id FROM #__{vm}_auth_user_vendor WHERE user_id='".$auth['user_id']."'";
    $db->query( $q );
    $db->next_record();
    $vendor = $db->f("vendor_id");
}

$search_sql = " (#__{vm}_product.product_name LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_sku LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_s_desc LIKE '%$keyword%' OR \n";
$search_sql .= "#__{vm}_product.product_desc LIKE '%$keyword%'";
$search_sql .= ") \n";

if( !empty($product_publish3) ) {
	$search_sql3	= " AND product_publish = '$product_publish3' ";
}else {
	$search_sql3	= "";
}


// Check to see if this is a search or a browse by category
// Default is to show all products
if (!empty($category_id)) {
	$list  = "SELECT #__{vm}_category.category_name,#__{vm}_product.product_id,#__{vm}_product.product_name,#__{vm}_product.product_sku,#__{vm}_product_options.product_sold_out,
			#__{vm}_product.vendor_id,product_publish,  #__{vm}_product_category_xref.product_list, #__{vm}_product_category_xref.product_list IS NULL AS product_list_null";
	$list .= " FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category,#__{vm}_product_options WHERE ";
	$count  = "SELECT count(*) as num_rows FROM #__{vm}_product, #__{vm}_product_category_xref, #__{vm}_category WHERE ";

	$q = "#__{vm}_product_category_xref.category_id='$category_id' ";
	$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$q .= "AND #__{vm}_product.product_id=#__{vm}_product_category_xref.product_id ";
	$q .= "AND #__{vm}_product.product_parent_id='' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	$count .= $q . $search_sql3;
	$q .= $search_sql3 . " GROUP BY jos_vm_product.product_id ORDER BY product_list_null ASC, #__{vm}_product_category_xref.product_list ASC, `#__{vm}_product`.`product_name`ASC";
}
elseif (!empty($keyword)) {
	$list  = "SELECT *, #__{vm}_product_options.product_sold_out FROM #__{vm}_product 
              LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id WHERE ";
	$count = "SELECT COUNT(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = $search_sql;
	$q .= "AND #__{vm}_product.product_parent_id='' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	$count .= $q . $search_sql3;
	$q .= $search_sql3 . " ORDER BY product_publish DESC,product_name ";
}
elseif (!empty($product_parent_id)) {
	$list  = "SELECT *, #__{vm}_product_options.product_sold_out FROM #__{vm}_product 
             LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id WHERE ";
	$count = "SELECT COUNT(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = "product_parent_id='$product_parent_id' ";
	$q .= !empty($vendor) ? "AND #__{vm}_product.vendor_id='$vendor'" : "";
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	//$q .= "AND #__{vm}_product.product_id=#__{vm}_product_reviews.product_id ";
	//$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$count .= $q . $search_sql3;
	$q .= $search_sql3 . " ORDER BY product_publish DESC,product_name ";
}
/** Changed Product Type - Begin */
elseif (!empty($product_type_id)) {
	$list  = "SELECT * FROM #__{vm}_product,#__{vm}_product_product_type_xref ,#__{vm}_product_options
         LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id WHERE ";
	$count = "SELECT count(*) as num_rows FROM #__{vm}_product,#__{vm}_product_product_type_xref WHERE ";
	$q = "#__{vm}_product.product_id=#__{vm}_product_product_type_xref.product_id ";
	$q .= "AND product_type_id='$product_type_id' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	if( !empty( $keyword)) {
		$q .= " AND $search_sql";
	}
	$q .= $search_sql3 . " ORDER BY product_publish DESC,product_name ";
	$count .= $q;
}  /** Changed Product Type - End */
/** Changed search by date - Begin */
elseif (!empty($search_date)) {
    list($time,$date) = explode(" ",$search_date);
    list($d["search_date_hour"],$d["search_date_minute"]) = explode(":",$time);
    list($d["search_date_day"],$d["search_date_month"],$d["search_date_year"]) = explode(".",$date);
    $d["search_date_use"] = true;
    if (process_date_time($d,"search_date",$VM_LANG->_PHPSHOP_SEARCH_LBL)) {
        $date = $d["search_date"];
        switch( $search_type ) {
            case "product" :
				$list  = "SELECT *, #__{vm}_product_options.product_sold_out FROM #__{vm}_product 
                         LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id WHERE ";
				$count = "SELECT COUNT(*) as num_rows FROM #__{vm}_product WHERE ";
                break;
            case "withoutprice" :
            case "price" :
                $list  = "SELECT DISTINCT #__{vm}_product.product_id,product_name,product_sku,vendor_id,";
                $list .= "product_publish,product_parent_id, #__{vm}_product_options.product_sold_out FROM #__{vm}_product ";
                $list .= "LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id ";
                $list .= "LEFT JOIN #__{vm}_product_price ON #__{vm}_product.product_id = #__{vm}_product_price.product_id WHERE ";
                $count = "SELECT DISTINCT count(*) as num_rows FROM #__{vm}_product ";
                $count.= "LEFT JOIN #__{vm}_product_price ON #__{vm}_product.product_id = #__{vm}_product_price.product_id WHERE ";
                break;
        }
        $where = array();
//         $where[] = "#__{vm}_product.product_parent_id='0' ";
        if (!$perm->check("admin")) {
            $where[] = " #__{vm}_product.vendor_id = '$ps_vendor_id' ";
        }
        elseif( !empty($vendor) ) {
            $where[] =  " #__{vm}_product.vendor_id='$vendor' ";
        }
        $q = "";
        switch( $search_type ) {
            case "product" :
                $where[] = "#__{vm}_product.mdate ". $search_order . " $date ";
                break;
            case "price" :
                $where[] = "#__{vm}_product_price.mdate ". $search_order . " $date ";
                $q = "GROUP BY #__{vm}_product.product_sku ";
                break;
            case "withoutprice" :
                $where[] = "#__{vm}_product_price.mdate IS NULL ";
                $q = "GROUP BY #__{vm}_product.product_sku ";
                break;
        }

        $q =  implode(" AND ",$where) . $search_sql3 .$q . " ORDER BY #__{vm}_product.product_publish DESC,#__{vm}_product.product_name ";
        $count .= $q;
    }
    else {
    	echo "<script type=\"text/javascript\">alert('".$d["error"]."')</script>\n";
    }
}
/** Changed search by date - End */
else {
	$list  = "SELECT *, #__{vm}_product_options.product_sold_out FROM #__{vm}_product
              LEFT JOIN #__{vm}_product_options ON #__{vm}_product.product_id=#__{vm}_product_options.product_id WHERE ";
	$count = "SELECT COUNT(*) as num_rows FROM #__{vm}_product WHERE ";
	$q = "product_parent_id='0' ";
	if (!$perm->check("admin")) {
		$q  .= "AND #__{vm}_product.vendor_id = '$ps_vendor_id' ";
	}
	elseif( !empty($vendor) ) {
		$q .=  "AND #__{vm}_product.vendor_id='$vendor' ";
	}
	//$q .= "AND #__{vm}_product.product_id=#__{vm}_product_reviews.product_id ";
	//$q .= "AND #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id ";
	$count .= $q . $search_sql3;
	$q .= $search_sql3 . " ORDER BY product_publish DESC,product_name ";
}
$db->query($count);
$db->next_record();
$num_rows = $db->f("num_rows");

// Create the Page Navigation
$pageNav = new vmPageNav( $num_rows, $limitstart, $limit );

$limitstart = $pageNav->limitstart;
$list .= $q . " LIMIT $limitstart, " . $limit;

//echo $list;

//echo "<br/><br/>$q";
$get_products_with_unpublished_canonical = "SELECT p.product_id,k.product_sku,k.product_name,k.product_publish FROM `jos_vm_product_options` as p
left join jos_vm_category as c on c.category_id=p.canonical_category_id 
left join jos_vm_product as k on k.product_id=p.product_id
where c.category_publish !='Y' and k.product_id is not null and k.product_publish='Y'";
$database->setQuery($get_products_with_unpublished_canonical);
$products_with_unpublished_canonical = $database->loadObjectList();
if($products_with_unpublished_canonical){
    echo '<div class="products_with_unpublished_canonical_alert"  role="alert"><span class="close_alert">X</span><h4>Canonical categories of these products are unpublished</h4>';
    foreach($products_with_unpublished_canonical as $pwuc){
        echo $pwuc->product_name.' ( <a href="/administrator/index2.php?page=product.product_form&limitstart=0&keyword=&product_id='.$pwuc->product_id.'&option=com_virtuemart" target="_blank">'.$pwuc->product_sku.'</a> ) , ';
    }
    echo '</div>';
}
?>
    <div style="float: left;margin: 10px auto">
        <p>Select Category To Set As Canonical For Checked Products</p>
        <select class="inputbox" id="setCatonicalCategory" name="setCatonicalCategory">
            <option value=""><?php echo _SEL_CATEGORY ?></option>
            <?php $ps_product_category->list_tree();  ?>
        </select>
        <input type="button" class="btnSetCatonicalCategory" value="Set Canonical">
        <p style="display: none" class="canonicalUpdateMsg"></p>
    </div>
<?php
// Create the List Object with page navigation
$listObj = new listFactory( $pageNav );

// print out the search field and a list heading
$listObj->writeSearchHeader($VM_LANG->_PHPSHOP_PRODUCT_LIST_LBL, IMAGEURL."ps_image/product_code.png", "product", "product_list");
echo '<input type="hidden" value="'.$product_publish3.'" name="product_publish33">';
// start the list table
$listObj->startTable();


if( $num_rows > $limit ) {
	$nCountSaveOrder		= $limit;
}else {
	$nCountSaveOrder		= $num_rows;
}

// these are the columns in the table
$columns = Array(  "#" => "",
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".$num_rows.")\" />" => "",
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_NAME => "width=\"30%\"",
					$VM_LANG->_PHPSHOP_PRODUCT_LIST_SKU => "width=\"15%\"",
					$VM_LANG->_PHPSHOP_CATEGORY => "width=\"15%\"" );
if( $category_id ) {
	//$columns["Reorder"] = "width=\"5%\"";
	$sReorder 	= vmCommonHTML::getSaveOrderButton( $nCountSaveOrder, 'reorderProduct' );
	$columns[$sReorder. " Reorder"] = 'width="8%"';
	echo '<input type="hidden" value="'.$category_id.'" name="category_reorder">';
}

$columns[$VM_LANG->_PHPSHOP_VENDOR_MOD] = "width=\"15%\"";
$columns['Ingredients list'] = "width=\"10%\"";
$columns['SOLD'] = "width=\"10%\"";
$columns[$VM_LANG->_PHPSHOP_REVIEWS] = "width=\"10%\"";
$columns[$VM_LANG->_PHPSHOP_PRODUCT_LIST_PUBLISH] = "width=\"5%\"";
$columns[$VM_LANG->_PHPSHOP_PRODUCT_CLONE] = "";
//$columns["French Version"] = "width=\"5%\"";
$columns[_E_REMOVE] = "width=\"5%\"";
$columns["ID"] = "width=\"5%\"";
$listObj->writeTableHeader( $columns );

if ($num_rows > 0) {

	$db->query($list);
	$i = 0;
	$db_cat = new ps_DB;
	$tmpcell = "";

	while ($db->next_record()) {

		$listObj->newRow();

		// The row number
		$listObj->addCell( $pageNav->rowNumber( $i ) );

		// The Checkbox
		$listObj->addCell( mosHTML::idBox( $i, $db->f("product_id"), false, "product_id" ) );

		// The link to the product form / to the child products
		$tmpcell = "<a href=\"".$sess->url( $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&limitstart=$limitstart&keyword=$keyword&product_id=" . $db->f("product_id")."&product_parent_id=".$product_parent_id )."\">".$db->f("product_name"). "</a>";
		if( $ps_product->parent_has_children( $db->f("product_id") ) ) {
			$tmpcell .= "&nbsp;&nbsp;&nbsp;<a href=\"";
			$tmpcell .= $sess->url($_SERVER['PHP_SELF'] . "?page=$modulename.product_list&product_parent_id=" . $db->f("product_id"));
			$tmpcell .=  "\">[ ".$VM_LANG->_PHPSHOP_PRODUCT_FORM_ITEM_INFO_LBL. " ]</a>";
		}
		$listObj->addCell( $tmpcell );

		// The product sku
		$listObj->addCell( $db->f("product_sku") );

		// The Categories or the parent product's name
		$tmpcell = "";
		if( empty($product_parent_id) ) {
		  $db_cat->query("SELECT #__{vm}_category.category_id, category_name FROM #__{vm}_category,#__{vm}_product_category_xref
							WHERE #__{vm}_category.category_id=#__{vm}_product_category_xref.category_id
							AND #__{vm}_product_category_xref.product_id='".$db->f("product_id") ."'");
		  while($db_cat->next_record()) {
			  $tmpcell .= $db_cat->f("category_name") . "<br/>";
		  }
		}
		else {
		  $tmpcell .= $VM_LANG->_PHPSHOP_CATEGORY_FORM_PARENT .": <a href=\"";
		  $url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&limitstart=$limitstart&keyword=$keyword&product_id=$product_parent_id";
		  $tmpcell .= $sess->url( $url );
		  $tmpcell .= "\">".$ps_product->get_field($product_parent_id,"product_name"). "</a>";
		}
		$listObj->addCell( $tmpcell );

		if( $category_id ) {
			$nProductListOrder	= $db->f('product_list');
			if( empty($nProductListOrder) ) {
				$nProductListOrder	= $db->f("product_id");
			}

			$listObj->addCell( "<input type='text' name='product_list[]' value ='$nProductListOrder' size='4' maxlength='4' style='font:bold 12px Tahoma;text-align:center;' />", "style='vertical-align:top;'" );
		}

		$listObj->addCell( $ps_product->getVendorName($db->f("vendor_id")) );
                
                $listObj->addCell($ps_product->get_il($db->f('product_id')));

        $productSoldOut = !empty($db->f("product_sold_out"));
        $product_sold_out = vmCommonHTML::getYesNoIcon($productSoldOut, "Publish", "Unpublish");
        $listObj->addCell($product_sold_out);

		$db_cat->query("SELECT count(*) as num_rows FROM #__{vm}_product_reviews WHERE product_id='".$db->f("product_id")."'");
		$db_cat->next_record();
		if ($db_cat->f("num_rows")) {
			$tmpcell = $db_cat->f("num_rows")."&nbsp;";
			$tmpcell .= "<a href=\"".$_SERVER["PHP_SELF"]."?option=com_virtuemart&page=product.review_list&product_id=".$db->f("product_id")."\">";
			$tmpcell .= "[".$VM_LANG->_PHPSHOP_SHOW."]</a>";
		}
		else {
			$tmpcell = " - ";
		}
		$listObj->addCell( $tmpcell );

		$tmpcell = "<a href=\"". $sess->url( $_SERVER['PHP_SELF']."?page=product.product_list&category_id=$category_id&product_id=".$db->f("product_id")."&func=changePublishState" );
		if ($db->f("product_publish")=='N') {
			$tmpcell .= "&task=publish\">";
		}
		else {
			$tmpcell .= "&task=unpublish\">";
		}
		$tmpcell .= vmCommonHTML::getYesNoIcon( $db->f("product_publish"), "Publish", "Unpublish" );
		$tmpcell .= "</a>";
		$listObj->addCell( $tmpcell );

		$tmpcell = "<a title=\"".$VM_LANG->_PHPSHOP_PRODUCT_CLONE."\" onmouseout=\"MM_swapImgRestore();\"  onmouseover=\"MM_swapImage('copy_$i','','". IMAGEURL ."ps_image/copy_f2.gif',1);\" href=\"";
		$url = $_SERVER['PHP_SELF'] . "?page=$modulename.product_form&clone_product=1&limitstart=$limitstart&keyword=$keyword&product_id=" . $db->f("product_id");
		if( !empty($product_parent_id) )
			$url .= "&product_parent_id=$product_parent_id";
		$tmpcell .= $sess->url( $url );
		$tmpcell .= "\"><img src=\"".IMAGEURL."/ps_image/copy.gif\" name=\"copy_$i\" border=\"0\" alt=\"".$VM_LANG->_PHPSHOP_PRODUCT_CLONE."\" /></a>";
		$listObj->addCell( $tmpcell );

		//$listObj->addCell( "<a href='index2.php?option=com_joomfish&act=translate&catid=vm_product&select_language_id=2&cid[]=".$db->f("product_id")."|2&task=edit' target='_blank'>Translate</a>" );
		$listObj->addCell( $ps_html->deleteButton( "product_id", $db->f("product_id"), "productDelete", $keyword, $limitstart ) );

		$listObj->addCell( $db->f("product_id") );

		$i++;
	}
}

$listObj->writeTable();

$listObj->endTable();

$listObj->writeFooter( $keyword,  "&product_parent_id=$product_parent_id&category_id=$category_id&product_type_id=$product_type_id&search_date$search_date");

?>