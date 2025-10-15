<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_Survey {

    function viewPage($user_id, $order_id){
        global $mosConfig_live_site;
        ?>

        <script type="text/javascript" src="<?php echo $GLOBALS['mosConfig_live_site']; ?>/templates/bloomex7/js/survey.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <div class="container-survey">
            <div class="logo-survey">
                <a href="<?php echo $mosConfig_live_site; ?>">
                    <img src="templates/bloomex_adaptive/images/bloomexlogo.png" alt="bloomex.com.au"/>
                </a>
            </div>
            <form method="post" action="index.php?option=com_survey&task=save&user_id=<?php echo $user_id ?>&order_id=<?php echo $order_id ?>" class="form-survey" id="form-survey">
                <div class="body-survey">
                    <div>
                        <p>Thank you for your purchase. Your Bloomex order has been delivered.</p>
                        <h2>HOW DID WE DO?</h2>
                        <p>Please share your feedback, so that we can continue to improve…</p>
                        <p><strong>“Rate Us” on a scale of 1 -5 with 1 being the lowest and 5 being the highest…</strong></p>
                    </div>





                    <div class="rate-survey-body">
                        <section>
                            <p><strong>Order Process:</strong></p>

                            <p>1. How would you rate the Bloomex website?</p>
                            <div class='rating-stars '>
                                <ul data-rating="" data-name="how_would_you_rate_the_bloomex_website" class='stars'>
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
                            <p>2. How would you rate the Bloomex product selection and prices?</p>
                            <div class='rating-stars '>
                                <ul data-rating="" data-name="how_would_you_rate_the_bloomex_product_selection_and_prices" class='stars'>
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
                            <p>3. How did you place your order? </p>
                            <div class='rating-radio '>
                                <label>
                                    <input type="radio" name="place_order" value="Phone" />
                                    <span>Phone</span>
                                </label>
                                <label>
                                    <input type="radio" name="place_order" value="Online" />
                                    <span>Online</span>
                                </label>
                            </div>
                            <p>4. How would you rate the Bloomex ordering process?</p>
                            <div class='rating-stars'>
                                <ul data-rating="" data-name="how_would_you_rate_the_bloomex_ordering_process" class='stars'>
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
                        </section>
                        <section>
                            <p><strong>Delivery and Fulfillment:</strong></p>

                            <p>5. How closely did your item(s) resemble the product description and photo on the website?</p>
                            <div class='rating-stars'>
                                <ul data-rating="" data-name="how_closely_did_your_item_s_resemble_the_product_description" class='stars'>
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
                            <p>6. How would you rate the freshness, quality and appearance of your item(s)?</p>
                            <div class='rating-stars'>
                                <ul data-rating="" data-name="how_would_you_rate_the_freshness_quality_and_appearance_of_your" class='stars'>
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
                            <p>7. How would you rate your delivery experience?</p>
                            <div class='rating-stars'>
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
                        </section>
                        <section>
                            <p><strong>Customer Service and User Experience:</strong></p>

                            <p>8. How would you rate your Customer Service experience (if applicable)?</p>
                            <div class='rating-stars'>
                                <ul data-rating="" data-name="how_would_you_rate_your_customer_service_experience" class='stars'>
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
                            <p>9. How was your experience overall?</p>
                            <div class='rating-stars'>
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
                            <p>10. How likely are you to recommend Bloomex to others?</p>
                            <div class='rating-stars'>
                                <ul data-rating="" data-name="how_likely_are_you_to_recommend_bloomex_to_others" class='stars'>
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
                        </section>
                        <section>
                            <div>
                                <p><strong>Please provide any additional comments and/or feedback:</strong></p>
                            </div>
                            <div>
                                <textarea placeholder="Comments:" cols="64" rows="7" name="comments" id="comments"></textarea>
                            </div>
                            <input onclick="send_survey()" type="button" id="Submit" title="Submit" value="Submit">
                            <button style="visibility: hidden" class="g-recaptcha capcha_validate"
                                    data-sitekey="6LdJvGgUAAAAAM_cyb03MYOn5oxYZlGwAonw7Npi"
                                    data-callback="submitform">
                            </button>
                        </section>

                        <div class="submit-survey">
                            <input type="hidden" name="option" value="com_survey"/>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />

                            <input type="hidden" name="how_would_you_rate_the_bloomex_website" id="how_would_you_rate_the_bloomex_website" value="" />
                            <input type="hidden" name="how_would_you_rate_the_bloomex_product_selection_and_prices" id="how_would_you_rate_the_bloomex_product_selection_and_prices" value="" />
                            <input type="hidden" name="how_would_you_rate_the_bloomex_ordering_process" id="how_would_you_rate_the_bloomex_ordering_process" value="" />
                            <input type="hidden" name="how_closely_did_your_item_s_resemble_the_product_description" id="how_closely_did_your_item_s_resemble_the_product_description" value="" />
                            <input type="hidden" name="how_would_you_rate_the_freshness_quality_and_appearance_of_your" id="how_would_you_rate_the_freshness_quality_and_appearance_of_your" value="" />
                            <input type="hidden" name="how_would_you_rate_your_delivery_experience" id="how_would_you_rate_your_delivery_experience" value="" />
                            <input type="hidden" name="how_would_you_rate_your_customer_service_experience" id="how_would_you_rate_your_customer_service_experience" value="" />
                            <input type="hidden" name="how_was_your_experience_overall" id="how_was_your_experience_overall" value="" />
                            <input type="hidden" name="how_likely_are_you_to_recommend_bloomex_to_others" id="how_likely_are_you_to_recommend_bloomex_to_others" value="" />




                        </div>
                    </div>
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                </div>
            </form>
        </div>

        <?php

    }
    function savePage(){
        global $mosConfig_live_site;
        ?>
        <div class="container-survey">
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
                    <p>Thank you for your feedback. We appreciate it! All comments will be reviewed to better serve you in the future.</p>
                </div>
            </div>
        </div>
        <?php
    }
}
?>