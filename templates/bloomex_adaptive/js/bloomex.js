function addProductToItems(e) {
    var t = parseFloat(jQuery('input[name="price_' + e + '"]').val()),
        r = jQuery('input[name="sku_' + e + '"]').val(),
        i = jQuery('input[name="name_' + e + '"]').val(),
        o = parseFloat(jQuery('input[name="discount_' + e + '"]').val()),
        a = jQuery('input[name="category_' + e + '"]').val(),
        s = jQuery('input[name="select_bouquet"]').val(),
        e = parseInt(jQuery('input[name="quantity_' + e + '"]').val());
    extra_products.push({
        item_name: i,
        item_id: r,
        price: t * e,
        discount: o,
        item_category: a,
        item_variant: null == s ? "standard" : s,
        quantity: e
    })
}


function updatezip(elem) {
    var val = elem.value;
    shipping_info_state = jQuery('#shipping_info_state');
    stateCode = getStateByPostalCode(val);
    shipping_info_state.val(stateCode)

    if( typeof postal_codes !== 'undefined') {
        if (postal_codes[val]) {
                var city = jQuery('#city').val();
                if(postal_codes[val] == undefined) {
                    jQuery('#city').val(city).addClass('autocomplete');
                } else {
                    jQuery('#city').val(postal_codes[val]).addClass('autocomplete');
                }
                shipping_info_state.addClass('autocomplete');
                elem.classList.remove('red-border');

        } else {
            elem.classList.add('red-border');
            shipping_info_state.removeClass('autocomplete');
            jQuery('#city').val('').removeClass('autocomplete');
        }
    }
    if (jQuery('#googleaddressupdate_shipping').val() == '' || jQuery('#googleaddressupdate_shipping').val() == undefined) {
        jQuery('#googleaddressupdate_shipping').val('');
    }
}
function identifyKlaviyo(email = undefined) {
    const user_email = email || localStorage.getItem('user_email');

    if (user_email && window._learnq) {
        window._learnq.push(['identify', {
            'email': user_email
        }]);
        KlaviyoTracker.addCustomerEmail(user_email);
    }
}

function setBouquet(e, t,i,p) {

    let newPrice = (parseFloat(jQuery(`[name='price_${i}']`).val()) + parseFloat(p)).toFixed(2);

    jQuery('#product_details_'+i).find(`.info_desktop`).find(`.product_price_span`).text(newPrice)
    jQuery('#product_details_'+i).find(`.info_mobile`).find(`.product_price_span`).text(newPrice)


    selected_bouquet = {
        product_name: e,
        bouquet: t
    }, sessionStorage.setItem("selected_bouquet", null), sessionStorage.setItem("selected_bouquet", JSON.stringify(selected_bouquet));

    jQuery('.variant-option').removeClass('selected');
    jQuery(`#${t}_${i}`).closest('.variant-option').addClass('selected');
}

function setBannerTop() {
    jQuery(".banner_top_1, .banner_top_2, .orders_count").toggle()
}




function checkCardProduct() {
    document.cookie = "userActivityTimeStart=" + Date.now() + "; path=/; secure", jQuery.ajax({
        type: "POST",
        async: !0,
        url: "/index.php?option=com_ajaxorder&task=checkCardProduct",
        success: function(e) {
            "success" == e ? $("#BeforeYouLeaveDiv").modal("show") : console.log("Coupon used")
        }
    })
}


function setExitPopupClick(e) {
    jQuery.ajax({
        type: "POST",
        async: !0,
        url: "/index.php?option=com_ajaxorder&task=CartAction",
        data: {
            action: "AddToCart",
            quantity: "1",
            select_bouquet: "standart",
            extra_touches: "0",
            product_id: e
        },
        dataType: "json",
        success: function(e) {
            "success" == e && console.log("setExitPopupClick")
        }
    }), jQuery.ajax({
        type: "POST",
        url: "/index.php?option=com_ajaxorder&task=setExitPopupClick",
        success: function(e) {
            document.getElementById("ExitPopUpPromotion").removeAttribute("style"), $("#BeforeYouLeaveDiv").modal("hide"), setTimeout(() => {
                window.location.reload()
            }, 1500)
        }
    })
}

