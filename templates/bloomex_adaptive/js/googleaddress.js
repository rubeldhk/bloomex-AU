var autocomplete_billing,
    autocomplete_shipping,
    address_details,
    country_billing = "AUS",
    componentForm = { street_number: "short_name", route: "long_name", locality: "long_name", administrative_area_level_1: "short_name", country: "short_name", postal_code: "short_name" };
function fillInAddress(e) {
    var t,
        a = jQuery.parseJSON(countries_json),
        s = jQuery.parseJSON(states_short_name_json);
    if (((t = ("billing" == e ? autocomplete_billing : autocomplete_shipping).getPlace()), (address_details = new Object()), t.address_components))
        for (var o = 0; o < t.address_components.length; o++) {
            var l,
                i = t.address_components[o].types[0];
            componentForm[i] && ((l = t.address_components[o][componentForm[i]]), "country" == i ? (address_details[i] = a[l]) : "administrative_area_level_1" == i && 2 < l.length ? (address_details[i] = s[l]) : (address_details[i] = l));
        }
    t.name && "Home/Residence" != jQuery("#shipping_info_address_type2").val() && (address_details.company_name = t.name);
}
function get_billing_country() {
    jQuery.ajax({
        type: "GET",
        url: "/",
        async: !1,
        data: { option: "com_ajaxorder", task: "get_billing_country" },
        dataType: "json",
        success: function (e) {
            e.msg && (country_billing = e.msg);
        },
    });
}
function initAutocomplete() {
    function a() {
        address_details.street_number ? jQuery("#shipping_info_street_number").val(address_details.street_number).addClass("autocomplete") : jQuery("#shipping_info_street_number").val("").removeClass("autocomplete"),
            address_details.route ? jQuery("#shipping_info_street_name").val(address_details.route).addClass("autocomplete") : jQuery("#shipping_info_street_name").val("").removeClass("autocomplete"),
            address_details.locality ? jQuery("#shipping_info_city").val(address_details.locality).addClass("autocomplete") : jQuery("#shipping_info_city").val("").removeClass("autocomplete"),
        0 == $("#checkoutStep").length &&
        (address_details.postal_code ? jQuery("#shipping_info_zip").val(address_details.postal_code).addClass("autocomplete"): jQuery("#shipping_info_zip").val("").removeClass("autocomplete"),
            jQuery("#shipping_info_country").val("AUS").addClass("autocomplete"),
            address_details.administrative_area_level_1 ? jQuery("#shipping_info_state").val(address_details.administrative_area_level_1).addClass("autocomplete") : jQuery("#shipping_info_state").val("").removeClass("autocomplete"));
    }
    function e() {
        var e, t;
        (e = jQuery("#shipping_info_address_type2").val().toLowerCase()),
            (t = jQuery(".autocomplete_place").val()),
            jQuery.ajax({
                type: "GET",
                url: "/",
                data: { option: "com_ajaxorder", task: "get_google_place", place: e, query_string: t },
                dataType: "json",
                success: function (e) {
                    "empty" != e.msg
                        ? (jQuery("#place_result").show().html(e.msg),
                            jQuery(".place_result_item").on("click", function () {
                                var t = jQuery(this).text();
                                jQuery.ajax({
                                    type: "GET",
                                    url: "/",
                                    data: { option: "com_ajaxorder", task: "get_google_geocode", address: jQuery(this).attr("address") },
                                    dataType: "json",
                                    success: function (e) {
                                        jQuery("#place_result").html("").hide(),
                                        "empty" != e.msg &&
                                        (((address_details = e.msg).company_name = t),
                                            (e = jQuery.parseJSON(states_short_name_json)),
                                        address_details.administrative_area_level_1 &&
                                        2 < address_details.administrative_area_level_1.length &&
                                        (address_details.administrative_area_level_1 = e[address_details.administrative_area_level_1]),
                                            a());
                                    },
                                });
                            }))
                        : jQuery("#place_result").html("").hide();
                },
            });
    }
    var t, s, o;
    jQuery("#registration_form").length
        ? (get_billing_country(),
            jQuery("#last_name")
                .parent()
                .after(
                    '<div class="form-group registraton_autocomplete"><label for="googleaddress">Address Autocomplete</label><input type="text" class="form-control" id="googleaddressbilling"  placeholder="Start typing your Address..."></div>'
                ),
            (t = document.getElementById("googleaddressbilling")),
            (autocomplete_billing = new google.maps.places.Autocomplete(t, { types: ["geocode"] })).addListener("place_changed", function (e) {
                fillInAddress("billing"),
                    address_details.street_number ? jQuery("#address_street_number").val(address_details.street_number).addClass("autocomplete") : jQuery("#address_street_number").val("").removeClass("autocomplete"),
                    address_details.route ? jQuery("#address_street_name").val(address_details.route).addClass("autocomplete") : jQuery("#address_street_name").val("").removeClass("autocomplete"),
                    address_details.locality ? jQuery("#city").val(address_details.locality).addClass("autocomplete") : jQuery("#city").val("").removeClass("autocomplete"),
                    address_details.postal_code ? jQuery("#zip").val(address_details.postal_code).addClass("autocomplete") : jQuery("#zip").val("").removeClass("autocomplete"),
                    address_details.country ? jQuery("#country_billing").val(address_details.country).addClass("autocomplete") : jQuery("#country_billing").val("").removeClass("autocomplete"),
                    changeStateList(),
                    address_details.administrative_area_level_1 ? jQuery("#state").val(address_details.administrative_area_level_1).addClass("autocomplete") : jQuery("#state").val("").removeClass("autocomplete");
            }))
        : jQuery("#update_billing_info_form").length
            ? (get_billing_country(),
                jQuery("#billing_info_user_email")
                    .parent()
                    .after(
                        '<div class="form-group autocomplete-form-group"><label for="googleaddress">Address Autocomplete</label><input type="text" class="form-control" id="googleaddressbilling_update"  placeholder="Start typing your Address..."></div>'
                    ),
                (o = document.getElementById("googleaddressbilling_update")),
                (autocomplete_billing = new google.maps.places.Autocomplete(o, { types: ["geocode"] })).addListener("place_changed", function (e) {
                    fillInAddress("billing"),
                        address_details.street_number ? jQuery("#billing_info_street_number").val(address_details.street_number).addClass("autocomplete") : jQuery("#billing_info_street_number").val("").removeClass("autocomplete"),
                        address_details.route ? jQuery("#billing_info_street_name").val(address_details.route).addClass("autocomplete") : jQuery("#billing_info_street_name").val("").removeClass("autocomplete"),
                        address_details.locality ? jQuery("#billing_info_city").val(address_details.locality).addClass("autocomplete") : jQuery("#billing_info_city").val("").removeClass("autocomplete"),
                        address_details.postal_code ? jQuery("#billing_info_zip").val(address_details.postal_code).addClass("autocomplete") : jQuery("#billing_info_zip").val("").removeClass("autocomplete"),
                        address_details.country ? jQuery("#billing_info_country").val(address_details.country).addClass("autocomplete") : jQuery("#billing_info_country").val("").removeClass("autocomplete"),
                        changeStateList("billing_info_state", "billing_info_country"),
                        address_details.administrative_area_level_1 ? jQuery("#billing_info_state").val(address_details.administrative_area_level_1).addClass("autocomplete") : jQuery("#billing_info_state").val("").removeClass("autocomplete");
                }))
            : jQuery("#update_shipping_info_form").length &&
            (jQuery("#shipping_info_user_email")
                .parent()
                .after(
                    '<div class="form-group autocomplete-form-group"><label for="googleaddressupdate_shipping">Address Autocomplete</label><input type="text" style="display: none" class="form-control autocomplete_place" placeholder="" id="googleaddressupdate_place"><div id="place_result"></div><input type="text" class="form-control  autocomplete_address" id="googleaddressupdate_shipping"  placeholder="start typing address..."></div>'
                ),
                jQuery("#shipping_info_user_email")
                    .parent()
                    .after(
                        '<div class="form-group autocomplete-form-group address_type_hidden"><label for="shipping_info_address_type2">Address Type:</label><select id="shipping_info_address_type2" class="form-control" name="address_type2"><option value="Home/Residence" >Home/Residence</option><option value="Business">Business</option><option value="Funeral Home">Funeral Home</option><option value="Hospital">Hospital</option><option value="School">School</option><option value="Place of Worship">Place of Worship</option><option value="Hotel">Hotel</option><option value="Nursing Home">Nursing/Retirement Home</option></select></div>'
                    ),
                (o = document.getElementById("googleaddressupdate_shipping")),
                (autocomplete_shipping = new google.maps.places.Autocomplete(o, { types: ["geocode"], componentRestrictions: { country: "AUS" } })).addListener("place_changed", function (e) {
                    fillInAddress("shipping"), a();
                }),
                jQuery("#shipping_info_address_type2").change(function () {
                    var e;
                    jQuery(".div_shipping_info_company").show(),
                        "Home/Residence" == jQuery(this).val() || "Business" == jQuery(this).val()
                            ? (jQuery(".autocomplete_address").show(),
                                jQuery(".autocomplete_place").hide(),
                                "Business" == jQuery(this).val()
                                    ? (jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing Business name..."), (e = ["establishment"]), autocomplete_shipping.setTypes(e))
                                    : (jQuery("#googleaddressupdate_shipping").attr("placeholder", "start typing address..."), (e = ["geocode"]), autocomplete_shipping.setTypes(e)))
                            : (jQuery("#googleaddressupdate_place").attr("placeholder", "start typing City or name of " + jQuery(this).val() + "..."), jQuery(".autocomplete_address").hide(), jQuery(".autocomplete_place").show()),
                        console.log(autocomplete_shipping),
                        jQuery("#place_result").html("").hide();
                }),
                (o = jQuery(".autocomplete_place")).on("keyup", function () {
                    clearTimeout(s), (s = setTimeout(e, 500));
                }),
                o.on("keydown", function () {
                    clearTimeout(s);
                }));
}
