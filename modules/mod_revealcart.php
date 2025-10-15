<?php
class CartValue{
    function value(){
        if( !is_array($_SESSION["cart"]) ) return modulesLanguage::get('newCartRevealEmpty');
        $items = 0;
        $total = 0;
        foreach ( $_SESSION["cart"] as $key => $value ) {
            if( $key !== 'idx' ){
                $items++;
                $price = $_SESSION["cart"][$key]['price'];
                $quantity = (int)$_SESSION["cart"][$key]['quantity'];
                $total += round( $price * $quantity, 2 );
            }
        }
        if( $items < 1 ) return modulesLanguage::get('newCartRevealEmpty');
        return "$items ".modulesLanguage::get('CartValue')." - ".LangNumberFormat::number_format($total, 2, '.', '');
    }
}
?>
<div id="newCart">
    <img alt="mini cart" src="/templates/bloomex7/images/miniCartImage.png" /><span class="cartValue"><?php echo CartValue::value(); ?></span>&nbsp;
    <a href="/index.php?page=shop.cart&option=com_virtuemart&Itemid=80&lang=en">
    <span class="newCart-checkout"><?php echo modulesLanguage::get('newCart_checkout'); ?><!--&nbsp;&nbsp; <span class="triangle"></span>--></span>
    </a>
    <div id="newCartClick"><?php echo modulesLanguage::get('newCartClick'); ?></div>
    <div id="newCartRevealClose"></div>
    <table id="newCartReveal" cellspacing="0"></table>
</div>
<?php
class RevealCart
{
    public $titles = array( 'Name', 'Image', 'SKU', 'Price', 'Quantity', 'Subtotal' );
    private $line = "<table id='cart-line-product'>
                        <tr>
                            <td rowspan='4' width='42'>{IMAGE}</td>
                            <td>{NAME}</td>
                            <td rowspan='2' width='70' align='center'><span class='new-cart-reveal-total-once'>{TOTAL}</span></td>
                        </tr>
                        <tr>
                            <td>{SKU}</td>                            
                        </tr>
                        <tr>
                            <td>{PRICE}</td>
                            <td rowspan='2' align='center' valign='bottom'><img alt='delete element' src='/images/deleteElementCart.png' width='18' height='18' border='0' onclick='deleteElementCartFunc({DELETE});' class='standart' /></td>
                        </tr>
                        <tr>
                            <td>
                            <div id='updateElementCartContent'>
                                <div id='updateElementCart'>
                                    <div id='left' onclick='updateQuantity({DELETE}, -1)'>
                                        ˅
                                    </div>
                                    <div id='center' class='count-element-{DELETE}'>
                                        {CART}
                                    </div>
                                    <div id='right' onclick='updateQuantity({DELETE}, 1)'>
                                        ˄
                                    </div>
                                </div>
                                <img src='/images/{UPDATEIMG}' onclick='updateElementCartFunc({DELETE})' />
                                </div>
                            </td>
                        </tr>
                    </table>";
    
    private $total = 0;
    private $header = '{COUNTITEMS} item in Cart';
    
    private $endl = "<table align='center' class='cart-product-endl'>
                        <tr>
                            <td>
                                <a href='/index.php?page=shop.cart&amp;option=com_virtuemart&amp;Itemid=80&amp;lang=en'>
                                  <span class='newCart-checkout'>{ORDERNOW}<!--&nbsp;&nbsp; <span class='triangle'></span>--></span>
                                </a>
                            </td>
                        </tr>
                    </table>";
    
    function checkPost(){
        if( isset( $_POST['getCart'] ) && $_POST['getCart'] == true ){
            ob_get_clean();
            echo json_encode( array( 'cart' => $this->getProducts(), 'endl' => str_replace("{ORDERNOW}", modulesLanguage::get('getCartPost'), $this->endl), 'total' => $this->total, 'header' => $this->header ) );
            require_once 'end_access_log.php';
            die();
        }
    }
    
