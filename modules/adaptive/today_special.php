<?php
global $database,$showOnlyJpegImageVersion,$mosConfig_aws_s3_bucket_public_url, $iso_client_lang,$cur_template,$VM_LANG, $mosConfig_live_site,$sef,$mosConfig_today_specials_category,$mosConfig_show_compare_at_price;

$query = "SELECT 
            `c`.`category_id`, 
            `c`.`category_name`,
            `p`.`product_id`, 
            `p`.`product_name`, 
            `p`.`product_sku`, 
            `po`.`product_sold_out`,
            `po`.`product_out_of_season`,
            `p`.`product_thumb_image`, 
            `s`.`medium_image_link_jpeg`,
            `s`.`medium_image_link_webp`,
            `p`.`alias`, 
            `pp`.`product_price`,
            `pp`.`discount_for_customer`,
             `pm`.`discount` as promotion_discount,
            CASE 
                WHEN pm.discount is not null  THEN (`pp`.`product_price`-`pp`.`saving_price`) - ((`pp`.`product_price`-`pp`.`saving_price`) * pm.discount/100)
                ELSE (`pp`.`product_price`-`pp`.`saving_price`) 
            END AS `product_real_price`,
            `c`.`category_flypage`, 
            `pm`.`end_promotion`,
            `c`.`category_id`, 
            `c`.`alias` AS 'category_alias', 
            `fr`.`rating`, 
            `fr`.`review_count`       
        FROM `jos_vm_product` AS `p`
            LEFT JOIN `jos_vm_product_price` AS `pp` ON `pp`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_s3_images` AS `s` ON `s`.`product_id`=`p`.`product_id`
            LEFT JOIN (SELECT 
                            CASE 
                                WHEN pmp.category_id > 0  THEN x.product_id
                                ELSE pmp.product_id
                            END AS `product_id`,pmp.discount,pmp.end_promotion
                            FROM `jos_vm_products_promotion` as pmp 
            left join jos_vm_product_category_xref as x on x.category_id = pmp.category_id
            WHERE pmp.public = 1  and ((CURRENT_DATE BETWEEN pmp.start_promotion AND pmp.end_promotion) OR (WEEKDAY(NOW()) = pmp.week_day)) GROUP by product_id) as pm on pm.product_id = p.product_id
            LEFT JOIN `jos_vm_product_options` AS `po` ON `po`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_product_category_xref` AS `cx` ON `cx`.`product_id`=`p`.`product_id`
            LEFT JOIN `jos_vm_category` AS `c` ON `c`.`category_id`=`cx`.`category_id`
            LEFT JOIN `tbl_product_fake_reviews` AS `fr` ON `fr`.`product_id`=`p`.`product_id`
            where  `p`.`product_publish`='Y'  AND `c`.`category_id`=$mosConfig_today_specials_category order by RAND() LIMIT 1";
