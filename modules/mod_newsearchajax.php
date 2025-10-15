<?php
class mod_newsearchajax
{
    private $productLine = "<table>
                               <tr>
                                   <td rowspan='2'>
                                       <img src='/components/com_virtuemart/shop_image/product/{PRODUCTIMAGE}' width='48' height='56' border='0' />
                                   </td>
                                   <td class='search-line-info'>
                                       <a href='/index.php?page=shop.product_details&category_id={CATEGORYID}&flypage=shop.flypage&product_id={PRODUCTID}&option=com_virtuemart&Itemid=565' >{PRODUCTNAME}</a>
                                   </td>
                               </tr>
                               <tr>
                                <td align='left' class='search-line-info'>
                                    {PRODUCTSKU}
                                </td>
                               </tr>
                           </table>"; 
    
    private $limit = 5;
    private $count = 0;
        
    
    private $container = array();
    private $containerNames = array( 'product_price', 'category_name', 'product_name', 'category_description', 'product_sku', 'product_desc', 'title', 'introtext' );
    
    function __construct() {
        include "../configuration.php";
        $link = mysql_connect( $mosConfig_host, $mosConfig_user, $mosConfig_password );
        if ( !$link ) {
            die( 'Could not connect: ' . mysql_error() );
        }
        if ( !mysql_select_db( $mosConfig_db ) ) {
            die( 'Could not select database: ' . mysql_error() );
        }
    }
    
    function search( $search )
    {
        $search = strtolower( $search );
        $this->searchPrice( $search );
        if( count( $this->container ) < 1 ){
            $this->searchProducts( $search );
            $this->searchContent( $search );
            $this->sortContainer();
        }
        return  ( count( $this->container ) < 1 ) ? json_encode( array( 'result' => "<center>Search returned no results.<center>" ) ) : json_encode( array( 'result' => "success", 'search' => $this->container ) ); 
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
            $this->count++;
            return true;
        }
        /* Other */
        foreach ( $this->containerNames as $key ) {
        if( strpos( strtolower( $row->{$key} ), $search ) !== false ){
                $this->container[$key][] = $line;
                $this->count++;
                break;
            }
        }
    }
    
    private function limit(){
        return ( $this->count < $this->limit ) ? true : false;
    }
    
    private function searchContent( $search )
    {
        $queryConst = "LIKE '%$search%'";
        $query = "SELECT DISTINCT * FROM  jos_content WHERE state = '1'
                    AND ( title $queryConst OR introtext $queryConst )
                  ORDER BY title, introtext ASC";
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) && $this->limit() ) {
                $this->addContainer( $search, $row, "<span class='search-line-info-category'><a href='/index.php?option=com_content&task=view&id={$row->id}' >{$row->title}</a></span>" );
            }
        }
    }
    
    private function searchProducts( $search )
    {
        $productContainer = array();
        $queryConst = "LIKE '%$search%'";
        $query = "SELECT DISTINCT P.product_id, P.product_name, P.product_sku, P.product_desc, P.product_thumb_image, PCX.category_id FROM jos_vm_product AS P 
                  LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = P.product_id
                  INNER JOIN jos_vm_product_price as `pp` ON `pp`.`product_id`=P.product_id
                  WHERE P.product_publish = 'Y'
                     AND ( P.product_name $queryConst 
                     OR P.product_sku $queryConst 
                     OR P.product_desc $queryConst ) AND  P.product_sku NOT LIKE  'AL%' 
                    AND (`pp`.`product_price`-`pp`.`saving_price`)>0
                  ORDER BY P.product_name, P.product_sku, P.product_desc ASC";
        $result = mysql_query( $query );
        if( $result ){
            while ( ($row = mysql_fetch_object( $result )) && $this->limit() ) {
                if( !in_array( $row->product_id, $productContainer ) ){ 
                    $this->addContainer( $search, $row, $this->productLine( $row ) );
                    $productContainer[] = $row->product_id;
                }                
            }
        }
    }
    
    private function searchPrice( $search )
    {
        $line = $this->checkPrice( $search );
        if( !is_null( $line ) ){
            $productContainer = array();
            $searchPrices = explode( '-', $line );
            $query = "SELECT DISTINCT PP.product_id, P.product_name, P.product_thumb_image, P.product_sku, PCX.category_id,
                        (IF(VD.`amount` IS NOT NULL AND UNIX_TIMESTAMP() > VD.`start_date` AND 
                                                                    ( IF(VD.`end_date` > 0, UNIX_TIMESTAMP() < VD.`end_date`, true )), 
                                                                    IF(VD.`is_percent`='1', 
                                                                       PP.`product_price` - PP.`product_price` * VD.`amount`, 
                                                                       PP.`product_price` - VD.`amount`),
                                                                    PP.`product_price`)) AS product_price
                      FROM jos_vm_product_price AS PP
                      LEFT JOIN jos_vm_product_category_xref AS PCX ON PCX.product_id = PP.product_id
                      LEFT JOIN jos_vm_product AS P ON P.product_id = PCX.product_id
                      LEFT JOIN jos_vm_product_discount AS VD ON VD.discount_id = P.product_discount_id
                      WHERE P.product_publish = 'Y' AND product_price";
           if( count( $searchPrices ) > 1 ){
               $query .= " >= '{$searchPrices[0]}' AND product_price <= '{$searchPrices[1]}'";
           }
           else {
               $minPrice = $searchPrices[0] - 2;
               $maxPrice = $searchPrices[0] + 2;
               $query .= " >= '{$minPrice}' AND product_price <= '{$maxPrice}'";
           }
           $query .= " ORDER BY product_price ASC";
           $result = mysql_query( $query );
           if( $result ){
                while ( ($row = mysql_fetch_object( $result )) && $this->limit() ) {
                    if( !$this->limit() ) break;
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
        $productLine = str_replace( "{CATEGORYID}",  $values->category_id,  $this->productLine );
        $productLine = str_replace( "{PRODUCTID}",   $values->product_id,   $productLine );
        $productLine = str_replace( "{PRODUCTNAME}", $values->product_name, $productLine );
        $productLine = str_replace( "{PRODUCTIMAGE}", $values->product_thumb_image, $productLine );
        $productLine = str_replace( "{PRODUCTSKU}", $values->product_sku, $productLine );
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

if( isset( $_POST['search'] ) && strlen( $_POST['search'] ) >= 2 )
{
    $mod_newsearchajax = new mod_newsearchajax();
    $result = $mod_newsearchajax->search( $_POST['search'] );
}

if( is_null( $result ) ){
    $result = "The search query is too small."; // ERROR
}

echo $result;
?>