function setModalSize() {
    var r = document.querySelector(".before-you-leave-content"),
        i = new Image;
    i.src = getComputedStyle(r).backgroundImage.replace(/url\(['"]?(.*?)['"]?\)/i, "$1"), i.onload = function() {
        var e = i.width,
            t = i.height;
        r.style.width = e + "px", r.style.height = t + "px"
    }
}
extra_products = [], selected_bouquet = null, jQuery(document).ready(function() {
    document.getElementById("BeforeYouLeaveDiv") && setModalSize()
// }), $(document).bind("mouseleave", function(e) {
//     e.pageY - $(window).scrollTop() <= 1 && !isMobile() && 6e4 < Date.now() - getCookie("userActivityTimeStart") && checkCardProduct()
});
let userIsActive = !1;

function checkActivity() {
    isMobile() || (0 == userIsActive ? checkCardProduct() : console.log("User is active.")), userIsActive = !1
}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function submitorder(e) {
    jQuery('#delivery_date_2').removeClass("has-error");
    checkFormValidation = function(){
        var rv = true;
        jQuery.each(billing_info_required_fields, function(e, t) {

            var r, t = jQuery("#billing_info_" + t);
            t.removeClass("has-error")
            if(t.attr('id') == 'billing_info_user_email' && !validateEmail(t.val())){
                return  rv = false,t.addClass("has-error"), t.focus();
            }
            if ("" == t.val())  return rv = false,t.addClass("has-error"), t.focus();
        })
        return rv;
    }

    if(!checkFormValidation()){
        jQuery("div.checkout_processing").hide()
        jQuery("div.checkout_buttons").show()
        stopLoader()
        return false;
    }

    if("" == jQuery("#delivery_date_2").val())
    {
        jQuery('#delivery_date_2').addClass("has-error");
        jQuery('div.checkout_buttons_wrapper div.result').text('Choose a delivery date.');
        return false;
    }

    let checkCardVariables = true;
    if ("0.00" == jQuery("div.price_inner div.total_price span.price").attr("totalPrice")
        || $("input[name='payment_method_state']:checked").val() == 'stripe') {
        checkCardVariables = false
    }

    if (checkCardVariables) {
        let e;
        if ("" == jQuery("#expire_month").val() && (e = "Please select expire month"), "" == jQuery("#card_cvv").val() && (e = "Credit Card Security Code can not be empty"), "" == jQuery("#card_number").val() && (e = "Credit Card Number can not be empty"), "" == jQuery("#name_on_card").val() && (e = "Name On Card can not be empty"), e) return stopLoader(), jQuery("div.checkout_buttons_wrapper div.result").text(e), jQuery("div.checkout_processing").hide(), jQuery("div.checkout_buttons").show(), !1
    }
    startLoader(), jQuery.ajax({
        type: "POST",
        url: "/?option=com_virtuemart&func=MakeOrder",
        data: {
            gcapcha: e,
            customer_occasion: jQuery("#customer_occasion").val(),
            billing_info_first_name: jQuery("#billing_info_first_name").val(),
            billing_info_last_name: jQuery("#billing_info_last_name").val(),
            billing_info_phone_1: jQuery("#billing_info_phone_1").val(),
            billing_info_user_email: jQuery("#billing_info_user_email").val(),
            billing_info_country: jQuery("#billing_info_country").val(),
            card_msg: jQuery("#card_msg").val(),
            signature: jQuery("#signature").val(),
            card_comment: jQuery("#card_comment").val(),
            find_us: jQuery("#find_us").val(),
            redeem_bucks: jQuery("#redeem_bucks").is(":checked") ? 1 : 0,
            redeem_credits: jQuery("#redeem_credits").is(":checked") ? 1 : 0,
            donation_id: jQuery("#donation_id").is(":checked") ? jQuery("#donation_id").val() : 0,
            payment_method_state: $("input[name='payment_method_state']:checked").val(),
            name_on_card: jQuery("#name_on_card").val(),
            card_number: jQuery("#card_number").val(),
            card_cvv: jQuery("#card_cvv").val(),
            expire_month: jQuery("#expire_month").val(),
            expire_year: jQuery("#expire_year").val()
        },
        dataType: "json",
        success: function (json) {
            if (json.result) {
                stopLoader();
                if (json.stripePaymentUrl) {
                    window.location.href = json.stripePaymentUrl;
                    return;
                }
                if ($('#nextcheckoutStep').length) {
                     window.location.href = "/checkout/" + $('#nextcheckoutStep').val();
                } else {
                     window.location.href = '/purchase-thankyou/';
                }
            } else if (json.try_again == false) {
                stopLoader();
                 document.location.href = '/cart/?msg='+json.error;
            } else {
                jQuery('div.checkout_buttons_wrapper div.result').text(json.error).css('display', 'inline-block');
                jQuery('div.checkout_processing').hide();
                jQuery('div.checkout_buttons').show();

                    stopLoader();
                    if (isMobile()) {
                        jQuery('div.checkout_buttons_wrapper div.result').append('<a href="tel:1800905147">Click To Call</a>')
                    }

                }
            }
        })
}

function subscribeUser() {
    var t = document.getElementById("subscribe_input_email"),
        e = t.value,
        r = document.getElementById("subscribe-alert"),
        i = document.getElementById("alert-text");
    if (r.classList.remove("alert-success", "alert-danger", "show"), !e.match(/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) return r.classList.add("alert-danger", "show"), !(i.innerHTML = "Invalid email address!");
    $.ajax({
        data: {
            email: e
        },
        type: "POST",
        dataType: "json",
        url: "/?option=com_user&task=subscribe",
        success: function(e) {
            e.result ? (r.classList.add("alert-success", "show"), t.value = "", KlaviyoTracker.addCustomerEmail(e)) : r.classList.add("alert-danger", "show"), i.innerHTML = e.message
        }
    })
}

function setTestimonials() {
    jQuery(".testimonial").hide();
    var e = jQuery(".testimonial").toArray(),
        t = e.length,
        r = Math.floor(Math.random() * t),
        t = Math.floor(Math.random() * t),
        r = e[r],
        t = e[t];
    return jQuery(r).fadeIn("slow"), jQuery(t).fadeIn("slow"), !0
}

function checkRegistrationForm(e) {
    e.preventDefault(), jQuery.each(required_fields, function(e, t) {
        t = jQuery("#registration_form #" + t);
        t.closest("div.form-group").removeClass("has-error")
        return "" == t.val() ? (t.closest("div.form-group").addClass("has-error"), t.focus(), !1) : e == required_fields.length - 1 ? (jQuery("#registration_form").submit(), !0) : void 0
    })
}
function checkGuestForm(e) {
    e.preventDefault(), jQuery.each(required_fields_guest, function(e, t) {
        t = jQuery("#guest_form  #" + t);
        t.closest("div.form-group").removeClass("has-error")
        return "" == t.val() ? (t.closest("div.form-group").addClass("has-error"), t.focus(), !1) : e == required_fields_guest.length - 1 ? (jQuery("#guest_form").submit(), !0) : void 0
    })
}

function checkUpdateBillingInfoForm(e) {
    e.preventDefault(), jQuery(".billing_address_btn").attr("disabled", "disabled"), jQuery.each(billing_info_required_fields, function(e, t) {
        var r, t = jQuery("#billing_info_" + t);
        if ("" == t.val()) return t.closest("div.form-group").addClass("has-error"), t.focus(), jQuery(".billing_address_btn").removeAttr("disabled"), !1;
        e == billing_info_required_fields.length - 1 && (r = jQuery("#update_billing_info_form")[0], e = new FormData(r), jQuery.ajax({
            url: "/",
            data: e,
            type: "post",
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function(e) {
                e.result ? (r.nextcheckoutStep.value && (window.location.href = "/checkout/" + r.nextcheckoutStep.value), jQuery(".billing_row .billing_company").text(e.user_info_obj.company), jQuery(".billing_row .billing_full_name").text(e.user_info_obj.full_name), jQuery(".billing_row .billing_address").text(e.user_info_obj.address), jQuery(".billing_row .billing_phone_1").text(e.user_info_obj.phone_1), jQuery(".billing_row .billing_fax").text(e.user_info_obj.fax), jQuery(".billing_row .billing_user_email").text(e.user_info_obj.user_email), jQuery("div.update_billing_info_wrapper div.close_form").trigger("click")) : alert(e.error), jQuery(".billing_address_btn").removeAttr("disabled")
            }
        }))
    })
}

function checkUpdateShippingInfoFormFast(t) {
    t.preventDefault(), startLoader();
    var r = !0;
    if (jQuery("div.form-group").removeClass("has-error"), jQuery.each(shipping_info_required_fields, function(e, t) {
        t = jQuery("#shipping_info_" + t);
        ("" == t.val() || "0" == t.val()) && (t.closest("div.form-group").addClass("has-error"), t.focus(), jQuery(".shipping_address_btn").removeAttr("disabled"), stopLoader(), r = !1)
    }), !r) return !1;
    var e = jQuery("#update_shipping_info_form")[0],
        e = new FormData(e);
    e.append("address_type", "ST"), jQuery.ajax({
        type: "POST",
        url: "/?option=com_ajaxorder&task=setCardMsgAndOccasion",
        data: e,
        contentType: !1,
        processData: !1,
        dataType: "json",
        success: function(e) {
            if(e.result) {
                window.location.href = "/purchase-thankyou/";
            }else{
                alert(e.error);
                stopLoader();
            }
        }
    })
}

function checkUpdateShippingInfoFormExtended(t) {
    t.preventDefault(), startLoader();
    checkFormValidation = function() {
        var rv = true;
        jQuery.each(shipping_info_required_fields, function (e, m) {

            var m = jQuery("#shipping_info_" + m);
            m.removeClass("has-error")
            if ("" == m.val()) return rv = false,m.addClass("has-error"), m.focus();
        });
        return rv;
    }

    if(!checkFormValidation()){
        stopLoader()
        return false;
    }
    var e = jQuery("#update_shipping_info_form")[0],
        e = new FormData(e);
    e.append("address_type", "ST"), jQuery.ajax({
        type: "POST",
        url: "/?option=com_ajaxorder&task=setCardMsgAndOccasion",
        data: e,
        contentType: !1,
        processData: !1,
        dataType: "json",
        success: function(e) {
            e.result ? checkUpdateShippingInfoForm(t) : (alert(e.error), stopLoader())
        }
    })
}

function checkUpdateShippingInfoForm(e) {
    e.preventDefault(), jQuery(".shipping_address_btn").attr("disabled", "disabled"),  jQuery("div.update_shipping_info_wrapper span.result").remove(), startLoader(), jQuery.each(shipping_info_required_fields, function(e, t) {
        t = jQuery("#shipping_info_" + t);
        t.removeClass("has-error")
        if ("" == t.val()) return t.addClass("has-error"), t.focus(), jQuery(".shipping_address_btn").removeAttr("disabled"), stopLoader(), !1;
        e == shipping_info_required_fields.length - 1 && (e = jQuery("#update_shipping_info_form")[0], (e = new FormData(e)).append("option", "com_ajaxorder"), e.append("task", "UpdateAddress"), e.append("address_type", "ST"), jQuery.ajax({
            url: "/",
            data: e,
            type: "post",
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function(e) {
                e.result ? ($("#checkoutStep").length && (window.location.href = "/purchase-thankyou/"), jQuery.ajax({
                    url: "/?option=com_virtuemart&func=getSTAddresses",
                    dataType: "json",
                    success: function(e) {
                        var r;
                        e.result && (r = "", jQuery.each(e.shipping_addresses, function(e, t) {
                            console.log(t), r += t
                        }), jQuery("table.shipping_addresses").html(r), jQuery("div.update_shipping_info_wrapper div.close_form").trigger("click"), jQuery.ajax({
                            type: "POST",
                            url: "/?option=com_virtuemart&func=getDonationData",
                            dataType: "json",
                            success: function(e) {
                                e.result && (jQuery("#donation_label").html(e.donate.label), jQuery("#donation_label").attr("title", e.donate.text), jQuery("#donation_id").val(e.donate.id), jQuery("label.container.donation").show()), stopLoader(), jQuery(".shipping_address_btn").removeAttr("disabled")
                            }
                        }))
                    }
                })) : (e.redirect && (window.location.href = e.redirect), alert(e.error), stopLoader(), jQuery(".shipping_address_btn").removeAttr("disabled"))
            }
        }))
    }), "" != jQuery("#delivery_date_2").val() && (jQuery("#delivery_date_2").val(""), jQuery(".ddate_tooltip").show(), setTimeout(function() {
        jQuery(".ddate_tooltip").hide("slow")
    }, 4e3))
}


document.addEventListener("mousemove", () => {
    userIsActive = !0
}), document.addEventListener("keydown", () => {
    userIsActive = !0
});

var subcategory_previews = jQuery("img.subcategory_image_real");
jQuery.each(subcategory_previews, function(e, t) {
    t.complete ? (jQuery(this).parent().find(".subcategory_image_loader").hide(), jQuery(this).show()) : (jQuery(this).on("load", function() {
        jQuery(this).parent().find(".subcategory_image_loader").hide(), jQuery(this).show()
    }), jQuery(this).on("error", function() {}))
});
var previews = jQuery("img.product_image_real");
jQuery.each(previews, function(e, t) {
    t.complete ? (jQuery(this).parent().find(".product_image_loader").hide(), jQuery(this).show()) : (jQuery(this).on("load", function() {
        jQuery(this).parent().find(".product_image_loader").hide(), jQuery(this).show()
    }), jQuery(this).on("error", function() {}))
});
var date, previewsSlider = jQuery("img.slider_image_real");



function initMap() {
     jQuery(".sale_banners").find("img").css("visibility", "hidden")
    jQuery(".landing_inner span.phone").html('<a href="tel:' + landing_phone.replace(/[^0-9]/g, "") + '">' + landing_phone + "</a>")
         jQuery("#map").css("background-image", "url(" + landing_image + ")")
         jQuery("#map").show()
}

function GetOrdering(i, o) {
    window.location.reload();
    jQuery.each(jQuery(".container.products .row"), function(e, t) {
        var r = jQuery.makeArray(jQuery(this).children("div.wrapper"));
        "rating_ordering" == i ? r.sort(function(e, t) {
            return Math.random() - .5
        }) : r.sort(function(e, t) {
            return e = parseFloat(jQuery(e).attr(i)), t = parseFloat(jQuery(t).attr(i)), "desc" == o ? t - e : "asc" == o ? e - t : void 0
        }), jQuery(r).appendTo(jQuery(this))
    })
}

function getCookie(e) {
    for (var t = e + "=", r = decodeURIComponent(document.cookie).split(";"), i = 0; i < r.length; i++) {
        for (var o = r[i];
             " " == o.charAt(0);) o = o.substring(1);
        if (0 == o.indexOf(t)) return o.substring(t.length, o.length)
    }
    return ""
}

function getValidCalendarDates(delivery_date=null) {
    disabled_dates=[];
    if ("" == $("#shipping_info_zip").val()) return alert("please enter postal code"), [];

    $("#shipping_info_zip").val().length == 4 ? "" == $("#shipping_info_state").val() ? (alert("please select state"), !1) : ( $.ajax({
        data: {
            zip: $("#shipping_info_zip").val(),
            state: $("#shipping_info_state").val(),
            delivery_date: delivery_date,
        },
        url: "/?option=com_ajaxorder&task=getValidCalendarDates",
        dataType: "json",
         async:false,
        cache: !1,
        method: "POST",
        success: function(e) {
            if(e.result){
                disabled_dates =   e.disabled_dates;
            } else{
                alert(e.error)
            }
        }
    })) : (alert("please enter valid postal code"), !1)
    return disabled_dates;
}

function selectDeliveryDateLight() {

    return  "" == $("#shipping_info_state").val() ? (alert("please select state"), !1) : (startLoader(), void $.ajax({
        data: {
            state: $("#shipping_info_state").val()
        },
        url: "/?option=com_ajaxorder&task=getOrSetShippingInfoId",
        dataType: "json",
        cache: !1,
        method: "POST",
        success: function(e) {
            e.result ? selectDeliveryDate() : e.error && (alert(e.error), stopLoader())
        }
    }))
}

function selectDeliveryDate() {
    jQuery('#delivery_date_2').removeClass("has-error");
    startLoader(), jQuery.ajax({
        type: "POST",
        url: "/?option=com_virtuemart&func=getDeliveryCalendarOptions",
        data: {
            delivery_date: jQuery("delivery_date").val()
        },
        dataType: "json",
        success: function(e) {
            e.result && (jQuery("div.calendar_wrapper .calendar").html(e.calendar), jQuery("div.calendar_wrapper .delivery").html(e.options), jQuery("div.delivery_date_wrapper").css("visibility", "hidden").slideUp("slow"), jQuery("div.calendar_wrapper").slideDown("slow").addClass('showCalendar'), $(".day_inner").hasClass("ready") ? stopLoader() : $(".next_month").click(), e.error && calendarErrorPopup(e))
        }
    })
}
function calendarErrorPopup(e){

    var calendar_error_modal_html = '<div class="cart_modal_wrapper">';
    calendar_error_modal_html += '<div class="cart_modal_inner height-auto">';
    calendar_error_modal_html += '<div class="remove">';
    calendar_error_modal_html += '</div>';
    calendar_error_modal_html += '<div class="text-warning ">';
    calendar_error_modal_html += e.error;
    calendar_error_modal_html += '</div>';
    if(e.suggested_products) {
        calendar_error_modal_html += e.suggested_products;
    }
    calendar_error_modal_html += '</div>';
    calendar_error_modal_html += '</div>';

    jQuery('.page').append(calendar_error_modal_html);

    jQuery('.cart_modal_wrapper').show();
    stopLoader();

}

jQuery.each(previewsSlider, function(e, t) {
    t.complete ? (jQuery(this).parents().find(".slider_image_loader").hide(), jQuery(this).show()) : (jQuery(this).on("load", function() {
        jQuery(this).parents().find(".slider_image_loader").hide(), jQuery(this).show()
    }), jQuery(this).on("error", function() {}))
}), 0 < $("#shipping_info_zip").length && $("#shipping_info_zip").on("keyup change", function() {
    jQuery("#delivery_date_2").val("");
    var e = {
        NW: [
            [1e3, 1999],
            [2e3, 2599],
            [2619, 2899],
            [2921, 2999]
        ],
        AT: [
            [200, 299],
            [2600, 2618],
            [2900, 2920]
        ],
        VI: [
            [3e3, 3999],
            [8e3, 8999]
        ],
        QL: [
            [4e3, 4999],
            [9e3, 9999]
        ],
        SA: [
            [5e3, 5799],
            [5800, 5999]
        ],
        WA: [
            [6e3, 6797],
            [6800, 6999]
        ],
        TA: [
            [7e3, 7799],
            [7800, 7999]
        ],
        NT: [
            [800, 899],
            [900, 999]
        ]
    };
    for (s in e)
        if (Array.isArray(e[s]))
            for (m in e[s]) $("#shipping_info_zip").val() >= m[0] && $("#shipping_info_zip").val() <= m[1] && $("#shipping_info_state").val(s)
}), 0 < $("#shipping_info_state").length && $("#shipping_info_state").on("change", function() {
    jQuery("#delivery_date_2").val("");
    jQuery("div.calendar_wrapper div.close_form").click()
}), jQuery(document).ready(function() {


    var t, r;
    switch ($(".description_button").click(function() {
        $(this).hasClass("showDescription") ? ($(this).removeClass("showDescription"), $(this).val("Read Description"), $(".description").slideUp()) : ($(this).addClass("showDescription"), $(this).val("Hide Description"), $(".description").slideDown())
    }), $(".footer_addToCart_button").click(function() {
        $(this).hide(), jQuery(".form-add-cart .add").click()
    }), $(".custom_accordion_header").click(function() {
        $(this).find(".glyphicon").hasClass("glyphicon-circle-arrow-down") ? ($(this).find(".glyphicon").removeClass("glyphicon-circle-arrow-down").addClass("glyphicon-circle-arrow-right"), $(this).next(".custom_accordion_text").slideUp("slow")) : ($(".custom_accordion_text").hide(), $(".custom_accordion_header").find(".glyphicon").removeClass("glyphicon-circle-arrow-down").addClass("glyphicon-circle-arrow-right"), $(this).find(".glyphicon").removeClass("glyphicon-circle-arrow-right").addClass("glyphicon-circle-arrow-down"), $(this).next(".custom_accordion_text").slideDown("slow"))
    }),
        getCookie("product_ordering")) {
        case "desc":
            // GetOrdering("price_ordering", "desc");
            break;
        case "asc":
            // GetOrdering("price_ordering", "asc")
    }
    jQuery("body").on("click", ".sort_by_select", function(e) {
        !jQuery(".sort_by_select").find("span").hasClass("glyphicon-sort-by-attributes-alt") && jQuery(".sort_by_select").find("span").hasClass("glyphicon-sort-by-attributes") ? (jQuery(".sort_by_select").find("span").removeClass("glyphicon-sort-by-attributes").addClass("glyphicon-sort-by-attributes-alt"), GetOrdering("price_ordering", "desc"), document.cookie = "product_ordering=desc; path=/; secure") : (jQuery(".sort_by_select").find("span").removeClass("glyphicon-sort-by-attributes-alt").addClass("glyphicon-sort-by-attributes"), GetOrdering("price_ordering", "asc"), document.cookie = "product_ordering=asc; path=/; secure")
    }), jQuery(".testimonials").is(":visible") && (setTestimonials(), setInterval(setTestimonials, 1e4)), setBannerTop(), setInterval(setBannerTop, 5e3), $(document).on("click", ".form-add-cart .add", function(e) {
        e.preventDefault(), startLoader();
        var el =jQuery(this);
        var product_thumb_image = jQuery(this)
            .closest('.wrapper')
            .find('img.product_image_real')
            .attr('src') || '';

        var t, o = parseInt(jQuery(this).attr("product_id")),
            r = parseInt(jQuery('input[name="quantity_' + o + '"]').val()),
            a = jQuery("[name='select_bouquet']").is(":checked") ? jQuery("[name='select_bouquet']:checked").val() : "standart",
            s = jQuery("[name='select_sub']").is(":checked") ? jQuery("[name='select_sub']:checked").val() : "",
            i = jQuery('input[name="extra_products"]').is(":checked") ? jQuery('input[name="extra_products"]:checked').serialize() : 0,
            n = parseFloat(jQuery('input[name="price_' + o + '"]').val()),
            u = jQuery('input[name="sku_' + o + '"]').val(),
            d = jQuery('input[name="name_' + o + '"]').val(),
            c = parseFloat(jQuery('input[name="discount_' + o + '"]').val()),
            e = jQuery('input[name="category_' + o + '"]').val(),
            e = (jQuery('input[name="select_bouquet"]').val(), [{
                item_name: d,
                item_id: u,
                price: n * r,
                image_url: product_thumb_image,
                discount: c,
                item_category: e,
                item_variant: null == selected_bouquet ? "standard" : selected_bouquet.bouquet,
                quantity: r
            }]);
        for (t in "undefined" != typeof extra_products && (e = [...e, ...extra_products]), total_price = 0, e) total_price += t.price;
         jQuery("div.cart_modal_wrapper div.cart_modal_inner div.remove").trigger("click"), jQuery.ajax({
            type: "POST",
            url: "/?option=com_ajaxorder&task=CartAction",
            data: {
                action: "AddToCart",
                product_id: o,
                quantity: r,
                select_bouquet: a,
                select_sub: s,
                extra_touches: i
            },
            product_id: o,
            dataType: "json",
            success: function(i) {
                if (i.alreadyExistPromoProduct) return alert("You can add only one promo product"), void stopLoader();
                setCart(i), gtag_report_conversion(),
                    pushGoogleAnalytics("add_to_cart", e, i.cart_price), pos = location.href.indexOf("/checkout/2/");

                    if(el.hasClass('add_and_buy')){
                        return window.location.href = "/fast-checkout/";
                    }
                    if(el.hasClass('add_and_reload')){
                        added_product_element_number = Object.keys(i.products).length - 1
                        last_element_of_array = i.products[added_product_element_number]
                        if(jQuery('.cart_product_id_'+o).length > 0){
                            jQuery('.cart_product_id_'+o+' .quantity_input').val(
                                parseInt(jQuery('.cart_product_id_'+o+' .quantity_input').val()) + 1
                            )
                        } else {
                             if(last_element_of_array.promo == '1') {
                                var quantity = '<div class="text-center">1</div>';
                            } else {
                                 var quantity = '<div class="quantity"><span class="minus">-</span><input type="text" class="quantity_input" value="1"><span class="plus">+</span></div>';
                             }
                        new_item= '<tr cart_product_id="' + last_element_of_array.id + '" i="' + added_product_element_number + '" class="cart_product_tr cart_product_id_' + last_element_of_array.id + '">\n' +
                            '                                <td style="position: relative">\n' +
                            '                                    <div class="image shopping_cart_table_image">\n' +
                            '                                        <img alt="' + last_element_of_array.name + '" src="' + last_element_of_array.image + '">\n' +
                            '                                    </div>\n' +
                            '                                    <div class="info shopping_cart_table_info" >\n' +
                            '                                        <a href="' + last_element_of_array.url + '">\n' +
                            '                                            <p>' + last_element_of_array.name + '</p>\n' +
                            '                                        </a>\n' +
                            '                                        <p class="d-none d-xl-table-cell">SKU: <span>' + last_element_of_array.sku + '</span></p>\n' +
                            '                                    </div>\n' +
                            '                                </td>\n' +
                            '                                <td data-th="price" class="d-none d-xl-table-cell">\n' +
                            '                                    $' + last_element_of_array.price + '\n' +
                            '                                </td>\n' +
                            '                                <td  data-th="quantity">' + quantity + '\n' +
                            '                                </td>\n' +
                            '                                <td class="cart_product_subtotal_' + last_element_of_array.id + '" data-th="total">\n' +
                            '                                    $' + last_element_of_array.price + '\n' +
                            '                                </td>\n' +
                            '                                <td  data-th="remove">\n' +
                            '                                   <div class="remove" product_id="' + last_element_of_array.id + '"></div>\n' +
                            '                                </td>\n' +
                            '                            </tr>';
                            jQuery('.cart_wrapper .table.cart tbody').prepend(new_item)
                        }
                        stopLoader();
                        return ;

                    }
                const klaviyoData = {
                    total: i.cart_price,
                    ItemNames: [],
                    Items: []
                };

               const itemIdes = [];

                    -1 != pos ? location.reload() : 0 < i.cart_items && jQuery.each(i.products, function(e, t) {
                        klaviyoData.Items.push({
                            ProductID: t.id,
                            SKU: t.sku,
                            ProductName:  t.name,
                            Quantity: t.quantity,
                            ItemPrice: t.price,
                            RowTotal: t.price * t.quantity,
                            ProductURL: t.url,
                            ImageURL: t.image
                        });
                        itemIdes.push(t.id)
                        klaviyoData.ItemNames.push(t.name);
                    var r;
                    t.id == o && t.select_bouquet == a && t.select_sub == s && (r = '<div class="cart_modal_wrapper">', r += '<div class="cart_modal_inner">', r += '<div class="remove">', r += "</div>", r += '<div class="title">', r += '<img src="/templates/bloomex_adaptive/images/mark_cart.svg" alt="mark_cart" />', r += "Product successfully added to your Shopping Cart", r += "</div>", r += '<div class="product">', r += '<div class="product_image">', r += '<img src="' + (t = t).image + '" alt="' + t.name + '" />', r += "</div>", r += '<div class="product_info">', r += '<div class="name">', r += t.name, r += "standart" != a ? " (" + a.toUpperCase() + ")" : "", r += "</div>", r += '<div class="quantity">', r += "Quantity: " + t.quantity, r += "</div>", r += '<div class="total">', r += "Total: <span>$" + parseFloat(t.price * t.quantity).toFixed(2) + "</span>", r += "</div>", r += "</div>", r += "</div>", r += "<hr/>", r += '<div class="items">', r += " " + i.cart_items + " item(s) in your cart.", r += "</div>", r += '<div class="total">', r += "Total: <span>$" + i.cart_price + "</span>", r += "</div>",
                        r += '<div class="buttons">',
                        r += '<button type="submit" class="btn continue">continue shopping</button>',
                        r += '<button type="submit" class="btn proceed">proceed to checkout</button>',
                        r += i.enableFastCheckout ? '<button type="submit" class="btn proceed_fast">buy now</button>':'',
                        r += "</div>", r += "</div>", r += "</div>", jQuery(".page").append(r), (r = jQuery(".cart_modal_wrapper .cart_modal_inner .product .product_image img")).complete ? (jQuery(".cart_modal_wrapper").show(), stopLoader()) : (r.on("load", function() {
                        jQuery(".cart_modal_wrapper").show(), stopLoader()
                    }), r.on("error", function() {})))
                })


                if (typeof fbq === 'undefined') {
                    console.warn('Facebook Pixel might be blocked for tracking AddToCart.');
                } else {
                    setTimeout(function() {
                        if (typeof fbq === 'function') {
                            fbq('track', 'AddToCart', {
                                content_ids: itemIdes,
                                content_type: 'product'
                            });
                        }
                    }, 1000);
                }

                KlaviyoTracker.track('Added to Cart', klaviyoData);
            }
        })
    }), jQuery(".mobile_menu_toggle, .mobile_menu_wrapper, .mobile_menu ul.wrapper li div.remove").click(function(e) {
        e.preventDefault(), 0 == parseInt(jQuery(".mobile_menu").css("left")) ? (jQuery(".page").css({
            left: "0px"
        }), jQuery(".mobile_menu").css({
            left: "-" + jQuery(".mobile_menu").css("width")
        }), jQuery(".mobile_menu_wrapper").css("opacity", "0").hide(), jQuery(".page").css({
            position: "relative",
            display: "block",
            "overflow-y": "auto",
            height: "auto"
        })) : (jQuery(".mobile_menu").css({
            left: "0px"
        }), jQuery(".mobile_menu_wrapper").show().css("opacity", "1"), jQuery(".page").css({
            "overflow-y": "hidden",
            height: "100vh"
        }))
    }), jQuery(".mobile_menu ul.wrapper li>span.plus, .mobile_menu ul.wrapper li>span.minus").click(function(e) {
        e.preventDefault(), jQuery(this).parent().find(".inner").is(":visible") ? (jQuery(this).hide(), jQuery(this).parent().find(".plus").show(), jQuery(this).parent().find(".inner").slideUp("slow")) : (jQuery(this).hide(), jQuery(this).parent().find(".minus").show(), jQuery(this).parent().find(".inner").slideDown("slow"))
    }), jQuery(".mobile_menu ul.delivery_outside li>span.plus, .mobile_menu ul.delivery_outside li>span.minus").click(function(e) {
        e.preventDefault(), jQuery(this).parent().find(".inner").is(":visible") ? (jQuery(this).hide(), jQuery(this).parent().find(".plus").show(), jQuery(this).parent().find(".inner").slideUp("slow")) : (jQuery(this).hide(), jQuery(this).parent().find(".minus").show(), jQuery(this).parent().find(".inner").slideDown("slow"))
    }), jQuery("#mobile_search").click(function(e) {
        e.preventDefault(), jQuery(".mobile_search").is(":visible") ? jQuery(".mobile_search").slideUp("slow") : jQuery(".mobile_search").slideDown("slow")
    }), jQuery("#md_search").click(function(e) {
        e.preventDefault(), jQuery(".md_search_wrapper").is(":visible") ? jQuery(".md_search_wrapper").slideUp("slow") : jQuery(".md_search_wrapper").slideDown("slow")
    }), jQuery("#mobile_search_btn").click(function(e) {
        e.preventDefault(), jQuery("#mobile_search_form").submit()
    }), jQuery("#search_btn").click(function(e) {
        var t = document.getElementById("mainsearch");
        window.location = window.location.origin + "/search/" + encodeURIComponent(t.value.trim()).toLowerCase() + "/"
    }), jQuery("#search_md_btn").click(function(e) {
        e.preventDefault(), jQuery("#search_md_form").submit()
    }), jQuery("div.container.product_details_wrapper div.help").hover(function() {
        jQuery(this).children().show()
    }, function() {
        jQuery(this).children().hide()
    }), jQuery("div.container.product_details_wrapper div.sizes div.size").click(function(e) {
        e.preventDefault(), jQuery("#select_size").val(jQuery(this).attr("size")), jQuery("div.container.product_details_wrapper div.sizes div.size").removeClass("active"), jQuery(this).addClass("active")
    }),jQuery(document).on('click', 'table.table.cart tr td .quantity span', function(e){
        e.preventDefault(), startLoader();
        var t = jQuery(this).closest("div").find("input"),
            r = parseInt(t.val()),
            i = 1,
            e = jQuery(this).attr("class"),
            o = parseInt(jQuery(this).closest("tr").attr("cart_product_id")),
            a = parseInt(jQuery(this).closest("tr").attr("i"));
        "plus" == e ? i = r + 1 : "minus" == e && 1 < r && (i = r - 1), t.val(i), jQuery.ajax({
            type: "POST",
            url: "/?option=com_ajaxorder&task=CartAction",
            data: {
                action: "SetQuantityCart",
                product_id: o,
                product_i: a,
                new_val: i
            },
            dataType: "json",
            new_val: i,
            success: function(e) {
                setCart(e), jQuery('tr[i="' + a + '"]').find("td.cart_product_subtotal_" + o).html("$" + e.new_subtotal_product),jQuery("#delivery_date_2").val(""),jQuery("div.calendar_wrapper div.close_form").click(), stopLoader()
            }
        })
    }),
        jQuery(document).on('change', 'table.table.cart tr td .quantity input', function(e) {
        startLoader();
        var e = parseInt(jQuery(this).val()),
            t = parseInt(jQuery(this).closest("tr").attr("cart_product_id")),
            r = parseInt(jQuery(this).closest("tr").attr("i"));
        (isNaN(e) || e < 1) && (e = 1), jQuery(this).val(e), jQuery.ajax({
            type: "POST",
            url: "/?option=com_ajaxorder&task=CartAction",
            data: {
                action: "SetQuantityCart",
                product_id: t,
                product_i: r,
                new_val: e
            },
            dataType: "json",
            success: function(e) {
                setCart(e), jQuery('tr[i="' + r + '"]').find("td.cart_product_subtotal_" + t).text("$" + e.new_subtotal_product),jQuery("#delivery_date_2").val(""),jQuery("div.calendar_wrapper div.close_form").click(), stopLoader()
            }
        })
    }), jQuery(document).on('click', 'tr.cart_product_tr div.remove', function(e){
        e.preventDefault(), targetElement = e.target, startLoader();
        var t = parseInt(targetElement.getAttribute("product_id")),
            r = parseFloat(jQuery('input[name="price_' + t + '"]').val()),
            i = jQuery('input[name="sku_' + t + '"]').val(),
            o = jQuery('input[name="name_' + t + '"]').val(),
            a = parseFloat(jQuery('input[name="discount_' + t + '"]').val()),
            s = jQuery('input[name="category_' + t + '"]').val(),
            e = jQuery('input[name="select_bouquet"]').val(),
            t = parseInt(jQuery('input[name="quantity_' + t + '"]').val());
        pushGoogleAnalytics("remove_from_cart", [{
            item_name: o,
            item_id: i,
            price: r * t,
            discount: a,
            item_category: s,
            item_variant: null == e ? "standard" : e,
            quantity: t
        }], r * t);



        var t = parseInt(jQuery(this).closest("tr").attr("cart_product_id")),
            n = parseInt(jQuery(this).closest("tr").attr("i"));
        jQuery.ajax({
            type: "POST",
            url: "/?option=com_ajaxorder&task=CartAction",
            data: {
                action: "RemoveFromCart",
                product_id: t,
                product_i: n
            },
            dataType: "json",
            cart_product_id: t,
            success: function(e) {
                if(!e.alcohol_exist) {
                    jQuery('.drink_age_message').hide()
                }
                if (setCart(e), 0 == e.cart_items) return window.location.href = "/cart/", !0;
                jQuery('tr[i="' + n + '"]').remove(), jQuery.each(jQuery("tr.cart_product_tr"), function(e) {
                    jQuery(this).attr("i", e)
                }),jQuery("#delivery_date_2").val(""), stopLoader()
            }
        })
    }), jQuery("div.coupon_field div.hide_field span").click(function(e) {
        e.preventDefault(), startLoader(), jQuery("div.coupon_field div.hide_field").slideUp("slow"), jQuery("div.coupon_field div.show_field").slideDown("slow"), stopLoader()
    }), jQuery("div.billing_wrapper div.billing_info_wrapper button").click(function(e) {
        e.preventDefault(), startLoader(), jQuery(".billing_info_wrapper").css("visibility", "hidden").slideUp("slow"), jQuery(".update_billing_info_wrapper").slideDown("slow"), stopLoader()
    }), jQuery("div.update_billing_info_wrapper div.close_form").click(function(e) {
        e.preventDefault(), startLoader(), jQuery(".billing_info_wrapper").css("visibility", "visible").slideDown("slow"), jQuery(".update_billing_info_wrapper").slideUp("slow"), stopLoader()
    }), jQuery("div.shipping_wrapper div.shipping_info_wrapper button.add_shipping_address").click(function(e) {
        e.preventDefault(), startLoader(), jQuery("div.form-group").removeClass("has-error"), jQuery.each(jQuery("form#update_shipping_info_form input"), function() {
            jQuery(this).val("")
        }), jQuery.each(jQuery("form#update_shipping_info_form select"), function() {
            "shipping_info_country" != jQuery(this).attr("id") && jQuery(this).val("")
        }), jQuery("#shipping_info_address_type2").val("Home/Residence"), jQuery(".shipping_info_wrapper").css("visibility", "hidden").slideUp("slow"), jQuery(".update_shipping_info_wrapper").slideDown("slow"), stopLoader()
    }), jQuery("div.update_shipping_info_wrapper div.close_form").click(function(e) {
        e.preventDefault(), startLoader(), jQuery("div.update_shipping_info_wrapper span.result").remove(), jQuery(".shipping_info_wrapper").css("visibility", "visible").slideDown("slow"), jQuery(".update_shipping_info_wrapper").slideUp("slow"), stopLoader()
    }), jQuery("body").on("click", "button.edit_shipping_address", function(e) {
        e.preventDefault(), startLoader(), jQuery("div.form-group").removeClass("has-error");
        e = jQuery(this).closest("tr").attr("user_info_id");
        jQuery.ajax({
            type: "POST",
            url: "/?option=com_ajaxorder&task=getUserAddress",
            data: {
                user_info_id: e
            },
            dataType: "json",
            user_info_id: e,
            success: function(e) {
                e.result ? (jQuery.each(e.shipping_obj, function(e, t) {
                    "address_type2" == e && ("B" == t && (t = "Business"), "R" != t && "" != t || (t = "Home/Residence"), jQuery(".div_shipping_info_company").show(), "Home/Residence" == t || "Business" == t ? ("Business" == t ? jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing Business name...") : jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing address..."), jQuery(".autocomplete_address").show(), jQuery(".autocomplete_place").hide()) : (jQuery("#googleaddressupdate_place").attr("placeholder", "start typing City or name of " + t + "..."), jQuery(".autocomplete_address").hide(), jQuery(".autocomplete_place").show()), jQuery("#place_result").html("").hide()), jQuery("#shipping_info_" + e).val(t)
                }), jQuery(".shipping_info_wrapper").css("visibility", "hidden").slideUp("slow"), jQuery(".update_shipping_info_wrapper").slideDown("slow")) : alert("Error."), stopLoader()
            }
        })
    }), jQuery("body").on("click", ".sameAsBillingBtn", function(e) {
        e.preventDefault(), startLoader(), jQuery("div.form-group").removeClass("has-error"), jQuery.ajax({
            type: "POST",
            url: "/index.php?option=com_ajaxorder&task=copyUserBillingAddress",
            dataType: "json",
            success: function(e) {
                e.result ? (jQuery.each(e.billingObj, function(e, t) {
                    "address_type2" == e && ("B" == t && (t = "Business"), "R" != t && "" != t || (t = "Home/Residence"), jQuery(".div_shipping_info_company").show(), "Home/Residence" == t || "Business" == t ? ("Business" == t ? jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing Business name...") : jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing address..."), jQuery(".autocomplete_address").show(), jQuery(".autocomplete_place").hide()) : (jQuery("#googleaddressupdate_place").attr("placeholder", "start typing City or name of " + t + "..."), jQuery(".autocomplete_address").hide(), jQuery(".autocomplete_place").show()), jQuery("#place_result").html("").hide()), jQuery("#shipping_info_" + e).val(t)
                }), jQuery(".shipping_info_wrapper").css("visibility", "hidden").slideUp("slow"), jQuery(".update_shipping_info_wrapper").slideDown("slow")) : alert("Error."), stopLoader()
            }
        })
    }), jQuery("body").on("click", "button.remove_shipping_address", function(e) {
        e.preventDefault(), startLoader();
        e = jQuery(this).closest("tr").attr("user_info_id");
        jQuery.ajax({
            type: "POST",
            url: "/?",
            data: {
                option: "com_ajaxorder",
                task: "RemoveAddress",
                user_info_id: e
            },
            dataType: "json",
            user_info_id: e,
            success: function(e) {
                e.result ? jQuery.ajax({
                    url: "/?option=com_virtuemart&func=getSTAddresses",
                    dataType: "json",
                    success: function(e) {
                        var r;
                        e.result ? (r = "", jQuery.each(e.shipping_addresses, function(e, t) {
                            console.log(t), r += t
                        }), jQuery("table.shipping_addresses").html(r), jQuery("div.update_shipping_info_wrapper div.close_form").trigger("click"), jQuery.ajax({
                            type: "POST",
                            url: "/?option=com_virtuemart&func=getDonationData",
                            dataType: "json",
                            success: function(e) {
                                e.result && (jQuery("#donation_label").html(e.donate.label), jQuery("#donation_label").attr("title", e.donate.text), jQuery("#donation_id").val(e.donate.id), jQuery("label.container.donation").show())
                            }
                        })) : jQuery("table.shipping_addresses").html(""), stopLoader()
                    }
                }) : (alert("Error."), stopLoader())
            }
        })
    }),jQuery('body').on({
            mouseenter: function () {
                jQuery('.unavailable_date_message').css({'visibility':'visible'});
            },
            mouseleave: function () {
                jQuery('.unavailable_date_message').css({'visibility':'hidden'});
            }
        },
        '.delivery_calendar .day_inner.past'
    ), jQuery("div.calendar_wrapper div.close_form").click(function(e) {
        e.preventDefault(), startLoader(), jQuery("div.delivery_date_wrapper").css("visibility", "visible").slideDown("slow"), jQuery("div.calendar_wrapper").removeClass('showCalendar').slideUp("slow"), stopLoader()
    }), jQuery("body").on("click", ".delivery_calendar .next_month, .delivery_calendar .pre_month", function(e) {
        e.preventDefault(), startLoader(), jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=getDeliveryCalendarHTML",
            data: {
                delivery_date: jQuery(this).attr("delivery_date")
            },
            dataType: "json",
            success: function(e) {
                e.result && (jQuery("div.calendar_wrapper .calendar").html(e.calendar), stopLoader())
            }
        })
    }), jQuery("body").on({
        mouseenter: function() {
            jQuery("#delivery_date").text(jQuery(this).attr("delivery_beaty_date")).show()
        },
        mouseleave: function() {
            jQuery("#delivery_date").hide()
        }
    }, ".delivery_calendar .day_inner.ready, .delivery_calendar .day_inner.now"), jQuery("body").on("click touchstart", ".delivery_calendar .day_inner.ready, .delivery_calendar .day_inner.now", function(e) {
         startLoader(), jQuery("#delivery_date_2").val(jQuery(this).attr("delivery_date")), jQuery("div.delivery_date_wrapper").css("visibility", "visible").slideDown("slow"), jQuery("div.calendar_wrapper").slideUp("slow", function() {
            jQuery("html, body").animate({
                scrollTop: jQuery("#delivery_date_2").offset().top
            }, 1e3, stopLoader()), jQuery("#calculate").click()
        })
    }), jQuery("#redeem_credits").change(function() {
        jQuery("#calculate").click()
    }), jQuery("body").on("change", "input[type=radio][name=delivery_option_id]", function() {
        startLoader(), jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=getDeliveryCalendarHTML",
            data: {
                delivery_date: jQuery("#delivery_date_2").val(),
                delivery_option_id: jQuery(this).val()
            },
            dataType: "json",
            success: function(e) {
                e.result && (jQuery("div.calendar_wrapper .calendar").html(e.calendar), stopLoader())
            }
        })
    }), jQuery("body").on("change", "input[type=radio][name=user_info_id]", function() {
        startLoader(), jQuery("div.delivery_date_wrapper").css("visibility", "visible").slideDown("slow"), jQuery("div.calendar_wrapper").removeClass('showCalendar').slideUp("slow"), jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=setDeliveryAddress",
            data: {
                user_info_id: jQuery(this).val()
            },
            dataType: "json",
            success: function(e) {
                e.result && jQuery.ajax({
                    type: "POST",
                    url: "/?option=com_virtuemart&func=getDonationData",
                    dataType: "json",
                    success: function(e) {
                        e.result && (jQuery("#donation_label").html(e.donate.label), jQuery("#donation_label").attr("title", e.donate.text), jQuery("#donation_id").val(e.donate.id), jQuery("label.container.donation").show(), "" != jQuery("#delivery_date_2").val() && (jQuery("#delivery_date_2").val(""), jQuery(".ddate_tooltip").show(), setTimeout(function() {
                            jQuery(".ddate_tooltip").hide("slow")
                        }, 4e3))), stopLoader()
                    }
                })
            }
        })
    }), jQuery("body").on("change", "input[type=radio][name=radio_shipping_address]", function() {
        startLoader();
        var t = jQuery(this).val();
        jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=setDeliveryAddress",
            data: {
                user_info_id: t
            },
            dataType: "json",
            success: function(e) {
                e.result && ($("#shipping_info_user_info_id").val(t), $("#shipping_info_first_name").val(e.data.first_name), $("#shipping_info_last_name").val(e.data.last_name), $("#shipping_info_phone_1").val(e.data.phone_1), $("#shipping_info_user_email").val(e.data.user_email), $("#shipping_info_company").val(e.data.company), $("#shipping_info_suite").val(e.data.suite), $("#shipping_info_street_number").val(e.data.street_number), $("#shipping_info_street_name").val(e.data.street_name), $("#shipping_info_city").val(e.data.city), $("#shipping_info_state").val(e.data.state), $("#shipping_info_zip").val(e.data.zip), stopLoader())
            }
        })
    }), jQuery("body").on("input keydown", "#card_number", function() {
        var e = parseInt(jQuery(this).val()[0]);
        jQuery(".payment_method").removeClass("active").addClass("unactive"), 4 == e ? jQuery(".visa").removeClass("unactive").addClass("active") : 5 == e || 6 == e ? jQuery(".mastercard").removeClass("unactive").addClass("active") : 3 == e && jQuery(".american_express").removeClass("unactive").addClass("active")
    }), jQuery("div.coupon_field button").click(function(e) {
        e.preventDefault(), jQuery("div.coupon_field div.result").hide(), startLoader(), jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=SetCouponCode",
            data: {
                coupon_code: jQuery("#coupon_code").val()
            },
            dataType: "json",
            success: function(e) {
                e.result ? (jQuery("div.coupon_field div.result").text(e.info).css("display", "inline-block"), "undefined" != typeof url_coupon_code && 0 != url_coupon_code && (jQuery("div.coupon_field .coupon_text_desc").hide(), jQuery("div.coupon_field #coupon_btn").hide()), loadCart()) : jQuery("div.coupon_field div.result").text(e.error).css("display", "inline-block"), stopLoader()
            }
        })
    }), "undefined" != typeof url_coupon_code && 0 != url_coupon_code && jQuery("div.coupon_field button").trigger("click"), jQuery("div.checkout_buttons_wrapper button#calculate").click(function(e) {
        e.preventDefault(), jQuery("div.checkout_buttons_wrapper div.result").hide(), startLoader();

            jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=GetTotalAjax",
            data: {
                delivery_date: jQuery("#delivery_date_2").val(),
                redeem_bucks: jQuery("#redeem_bucks").is(":checked") ? 1 : 0,
                donation_id: jQuery("#donation_id").is(":checked") ? jQuery("#donation_id").val() : 0,
                redeem_credits: jQuery("#redeem_credits").is(":checked") ? 1 : 0,
                proof_drinking_age: jQuery("#proof_drinking_age").is(":checked") ? 1 : 0
            },
            dataType: "json",
            success: function(e) {
                e.result ? (jQuery("div.price_inner div.total_item_price span.price").text("$" + e.products_price), jQuery("div.price_inner div.total_item_saved_price span.price").text("$" + e.products_saved_price), jQuery("div.price_inner div.corporate_discount span.price").text("-$" + e.corporate_discount), jQuery("div.price_inner div.coupon_discount span.price").text("-$" + e.coupon_discount), jQuery("div.price_inner div.total_bloomex_bucks span.price").text("-$" + e.used_bucks), jQuery("div.price_inner div.total_credits span.price").text("-$" + e.used_credits), jQuery("div.price_inner div.total_donate span.price").text("$" + e.donated_price), jQuery("div.price_inner div.total_delivery_price span.price").text("$" + e.shipping_price), jQuery("div.price_inner div.total_discount span.price").text("-$" + e.coupon_discount), jQuery("div.price_inner div.total_price span.price").text("$" + (parseFloat(e.total_price) + parseFloat(e.donated_price)).toFixed(2)), jQuery("div.price_inner div.total_price span.price").attr("totalPrice", (parseFloat(e.total_price) + parseFloat(e.donated_price)).toFixed(2)), jQuery("#redeem_credits").is(":checked") && parseFloat(e.total_price) + parseFloat(e.donated_price) == "0.00" ? jQuery(".payment_wrapper").slideUp(500) : jQuery(".payment_wrapper").slideDown(500)) : jQuery("div.checkout_buttons_wrapper div.result").text(e.error[0]),stopLoader()
            }
        })
    }),
        $("input[name='payment_method_state']").click(function(){
            ($(this).val() == 'stripe') ? jQuery(".payment_wrapper .payment_inner").slideUp(500) : jQuery(".payment_wrapper .payment_inner").slideDown(500)
        })

        , jQuery("div.checkout_buttons_wrapper button#confirm").click(function(e) {
        return e.preventDefault(), 1 == jQuery(".update_shipping_info_wrapper").is(":visible") ? (jQuery(".update_shipping_info_wrapper .btn").after('<span class="result">Please save address first</span>'), jQuery("html, body").animate({
            scrollTop: jQuery(".update_shipping_info_wrapper .btn").offset().top - 100
        }, 1e3, stopLoader()), !1) : (jQuery("div.checkout_buttons").hide(), jQuery("div.checkout_buttons_wrapper div.result").hide(), jQuery("div.checkout_processing").show(), startLoader(), void jQuery.ajax({
            type: "POST",
            url: "/?option=com_virtuemart&func=GetTotalAjax",
            data: {
                delivery_date: jQuery("#delivery_date_2").val(),
                redeem_bucks: jQuery("#redeem_bucks").is(":checked") ? 1 : 0,
                donation_id: jQuery("#donation_id").is(":checked") ? jQuery("#donation_id").val() : 0,
                redeem_credits: jQuery("#redeem_credits").is(":checked") ? 1 : 0,
                proof_drinking_age: jQuery("#proof_drinking_age").is(":checked") ? 1 : 0
            },
            dataType: "json",
            success: function(e) {
                e.result ? (jQuery("div.price_inner div.total_item_price span.price").text("$" + e.products_price), jQuery("div.price_inner div.total_item_saved_price span.price").text("$" + e.products_saved_price), jQuery("div.price_inner div.total_delivery_price span.price").text("$" + e.shipping_price), jQuery("div.price_inner div.corporate_discount span.price").text("-$" + e.corporate_discount), jQuery("div.price_inner div.total_discount span.price").text("$" + e.coupon_discount), jQuery("div.price_inner div.total_price span.price").text("$" + (parseFloat(e.total_price) + parseFloat(e.donated_price)).toFixed(2)), jQuery("div.price_inner div.total_price span.price").attr("totalPrice", (parseFloat(e.total_price) + parseFloat(e.donated_price)).toFixed(2)), jQuery("#redeem_credits").is(":checked") && parseFloat(e.total_price) + parseFloat(e.donated_price) == "0.00" ? jQuery(".payment_wrapper").slideUp(500) : jQuery(".payment_wrapper").slideDown(500), grecaptcha.reset(), grecaptcha.execute()) : (jQuery("div.checkout_buttons_wrapper div.result").show().text(e.error[0]), jQuery("div.checkout_processing").hide(), jQuery("div.checkout_buttons").show(), stopLoader())
            }
        }))
    }), jQuery("body").on("click", "div.cart_modal_wrapper div.remove", function(e) {
        e.preventDefault(), jQuery(this).closest("div.cart_modal_wrapper").remove()
    }), jQuery("body").on("click", "div.cart_modal_wrapper div.cart_modal_inner div.buttons button.continue", function(e) {
        e.preventDefault(), jQuery(this).closest("div.cart_modal_wrapper").remove()
    }), jQuery("body").on("click", "div.cart_modal_wrapper div.cart_modal_inner div.buttons button.proceed", function(e) {
        e.preventDefault(), window.location.href = "/cart/"
    }), jQuery("body").on("click", "div.cart_modal_wrapper div.cart_modal_inner div.buttons button.proceed_fast", function(e) {
        e.preventDefault(), window.location.href = "/fast-checkout/"
    }), jQuery("body").on("click", "div.hover_cart_wrapper div.hover_cart_inner div.buttons button", function(e) {
        e.preventDefault(), startLoader(), window.location.href = "/cart/"
    }), jQuery("body").on("click", "div.hover_cart_wrapper div.hover_cart_inner div.buttons button.proceed_fast", function(e) {
        e.preventDefault(), startLoader(), window.location.href = "/fast-checkout/"
    }), jQuery("#lostpass_button").click(function(e) {
        e.preventDefault(), jQuery("#lostpass_info").hide(), jQuery("#lostpass_error").hide(), jQuery("#lostpass_success").hide(), jQuery("#lostpass_table").hide(), jQuery("#lostpass_loader").show(), jQuery.ajax({
            type: "POST",
            url: "/",
            async: !0,
            dataType: "json",
            data: {
                option: "com_registration",
                task: "sendNewPass",
                confirmEmail: jQuery("#confirmEmail").val()
            },
            success: function(e) {
                jQuery("#lostpass_loader").hide(), (e.result ? jQuery("#lostpass_success") : jQuery("#lostpass_error")).html(e.msg).show(), jQuery("#lostpass_info").show()
            }
        })
    }), jQuery("#updatePass_button").click(function(e) {
        e.preventDefault(), jQuery("#updatePass_info").hide(), jQuery("#updatePass_error").hide(), jQuery("#updatePass_success").hide(), jQuery("#updatePass_loader").show(), jQuery.ajax({
            type: "POST",
            url: "/",
            async: !0,
            dataType: "json",
            data: {
                option: "com_registration",
                task: "updatePass",
                update: "1",
                old_password: jQuery("#old_password").val(),
                new_password: jQuery("#new_password").val(),
                new_password_2: jQuery("#new_password_2").val()
            },
            success: function(e) {
                jQuery("#updatePass_loader").hide(), (e.result ? jQuery("#updatePass_success") : jQuery("#updatePass_error")).html(e.msg).show(), jQuery("#updatePass_info").show()
            }
        })
    }), jQuery("#lostpass_button_new").click(function(e) {
        e.preventDefault(), jQuery("#lostpass_info").hide(), jQuery("#lostpass_error").hide(), jQuery("#lostpass_success").hide(), jQuery("#lostpass_table").hide(), jQuery("#lostpass_loader").show(), jQuery.ajax({
            type: "POST",
            url: "/",
            async: !0,
            dataType: "json",
            data: {
                option: "com_registration",
                task: "createPass",
                createnew: "1",
                hash: jQuery("#user_hash").val(),
                new_password: jQuery("#new_password").val(),
                new_password_2: jQuery("#new_password_2").val()
            },
            success: function(data) {
                jQuery('#lostpass_loader').hide();

                if (data.result) {
                    jQuery('#lostpass_success').html(data.msg + " <br><b>Redirecting in <span id='countdown' style='font-size: 16px; color:red;'>5</span> seconds...</b>").show();
                    jQuery('#lostpass_info').show();

                    let countdown = 5;
                    let interval = setInterval(function () {
                        countdown--;
                        jQuery('#countdown').text(countdown);

                        if (countdown === 0) {
                            clearInterval(interval);
                            window.location.href = '/account/';
                        }
                    }, 1000);
                } else {
                    jQuery('#lostpass_error').html(data.msg).show();
                    jQuery('#lostpass_info').show();
                }
            }
        })
    }), jQuery("span.landing_content_more_btn").click(function(e) {
        e.preventDefault(), jQuery(this).hide(), jQuery("span.landing_content_more").show()
    }),
    // "undefined" != typeof landing_image && null !== landing_image && initMap(),
        jQuery("span.city_load_more").click(function(e) {
        e.preventDefault(), jQuery(this).hide(), jQuery(".hidden_cities").show()
    }), document.getElementById("shipping_info_zip") && (t = document.getElementById("shipping_info_zip"), r = function(e) {
        return /^\d*$/.test(e)
    }, ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(e) {
        t.addEventListener(e, function() {
            r(this.value) ? (this.oldValue = this.value, this.oldSelectionStart = this.selectionStart, this.oldSelectionEnd = this.selectionEnd) : this.hasOwnProperty("oldValue") && (this.value = this.oldValue, this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd))
        })
    })), jQuery(".tooltripHoverBox").mouseover(function() {
        jQuery(".tooltripDiv").hasClass("hideTooltrip") ? jQuery(this).find(".tooltripDiv").hide() : jQuery(this).find(".tooltripDiv").show()
    }), jQuery(".tooltripHoverBox").mouseout(function() {
        jQuery(this).find(".tooltripDiv").hide()
    }), jQuery(".closeTooltrip").click(function() {
        jQuery(".tooltripDiv").addClass("hideTooltrip").hide()
    }), jQuery(".bouquet_info_icon").click(function(e) {
        e.preventDefault();
        var r = "";
        jQuery(".product_size_type_tooltrip").each(function(e, t) {
            t = jQuery(t).clone();
            jQuery("span", t).remove(), r += t.html() + "<br>", 0 == e && (r += "<hr style='width: 50%'><br>")
        }), jQuery(".bouquet_info").html('<span class="close_tooltrip_popup">X</span>' + r).show()
    }), jQuery(".bouquet_info").click(function() {
        jQuery(this).hide()
    }), jQuery(".mobile_popup > .inner > .close_btn").click(function(e) {
        e.preventDefault(), jQuery(".mobile_popup").animate({
            top: -154
        }, 500)
    }), jQuery("#registration_form #email").change(function(e) {
        jQuery.ajax({
            type: "POST",
            url: "/index.php",
            async: !0,
            dataType: "json",
            data: {
                option: "com_registration",
                task: "checkEmail",
                email: jQuery(this).val()
            },
            context: this,
            beforeSend: function() {
                jQuery(this).closest("form").find("button").prop("disabled", !0), jQuery(this).parent().removeClass("has-success has-error"), jQuery(this).next(".error").hide(), jQuery(this).addClass("loading")
            },
            success: function(e) {
                e.result ? (jQuery(this).removeClass("loading"), jQuery(this).closest("form").find("button").prop("disabled", !1)) : (jQuery(this).removeClass("loading"), jQuery(this).parent().addClass("has-error"), jQuery(this).next(".error").css("display", "block"))
            }
        })
    }), jQuery("#guest_form #email").change(function(e) {
        jQuery.ajax({
            type: "POST",
            url: "/index.php",
            async: !0,
            dataType: "json",
            data: {
                option: "com_registration",
                task: "checkEmail",
                email: jQuery(this).val()
            },
            context: this,
            beforeSend: function() {
                jQuery(this).closest("form").find("button").prop("disabled", !0), jQuery(this).parent().removeClass("has-success has-error"), jQuery(this).next(".error").hide(), jQuery(this).addClass("loading")
            },
            success: function(e) {
                e.result ? (jQuery(this).removeClass("loading"), jQuery(this).closest("form").find("button").prop("disabled", !1)) : (jQuery(this).removeClass("loading"), jQuery(this).parent().addClass("has-error"), jQuery(this).next(".error").css("display", "block"))
            }
        })
    }), jQuery(".description-footer > .row .toggle").click(function(e) {
        e.preventDefault();
        e = jQuery(this).closest(".description-footer");
        e.hasClass("opened") ? e.removeClass("opened") : e.addClass("opened")
    }), $('input[name="extra_products"]').change(function() {
        const parentBox = $(this).parents(".extraProductBox");
        const priceSpan = parentBox.find(".price");
        if ($(this).prop("checked")) {
            parentBox.css({
                "border-color": "#4CAF50"
            });
            priceSpan.css("color", "#4CAF50");
        } else {
            parentBox.css({
                "border-color": "#eee"
            });
            priceSpan.css("color", "");
        }
    }), jQuery("#bouquet_info_icon").click(function(e) {
        e.preventDefault(), jQuery("#bouquet-info-alert").toggle()
    })
})



