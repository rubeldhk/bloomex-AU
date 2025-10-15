<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__) . '/';

include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/configuration.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/cron/cron_mysqli.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/ringcentral/RestClient.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/logger/contract/LoggerInterface.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/logger/LogFile.php';

use logger\LogFile;

global $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_ringcentral_client_id,
       $mosConfig_ringcentral_client_secret, $mosConfig_ringcentral_client_jwt, $mosConfig_ringcentral_account_id,
       $mosConfig_ringcentral_campaign_id_corporate, $mosConfig_logger_file_path, $selectLimitOccasion,
       $mosConfig_live_site, $timeZone;

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
$logger = new LogFile($mosConfig_logger_file_path);
$ev = new RestClient($mosConfig_ringcentral_client_id, $mosConfig_ringcentral_client_secret, $mosConfig_ringcentral_account_id);

$logger->setPrefix('ringcentral_occasion')
    ->setLogFileName('send_occasion_ringcentral');

date_default_timezone_set('Australia/Sydney');

$dateFrom = date('Y-m-d', strtotime('-1 month'));

$query_numbers = "SELECT 
        `o`.`order_id` AS id, 
        `ou`.`first_name`, 
        `ou`.`last_name`, 
        `ou`.`middle_name`, 
        `ou`.`company`, 
        `ou`.`phone_1` AS number,
        `ou`.`user_email`, 
        `ou`.`address_1`, 
        `ou`.`state`, 
        `ou`.`city`, 
        `ui`.`address_1` AS recipient_address1,
        `ui`.`first_name` AS recipient_name,
        `o`.`user_id`, 
        `o`.`customer_note`,
        `o`.`customer_comments`,
        `oo`.`order_occasion_name`
    FROM `jos_vm_orders` AS `o` 
    LEFT JOIN `jos_vm_order_user_info` AS `ou` 
        ON 
        `ou`.`address_type` = 'BT' 
        AND 
        `ou`.`order_id` = `o`.`order_id` 
    LEFT JOIN jos_vm_order_user_info AS ui 
        ON 
            ui.order_id=o.order_id AND `ui`.`address_type` = 'ST' 
    LEFT JOIN `jos_vm_shopper_vendor_xref` AS `x` 
        ON 
        `x`.`user_id` = `o`.`user_id` 
    LEFT JOIN `jos_new_corporate_domains` AS `d` 
        ON 
        `d`.`domain`=SUBSTRING_INDEX(`ou`.`user_email`, '@', -1) 
    LEFT JOIN `jos_vm_api2_orders` AS `ao` 
        ON 
        `ao`.`order_id` = `o`.`order_id`
    LEFT JOIN `jos_vm_corporate_calls` AS `cc`
        ON
        `cc`.`user_id` = `o`.`user_id`
    LEFT JOIN jos_vm_order_occasion AS oo 
        ON 
        oo.order_occasion_code = o.customer_occasion
    WHERE 
        `ou`.`user_email` IS NOT NULL 
        AND 
        `ou`.`call_customer` !='NEVER' 
        AND 
        `d`.`id` IS NULL 
        AND 
        `x`.`shopper_group_id` = 5
        AND 
        CHAR_LENGTH(ou.phone_1) > 8
        AND 
        FROM_UNIXTIME(`o`.`cdate`  + 11 * 60 * 60, '%Y-%m-%d') > '" . $dateFrom . "'
        AND 
        `cc`.`id` IS NULL 
        AND 
        `ao`.`id` IS NULL
    GROUP BY 
        `ou`.`phone_1` 
    ORDER BY 
        `o`.`cdate` ASC 
    LIMIT 100";



$result = $mysqli->query($query_numbers);
if (!$result) {
    $logger->error('SELECT get corporate calls error: ' . $mysqli->error);
    die('SELECT get corporate calls error: ' . $mysqli->error);
}

