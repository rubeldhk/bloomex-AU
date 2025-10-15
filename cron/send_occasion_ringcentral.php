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
       $mosConfig_ringcentral_campaign_id_occasion, $mosConfig_logger_file_path, $twoYearsOccasion, $fourYearsOccasion,
       $selectLimitOccasion, $mosConfig_live_site, $timeZone;

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
$logger = new LogFile($mosConfig_logger_file_path);
$ev = new RestClient($mosConfig_ringcentral_client_id, $mosConfig_ringcentral_client_secret, $mosConfig_ringcentral_account_id);

$logger->setPrefix('ringcentral_occasion')
    ->setLogFileName('send_occasion_ringcentral');

date_default_timezone_set('Australia/Sydney');

function getNeedStates($mysqli): array
{
    global $min_hour, $max_hour;

    $now_hour = (int)date('H');
    $query_country = "SELECT `country_id` FROM `jos_vm_country` WHERE `country_3_code`='AUS'";
    $resultCountry = $mysqli->query($query_country);
    $out_country = $resultCountry->fetch_all(MYSQLI_ASSOC);
    $resultCountry->close();

    $query_states = sprintf("SELECT 
            `state_2_code`, 
            `timezone_offsets` 
        FROM `jos_vm_state` 
        WHERE `country_id`='%s'",
        $out_country[0]['country_id']
    );
    $resultState = $mysqli->query($query_states);
    $sql_states = $resultState->fetch_all(MYSQLI_ASSOC);
    $resultState->close();

    $need_states = [];
    foreach ($sql_states as $out_states) {
        $need_states[] = $out_states['state_2_code'];
    }

    return $need_states;
}



function checkNeverCall($number, $mysqli): bool
{
    $query = "SELECT 
        `order_id`  
    FROM  `jos_vm_order_user_info` 
    WHERE  
        `phone_1`='" . $number . "'
        AND  
        `call_customer`='NEVER'";
    $result = $mysqli->query($query);

    return $result->num_rows <= 0;
}

function checkTodayCall($number, $mysqli): bool
{
    $query = sprintf("SELECT *
        FROM `jos_vm_occasion_calls`
        WHERE `phone_number`='" . $number . "' 
            AND DATE_FORMAT(created_at,'%%Y-%%m-%%d')='%s'",
        date('Y-m-d')
    );
    $result = $mysqli->query($query);

    return $result->num_rows <= 0;
}

function checkCallLater($number, $id_info, $mysqli): bool
{
    $datetimeFrom = date('Y-m-d G:i:s', strtotime('-1 hour', strtotime(date('Y-m-d G:i:s'))));
    $datetimeTo = date('Y-m-d G:i:s');

    $query = sprintf("SELECT *
        FROM `tbl_numbers_to_give`
        WHERE 
            `id_info` = '%s' AND `number`='%s' 
        AND `answer_type` LIKE 'DEFAULT'
        AND end_datetime BETWEEN '%s' AND '%s'",
        $id_info,
        $number,
        $datetimeFrom,
        $datetimeTo
    );
    $result = $mysqli->query($query);

    return $result->num_rows <= 0;
}

function extractOccasionCalls($mysqli)
{
    $querySelect = "SELECT * 
    FROM `jos_vm_occasion_calls` 
    WHERE 
        (
            `created_at` < NOW() - INTERVAL 3 DAY
            OR (
            	(
                    MONTH(`occasion_date`) = MONTH(CURDATE()) 
                    AND DAY(`occasion_date`) <= DAY(CURDATE())
                ) OR (
                	MONTH(`occasion_date`) < MONTH(CURDATE()) 
                )
            )
        )
        AND status='READY'";


    $result = $mysqli->query($querySelect);

    if (!$result) {
        die('SELECT tbl_cart_abandonment error: ' . $mysqli->error);
    }

    return $result;
}

$occasionCalls = extractOccasionCalls($mysqli);

