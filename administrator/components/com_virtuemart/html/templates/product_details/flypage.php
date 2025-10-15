
<script type="application/ld+json" id="productData">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "{product_name_text}",
        "sku": "{product_sku}",
        "image": "{product_image_url}",
        "description": "{product_description_text}",
        "brand": {
            "@type": "Brand",
            "name": "Bloomex Australia"
        },
        "offers": {
            "@type": "Offer",
            "url": "{product_url}",
            "priceCurrency": "AUD",
            "price": "{product_price_number}",
            "availability": "https://schema.org/InStock",
            "itemCondition": "https://schema.org/NewCondition"
        },
        "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "{product_rating}",
          "reviewCount": "{product_review_count}"
        }
    }
</script>

<div class="container product_details_wrapper" >
    <div class="row" id="product_details_{product_id}">
        <div class="col-12  col-md-5 col-lg-5 image position-relative">
            {product_no_delivery_order_html}
            {product_is_bestseller_html}
            {product_show_sale_overlay_html}
            {product_out_of_season_or_sold_out_html}
            {product_image}
            <div class="info_mobile d-none d-xs-block">
                <div class="name"><h2><span>{product_name}</span></h2></div>
                <div class="rating">
<!--                    {data_rating_mobile}-->
                    <div class="sku">SKU: {product_sku}</div>
                </div>
                <div class="price">
                    <span>
                        {product_price}

                    </span>
                </div>
            </div>
            <input type="button" class="description_button" value="Read Description">

            {surprise}

        </div>
        <div class="col-12 col-md-7 col-lg-7 info">
            <div class="info_desktop d-xs-none mt-2">
                <div class="name"><h1><span>{product_name}</span></h1></div>
                <div class="sku">SKU: {product_sku}</div>
                <div class="price">
                    <span>
                        {product_price}
                    </span>
                </div>
            </div>
            <div class="step">{step_1_title}<span class="glyphicon glyphicon-info-sign" id="bouquet_info_icon"></span></div>
            <div id ="bouquet-info-alert" class=" alert alert-warning alert-dismissible collapse" role="alert">
                {bouquet-info-alert}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="bouquets">
                {petite_html}
                {regular_html}
                {deluxe_html}
                {supersize_html}
            </div>
            <div class="subscribes">
                {subscribes_html}
            </div>
            {add_to_cart_form}

            <div class="description" >
                {product_description}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="step">{step_2_title}</div>
            {extra_products}
            {flowerBelowImages}
        </div>
    </div>
</div><!--!product_details_wrapper-->
<div class="footer_addToCart_button">
    BUY NOW
</div>
