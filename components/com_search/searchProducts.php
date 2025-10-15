<?php
class ResultSerchProducts
{       
    // translates
    private $reg_rice = array( "en" => "Bloomex Reg Price", "fr" => "Bloomex Prix courant" );
    private $sale_price = array( "en" => "BLOOMEX SALE PRICE", "fr" => "BLOOMEX PRIX DE VENTE" );
    private $you_save = array( "en" => "YOU SAVE", "fr" => "Vous Г©conomisez" );
    
    private $productLine = "<table class='page-s-table'>
                               <tr>
                                   <td rowspan='3' width='150' valign='top'>
                                       <img src='/components/com_virtuemart/shop_image/product/{PRODUCTIMAGE}' width='150' border='0' />
                                   </td>
                                   <td width='250' class='page-s-red' valign='top'>
                                    <a href='{URL}' target='_blank'>{PRODUCTNAME}</a>
                                   </td>
                                   <td rowspan='3' width='300' valign='top'>
                                    {PRODUCTDESC}
                                   </td>
                                    <td rowspan='3' width='300' class='page-s-content-price' valign='top'>
                                    <span class='search-price'>{PHPSHOP_PRODUCT_RPRICE}:</span><span class='page-s-price-red'>{REGPRICE}</span><br> 
                                    {SALEPRICE}
                                   </td>
                                   <td rowspan='3' valign='top'>
                                   <div class='form-add-cart' id='div_{PRODUCTID}'>
                                    <form action='/index.php' method='post' name='addtocart' id='formAddToCart_{PRODUCTID}'><input type='hidden' name='product_id' value='{PRODUCTID}'>
                                         <input type='hidden' class='inputbox' size='4' id='quantity_{PRODUCTID}' name='quantity_{PRODUCTID}' value='1'>
                                         {atagopen}
                                         <img style='cursor:pointer;' align='absmiddle' class='add-to-cart' name='{PRODUCTID}' border='0' src='components/com_virtuemart/shop_image/ps_image/{IMAGE}.png'>
                                         {atagclose}
                                         <input type='hidden' name='product_id_{PRODUCTID}' value='{PRODUCTID}'>
                                         <input type='hidden' name='category_id_{PRODUCTID}' value='{CATEGORYID}'>
                                         <input type='hidden' name='price_{PRODUCTID}' value='{PRODPRICE}'>
                                    </form>
                                    </div>
                                   </td>
                               </tr>
                               <tr>
                                <td align='left' valign='top'>
                                    ({CATEGORYNAME})
                                </td>
                               </tr>
                               <tr>
                                <td align='left' valign='top'>
                                    {PRODUCTSKU}
                                </td>
                               </tr>
                           </table>
                           
<div class='search_product_mobile product-list' style='display:none;width:23%;'>
                                    <a class='product-title' href='/index.php?page=shop.product_details&category_id={CATEGORYID}&flypage=shop.flypage&product_id={PRODUCTID}&option=com_virtuemart&Itemid=565' target='_blank'>{PRODUCTNAME}</a>
                                    <span class='sku-code'>{PRODUCTSKU}</span>
                                    <span class='price'>{REGPRICE}</span>
                                    <div class='product-image'>
                                       <img src='/components/com_virtuemart/shop_image/product/{PRODUCTIMAGE}' height='262' width='225' border='0' />
                                   </div>
                                   <div class='form-add-cart' id='mobile_add_cart_{PRODUCTID}'>
                                        <form action='/index.php' method='post' name='addtocart' id='formAddToCart_{PRODUCTID}'><input type='hidden' name='product_id' value='{PRODUCTID}'>
                                         <input type='hidden' class='inputbox' size='4' id='quantity_{PRODUCTID}' name='quantity_{PRODUCTID}' value='1'>
                                         {atagopen}
                                         <div style='display:none' align='absbottom' class='img_add_cart add-to-cart' name='{PRODUCTID}'>Add to Cart</div>
                                         <input type='hidden' name='product_id_{PRODUCTID}' value='{PRODUCTID}'>
                                         <input type='hidden' name='category_id_{PRODUCTID}' value='{CATEGORYID}'>
                                         <input type='hidden' name='price_{PRODUCTID}' value='{PRODPRICE}'>
                                        </form>
                                        

                           </div><br></div>

"; 
    
    private $count = 0;
    