$(".promotion_countdown").each(function(e, t) {
    countdownPromotion($(t).attr("date_end"), $(t).attr("product_id"))
});

var modalElement = document.getElementById('BeforeYouLeaveDiv');
var isModalVisible = null;
if (modalElement) {
    isModalVisible = window.getComputedStyle(modalElement).display === 'block';
}
if(!isMobile() && !isModalVisible && !getCookie("closedOveralPopup")) {
    showClientOverlayCapture()
}

function selectItemFromList(items,item_list_name,item_list_id,href) {
    pushGoogleAnalytics(
        'select_item',
        [items],
        null,
        null,
        "AUD",
        item_list_name,
        item_list_id
    );

    window.location.href = href;
    return true;
}

document.addEventListener("DOMContentLoaded", function () {
    const cartBox = document.querySelector(".sticky-buy-box");
    if (cartBox) {
        const cartOffsetTop = cartBox.offsetTop; // исходная позиция блока

        function toggleSticky() {
            if (window.scrollY > cartOffsetTop) {
                if (!cartBox.classList.contains("fixed")) {
                    cartBox.classList.add("fixed");
                }
            } else {
                if (cartBox.classList.contains("fixed")) {
                    cartBox.classList.remove("fixed");
                }
            }
        }

        window.addEventListener("scroll", toggleSticky);
    }
});
