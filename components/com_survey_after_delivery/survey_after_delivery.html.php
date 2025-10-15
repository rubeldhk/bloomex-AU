<?php
defined('_VALID_MOS') or die('Restricted access');

class HTML_Survey_after_delivery {

    function viewPage($user_id, $order_id) {
        global $mosConfig_live_site;
        ?>

        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bootstrap/js/moment.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/js/survey.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/js/jquery.datetimepicker.js"></script>
        <link rel="stylesheet" href="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/css/jquery.datetimepicker.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <div class="container padding-0">
            <form method="post" action="index.php?option=com_survey_after_delivery&task=save&user_id=<?php echo $user_id ?>&order_id=<?php echo $order_id ?>" class="form-survey" id="form-survey">
                <div class="body-survey">
                    <div>
                        <h2>Customer Care Survey</h2>
                        <p>Your experience matters to us.</p>
                        <hr>
                    </div>

                    <div class="rate-survey-body">

                        <p class="survey_section_title"><strong>How would you rate your experience with us? </strong></p>
                        <table class="survey_table">

                            <tr>
                                <td>How would you rate the freshness, quality and appearance of your item(s)?</td>
                                <td>
                                    <div class='rating-stars '>
                                        <ul data-rating="" data-name="how_would_you_rate_the_freshness_and_appearance_of_your_items" class='stars'>
                                            <li class='star' title='Poor' data-value='1'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Fair' data-value='2'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Good' data-value='3'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Excellent' data-value='4'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='WOW!!!' data-value='5'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>How would you rate your delivery experience?</td>
                                <td>
                                    <div class='rating-stars '>
                                        <ul data-rating="" data-name="how_would_you_rate_your_delivery_experience" class='stars'>
                                            <li class='star' title='Poor' data-value='1'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Fair' data-value='2'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Good' data-value='3'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Excellent' data-value='4'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='WOW!!!' data-value='5'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>How was your experience overall?</td>
                                <td>
                                    <div class='rating-stars '>
                                        <ul data-rating="" data-name="how_was_your_experience_overall" class='stars'>
                                            <li class='star' title='Poor' data-value='1'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Fair' data-value='2'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Good' data-value='3'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='Excellent' data-value='4'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                            <li class='star' title='WOW!!!' data-value='5'>
                                                <i class='fa fa-star fa-fw'></i>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                        </table>
                        <hr>

                        <p class="survey_section_title"><strong>Would you like to book an appointment with our care manager? (optional)</strong></p>
                        <input id="datetimepicker" name="book_datetime" type="text" autocomplete="off" >

                        <p class="survey_section_title"><strong>How can we improve our service?</strong></p>
                        <textarea placeholder="Please add your comments here." cols="64" rows="7" class="form-control" name="comments" id="comments"></textarea>


                        <input onclick="send_survey()" type="button" id="Submit" title="Submit" value="Submit Survey">
                        <button style="visibility: hidden" class="g-recaptcha capcha_validate"
                                data-sitekey="6LdJvGgUAAAAAM_cyb03MYOn5oxYZlGwAonw7Npi"
                                data-callback="submitform">
                        </button>


                        <div class="submit-survey">
                            <input type="hidden" name="option" value="com_survey_after_delivery"/>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />

                            <input type="hidden" name="how_would_you_rate_the_freshness_and_appearance_of_your_items" id="how_would_you_rate_the_freshness_and_appearance_of_your_items" value="" />
                            <input type="hidden" name="how_would_you_rate_your_delivery_experience" id="how_would_you_rate_your_delivery_experience" value="" />
                            <input type="hidden" name="how_was_your_experience_overall" id="how_was_your_experience_overall" value="" />

                        </div>
                    </div>
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                </div>
            </form>
        </div>
        <script>
               jQuery('#datetimepicker').datetimepicker({
                                minDate: 0,
                                minTime: 0,
                                allowTimes: [
                                    '10:00',
                                    '11:00',
                                    '12:00',
                                    '13:00',
                                    '14:00',
                                    '15:00',
                                    '16:00',
                                    '17:00',
                                    '18:00',
                                    '19:00',
                                    '20:00'

                                ],
                                format: 'Y-m-d H:i',
                                disabledWeekDays: [0, 6],
                                scrollTime: true
                });
        </script>
        <style>
            .top_4,.top_5{
                display: none;
            }
            @media(max-width: 761.9px) {
                table.survey_table tr td:first-child{
                    width: 55%;
                }
                .rating-stars ul > li.star > i.fa{
                    font-size: 1.2em;
                }
            }
        </style>


        <?php
    }

