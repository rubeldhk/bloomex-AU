<?php

$HTTPS = (isset($_SERVER['HTTPS'])) ? $_SERVER['HTTPS'] : null;
// change the following paths if necessary
$config = $mosConfig_absolute_path . '/hybridauth/config.php';
require_once( $mosConfig_absolute_path . '/hybridauth/Hybrid/Auth.php' );
ob_clean();
$social = $_REQUEST['social'];

try {
    // create an instance for Hybridauth with the configuration file path as parameter
    $hybridauth = new Hybrid_Auth($config);

    // try to authenticate the user with twitter,
    // user will be redirected to Twitter for authentication,
    // if he already did, then Hybridauth will ignore this step and return an instance of the adapter

    global $mainframe, $my;
    //  used as filter for post
    $social = ucfirst($social);

    switch ($social) {
        case 'Twitter' :
        case 'Facebook' :
        case 'Google' :

            //if (!$HTTPS) {

            if (isset($_GET['ok']) && $_GET['ok'] == 1)
            {
                $social_auth = $hybridauth->authenticate($social);
                $user_profile = $social_auth->getUserProfile();
                $social_user_id = $user_profile->identifier;
                $username = $user_profile->firstName;
                $user_id = CheckForSocialUser($social, $social_user_id, $username);
  
                $mainframe->login_social($user_id);


                $redirect = "https://" . $_SERVER['HTTP_HOST'] . str_replace('&ok=1', '', $_SERVER['REQUEST_URI']);

                header("Location: $redirect");
             }
             else
             {
                $html = '<html>'
                        . '<head>'
                        . '</head>'
                        . '<body>'
                        . '<script type ="text/javascript">'
                        . 'window.opener.location.reload(false);'
                        . 'window.close()'
                        . '</script>'
                        . '</body>'
                        . '</html>';
                die($html);
            }

            break;
        default:
            $html = '<html>'
                    . '<head>'
                    . '</head>'
                    . '<body>'
                    . '<script type ="text/javascript">'
                    . 'window.close()'
                    . '</script>'
                    . '</body>'
                    . '</html>';
            die($html);
    }
} catch (Exception $e) {
    // Display the recived error,
    // to know more please refer to Exceptions handling section on the userguide
    echo "<h3>ERROR!</h3>";
    switch ($e->getCode()) {
        case 0 : echo "Unspecified error.";
            break;
        case 1 : echo "Hybriauth configuration error.";
            break;
        case 2 : echo "Provider not properly configured.";
            break;
        case 3 : echo "Unknown or disabled provider.";
            break;
        case 4 : echo "Missing provider application credentials.";
            break;
        case 5 : echo "Authentification failed. "
            . "The user has canceled the authentication or the provider refused the connection.";
            break;
        case 6 : echo "User profile request failed. Most likely the user is not connected "
            . "to the provider and he should authenticate again.";
            break;
        case 7 : echo "User not connected to the provider.";
            break;
        case 8 : echo "Provider does not support this feature.";
            break;
    }
    echo '<br/><a onclick="window.close()"> Close this window </a>';
    // well, basically your should not display this to the end user, just give him a hint and move on..
    echo "<!--<br /><br /><b>Original error message:</b> " . $e->getMessage() . "-->";
}

function CheckForSocialUser($social, $social_user_id, $username) {
    global $database;
    $query = "SELECT user_id from tbl_social_users WHERE social = '$social' AND social_user_id = '$social_user_id'";
    $database->setQuery($query);
    $user_id = $database->loadResultArray();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }
    $user_id = $user_id[0];
    if (!$user_id) {
        $user_id = saveUser($username);
        $query = 'INSERT INTO tbl_social_users ( `user_id`,`social`,`social_user_id` ) '
                . 'VALUES ("' . $user_id . '",  "' . $social . '" , "' . $social_user_id . '")';
        $database->setQuery($query);
        $database->query();
        if ($database->getErrorMsg()) {
            die(__LINE__ . $database->getErrorMsg());
        }
    }
    return $user_id;
}

function saveUser($username) {
    global $database;
    $username = preg_replace('/([А-Яа-я]+)/siu', '', $username);
    
    $username = mysql_real_escape_string($username);
    
    $pass = mosMakePassword();
    $password = md5($pass);
    $usertype = 'Registered';
    $gid = '18'; //shopper
    $params = "admin_language=
language=
editor=
helpsite=
timezone=0";

    $command = "INSERT INTO jos_users VALUES ('', "
            . "'" . $username . "',"
            . " '" . $username . "',"
            . " '',"
            . " '',"
            . " '$password', "
            . "'" . $usertype . "',"
            . " 0,"
            . " 0,"
            . " '" . $gid . "',"
            . "   now(),"
            . " now(),"
            . " '',"
            . " '$params', 5 )";
    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }
    $need_id1 = $database->insertid();


    $command = "INSERT INTO jos_core_acl_aro VALUES ('', 'users', '$need_id1', '0', '" . $username . "', 0)";
    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }
    $need_id2 = $database->insertid();

    $command = "INSERT INTO jos_core_acl_groups_aro_map VALUES ('18', '', '$need_id2')";
    $database->setQuery($command);
    $database->query();
    if ($database->getErrorMsg()) {
        die(__LINE__ . $database->getErrorMsg());
    }

    return $need_id1;
}
