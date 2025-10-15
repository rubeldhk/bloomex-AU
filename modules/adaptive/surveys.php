<link rel="stylesheet" href="/templates/<?php echo $cur_template; ?>/css/slick.css">
<link rel="stylesheet" href="/templates/<?php echo $cur_template; ?>/css/slick-theme.css">
<div class="container bottom_0 pt-4 pb-5">
    <h3 class="text-center">What Our Customers Are Saying</h3>
    <div class="row survey_slick">
<?php
global $database;

$query = " SELECT tsad.*,ju.name from tbl_survey_after_order tsad 
inner join (select name,id from jos_users where CHAR_LENGTH(name) > 3 and name != email) as ju on ju.id = tsad.user_id 
where tsad.how_would_you_rate_the_bloomex_website > 3 
and tsad.how_would_you_rate_the_bloomex_product_selection_and_prices > 3 
and tsad.how_would_you_rate_the_bloomex_ordering_process > 3 
and tsad.comments != ''
group by tsad.user_id  order by tsad.user_id desc limit 10 ";

$database->setQuery($query);

$surveys = $database->loadObjectList();

if ($surveys) {
    foreach ($surveys as $survey) {

        echo ' <div class="col-xs-12 col-md-3 col-sm-6 survey_item padding0">
                            <div class="survey_head">
                                <span class="first_letter_cycle">' .ucfirst($survey->name)[0] . '</span>
                                <span class="survey_name">' . $survey->name . '</span>
                            </div>
                            <hr>
                            <div class="survey_stars">
                                <table>
                                    <tr>
                                        <td>
                                            <span class="stars_list">Service</span>
                                        </td>
                                        <td> 
                                            <img alt="Star" class="star_image" src="/templates/'.$cur_template.'/images/star-1.svg"><span style="font-weight: bold;">' . $survey->how_would_you_rate_the_bloomex_website . '</span>/5
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="stars_list">Price/Quality</span> 
                                        </td>
                                        <td> 
                                            <img alt="Star" class="star_image" src="/templates/'.$cur_template.'/images/star-1.svg"><span style="font-weight: bold;">' . $survey->how_would_you_rate_the_bloomex_product_selection_and_prices . '</span>/5
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="stars_list">Delivery</span> 
                                        </td>
                                        <td>
                                            <img alt="Star" class="star_image" src="/templates/'.$cur_template.'/images/star-1.svg"><span style="font-weight: bold;">' . $survey->how_would_you_rate_the_bloomex_ordering_process . '</span>/5
                                        </td>
                                    </tr>
                                </table>
    
                            </div>
                            <div class="tooltripHoverBox">
                                <p class="survey_comments">'.$survey->comments.'</p>
                                <div class="tooltripDiv">
                                  <span>
                                        '.$survey->comments.'
                                   </span>
                                </div>  
                            </div>  
                        </div>';
    }
}
?>
    </div>
</div>


<script>
    $(document).ready(function(){
        $('.survey_slick').slick({
            dots: !0,
            slidesToShow: 4,
            draggable:!1,
            prevArrow: !1,
            nextArrow: !1,
            zIndex: 1000,
            slidesToScroll: 4,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: !0,
                    dots: !0
                }
            }, {
                breakpoint: 991,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            }, {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: !1
                }
            }]
        });

    });
</script>