    function checkPage(){
        return ( $_GET['page'] == 'shop.cart' ) ? 1 : 0;
    }
    
    private function getProducts(){
        global $iso_client_lang;
        if( isset( $_POST['lang'] ) && strlen($_POST['lang']) > 0 ) $iso_client_lang = $_POST['lang'];
        $products = array();
        $line = 0;
        $quantityItems = 0;
        $line_product_ids = array();
        $line_product_id = null;
        if( !is_array( $_SESSION["cart"] ) ) return $products;
        foreach ( $_SESSION["cart"] as $key => $value ) {
            if( $key !== 'idx' ){
                $product_id = $_SESSION["cart"][$key]['product_id'];
                $line_product_id .= ( is_null( $line_product_id ) ? '' : ',' ) . $product_id;
                $line_product_ids[$line] = $product_id;
                $price = $_SESSION["cart"][$key]['price'];
                $quantity = (int)$_SESSION["cart"][$key]['quantity'];
                $total = round( $price * $quantity, 2 );
                $this->total += $total;
                $quantityItems += $quantity;
                $products[$line] = str_replace( "{PRICE}", LangNumberFormat::number_format($price, 2, '.', ''), $this->line );
                $products[$line] = str_replace( "{UPDATEIMG}",  modulesLanguage::get('cartButtonUpdate'), $products[$line] );
                $products[$line] = str_replace( "{CART}",  $quantity, $products[$line] );
                $products[$line] = str_replace( "{TOTAL}", LangNumberFormat::number_format($total, 2, '.', ''), $products[$line] );
                $line++;
            }
        }

        $this->header = str_replace( '{COUNTITEMS}', $quantityItems, $this->header );
        $this->header = str_replace( '{COUNTPRODUCTS}', $line, $this->header );
        $this->total = LangNumberFormat::number_format( $this->total, 2, '.', '' );
        
        $selectProducts = $this->selectProduct( $line_product_id );
        for ($index = 0; $index < $line; $index++) {
            $product_id = $line_product_ids[$index];
            $selectProducts[$product_id]['image'] = "<img src='/components/com_virtuemart/shop_image/product/{$selectProducts[$product_id]['image']}' width='42' height='49' border='0'>";
            $products[$index] = str_replace( "{NAME}",      $selectProducts[$product_id]['name'],   $products[$index] );
            $products[$index] = str_replace( "{IMAGE}",     $selectProducts[$product_id]['image'],  $products[$index] );
            $products[$index] = str_replace( "{SKU}",       $selectProducts[$product_id]['sku'],    $products[$index] );
            $products[$index] = str_replace( "{DELETE}",    $product_id,                            $products[$index] );
        }
        return $products;
    }
    
    private function selectProduct( $line_product_id ){
        global $database;
        if( is_null( $line_product_id ) ) return null;
        $products = array();
        $query = "SELECT product_id, product_sku, product_thumb_image, product_name 
                FROM  jos_vm_product WHERE product_id IN ($line_product_id)";
        $database->setQuery( $query );
        $result = $database->loadObjectList();
        if( $result ){
            foreach ( $result as $key ) {
                $products[$key->product_id]['sku'] = $key->product_sku;
                $products[$key->product_id]['image'] = $key->product_thumb_image;
                $products[$key->product_id]['name'] = $key->product_name;  
            }
        }
        return $products;
    }
}
global $iso_client_lang;
$RevealCart = new RevealCart();
$RevealCart->checkPost();