    private $addToCart = '';
        
    
    private $containerNames = array( 'product_name_fr','product_desc_fr','product_price', 'category_name', 'product_name', 'category_description', 'product_sku', 'product_desc', 'title', 'introtext' );
    private $container = array();
    
    
    function search( $search )
    {
        $search = strtolower( $search );
        $this->searchPrice( $search );
        if( count( $this->container ) < 1 ){
            $this->searchProducts( $search );
            $this->searchContent( $search );
           // $this->sortContainer();
        }
        return ( count( $this->container ) < 1 ) ? "<center>Search returned no results.<center>" : $this->container; 
    }
    
    private function sortContainer()
    {
        $container = array();
        foreach ( $this->containerNames as $value)  {
            if(array_key_exists( $value, $this->container ) ) $container[$value] = $this->container[$value];
        }
        $this->container = $container;
    }
    
    private function addContainer( $search, $row, $line )
    {
        /* Price */
        if( is_null( $search ) && !is_array( $row ) ){
            $this->container[$row][] = $line;
            return true;
        }
        /* Other */
        $search_a = explode(' ', $search);
        foreach ( $this->containerNames as $key ) {
            foreach ($search_a as $search_one) {
                if( strpos( strtolower( $row->{$key} ), $search_one ) !== false ){
                        $this->container[$key][] = $line;
                        break;
                    }
                }
        }
    }
    
