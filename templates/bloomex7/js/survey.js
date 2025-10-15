jQuery(document).ready(function(){
    jQuery('.stars li').on('mouseover', function(){
        var onStar = parseInt(jQuery(this).attr("data-value"), 10);
        jQuery(this).parent().children('li.star').each(function(e){
            if (e < onStar) {
                jQuery(this).addClass('hover');
            }
            else {
                jQuery(this).removeClass('hover');
            }
        });
    }).on('mouseout', function(){
        jQuery(this).parent().children('li.star').each(function(e){
            jQuery(this).removeClass('hover');
        });
    });

    jQuery('.stars li').on('click', function(){
        var onStar = parseInt(jQuery(this).attr("data-value"), 10);
        var stars = jQuery(this).parent().children('li.star');
        for (i = 0; i < stars.length; i++) {
            jQuery(stars[i]).removeClass('selected');
        }
        for (i = 0; i < onStar; i++) {
            jQuery(stars[i]).addClass('selected');
        }

        var ratingValue = parseInt(jQuery(this).parent().find('.selected').last().attr("data-value"), 10);
        var input_name = jQuery(this).parent().attr('data-name');
        jQuery('#'+input_name).val(ratingValue);
        jQuery(this).parent().attr('data-rating',ratingValue);
    });
});
function submitform() {
    document.getElementById("form-survey").submit();
}
function send_survey(){
    var checked = false;
    jQuery('.rating-stars ul.stars').each(function( index ) {
        if(jQuery( this ).attr('data-rating')){
            checked = true
        }
    });
    if(checked){
        jQuery('.capcha_validate').click()
    }else{
        alert('we can not submit empty form');
    }
}