    function savePage($excellent_rate = false) {
        global $mosConfig_live_site, $database;
        ?>

        <style>
            .top_4,.top_5{
                display: none;
            }
            .body-survey{
                text-align: center;
            }
            .logo-survey{
                width: 50%;
                margin: 0 auto;
            }
            .logo-survey img{
                width:100%;
            }
            .inner_image{
                width: 50%;
            }
            .inner_image img{
                max-width: 100%;
            }
            .inner_image.left{
                float: left;
            }
            .inner_image.right{
                float: right;
            }
        </style>
        <div class="container padding-0">
            <div class="logo-survey">
                <a href="<?php echo $mosConfig_live_site; ?>">
                    <img src="<?php echo $mosConfig_live_site; ?>/templates/bloomex7/images/logo_survey.jpg" alt="BloomEx.ca"/>
                </a>
            </div>
            <div class="body-survey">

                <div>
                    <h2 class="header-survey">Successfully Submitted!</h2>
                </div>
                <div class="event-date-p">
                    <?php if ($excellent_rate) { ?>
                        <p>We take pride in being able to provide the best customer experience that we can. We would appreciate it a lot if you could leave us a review on one of the following platforms (your choice):</p>

                    <div class="image">
                        <img style="max-width:100% " src="/templates/bloomex7/images/20_gift_card_banner_english_Bloomex_top.jpg"/><br/>

                        <?php
                        $database->setQuery("SELECT
                            `id`, 
                            `image`, 
                            `url`, 
                            `type`, 
                            `percent`
                        FROM `tbl_thankyou_review_links` 
                        WHERE `published`=1");
                        $review_obj = $database->loadObjectList();

                        if ($review_obj) {
                            $rev_arr = array();
                            $rev_arr_new = array();
                            foreach ($review_obj as $rev) {
                                $rev_arr_new[$rev->id] = $rev;
                                if ($rev->type == 'company') {
                                    if ($rev->percent) {
                                        for ($j = 0; $j < $rev->percent; $j++) {
                                            $rev_arr['company'][] = $rev->id;
                                        }
                                    }
                                }
                                if ($rev->type == 'google') {
                                    if ($rev->percent) {
                                        for ($m = 0; $m < $rev->percent; $m++) {
                                            $rev_arr['google'][] = $rev->id;
                                        }
                                    }
                                }
                            }
                            $google_index = array_rand($rev_arr['google']);
                            $google_review_link = $rev_arr_new[$rev_arr['google'][$google_index]]->url;
                            $google_image_link = $rev_arr_new[$rev_arr['google'][$google_index]]->image;

                            $company_index = array_rand($rev_arr['company']);
                            $company_link = $rev_arr_new[$rev_arr['company'][$company_index]]->url;
                            $company_image_link = $rev_arr_new[$rev_arr['company'][$company_index]]->image;
                            ?>
                            <div style="clear:both"></div><br/>
                            <div>
                                <div >
                                    <a href="<?php echo $google_review_link; ?>"  target="_blank">
                                        <img style="margin-top: 5px" src="/images/thankyou_images/<?php echo $google_image_link; ?>">
                                    </a>
                                </div>

                            </div>

                        <?php } ?>

                    </div>
                        <div style="clear:both"></div><br/>
                        <div class="foot_review_container">
                            <img style="max-width: 100%" src="/templates/bloomex7/images/footer-thank-you-banner.jpg">
                        </div>
                        <div style="clear:both"></div><br/>

                    <?php } else { ?>
                        <p>Thank you for your feedback. We appreciate it! All comments will be reviewed to better serve you in the future.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }

}
?>