    private function searchContent( $search )
    {
        $queryConst = "LIKE '%$search%'";
        $query = "SELECT DISTINCT * FROM  jos_content WHERE state = '1'
                    AND ( title $queryConst OR introtext $queryConst )
                  ORDER BY title, introtext ASC";
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) ) {
                $this->addContainer( $search, $row, "<table class='page-s-table'><tr><td><span class='search-line-info-category'><a href='/index.php?option=com_content&task=view&id={$row->id}' target='_blank'>{$row->title}</a></span></td></tr></table>" );
            }
        }
    }
    
    private function searchProducts( $search )
    {        
        global $database;
        
        require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_product.php';

        $ps_product = new ps_product;
        
        $query = "SELECT 
            `p`.`product_id`
            FROM (
                `jos_vm_product` AS `p`
            ) 
            WHERE 
                `p`.`product_parent_id`='0' 
                AND 
                `p`.`product_publish`='Y' 
                AND 
                ( 
                    `p`.`product_publish` LIKE '%".$search."%' 
                    OR
                    `p`.`product_sku` LIKE '%".$search."%' 
                    OR
                    `p`.`product_desc` LIKE '%".$search."%' 
                )
            ORDER BY RAND() LIMIT 0, 16";

        $database->setQuery($query);
        $products_obj = $database->loadObjectList();
        shuffle($products_obj);
        $products = array();

        foreach ($products_obj as $product_obj) {
            $products[] = $product_obj->product_id;
        }

        echo $ps_product->show_product_list($products);
        
        /*
        global $mosConfig_lang;
    
    
       $q = "SELECT category_id FROM jos_vm_category_unsearchable";
        $result_cat = mysql_query( $q );
        $str = "('0',";
        if(mysql_num_rows($result_cat)>0){
             while ( $row_cat = mysql_fetch_assoc( $result_cat )) {
                 $str.="'".$row_cat['category_id']."',";
             }
        }
        $str = rtrim($str, ",");
        $str .= ")";
    
    
        $productContainer = array();
        $queryConst = "LIKE '%$search%'";
        $search_a = explode(' ', $search);
        
        $query = "SELECT DISTINCT PP.product_id, P.product_name,";
           if($mosConfig_lang ==  'french') {
            $query .= "j.value as product_name_fr ,f.Value as product_desc_fr,";
           }
            $query .= "P.product_desc, P.product_thumb_image, P.product_sku, PCX.category_id, CAT.category_name, PP.`product_price` AS start_product_price,
                        (IF(VD.`amount` IS NOT NULL AND UNIX_TIMESTAMP() > VD.`start_date` AND 
                                                                    ( IF(VD.`end_date` > 0, UNIX_TIMESTAMP() < VD.`end_date`, true )), 
                                                                    IF(VD.`is_percent`='1', 
                                                                       PP.`product_price` - PP.`product_price` * VD.`amount`, 
                                                                       PP.`product_price` - VD.`amount`),
                                                                    PP.`product_price`) - PP.saving_price) AS product_price
                      FROM jos_vm_product_price AS PP
                      LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = PP.product_id
                      LEFT JOIN jos_vm_product AS P ON P.product_id = PCX.product_id
                      LEFT JOIN jos_vm_product_discount AS VD ON VD.discount_id = P.product_discount_id
                      LEFT JOIN jos_vm_category AS CAT ON CAT.category_id = PCX.category_id";
                   if($mosConfig_lang ==  'french') {
                   $query .= " LEFT JOIN jos_jf_content  as f ON f.reference_id=PP.product_id AND f.reference_table = 'vm_product' AND f.reference_field = 'product_desc' ";
                   $query .= " LEFT JOIN jos_jf_content  as j ON j.reference_id=PP.product_id AND j.reference_table = 'vm_product' AND  j.reference_field = 'product_name' ";
}

            $where_a = array();
            
            foreach ($search_a as $search_a_one) {
                $where_a[] = "P.product_name LIKE '%$search_a_one%'";
            }
            
            $where = implode(' OR ', $where_a);

              $query .= " WHERE P.product_publish = 'Y' AND  P.product_name NOT LIKE  '%free%' 
                     AND (".$where.") group by PP.product_id 
                  ORDER BY product_price, P.product_name, P.product_sku, P.product_desc ASC";
              
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) ) {
                if( !in_array( $row->product_id, $productContainer ) ){ 
                    $query_funeral = "SELECT * from jos_vm_product_category_xref where product_id={$row->product_id} AND category_id  IN {$str} ";
                    $result_funeral = mysql_query( $query_funeral );
                   if(mysql_num_rows($result_funeral)==0){
                        $this->addContainer( $search, $row, $this->productLine( $row ) );
                        $productContainer[] = $row->product_id;
                    }
                }                
            }
        }

        $query = "SELECT DISTINCT PP.product_id, P.product_name,";
           if($mosConfig_lang ==  'french') {
            $query .= "j.value as product_name_fr ,f.Value as product_desc_fr,";
           }
            $query .= "P.product_desc, P.product_thumb_image, P.product_sku, PCX.category_id, CAT.category_name, PP.`product_price` AS start_product_price,
                        (IF(VD.`amount` IS NOT NULL AND UNIX_TIMESTAMP() > VD.`start_date` AND 
                                                                    ( IF(VD.`end_date` > 0, UNIX_TIMESTAMP() < VD.`end_date`, true )), 
                                                                    IF(VD.`is_percent`='1', 
                                                                       PP.`product_price` - PP.`product_price` * VD.`amount`, 
                                                                       PP.`product_price` - VD.`amount`),
                                                                    PP.`product_price`) - PP.saving_price) AS product_price
                      FROM jos_vm_product_price AS PP
                      LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = PP.product_id
                      LEFT JOIN jos_vm_product AS P ON P.product_id = PCX.product_id
                      LEFT JOIN jos_vm_product_discount AS VD ON VD.discount_id = P.product_discount_id
                      LEFT JOIN jos_vm_category AS CAT ON CAT.category_id = PCX.category_id";
                   if($mosConfig_lang ==  'french') {
                   $query .= " LEFT JOIN jos_jf_content  as f ON f.reference_id=PP.product_id AND f.reference_table = 'vm_product' AND f.reference_field = 'product_desc' ";
                   $query .= " LEFT JOIN (SELECT reference_id,value FROM jos_jf_content where `reference_table`='vm_product'  AND `reference_field`='product_name') as j on `j`.`reference_id`=`PP`.`product_id`  ";
                   }
                $where_a = array();

                foreach ($search_a as $search_a_one) {
                    $where_a[] = "P.product_sku LIKE '%$search_a_one%'";
                    $where_a[] = "`j`.`value` LIKE '%".$search_a_one."%'";
                }

                $where = implode(' OR ', $where_a);

              $query .= " WHERE P.product_publish = 'Y' AND  P.product_name NOT LIKE  '%free%' 
                     AND (".$where.") group by PP.product_id 
                  ORDER BY product_price, P.product_name, P.product_sku, P.product_desc ASC";
       
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) ) {
                if( !in_array( $row->product_id, $productContainer ) ){ 
                      $query_funeral = "SELECT * from jos_vm_product_category_xref where product_id={$row->product_id} AND category_id  IN {$str} ";
                    $result_funeral = mysql_query( $query_funeral );
                   if(mysql_num_rows($result_funeral)==0){
                        $this->addContainer( $search, $row, $this->productLine( $row ) );
                        $productContainer[] = $row->product_id;
                    }
                    
                    
                }                
            }
        }
        
        
                $query = "SELECT DISTINCT PP.product_id, P.product_name,";
           if($mosConfig_lang ==  'french') {
            $query .= "j.value as product_name_fr ,f.Value as product_desc_fr,";
           }
            $query .= "P.product_desc, P.product_thumb_image, P.product_sku, PCX.category_id, CAT.category_name, PP.`product_price` AS start_product_price,
                        (IF(VD.`amount` IS NOT NULL AND UNIX_TIMESTAMP() > VD.`start_date` AND 
                                                                    ( IF(VD.`end_date` > 0, UNIX_TIMESTAMP() < VD.`end_date`, true )), 
                                                                    IF(VD.`is_percent`='1', 
                                                                       PP.`product_price` - PP.`product_price` * VD.`amount`, 
                                                                       PP.`product_price` - VD.`amount`),
                                                                    PP.`product_price`) - PP.saving_price) AS product_price
                      FROM jos_vm_product_price AS PP
                      LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = PP.product_id
                      LEFT JOIN jos_vm_product AS P ON P.product_id = PCX.product_id
                      LEFT JOIN jos_vm_product_discount AS VD ON VD.discount_id = P.product_discount_id
                      LEFT JOIN jos_vm_category AS CAT ON CAT.category_id = PCX.category_id";
        if($mosConfig_lang ==  'french') {
            $query .= " LEFT JOIN jos_jf_content  as f ON f.reference_id=PP.product_id AND f.reference_table = 'vm_product' AND f.reference_field = 'product_desc' ";
            $query .= " LEFT JOIN (SELECT reference_id,value FROM jos_jf_content where `reference_table`='vm_product'  AND `reference_field`='product_name') as j on `j`.`reference_id`=`PP`.`product_id`  ";
        }
                $where_a = array();

                foreach ($search_a as $search_a_one) {
                    $where_a[] = "P.product_desc LIKE '%$search_a_one%'";
                    $where_a[] = "`j`.`value` LIKE '%".$search_a_one."%'";
                }

                $where = implode(' OR ', $where_a);

              $query .= " WHERE P.product_publish = 'Y' AND  P.product_name NOT LIKE  '%free%' 
                     AND (".$where.") group by PP.product_id 
                  ORDER BY product_price, P.product_name, P.product_sku, P.product_desc ASC";
       
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) ) {
                if( !in_array( $row->product_id, $productContainer ) ){ 
                      $query_funeral = "SELECT * from jos_vm_product_category_xref where product_id={$row->product_id} AND category_id  IN {$str} ";
                    $result_funeral = mysql_query( $query_funeral );
                   if(mysql_num_rows($result_funeral)==0){
                        $this->addContainer( $search, $row, $this->productLine( $row ) );
                        $productContainer[] = $row->product_id;
                    }
                    
                    
                }                
            }
        }
        */
    }
    
    
    private function searchPrice( $search )
    {
        $line = $this->checkPrice( $search );
        if( !is_null( $line ) ){
            $productContainer = array();
            $searchPrices = explode( '-', $line );
            $wherePrice = "(IF(VD.`amount` IS NOT NULL AND UNIX_TIMESTAMP() > VD.`start_date` AND ( IF(VD.`end_date` > 0, UNIX_TIMESTAMP() < VD.`end_date`, true )), IF(VD.`is_percent`='1', PP.`product_price` - PP.`product_price` * VD.`amount`, PP.`product_price` - VD.`amount`), PP.`product_price`) - PP.saving_price)";
            $query = "SELECT DISTINCT PP.product_id, P.product_name, P.product_desc, P.product_thumb_image, P.product_sku, PCX.category_id, CAT.category_name, PP.`product_price` AS start_product_price,
                        $wherePrice AS product_price
                      FROM jos_vm_product_price AS PP
                      LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = PP.product_id
                      LEFT JOIN jos_vm_product AS P ON P.product_id = PCX.product_id
                      LEFT JOIN jos_vm_product_discount AS VD ON VD.discount_id = P.product_discount_id
                      LEFT JOIN jos_vm_category AS CAT ON CAT.category_id = PCX.category_id
                      WHERE P.product_publish = 'Y' AND $wherePrice";
           if( count( $searchPrices ) > 1 ){
               $query .= " >= '{$searchPrices[0]}' AND $wherePrice <= '{$searchPrices[1]}'";
           }
           else {
               $minPrice = $searchPrices[0] - 2;
               $maxPrice = $searchPrices[0] + 2;
               $query .= " >= '{$minPrice}' AND $wherePrice <= '{$maxPrice}'";
           }
           $query .= " ORDER BY product_price ASC";
           $result = mysql_query( $query );
           if( $result ){
                while ( ($row = mysql_fetch_object( $result )) ) {
                    if( !in_array( $row->product_id, $productContainer ) ){ 
                        $this->addContainer( null, 'product_price', $this->productLine( $row ) );
                        $productContainer[] = $row->product_id;
                    } 
                }
            }
        }
    }
    
    private function productLine( $values )
    {
        global $iso_client_lang, $database,$mosConfig_lang,$mosConfig_absolute_path,$mm_action_url;
        $sql = "SELECT pick_up FROM #__vm_product_options WHERE product_id = " . $values->product_id;
        $database->setQuery($sql);
        $pick_up = $database->loadResult();
        require_once( $mosConfig_absolute_path."/components/com_virtuemart/virtuemart_parser.php");
        $sess = new ps_session;

            $atagopen = '';
            $atagclose = '';
             if($mosConfig_lang ==  'french') {
                 $product_name = $values->product_name_fr?$values->product_name_fr:$values->product_name;
                 $product_desc = $values->product_desc_fr?$values->product_desc_fr:$values->product_desc;
                 $addtocart = 'new_button2_fr';
                 $lang='fr';
             }  else{
                 $lang='en';
                 $product_name = $values->product_name;
                 $product_desc = $values->product_desc;
                 $addtocart = 'new_button2';
             }
             if(isset($sess)){

             $url="/index.php?page=shop.product_details&category_id={$values->category_id}&flypage=shop.flypage&lang={$lang}&product_id={$values->product_id}&option=com_virtuemart&Itemid=565";
            $url = $sess->url($mm_action_url . "index.php" . $url);
             }

        $productLine = str_replace( "{CATEGORYID}",  $values->category_id,  $this->productLine );
        $productLine = str_replace( "{PRODUCTID}",   $values->product_id,   $productLine );
        $productLine = str_replace( "{PRODUCTNAME}", $product_name, $productLine );
        $productLine = str_replace( "{PRODUCTDESC}", $product_desc, $productLine );
        $productLine = str_replace( "{PRODUCTIMAGE}", $values->product_thumb_image, $productLine );
        $productLine = str_replace( "{PRODUCTSKU}", $values->product_sku, $productLine );
        $productLine = str_replace( "{PHPSHOP_PRODUCT_RPRICE}", $this->reg_rice[$iso_client_lang], $productLine );
        $productLine = str_replace( "{REGPRICE}", "".LangNumberFormat::number_format( $values->start_product_price, 2, ".", " " ), $productLine);
        $productLine = str_replace( "{atagopen}",   $atagopen,   $productLine );
        $productLine = str_replace( "{atagclose}",   $atagclose,   $productLine );
        $productLine = str_replace( "{IMAGE}", $addtocart,  $productLine );
        $productLine = str_replace( "{URL}",  $url,  $productLine );
        $youSave = $values->start_product_price - $values->product_price;
        $salePrice = "";
        $prodPrice = ( $youSave > 0 ) ? $values->product_price : $values->start_product_price;
        if( $youSave > 0 ){
            $youSave = "".LangNumberFormat::number_format( ($values->start_product_price - $values->product_price), 2, ".", " " );
            $salePrice = "<span class='search-price'>{$this->sale_price[$iso_client_lang]}:</span>". "".LangNumberFormat::number_format($values->product_price, 2, ".", " ") ."<br>
                          <span class='search-price'><span class='page-s-price-red'>{$this->you_save[$iso_client_lang]}:</span></span>$youSave<br>";
        }
        
        $productLine = str_replace( "{PRODPRICE}", $prodPrice, $productLine );
        $productLine = str_replace( "{SALEPRICE}", $salePrice, $productLine );
        $productLine = str_replace( "{CATEGORYNAME}", $values->category_name, $productLine );
        return $productLine;
    }
    
    private function checkPrice( $search )
    {
        $search = preg_replace( "/[ \$]/s", "", $search );
        $search = str_replace( ',', '.', $search );
        $test1 = preg_replace( "/[^0-9.,-]/s", "", $search );
        $test2 = preg_replace( "/[^0-9]/s", "", $search );
        if( strlen( $test2 ) > 0 && strlen( $test1 ) == strlen( $search ) ){
            return $search;
        }   
        return null;
    }
}

$result = null;

if( isset( $_REQUEST['searchword'] ) && strlen( $_REQUEST['searchword'] ) >= 2 )
{
    $ResultSerchProducts = new ResultSerchProducts();
    $result = $ResultSerchProducts->search( $_REQUEST['searchword'] );
}

if( is_null( $result ) ){
    $result = "The search query is too small."; // ERROR
}

if( is_array( $result ) ){
    $html = "<table>";
    foreach ($result as $value) {
        foreach ($value as $item) {
            $html .= "<tr><td>$item</td></tr>";
        }
    }
    $html .= "</html>";
    echo $html;
}
else{
    echo $result;
}
?>
