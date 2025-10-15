<?php

defined('_VALID_MOS') or die('Restricted access');
require_once( $mainframe->getPath('front_html') );


switch ($task) {
    case 'save':
        savePage();
        break;
    default:
        viewPage();
        break;
}

function viewPage() {
    global $mosConfig_live_site, $database;
    $data = parse_str(base64_decode(trim(mosGetParam($_GET, "data"))), $output);
    $user_id = $output['user_id'];
    $order_id = $output['order_id'];

    $q = "SELECT id from tbl_cron_survey_send 
      where order_id='" . $database->getEscaped($order_id) . "' and type='order'  and survey_page_open_datetime is null ";
    $database->setQuery($q);
    $res = $database->loadResult();

    if ($res) {
        $q_u="UPDATE tbl_cron_survey_send SET  survey_page_open_datetime = NOW() WHERE id=".$res;
        $database->setQuery($q_u);
        $database->query();
    }

    $q = "SELECT id from tbl_survey_after_order where user_id='" . $user_id . "' and order_id='" . $order_id . "'";
    $database->setQuery($q);
    $res = $database->loadResult();
    if ($res) {
        $msg = 'You have already filled this survey';
        header("Location: $mosConfig_live_site" . "?mosmsg=" . urlencode($msg));
    }
    if ($user_id && (int) $order_id) {
        HTML_Survey_after_order::viewPage($user_id, $order_id);
    } else {
        $msg = 'Sorry, we cannot find your survey';
        header("Location: $mosConfig_live_site" . "?mosmsg=" . urlencode($msg));
    }
}

function savePage() {
    global $database, $mosConfig_mailfrom, $survey_notification_email_address, $mosConfig_fromname;

    if (!strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Trident')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=6LdJvGgUAAAAAPH2Fo5RBuQy_EIkhm-6wgQuineo&response=" . $_REQUEST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if (curl_error($ch)) {
            echo 'error:' . curl_error($ch);
        }
        $capcha_res = json_decode($server_output);
        if ($capcha_res->success) {
            $capcha = true;
        } else {
            $capcha = false;
        }
    } else {
        $capcha = true;
    }


    $user_id = trim(mosGetParam($_POST, "user_id"));
    if ($capcha && $user_id) {
        $order_id = trim(mosGetParam($_POST, "order_id"));
        $place_order = trim(mosGetParam($_POST, "place_order"));
        $comments = trim(mosGetParam($_POST, "comments"));
        $book_datetime = trim(mosGetParam($_POST, "book_datetime"));
        $how_would_you_rate_the_bloomex_website = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_website"));
        $how_would_you_rate_the_bloomex_product_selection_and_prices = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_product_selection_and_prices"));
        $how_would_you_rate_the_bloomex_ordering_process = trim(mosGetParam($_POST, "how_would_you_rate_the_bloomex_ordering_process"));
        $how_would_you_rate_your_customer_service_experience = trim(mosGetParam($_POST, "how_would_you_rate_your_customer_service_experience"));
        $how_was_your_experience_overall = trim(mosGetParam($_POST, "how_was_your_experience_overall"));
        $how_likely_are_you_to_recommend_bloomex_to_others = trim(mosGetParam($_POST, "how_likely_are_you_to_recommend_bloomex_to_others"));


        $q = "INSERT INTO `tbl_survey_after_order` ( 
                        `user_id`,
                        `place_order`,
                        `how_would_you_rate_the_bloomex_website`,
                        `how_would_you_rate_the_bloomex_product_selection_and_prices`,
                        `how_would_you_rate_the_bloomex_ordering_process`,
                        `comments`,
                        `how_would_you_rate_your_customer_service_experience`,
                        `survey_date`,
                        `book_datetime`,
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
        $q .= "'" . $comments . "',";
        $q .= "'" . $how_would_you_rate_your_customer_service_experience . "',";
        $q .= "UNIX_TIMESTAMP(NOW()),";
        $q .= "'" . $book_datetime . "',";
        $q .= "'" . $order_id . "',";
        $q .= "'" . $how_was_your_experience_overall . "',";
        $q .= "'" . $how_likely_are_you_to_recommend_bloomex_to_others . "')";
        $database->setQuery($q);
        $database->query();

        $subject = "New survey. Order " . $order_id;
        $body = "Book Datetime: " . $book_datetime . "\r\n\r\n";
        $body .= "Survey Type: Sender \r\n\r\n";
        $body .= "Comment: " . $comments . "\r\n\r\n";
        mosMail($mosConfig_mailfrom, $mosConfig_fromname, $survey_notification_email_address, $subject, $body);

        $q = "SELECT id from tbl_cron_survey_send 
      where order_id='" . $database->getEscaped($order_id) . "' and type='order'  and survey_send_datetime is null ";
        $database->setQuery($q);
        $res = $database->loadResult();
        if ($res) {
            $q_u="UPDATE tbl_cron_survey_send SET  survey_send_datetime = NOW() WHERE id=".$res;
            $database->setQuery($q_u);
            $database->query();
        }


        if (
                in_array($how_likely_are_you_to_recommend_bloomex_to_others, ['4', '5']) &&
                in_array($how_was_your_experience_overall, ['4', '5']) &&
                in_array($how_would_you_rate_your_customer_service_experience, ['4', '5']) &&
                in_array($how_would_you_rate_the_bloomex_product_selection_and_prices, ['4', '5']) &&
                in_array($how_would_you_rate_the_bloomex_ordering_process, ['4', '5']) &&
                in_array($how_would_you_rate_the_bloomex_website, ['4', '5'])
        ) {
            HTML_Survey_after_order::savePage(true);
        } else {
            HTML_Survey_after_order::savePage(false);
        }
    } else {
        header('location: http://bloomex.com.au');
    }
}

?>