$database->setQuery($query);
$product=false;
$database->loadObject($product);
if($product){
$product_old_price = number_format(round($product->product_price, 2), 2, '.', '');
$product_real_price = number_format(round($product->product_real_price, 2), 2, '.', '');
if(isset($_SESSION['enableSpecialDiscountInProductsForCustomer'])){
    $product_real_price = round($product_real_price - $product_real_price*$product->discount_for_customer/100,2);
}
$savingPrice = $product_old_price - $product_real_price;
$product_rating = round($product->rating, 1);
$link = $sef->getCanonicalProductById($product->product_id);
$canonical = true;

Switch ($mosConfig_lang) {
    case 'french':
        $todaySpecialTitle = "Spéciale du Jour";
        break;

    case 'english':
    default:
        $todaySpecialTitle = "Today's Special";
        break;
}
?>
<div class='today_special'>
    <div class="today_special_head"><?php echo $todaySpecialTitle; ?><span class="close_today_special">X</span></div>
    <div class="products">
        <div class="wrapper" price_ordering="<?php echo $product_real_price; ?>" rating_ordering="<?php echo $product_rating; ?>">
            <div class="inner">
                <?php
                if($product->promotion_discount) {

                    if(date("Y-m-d") == $product->end_promotion || $product->end_promotion == '0000-00-00') {
                        echo '<span class="promotion_product" style="display: block">TODAY\'S SALE</span>';
                    } else {
                        echo '<div class="new promotion_product">
                                <span>Sale Ends In: </span> 
                                <span class="promotion_countdown promotion_product_' . $product->product_id . '" product_id="' . $product->product_id . '" date_end="' . date("m/d/Y", strtotime($product->end_promotion)) . '"></span>
                            </div>';
                    }
                }
                ?>
                <a class="product-link" <?php echo (($canonical == true) ? '' : 'rel="nofollow"'); ?> href="<?php echo $link; ?>">
                    <div class="product-image">
                        <img class="product_image_real" src="<?php echo $mosConfig_aws_s3_bucket_public_url . ($showOnlyJpegImageVersion ? $product->medium_image_link_jpeg : $product->medium_image_link_webp); ?>" alt="name: <?php echo $product->product_name; ?>">
                    </div>
                    <span class="product-title text-center"><?php echo $product->product_name; ?></span>
                </a>
                <?php
                if ($product_old_price != $product_real_price && $mosConfig_show_compare_at_price) {
                    ?>
                    <div style="font-size: 15px">Compare at: <span class="old_price"><s>$<?php echo $product_old_price; ?></s></span></div>
                    <?php
                }
                ?>

                <div style="font-size: 14px;color: #A40001;font-weight: bold;">Bloomex Price: <span class="price">$<?php echo $product_real_price; ?></span></div>
                <?php if ($product->product_out_of_season) { ?>
                    <div class="add" product_id="<?php echo $product->product_id; ?>"><?php echo ($mosConfig_lang == 'french') ? 'Hors-Saison' : 'Out Of Season'; ?></div>
                <?php } elseif ($product->product_sold_out) { ?>
                    <div class="add" product_id="<?php echo $product->product_id; ?>"><?php echo ($mosConfig_lang == 'french') ? '?puis?' : 'Sold Out'; ?></div>
                <?php }  else { ?>


                        <?php if ($product_real_price AND $product_real_price == '0.00') { ?>

                            <a style="display: block;text-align: center;margin: 20px auto;" href='tel:1800905147'><div class="add">Call For Pricing</div></a>

                        <?php }else{ ?>

                        <div class="form-add-cart" id="div_<?php echo $product->product_id; ?>">
                        <form action="<?php echo $mosConfig_live_site; ?>/index.php" method="post" name="addtocart" id="formAddToCart_<?php echo $product->product_id; ?>">
                            <input name="quantity_<?php echo $product->product_id; ?>" class="inputbox" type="hidden" size="3" value="1">
                            <div class="add" product_id="<?php echo $product->product_id; ?>"><?php echo ($mosConfig_lang == 'french') ? 'Magasinez' : 'Add to Cart'; ?></div>
                            <input type="hidden" name="category_id_<?php echo $product->product_id; ?>" value="<?php echo $product->category_id; ?>">
                            <input type="hidden" name="product_id_<?php echo $product->product_id; ?>" value="<?php echo $product->product_id; ?>">
                            <input type="hidden" name="price_<?php echo $product->product_id; ?>" value="<?php echo $product_real_price; ?>">
                            <input type="hidden" name="sku_<?php echo $product->product_id; ?>" value="<?php echo $product->product_sku; ?>">
                            <input type="hidden" name="name_<?php echo $product->product_id; ?>" value="<?php echo $product->product_name; ?>">
                            <input type="hidden" name="discount_<?php echo $product->product_id; ?>" value="<?php echo $savingPrice; ?>">
                            <input type="hidden" name="category_<?php echo $product->product_id; ?>" value="<?= $product->category_name ?>">
                        </form>
                        </div>

                        <?php } ?>


                <?php } ?>
            </div>
        </div>



    </div>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery('.close_today_special').click(function(){
            var date = new Date;
            document.cookie = "todaySpecialBannerHide=true; expires=" + date.toGMTString(date.setDate(date.getDate() + 7));
            jQuery('.today_special').hide(1000)
        })

        var containerWidth = $('.container').width();
        var docWidth = $(document).width();
        $('.today_special').css({left: (docWidth-containerWidth)/4-145});

        if ((document.cookie.replace(/(?:(?:^|.*;\s*)todaySpecialBannerHide\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "true")){
            jQuery('.today_special').slideToggle( "slow" )
        }
    })
</script>
<style>
    .today_special{
        display: none;
        width: 270px;
        position: fixed;
        left: 10px;
        bottom: 0;
        z-index: 2;
    }
    .today_special .inner{
        height: 400px !important;
        margin: 0px !important;
    }
    .today_special .wrapper{
        padding: 0px !important;
    }
    .today_special .products .wrapper .inner .add{
        left: 0px;
    }
    .today_special .inner .product-image{
        height: 265px !important;
    }

    .today_special_head{
        height: 30px;
        color: white;
        z-index: 2;
        font-weight: bold;
        font-size: 16px;
        background: #ab0917;
        width: 100%;
        text-align: center;
        padding: 5px;
    }
    .close_today_special{
        position: absolute;
        right: 5px;
        font-weight: normal;
        cursor: pointer;
    }
    @media (max-width: 1750px){
        .today_special{
            display: none !important;
        }
    }

</style>
<?php } ?>
