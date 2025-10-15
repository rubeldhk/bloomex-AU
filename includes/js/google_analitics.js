function pushGoogleAnalytics(
    eventName,
    items = [],
    value = null,
    shipping_tier = null,
    currency = "AUD",
    item_list_name = null,
    item_list_id = null
) {

    dataLayer.push({ ecommerce: null });

    var ecommerceItems = {
        currency: currency,
        items: items
    }

    if (value != null) {
        ecommerceItems.value = value;
    }
    if (item_list_name != null) {
        ecommerceItems.item_list_name = item_list_name;
    }
    if (item_list_id != null) {
        ecommerceItems.item_list_id = item_list_id;
    }
    if (shipping_tier != null) {
        ecommerceItems.shipping_tier = shipping_tier;
    }

    dataLayer.push({
        event: eventName,
        ecommerce: ecommerceItems
    });



}

function pushPurchaseGoogleAnalytics(
    eventName,
    items = [],
    value = null,
    transaction_info = null,
    currency = "AUD"
) {
    dataLayer.push({ ecommerce: null });

    var ecommerceItems = {
        currency: currency,
        items: items
    }

    ecommerceItems.value = value;
    if (transaction_info != null) {
        ecommerceItems = Object.assign(ecommerceItems, transaction_info);
    }

    dataLayer.push({
        event: eventName,
        ecommerce: ecommerceItems
    });
}
