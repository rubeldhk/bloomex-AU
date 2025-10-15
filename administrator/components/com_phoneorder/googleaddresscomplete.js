
var autocomplete_billing;
var autocomplete_shipping;
var address_details ;
var country_billing='AUS';
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    sublocality_level_1: 'short_name',
    country: 'short_name',
    postal_code: 'short_name'
};
function fillInAddress(type) {

    var countries = jQuery.parseJSON(countries_json)
    if(type=='billing'){
        var place = autocomplete_billing.getPlace();
    }else{
        var place = autocomplete_shipping.getPlace();
    }
    address_details = new Object();
    if(place.address_components){
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                if(addressType=='country'){
                    address_details[addressType] = countries[val];
                }else{
                    address_details[addressType] = val;
                }

            }
        }
    }
    if(place.name && jQuery('#delivery_address_type2').val()!='Home/Residence'){
        address_details['company_name'] = place.name;
    }
}



function initAutocomplete() {

    jQuery('input[name="bill_last_name"]').parent().parent().after('<tr><td width="30%" class="title">Address Autocomplete<font color="red"></font>:</td>' +
        '<td width="70%"><input size="30" type="text" class="form-control" id="googleaddressbilling_update"  placeholder="start typing address..."></td> </tr>');
    var googleaddressbilling_update = (document.getElementById('googleaddressbilling_update'));
    autocomplete_billing = new google.maps.places.Autocomplete(googleaddressbilling_update,
        {types: ['geocode']});
    autocomplete_billing.addListener('place_changed', function(event) {
        fillInAddress('billing');
        if(address_details.street_number){
            jQuery('input[name="bill_address_street_number"]').val(address_details.street_number).addClass('autocomplete')
        }else{
            jQuery('input[name="bill_address_street_number"]').val('').removeClass('autocomplete')
        }
        if(address_details.route){
            jQuery('input[name="bill_address_street_name"]').val(address_details.route).addClass('autocomplete')
        }else{
            jQuery('input[name="bill_address_street_name"]').val('').removeClass('autocomplete')
        }
        if(address_details.sublocality_level_1){
            jQuery('input[name="bill_district"]').val(address_details.sublocality_level_1).addClass('autocomplete')
        }else{
            jQuery('input[name="bill_district"]').val('').removeClass('autocomplete')
        }
        if(address_details.locality){
            jQuery('input[name="bill_city"]').val(address_details.locality).addClass('autocomplete')
        }else{
            jQuery('input[name="bill_city"]').val('').removeClass('autocomplete')
        }
        if(address_details.postal_code){
            jQuery('input[name="bill_zip_code"]').val(address_details.postal_code).addClass('autocomplete')
        }else{
            jQuery('input[name="bill_zip_code"]').val('').removeClass('autocomplete')
        }
        if(address_details.country){
            jQuery('#bill_country').val(address_details.country).addClass('autocomplete')
        }else{
            jQuery('#bill_country').val('').removeClass('autocomplete')
        }
        jQuery('#bill_country').change()
        setTimeout(function() {
                if (address_details.administrative_area_level_1) {
                    jQuery('#bill_state').val(address_details.administrative_area_level_1).addClass('autocomplete')
                } else {
                    jQuery('#bill_state').val('').removeClass('autocomplete')
                }
            }
            , 3000);
    });


    jQuery('input[name="deliver_last_name"]').parent().parent().after('<tr><td width="30%" class="title">Address Autocomplete<font color="red"></font>:</td>' +
        '<td width="70%"><input size="30" type="text" style="display: none" class="form-control autocomplete_place" placeholder="" id="googleaddressupdate_place"><div id="place_result"></div>' +
        '<input size="30" type="text" class="form-control  autocomplete_address" id="googleaddressupdate_shipping"  placeholder="start typing address..."></td></tr>');
    jQuery('input[name="deliver_last_name"]').parent().parent().after('<tr>'+
        '<td width="30%" class="title">Address Type<font color="red"></font>:</td>'+
        '<td width="70%">'+
        '<select id="delivery_address_type2" class="form-control" name="delivery_address_type2">'+
        '<option value="Home/Residence">Home/Residence</option>'+
        '<option value="Business">Business</option>'+
        '<option value="Funeral Home">Funeral Home</option>'+
        '<option value="Hospital">Hospital</option>'+
        '<option value="School">School</option>'+
        '<option value="Place of Worship">Place of Worship</option>'+
        '<option value="Hotel">Hotel</option>'+
        '<option value="Nursing Home2">Nursing/Retirement Home</option>'+
        '</select></td></tr>');

    var googleaddressupdate_shipping = (document.getElementById('googleaddressupdate_shipping'));
    autocomplete_shipping = new google.maps.places.Autocomplete(googleaddressupdate_shipping,
        {types: ['geocode'],componentRestrictions: {country: jQuery('#deliver_country').val()}});
    autocomplete_shipping.addListener('place_changed', function(event) {
        fillInAddress('shipping');
        setshippingvalue()
    });

    jQuery('#delivery_address_type2').change(function(){
        if(jQuery(this).val()=='Home/Residence' || jQuery(this).val()=='Business'){
            jQuery('.autocomplete_address').show()
            jQuery('.autocomplete_place').hide()
            if(jQuery(this).val()=='Business'){
                jQuery("#googleaddressupdate_shipping").attr('placeholder','start typing Business name...')
                autocomplete_shipping.types[0] = 'establishment';
            }else{
                jQuery("#googleaddressupdate_shipping").attr('placeholder','start typing address...')
                autocomplete_shipping.types[0] = 'geocode';
            }
        }else{
            jQuery("#googleaddressupdate_place").attr('placeholder','start typing City or name of '+jQuery(this).val()+'...')
            jQuery('.autocomplete_address').hide()
            jQuery('.autocomplete_place').show()
        }
        jQuery('#place_result').html('').hide()
    });


    function setshippingvalue(){

        const $stateObj =
            {
                'VIC':'VI',
                'NT':'NT',
                'WA':'WA',
                'ACT':'AT',
                'QLD':'QL',
                'TAS':'TA',
                'SA':'SA',
                'NSW':'NW',
                'Auckland':'AU',
                'Bay of Plenty':'BP',
                'Canterbury':'CA',
                'Gisborne':'GS',
                'Hawke`s Bay':'HB',
                'Manawatu-Wanganui':'MW',
                'Marlborough':'MB',
                'Nelson':'NS',
                'Northland':'NL',
                'Otago':'OT',
                'Southland':'SL',
                'Taranaki':'TK',
                'Waikato':'WK',
                'Wellington':'WG',
                'West Coast':'WC'
            };


        if(address_details.street_number){
            jQuery('input[name="deliver_address_street_number"]').val(address_details.street_number).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_address_street_number"]').val('').removeClass('autocomplete')
        }
        if(address_details.route){
            jQuery('input[name="deliver_address_street_name"]').val(address_details.route).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_address_street_name"]').val('').removeClass('autocomplete')
        }
        if(address_details.locality){
            jQuery('input[name="deliver_city"]').val(address_details.locality).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_city"]').val('').removeClass('autocomplete')
        }
        if(address_details.sublocality_level_1){
            jQuery('input[name="deliver_district"]').val(address_details.sublocality_level_1).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_district"]').val('').removeClass('autocomplete')
        }
        if(address_details.postal_code){
            jQuery('input[name="deliver_zip_code"]').val(address_details.postal_code).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_zip_code"]').val('').removeClass('autocomplete')
        }
        if(address_details.company_name){
            jQuery('input[name="deliver_company_name"]').val(address_details.company_name).addClass('autocomplete')
        }else{
            jQuery('input[name="deliver_company_name"]').val('').removeClass('autocomplete')
        }
        jQuery('#deliver_country').val(address_details.country).addClass('autocomplete').change()


        setTimeout(function(){

            if(address_details.administrative_area_level_1 && $stateObj[address_details.administrative_area_level_1]){
                jQuery('#deliver_state').val($stateObj[address_details.administrative_area_level_1]).addClass('autocomplete')
            }else{
                jQuery('#deliver_state').val('').removeClass('autocomplete')
            }
        }, 3000)

    }



    function create_places_list(place,query_string){
        jQuery.ajax({
            type: 'GET',
            url: '/index.php?',
            data: ({
                option: 'com_ajaxorder',
                task: 'get_google_place',
                place: place,
                query_string:query_string
            }),
            dataType: 'json',
            success: function (json) {
                if (json.msg!='empty') {
                    jQuery('#place_result').show().html(json.msg)
                    jQuery('.place_result_item').bind( "click",function(){
                        var company_name_selected = jQuery(this).text();
                        jQuery.ajax({
                            type: 'POST',
                            url: '/index.php?',
                            data: ({
                                option: 'com_ajaxorder',
                                task: 'get_google_geocode',
                                address:jQuery(this).attr('address')
                            }),
                            dataType: 'json',
                            success: function (json_details) {
                                jQuery('#place_result').html('').hide()
                                if (json_details.msg!='empty') {
                                    address_details = json_details.msg
                                    address_details.company_name = company_name_selected
                                    setshippingvalue()
                                }
                            }
                        })


                    })
                }else{
                    jQuery('#place_result').html('').hide()
                }
            }
        });

    }


    var typingTimer;
    var doneTypingInterval = 2000;
    var autocomplete_place = jQuery('.autocomplete_place');
    autocomplete_place.bind( "keyup", function(e) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });
    autocomplete_place.bind( "keydown", function(e) {
        clearTimeout(typingTimer);
    });
    function doneTyping () {
        create_places_list(jQuery('#delivery_address_type2').val(),jQuery('.autocomplete_place').val());
    }



}

