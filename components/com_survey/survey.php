<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
require_once( $mainframe->getPath( 'front_html' ) );


switch( $task ) {
    case 'save':
        savePage();
        break;
    default:
        viewPage( );
        break;
}
function viewPage(  ) {
    global $mosConfig_live_site,$database;
    $user_id 	= trim(mosGetParam( $_GET, "user_id" ));
    $order_id 	= trim(mosGetParam( $_GET, "order_id" ));

    $q = "SELECT id from tbl_survey where user_id='".$user_id."' and order_id='".$order_id."'";
    $database->setQuery($q);
    $res = $database->loadResult();
    if( (int)$order_id < 1 OR $res) header("Location: $mosConfig_live_site");
    if( $user_id ) {
        HTML_Survey::viewPage($user_id, $order_id);
    } else {
        header('location: http://bloomex.com.au');
    }
}
function savePage(  ) {
    global  $database;

    if(!strpos($_SERVER['HTTP_USER_AGENT'],'Firefox') && !strpos($_SERVER['HTTP_USER_AGENT'],'Trident')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=6LdJvGgUAAAAAPH2Fo5RBuQy_EIkhm-6wgQuineo&response=" . $_REQUEST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if(curl_error($ch))
        {
            echo 'error:' . curl_error($ch);
        }
        $capcha_res = json_decode($server_output);
        if ($capcha_res->success)
        {
            $capcha = true;
        }else{
            $capcha = false;
        }
    }else{
        $capcha = true;
    }


    $user_id = trim(mosGetParam($_POST, "user_id"));
    if ($capcha && $user_id) {
        $order_id = trim(mosGetParam($_POST, "order_id"));
        $place_order = trim(mosGetParam($_POST, "place_order"));
        $comments = trim(mosGetParam($_POST, "comments"));
        $how_would_you_rate_the_bloomex_website = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_website"));
        $how_would_you_rate_the_bloomex_product_selection_and_prices = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_product_selection_and_prices"));
        $how_would_you_rate_the_bloomex_ordering_process = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_ordering_process"));
        $how_closely_did_your_item_s_resemble_the_product_description = trim(mosGetParam($_POST, "how_closely_did_your_item_s_resemble_the_product_description"));
        $how_would_you_rate_the_freshness_quality_and_appearance_of_your = trim(mosGetParam($_POST, "how_would_you_rate_the_freshness_quality_and_appearance_of_your"));
        $how_would_you_rate_your_delivery_experience = trim(mosGetParam($_POST, "how_would_you_rate_your_delivery_experience"));
        $how_would_you_rate_your_customer_service_experience = trim(mosGetParam($_POST, "how_would_you_rate_your_customer_service_experience"));
        $how_was_your_experience_overall = trim(mosGetParam($_POST, "how_was_your_experience_overall"));
        $how_likely_are_you_to_recommend_bloomex_to_others = trim(mosGetParam($_POST, "how_likely_are_you_to_recommend_bloomex_to_others"));


        $q = "INSERT INTO `tbl_survey` ( 
                        `user_id`,
                        `place_order`,
                        `how_would_you_rate_the_bloomex_website`,
                        `how_would_you_rate_the_bloomex_product_selection_and_prices`,
                        `how_would_you_rate_the_bloomex_ordering_process`,
                        `how_closely_did_your_item_s_resemble_the_product_description`,
                        `how_would_you_rate_the_freshness_quality_and_appearance_of_your`,
                        `how_would_you_rate_your_delivery_experience`,
                        `comments`,
                        `how_would_you_rate_your_customer_service_experience`,
                        `survey_date`,
                        `order_id`,
                        `how_was_your_experience_overall`,
                        `how_likely_are_you_to_recommend_bloomex_to_others`
                        )
    VALUES (";
        $q .= $user_id . ",";
        $q .= "'" . $place_order . "',";
        $q .= "'" . $how_would_you_rate_the_bloomex_website . "',";
        $q .= "'" . $how_would_you_rate_the_bloomex_product_selection_and_prices . "',";
        $q .= "'" . $how_would_you_rate_the_bloomex_ordering_process . "',";
        $q .= "'" . $how_closely_did_your_item_s_resemble_the_product_description . "',";
        $q .= "'" . $how_would_you_rate_the_freshness_quality_and_appearance_of_your . "',";
        $q .= "'" . $how_would_you_rate_your_delivery_experience . "',";
        $q .= "'" . $comments . "',";
        $q .= "'" . $how_would_you_rate_your_customer_service_experience . "',";
        $q .= "UNIX_TIMESTAMP(NOW()),";
        $q .= "'" . $order_id . "',";
        $q .= "'" . $how_was_your_experience_overall . "',";
        $q .= "'" . $how_likely_are_you_to_recommend_bloomex_to_others . "')";
        $database->setQuery($q);
        $database->query();
        HTML_Survey::savePage();
    } else {
        header('location: http://bloomex.com.au');
    }

}
?>
