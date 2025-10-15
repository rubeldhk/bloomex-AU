<?php

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// load the html drawing class
require_once( $mainframe->getPath( 'front_html' ) );


$mainframe->setPageTitle( _CONTACT_TITLE );

switch( $task ) {
	case 'get_20_gift_card':
        get_20_gift_card($option);
		break;
	default:
		viewForm( $option );
		break;
}


function viewForm( $option) {
    HTML_get_20_gift_card::viewForm($option);
}

 function generateCode() {
    global $database;
    $code = 'RGC-';
   $coupon_symbols=array(
         'B',
         'D',
         'G',
         'H',
         'J',
         'K',
         'L',
         'M',
         'N',
         'Q',
         'R',
         'T',
         'V',
         'W',
         'X',
         'Z',
         '1',
         '2',
         '3',
         '4',
         '5',
         '6',
         '7',
         '8',
         '9'
     );

    for ($i = 1; $i <= 6; $i++) {
        $code .= $coupon_symbols[array_rand($coupon_symbols)];
    }
     $query = "SELECT 
            `c`.`coupon_id`
        FROM `jos_vm_coupons` AS `c`
        WHERE `c`.`coupon_code`='".$code."'
        ";

     $database->setQuery($query);
     $res = $database->loadRow();
    if (!$res) {
        $query = "INSERT INTO `jos_vm_coupons`
            (
                `coupon_code`, 
                `percent_or_total`,
                `coupon_type`,
                `coupon_value`
            ) 
            VALUES (
                '".$database->getEscaped($code)."',
                'total',
                'gift',
                '20.00'
            )";

        $database->setQuery($query);
        $database->query();
        return $code;
    }
    else {
        return generateCode();
    }
}

function get_20_gift_card( $option ) {
    global $mainframe, $database, $mosConfig_fromname,$mosConfig_mailfrom_noreply;

    $email= strval( mosGetParam( $_POST, 'email', 		'' ) );
    $first_name = strval( mosGetParam( $_POST, 'first_name', 		'' ) );
    $last_name = strval( mosGetParam( $_POST, 'last_name', 		'' ) );
    $upass = strval( mosGetParam( $_POST, 'upass', 		'' ) );
    $phone = strval( mosGetParam( $_POST, 'phone', 		'' ) );
    $country = strval( mosGetParam( $_POST, 'country', 		'' ) );

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

    if($capcha){
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip_address = "unknown";
        }



    $query = "SELECT 
            `email`
        FROM `tbl_get_20_gift_card` AS `c`
        WHERE `email`='".$database->getEscaped($email)."' OR `ip_address`='".$database->getEscaped($ip_address)."'
        ";

    $database->setQuery($query);
    $res = $database->loadRow();

    if (!$res) {

        $query = "SELECT id from jos_users WHERE email = '".$database->getEscaped($email)."'";
        $database->setQuery($query);
        $user = $database->loadResult();
        if ($database->getErrorMsg()) {
            die(__LINE__ . $database->getErrorMsg());
        }

        if (!$user) {
            $user_id = addRegisteredUser($email,$upass,$first_name,$last_name,$phone,$country);
        }

        $code=generateCode();
        $query = "INSERT INTO `tbl_get_20_gift_card`
            (
                `coupon_code`, 
                `email`,
                `user_id`,
                `first_name`,
                `last_name`,
                `phone`,
                `country`,
                `ip_address`,
                `date_added`
            ) 
            VALUES (
                '".$database->getEscaped($code)."',
                '".$database->getEscaped($email)."',
                '".($user??$user_id??'')."',
                '".$database->getEscaped($first_name)."',
                '".$database->getEscaped($last_name)."',
                '".$database->getEscaped($phone)."',
                '".$database->getEscaped($country)."',
                '".$database->getEscaped($ip_address)."',
                NOW()
            )";

        $database->setQuery($query);
        $database->query();


        $query = "SELECT
            `e`.`email_subject`, 
            `e`.`email_html` 
        FROM `jos_vm_emails` AS `e`
        WHERE 
            `e`.`email_type`='10'
        ";
        $database->setQuery($query);
        $emails = $database->loadObjectList();
        if($emails){
            $subject = $emails[0]->email_subject;
            $body = str_replace('{CouponCode}', $code, $emails[0]->email_html);
        }
        mosMail( $mosConfig_mailfrom_noreply, $mosConfig_fromname , $email, $subject, $body,true );
        $msg = 'success';
    }else{
        $msg = "<h4><b class='text-danger'>You can't get coupon more than one time</b></h4>";
    }

    }else{
        $msg = "Incorrect CAPTCHA";
    }
    $link = sefRelToAbs( '?msg='.$msg.'&user='.($user??$user_id??'') );

    mosRedirect( $link);

}


?>