?>
<script>
    var cartLink = '/index.php?page=shop.cart&option=com_virtuemart&Itemid=80&lang=en';
    var titles = ("<?php echo implode(',', $RevealCart->titles) ?>").split(',');
    var checkPage = <?php echo $RevealCart->checkPage(); ?>;
    
    $j('#newCartClick').click(function(){
        getCart();
    });
    
    
    $j('#newCart #newCartRevealClose').click(function(){
        $j('#newCartReveal').css('display', 'none');
        $j('#newCart #newCartRevealClose').css('display', 'none');
    });

    function getCart(){ 
        $j('#newCartReveal').html(headerTriangle()+'<tr><td width="'+$j('#newCartReveal').css('width')+'" align="center"><?php echo modulesLanguage::get('newCartRevealLoad'); ?>...</td></tr>');
        $j('#newCartReveal').css('display', 'block');
        $j('#newCart #newCartRevealClose').css('display', 'block');
        $j.post( cartLink,
                {
                    getCart: true,
                    lang: '<?php echo $iso_client_lang; ?>'
                },
                function(data){
                    var issetData = false;
                    var countLines = 0;
                    var countLinesLimit = 5;
               //     console.log(data.cart);
                    $j('#newCartReveal').html(headerTriangle());
                    $j.each(data.cart, function(key,item){
                        if( item.length > 1 ){
                            if( countLines < countLinesLimit ){
                                if( !issetData ) $j('#newCartReveal').append('<tr><td>'+data.header+'</td></tr>');
                                $j('#newCartReveal').append('<tr><td>'+item+'</td></tr>');
                            }
                            else{
                                $j('#newCartReveal').append('<tr><td width="100%" colspan="'+titles.length+'" align="center"><a href="'+cartLink+'"><?php echo modulesLanguage::get('newCartReveal'); ?>...</a></td></tr>');
                                return false;
                            }
                            countLines++;
                        }
                        issetData = true;
                    });
                    cartValue(data.cart.length, data.total);
                    if( !issetData ){
                        $j('#newCartReveal').html(headerTriangle()+'<tr><td width="'+$j('#newCartReveal').css('width')+'" align="center"><center><?php echo modulesLanguage::get('newCartRevealEmpty'); ?>.</center></td></tr>');
                    }
                    else{
                        $j('#newCartReveal').append('<tr><td class="new-cart-reveal-total"><?php echo modulesLanguage::get('newCartRevealTotal'); ?>: <span class="new-cart-reveal-total-price">'+data.total+'</span></td></tr>');
                        $j('#newCartReveal').append('<tr><td>'+data.endl+'</td></tr>');
                    }
                },
                'json'
        ); 
    }
    
    function headerTriangle(){
        return '<tr><td id="newCartReveal-top"><div id="newCartReveal-top-triangle"></div></td></tr>';
    }
    
    function updateQuantity(id, value){
        newValue = parseInt($j.trim($j('.count-element-'+id).html())) + value;
        if( newValue <= 9999 && newValue > 0 ) $j('.count-element-'+id).html(newValue);
    }
    
    function updateElementCartFunc(id){
        $j.post(cartLink,
                {
                    option:     'com_virtuemart',
                    page:       'shop.cart',
                    func:       'cartUpdate',
                    action:     'ajax',
                    product_id: id,
                    quantity:   $j.trim($j('.count-element-'+id).html()),
                    ajaxSend:   'undefined',
                    lang: '<?php echo $iso_client_lang; ?>'
                },
                function(data){
                    if( checkPage ) location.reload();
                    getCart();
                }
            );
    }
    
    function deleteElementCartFunc(id){
        $j.post(cartLink,
                {
                    option:     'com_virtuemart',
                    page:       'shop.cart',
                    func:       'cartDelete',
                    action:     'ajax',
                    product_id: id,
                    ajaxSend:   'undefined',
                    lang: '<?php echo $iso_client_lang; ?>'
                },
                function(data){
                    if( checkPage ) location.reload();
                    getCart();
                }
            );
        }
    
    function cartValue(items, total){
        $j('.cartValue').html(( ( items < 1 ) ? '<?php echo modulesLanguage::get('newCartRevealEmpty'); ?>' : items+" <?php echo modulesLanguage::get('CartValue'); ?> - "+total ));
    }
</script>