$leads = [];
if ($result->num_rows > 0) {
    $leads = [
        'description' => 'Bloomex Corporate Calls',
        'dialPriority' => 'IMMEDIATE',
        'duplicateHandling' => 'REMOVE_FROM_LIST',
        'listState' => 'ACTIVE',
        'timeZoneOption' => 'EXPLICIT',
        'phoneNumbersI18nEnabled' => true,
        "internationalNumberFormat" => true,
        "numberOriginCountry" => "e164",
        'uploadLeads' => [],
        'dncTags' => [],
        'type' => 'corporate users'
    ];

    $return['result'] = true;
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $index => $out) {
        $only_d = preg_replace("/\D/", '', $out['number']);
        if (mb_strlen($only_d) > 8) {
            $stateTimeZone = $timeZone[$out['state']]??'Australia/Sydney';
            $phoneNumber = '+61'.substr($only_d,-9);


            $linkSetOrder = $mosConfig_live_site . '/administrator/index2.php?option=corporate&task=corporate-user&user_id=' . $out['user_id'];
            $leads['uploadLeads'][] = [
                'externId' => $out['id'],
                'leadPhone' => $phoneNumber,
                'countryId' => "AUS",
                'countryCode' => "61",
                'firstName' => $out['first_name'],
                'lastName' => $out['last_name'],
                'middleName' => $out['middle_name'],
                'email' => $out['user_email'],
                'address1' => $out['address_1'],
                'city' => $out['city'],
                'state' => $out['state'],
                'leadTimezone' => $stateTimeZone,
                'auxData1' => $linkSetOrder,
                'auxData2' => $out['customer_note'] ?? $out['customer_comments'],
                'auxData3' => $out['recipient_name'],
                'auxData4' => $out['recipient_address1'],
                'auxData5' => $out['order_occasion_name'],
                "extendedLeadData" => [
                    "auxExternalUrl" => $linkSetOrder,
                    "important" => "data",
                    "interested" => true
                ]
            ];

            $queryInsert = sprintf("INSERT INTO `jos_vm_corporate_calls`(
                    `order_id`, 
                    `user_id`,
                    `first_name`,
                    `last_name`,
                    `middle_name`,
                    `email`,
                    `address_1`,
                    `phone_number`,
                    `city`,
                    `state`,
                    `timezone`,
                    `country_id`,
                    `extern_id`,
                    `status`,
                    `created_at`,
                    `updated_at`
                ) VALUES (
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )",
                $mysqli->real_escape_string($out['id']),
                $mysqli->real_escape_string($out['user_id']),
                $mysqli->real_escape_string($out['first_name']),
                $mysqli->real_escape_string($out['last_name']),
                $mysqli->real_escape_string($out['middle_name']),
                $mysqli->real_escape_string($out['user_email']),
                $mysqli->real_escape_string($out['address_1']),
                $mysqli->real_escape_string($phoneNumber),
                $mysqli->real_escape_string($out['city']),
                $mysqli->real_escape_string($out['state']),
                $mysqli->real_escape_string($stateTimeZone),
                $mysqli->real_escape_string(1),
                $mysqli->real_escape_string($out['user_id']),
                'READY',
                $mysqli->real_escape_string(date('Y-m-d H:i:s')),
                $mysqli->real_escape_string(date('Y-m-d H:i:s'))
            );

            $resultOccasionCalls = $mysqli->query($queryInsert);

            $email = $out['user_email'];
            $parts = explode('@', $email);
            $domain = $parts[1];
            $queryInsertDomain = "INSERT INTO jos_new_corporate_domains (domain, status) VALUES ('".$domain."', '1');";
            $resultOccasionCalls = $mysqli->query($queryInsertDomain);

            if (!$resultOccasionCalls) {
                $logger->error("INSERT jos_vm_corporate_calls failed: " . $mysqli->error . PHP_EOL);
                die("INSERT jos_vm_corporate_calls failed: " . $mysqli->error . PHP_EOL);
            }

            $return['id_info'] = $out['id'];
            $return['number'] = (mb_strlen($only_d) == 10) ? '1' . $only_d : $only_d;
        }
    }

}
$mysqli->close();

if ($leads && count($leads['uploadLeads']) > 0) {
    try {
        $ev->login(['jwt' => $mosConfig_ringcentral_client_jwt]);

        $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaigns/{$mosConfig_ringcentral_campaign_id_corporate}/leadLoader/direct";
        $response = $ev->post($url, $leads);
    } catch (Exception $e) {
        $logData = [
            'leads' => $leads,
            'code' => $e->getCode(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];
        $logger->error(json_encode($logData));
        die("API Error: " . $e->getMessage());
    }
}