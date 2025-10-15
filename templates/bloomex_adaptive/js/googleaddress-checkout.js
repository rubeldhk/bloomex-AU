function fillInAddress(place) {
    var componentForm = {
        colloquial_area: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        postal_code: 'short_name',
        postal_code_prefix: 'short_name'
    };
    var address_details = {};
    if (place.address_components) {
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                address_details[addressType] = val;
            }

        }
    }
    var address = {};
    address['state'] = address_details.administrative_area_level_1;
    return address;
}

function initAutocomplete() {

    var cfg = {
        types: ['geocode'],
        language: 'en-EN',
        componentRestrictions: {
            country: 'AUS'
        }
    }

    var googleaddressupdate_shipping = (document.getElementById('googleaddressupdate_shipping'));

    var autocomplete_shipping = new google.maps.places.Autocomplete(
        googleaddressupdate_shipping,
        cfg
    );

    autocomplete_shipping.addListener('place_changed', function (event) {
        const place = autocomplete_shipping.getPlace();
        const delivery_address = fillInAddress(place);
        setshippingvalue(delivery_address);
    });
}

function setshippingvalue(address) {
    let statesNames = {
        'ACT' : 'AT',
        'NSW' : 'NW',
        'NT' : 'NT',
        'QLD' : 'QL',
        'SA' : 'SA',
        'TAS' : 'TA',
        'VIC' : 'VI',
        'WA' : 'WA'
    }
    if (address.state) {
        jQuery('#shipping_info_state').val(statesNames[address.state]).addClass('autocomplete');
    } else {
        jQuery('#shipping_info_state').removeClass('autocomplete');
    }
    jQuery('#delivery_date_2').val('');
    jQuery('div.calendar_wrapper div.close_form').click();
}