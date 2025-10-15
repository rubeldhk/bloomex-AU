jQuery( document ).ready(function() {
   
    jQuery('#send-form').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(jQuery(this)[0]);
        
        jQuery.ajax({
            url: '/scripts/deliveries/optimoroute/index.php?action=send',
            data: formData,
            type: 'post',
            dataType: 'json',
            contentType: false,
            processData: false,
            context: this,
            beforeSend: function() {
                jQuery(this)
                .find('.btn')
                .prop('disabled', true)
                .find('.spinner-border').removeClass('d-none');
            },
            success: function (json) {
                if (json.result) {
                    if(jQuery('#amount_of_packages').val()>1){
                        for(var k=1;k<jQuery('#amount_of_packages').val();k++){
                            jQuery('.label_div').append(jQuery('.label_wrapper').html())
                        }
                    }


                    jQuery('.barcode > img').attr('src', 'data:image/png;base64,' + json.barcode);
                    jQuery('.barcode_text').text(json.order_id);
                    
                    for (let i = 1; i <= json.clones; i++) {
                        jQuery('.labels').append(jQuery('#label').clone());
                    }
                    
                    jQuery(this).hide();
                    
                    jQuery('.labels').show();


                    window.opener.jQuery('.delivery_icon_'+json.order_id)
                    .addClass('default')
                    .attr('href', '')
                    .attr('order_id', json.order_id)
                    .find('img').attr('src', '/templates/bloomex7/images/deliveries/Optimoroute_logo.png');
                    window.opener.jQuery('.delivery_icon_span_' + json.order_id).text('Updated');
                }
                else {
                    alert(json.error);
                }
                
                jQuery(this)
                .find('.btn')
                .prop('disabled', false)
                .find('.spinner-border').addClass('d-none');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                jQuery(this)
                .find('.btn')
                .prop('disabled', false)
                .find('.spinner-border').addClass('d-none');
            },
            timeout: 30000
        })
        
    });
    
});
