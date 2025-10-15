<?php

define('_VALID_MOS', 1);

require( '../globals.php' );
require_once( '../configuration.php' );

global $database, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_live_site, $mosConfig_user_adm, $mosConfig_password_adm, $mosConfig_absolute_path;

require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
require_once( $mosConfig_absolute_path . '/administrator/includes/admin.php' );

session_name(md5($mosConfig_live_site));
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);

// TODO: add configuration to disable the feature
if (isset($_SESSION['session_user_id']) && $_SESSION['session_user_id'] != '') {
    $userId = (int) $_SESSION['session_user_id'];

    $user = null;
    $query = "SELECT * FROM `jos_users` WHERE `id`={$userId}";
    $database->setQuery($query);
    $database->loadObject($user);

    generateMfaCodeAndSendEmail($user);

    echo json_encode(["message" => 'ok',"email" => $user->email]);
} else {
    echo json_encode(['message' => 'session_expired']);
    exit();
}

function generateMfaCodeAndSendEmail($user)
{
    global $mosConfig_fromname, $mosConfig_mailfrom, $database, $mosConfig_mfa_mailfrom;

    $generatedDate = date("Y-m-d\TH:i:s");
    $code = mt_rand(1000, 9999);

    $subject = 'Bloomex Admin Verification Code';
    $content = "We received a request to access your Bloomex Admin Account with username: {$user->username}. Your verification code is {$code}";
    $mailFrom = $mosConfig_mfa_mailfrom ?? $mosConfig_mailfrom;
    mosMail($mailFrom, $mosConfig_fromname, $user->email, $subject, $content);

    $query = "
        INSERT INTO `jos_vm_users_mfa` 
            (`user_id`, `mfa_code`, `last_generated`, `has_verified`)
        VALUES 
            ({$user->id}, '{$code}', '{$generatedDate}', 0)
        ON DUPLICATE KEY UPDATE
          `mfa_code` = VALUES(`mfa_code`), 
          `last_generated` = VALUES(`last_generated`),
          `has_verified` = VALUES(`has_verified`)"
    ;
    $database->setQuery($query);
    $database->query();
}