if ($occasionCalls->num_rows > 0) {
    $calls = $occasionCalls->fetch_all(MYSQLI_ASSOC);

    try {
        $ev->login(['jwt' => $mosConfig_ringcentral_client_jwt]);
        $params = [
            "campaignIds" => [$mosConfig_ringcentral_campaign_id_occasion],
            "leadStates" => ["READY"]
        ];
        $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaignLeads/leadSearch";
        $response = $ev->post($url, $params);
        $data = json_decode($response, true);

        $searchedLeads = [];
        foreach ($data as $abandonmentCallRingcentral) {
            if ($abandonmentCallRingcentral['leadState'] === 'READY') {
                $searchedLeads[$abandonmentCallRingcentral['externId']] = [
                    "leadId" => $abandonmentCallRingcentral['leadId'],
                    "dialGroupId" => $abandonmentCallRingcentral['dialGroupId'],
                    "campaignId" => $abandonmentCallRingcentral['campaignId'],
                    "leadState" => $abandonmentCallRingcentral['leadState'],
                    "leadPhone" => $abandonmentCallRingcentral['leadPhone'],
                ];
            }
        }

        $leadIds = [];
        $occasionCallIds = [];
        foreach ($calls as $call) {
            $externId = $call['extern_id'];
            if (array_key_exists($externId, $searchedLeads)) {
                $leadIds[] = $searchedLeads[$externId]['leadId'];
            }
            $occasionCallIds[] = (string)$call['id'];
        }

        if (count($occasionCallIds) > 0) {
            $queryUpdate = sprintf("UPDATE `jos_vm_occasion_calls` 
                SET `status`='CANCELLED',
                `updated_at`='%s'
                WHERE `id` IN (%s)",
                date('Y-m-d H:i:s'),
                implode(',', $occasionCallIds)
            );
            $resultUpdate = $mysqli->query($queryUpdate);
            if (!$resultUpdate) {
                echo "Update jos_vm_occasion_calls failed: " . $mysqli->error . PHP_EOL;
                $logger->error("Update jos_vm_occasion_calls failed: " . $mysqli->error . PHP_EOL);
            }
        }

        if (count($leadIds) > 0) {
            $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaignLeads/actions?leadAction=CANCEL_LEADS";
            $params = [
                "campaignLeadSearchCriteria" => [
                    "leadIds" => $leadIds
                ]
            ];
            $response = $ev->put($url, $params);
        }

        if (count($leadIds) > 0) {
            foreach ($leadIds as $leadId) {
                $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaignLeads/{$leadId}?duplicateHandling=REMOVE_ALL_EXISTING&campaignId={$mosConfig_ringcentral_campaign_id_occasion}";
                $paramsCallsCancelled = [
                    "auxData3" => "Expired, 3 days have passed."
                ];
                $response = $ev->patch($url, $paramsCallsCancelled);
            }
        }
    } catch (Exception $e) {
        $logData = [
            'code' => $e->getCode(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];
        $logger->error(json_encode($logData));
        echo "API Error: " . $e->getMessage();
    }
}

$leads = [];
$need_states = getNeedStates($mysqli);

if (count($need_states) > 0) {


    $query_numbers = "SELECT 
        o.order_id AS id,
        o.customer_note,
        o.customer_comments,
        o.cdate,
        ou.phone_1 AS number,
        ou.user_id,
        ou.last_name,
        ou.first_name,
        ou.middle_name,
        ou.user_email,
        ou.address_1,
        ou.state,
        ou.city,
        ui.address_1 as recipient_address1,
        ui.first_name as recipient_name,
        oo.order_occasion_name
        FROM  jos_vm_orders AS o
        LEFT JOIN jos_vm_order_user_info AS ou ON ou.order_id = o.order_id
        LEFT JOIN jos_vm_order_user_info AS ui on ui.order_id=o.order_id
        LEFT JOIN jos_vm_api2_orders AS ao ON ao.order_id = o.order_id
        LEFT JOIN jos_vm_occasion_calls AS oc ON oc.order_id = o.order_id
        LEFT JOIN jos_vm_order_occasion AS oo on oo.order_occasion_code = o.customer_occasion
        WHERE
            (
                (
                    (o.`customer_occasion` IN ('" . implode("','", $twoYearsOccasion) . "') AND STR_TO_DATE(DATE_FORMAT( FROM_UNIXTIME(o.cdate) ,  '%Y-%m-%d'),  '%Y-%m-%d') >= STR_TO_DATE(DATE_FORMAT( DATE_SUB( NOW( ) , INTERVAL 1 YEAR ) ,  '%Y-%m-%d'),  '%Y-%m-%d'))
                        OR
                    (o.`customer_occasion` IN ('" . implode("','", $twoYearsOccasion) . "') AND STR_TO_DATE(DATE_FORMAT( FROM_UNIXTIME(o.cdate) ,  '%Y-%m-%d'),  '%Y-%m-%d') >= STR_TO_DATE(DATE_FORMAT( DATE_SUB( NOW( ) , INTERVAL 2 YEAR ) ,  '%Y-%m-%d'),  '%Y-%m-%d'))  
                        OR
                    (o.`customer_occasion` IN ('" . implode("','", $fourYearsOccasion) . "') AND STR_TO_DATE(DATE_FORMAT( FROM_UNIXTIME(o.cdate) ,  '%Y-%m-%d'),  '%Y-%m-%d') >= STR_TO_DATE(DATE_FORMAT( DATE_SUB( NOW( ) , INTERVAL 3 YEAR ) ,  '%Y-%m-%d'),  '%Y-%m-%d'))   
                )
            AND 
                (
                    ou.call_customer='DEFAULT' 
                    AND  ou.address_type = 'BT'
                    AND ou.country = 'AUS' 
                    AND ou.phone_1 !=  '' 
                    AND `ou`.`state` IN ('" . implode("','", $need_states) . "')
                )
            )
            AND STR_TO_DATE(DATE_FORMAT( FROM_UNIXTIME(o.cdate) ,  '%Y-%m-%d'),  '%Y-%m-%d') <= STR_TO_DATE(DATE_FORMAT( DATE_SUB( NOW( ) , INTERVAL 1 MONTH ) ,  '%Y-%m-%d'),  '%Y-%m-%d')
            AND  
            (
                DATE_FORMAT( FROM_UNIXTIME(o.cdate  + 11 * 60 * 60) ,  '%e-%b' ) LIKE DATE_FORMAT( DATE_SUB(DATE_SUB(NOW( ), INTERVAL 5 HOUR) ,  INTERVAL -1 DAY),  '%e-%b' )
                OR
                DATE_FORMAT( FROM_UNIXTIME(o.cdate  + 11 * 60 * 60) ,  '%e-%b' ) LIKE DATE_FORMAT( DATE_SUB(DATE_SUB(NOW( ), INTERVAL 5 HOUR) ,  INTERVAL -2 DAY),  '%e-%b' )
                OR
                DATE_FORMAT( FROM_UNIXTIME(o.cdate  + 11 * 60 * 60) ,  '%e-%b' ) LIKE DATE_FORMAT( DATE_SUB(DATE_SUB(NOW( ), INTERVAL 5 HOUR) ,  INTERVAL -3 DAY),  '%e-%b' )
            )
            AND ao.id IS NULL
            AND oc.id IS NULL
            AND ui.address_type = 'ST'
            AND o.order_status NOT IN ('X', '6')  and CHAR_LENGTH(ou.phone_1) > 8
        GROUP BY o.cdate, ou.phone_1
        ORDER BY `o`.`order_id` DESC
        LIMIT " . $selectLimitOccasion . ";";


    $result = $mysqli->query($query_numbers);
    if (!$result) {
        $logger->error('SELECT occasions error: ' . $mysqli->error);
        die('SELECT occasions error: ' . $mysqli->error);
    }

    if ($result->num_rows > 0) {
        $leads = [
            'description' => 'Bloomex occasion calls',
            'dialPriority' => 'IMMEDIATE',
            'duplicateHandling' => 'REMOVE_FROM_LIST',
            'listState' => 'ACTIVE',
            'timeZoneOption' => 'EXPLICIT',
            'phoneNumbersI18nEnabled' => true,
            "internationalNumberFormat" => true,
            "numberOriginCountry" => "e164",
            'uploadLeads' => [],
            'dncTags' => [],
            'type' => 'occasion'
        ];

        $return['result'] = true;
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as $index => $out) {
            $orderCreateAt = date('Y-m-d', $out['cdate']);
            $only_d = preg_replace("/\D/", '', $out['number']);
            if (
                checkTodayCall($only_d, $mysqli) &&
                checkNeverCall($out['number'], $mysqli) &&
                checkCallLater($only_d, $out['id'], $mysqli)
            ) {
                if (mb_strlen($only_d) > 8) {
                    $stateTimeZone = $timeZone[$out['state']]??'Australia/Sydney';
                    $phoneNumber = '+61'.substr($only_d,-9);

                    $linkSetOrder = $mosConfig_live_site . '/administrator/index2.php?option=com_phoneorder&user_id=' . $out['user_id'] . '&order_call_type=occasion';

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
                        'auxData5' => 'Occasion: ' . $out['order_occasion_name'] . ', order create date: ' . $orderCreateAt,
                        "extendedLeadData" => [
                            "auxExternalUrl" => $linkSetOrder,
                            "important" => "data",
                            "interested" => true
                        ]
                    ];

                    $queryInsert = sprintf("INSERT INTO `jos_vm_occasion_calls`(
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
                            `occasion_date`,
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
                        $mysqli->real_escape_string($out['id']),
                        'READY',
                        $mysqli->real_escape_string($orderCreateAt),
                        $mysqli->real_escape_string(date('Y-m-d H:i:s')),
                        $mysqli->real_escape_string(date('Y-m-d H:i:s'))
                    );

                    $resultOccasionCalls = $mysqli->query($queryInsert);

                    if (!$resultOccasionCalls) {
                        $logger->error("INSERT jos_vm_occasion_calls failed: " . $mysqli->error . PHP_EOL);
                        die("INSERT jos_vm_occasion_calls failed: " . $mysqli->error . PHP_EOL);
                    }

                    $return['id_info'] = $out['id'];
                    $return['number'] = (mb_strlen($only_d) == 10) ? '1' . $only_d : $only_d;
                } else {
                    $queryInsert = sprintf("INSERT INTO `tbl_numbers_to_give`(
                            `id_info`, 
                            `number`,
                            `type`,
                            `ext`,
                            `note`,
                            `datetime`
                        )
                        VALUES (
                            '%s',
                            '%s',
                            'occasion',
                            '111',
                            'wrong number',
                            '%s'
                        )",
                        $out['id'],
                        $only_d,
                        date('Y-m-d G:i:s')
                    );
                    $resultInsert = $mysqli->query($queryInsert);
                    if (!$resultInsert) {
                        $logger->error("INSERT tbl_numbers_to_give failed: " . $mysqli->error . PHP_EOL);
                        die("INSERT tbl_numbers_to_give failed: " . $mysqli->error . PHP_EOL);
                    }
                    $return['number_id'] = $mysqli->insert_id;
                }
            }
        }

    }
    $mysqli->close();
}

if ($leads && count($leads['uploadLeads']) > 0) {
    try {
        $ev->login(['jwt' => $mosConfig_ringcentral_client_jwt]);

        $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaigns/{$mosConfig_ringcentral_campaign_id_occasion}/leadLoader/direct";
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