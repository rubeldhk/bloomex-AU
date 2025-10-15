<?php

defined('_VALID_MOS') or die('Restricted access');

global $my, $database;

if ($my->gid != 25 && $my->gid != 24) {
    mosRedirect('index2.php', _NOT_AUTH);
}

$userId = $_GET['user_id']; //469901
if (!$userId) {
    mosRedirect('index2.php', "User ID required field.");
}

$query = sprintf("SELECT id from jos_users  WHERE  `id`='%s'", $userId);
$database->setQuery($query);
$userExists = $database->loadResult();

if (!$userExists) {
    mosRedirect('index2.php', "User not found.");
}

$query = sprintf("INSERT INTO `jos_vm_shopper_vendor_xref` (
            `user_id`, 
            `shopper_group_id`
        ) VALUES (
            '%s',
            '%s'
        )
        ON DUPLICATE KEY UPDATE `shopper_group_id`='%s'",
    $userId,
    16,
    16
);

$database->setQuery($query);
if (!$database->query()) {
    mosRedirect('index2.php', $database->getErrorMsg());
    exit();
}

mosRedirect('index2.php', 'Corporate user created successfully.');
exit();