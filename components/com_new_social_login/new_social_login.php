<?php

session_name('virtuemart');
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);

global $mainframe,$mosConfig_live_site;

function CheckSocialUser($user_profile) {
    global $database;

    $return = array();
    $return['result'] = false;

    $query = "SELECT 
        s.id as social_id,s.user_id,u.id
    FROM `tbl_social_users` as s
    LEFT JOIN jos_users as u on u.id=s.user_id
    WHERE 
        s.`social`='".$database->getEscaped($user_profile->social)."'
    AND 
        s.`social_user_id`='".$database->getEscaped($user_profile->social_id)."'
    ";

    $user_obj = false;
    $database->setQuery($query);
    $database->loadObject($user_obj);
    if($user_obj){
        if ($user_obj->id && $user_obj->user_id) {
            $return['result'] = true;
            $return['user_id'] = $user_obj->user_id;
        }else{
            $query = "DELETE FROM `tbl_social_users` 
        WHERE 
            `id`='".$database->getEscaped($user_obj->social_id)."'
        ";
            $database->setQuery($query);
            $database->query();
        }
    }

    return $return;
}
 
Switch ($social = $_REQUEST['social'] ? $_REQUEST['social'] : '') {
    
    case 'GooglePlus':
        Switch($task = $_REQUEST['task'] ? $_REQUEST['task'] : '') {
            case 'auth':
                $return = array();
                $return['result'] = true;

                $user_profile = new stdClass();
                $user_profile->social = $social;
                $user_profile->social_id = $_POST['social_user_id'];

                $check_return = CheckSocialUser($user_profile);

                if ($check_return['result'] == true) {
                    $mainframe->login_social($check_return['user_id']);
                }
                else {
                    $_SESSION['social_info'] = array(
                        'name' => $social,
                        'user_id' => $_POST['social_user_id']
                    );
                }

                echo json_encode($return);

                die;
            break;
        }
    break;

    case 'Twitter':
        $consumer_key = 'b1cG19N19CvTuGalpQKSuASfa';
        $consumer_secret = 'CGytGsg2j9ZUTiQUZHp7pDnkmmlmmQWG3npQblOJA8WUNqwvvv';

        $access_token = '478945998-uUJzhg8iSDCgdpg2wReXRtuuhgdHEzbvH4cZdWWE';
        $access_token_secret = 'IEM4PLR9xrcHU3Ir32N3g5yblwiKILy4lcamzor74pUDd';

        Switch($task = $_REQUEST['task'] ? $_REQUEST['task'] : '') {
            case 'get_token': 
                $return = array();
                $return['result'] = false;

                $headers = array(
                    'oauth_callback' => $mosConfig_live_site.'/login/twitter',
                    'oauth_consumer_key' => $consumer_key,
                    'oauth_token' => $access_token,
                    'oauth_nonce' => (string)mt_rand(),
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_timestamp' => time(),
                    'oauth_version' => '1.0'
                );
                $headers = array_map('rawurlencode', $headers);
                $url = 'https://api.twitter.com/oauth/request_token';
                asort($headers); 
                ksort($headers); 
                $querystring = urldecode(http_build_query($headers, '', '&'));

                $base_string = 'GET&'.rawurlencode($url).'&'.rawurlencode($querystring);

                $key = rawurlencode($consumer_secret).'&'.rawurlencode($access_token_secret);
                $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
                $headers['oauth_signature'] = $signature;

                function add_quotes($str) { 
                    return '"'.$str.'"'; 
                }
                $headers = array_map('add_quotes', $headers);

                $auth = 'OAuth '.urldecode(http_build_query($headers, '', ', '));

                $curl_request = curl_init();
                curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Authorization: '.$auth));
                curl_setopt($curl_request, CURLOPT_HEADER, false);
                curl_setopt($curl_request, CURLOPT_URL, $url);
                curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_request, CURLOPT_VERBOSE, 1);
                $str = curl_exec($curl_request);
                curl_close($curl_request);

                parse_str($str, $output);

                if ($output['oauth_callback_confirmed'] == true) {
                    $return['result'] = true;
                    $return['oauth_token'] = $output['oauth_token'];
                }

                echo json_encode($return);
                
                die;
            break;

            case 'auth':
                $headers = array(
                    'oauth_consumer_key' => $consumer_key,
                    'oauth_token' => $_GET['oauth_token'],
                    'oauth_nonce' => (string)mt_rand(),
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_timestamp' => time(),
                    'oauth_version' => '1.0'
                );
                $headers = array_map('rawurlencode', $headers);
                $url = 'https://api.twitter.com/oauth/access_token';
                asort($headers); 
                ksort($headers); 
                $querystring = urldecode(http_build_query($headers, '', '&'));

                $base_string = 'POST&'.rawurlencode($url).'&'.rawurlencode($querystring);

                $key = rawurlencode($consumer_secret).'&'.rawurlencode($access_token_secret);
                $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
                $headers['oauth_signature'] = $signature;

                function add_quotes($str) { 
                    return '"'.$str.'"'; 
                }
                $headers = array_map('add_quotes', $headers);

                $auth = 'OAuth '.urldecode(http_build_query($headers, '', ', '));

                $curl_request = curl_init();
                curl_setopt($curl_request, CURLOPT_HTTPHEADER, array('Authorization: '.$auth));
                curl_setopt($curl_request, CURLOPT_HEADER, false);
                curl_setopt($curl_request, CURLOPT_URL, $url);
                curl_setopt($curl_request, CURLOPT_POST, 1);
                curl_setopt($curl_request, CURLOPT_POSTFIELDS, 'oauth_verifier='.$_GET['oauth_verifier']);
                curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_request, CURLOPT_VERBOSE, 1);
                $str = curl_exec($curl_request);
                curl_close($curl_request);

                parse_str($str, $output);

                if (!isset($output['Reverse_auth_credentials_are_invalid'])) {
                    $user_profile = new stdClass();

                    $user_profile->social = $social;
                    $user_profile->social_id = $output['user_id'];

                    $return = CheckSocialUser($user_profile);

                    if ($return['result'] == true) {
                        $mainframe->login_social($return['user_id']);
                    }
                    else {
                        $_SESSION['social_info'] = array(
                            'name' => $social,
                            'user_id' => $output['user_id'],
                        );
                    }
                }
//                echo "<pre>";print_r($return);
//                echo "<pre>";print_r($output);
//                echo "<pre>";print_r($_SESSION);
//                die;
                header('Location: /checkout');
                
                die;
            break;
        }
    break;

    case 'Facebook':
        Switch($task = $_REQUEST['task'] ? $_REQUEST['task'] : '') {
            case 'auth':
                $return = array();
                $return['result'] = true;

                $user_profile = new stdClass();
                $user_profile->social = $social;
                $user_profile->social_id = $_POST['response']['id'];

                $check_return = CheckSocialUser($user_profile);

                if ($check_return['result'] == true) {
                    $mainframe->login_social($check_return['user_id']);
                }
                else {
                    $_SESSION['social_info'] = array(
                        'name' => $social,
                        'user_id' => $_POST['response']['id'],
                    );
                }

                echo json_encode($return);

                die;
            break;
        }
    break;
    
    default:
    break;
}

?>
            

