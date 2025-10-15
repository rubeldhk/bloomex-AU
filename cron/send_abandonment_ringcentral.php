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
       $mosConfig_ringcentral_campaign_id_abandonment, $mosConfig_logger_file_path, $timeZone, $selectLimitAbandonment,
       $mosConfig_live_site;

$mysqli = new cron_mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, __FILE__);
$logger = new LogFile($mosConfig_logger_file_path);
$ev = new RestClient($mosConfig_ringcentral_client_id, $mosConfig_ringcentral_client_secret, $mosConfig_ringcentral_account_id);

date_default_timezone_set('Australia/Sydney');

$logger->setPrefix('ringcentral_abandonment')
    ->setLogFileName('send_abandonment_ringcentral');

function checkNeverCall($number, $mysqli): bool
{
    $query = sprintf("SELECT id  
        FROM `tbl_cart_abandonment` 
        WHERE  
            number  = '%s' 
            AND  send_ringcentral = 'NEVER' 
            AND project IS NULL",
        $number
    );
    $result = $mysqli->query($query);
    if ($result->num_rows > 0) {
        return false;
    }
    if ($result) {
        $result->close();
    }

    $query = sprintf("SELECT `id` 
        FROM `tbl_not_receive` 
        WHERE `number`='%s'",
        $number
    );
    $result = $mysqli->query($query);

    return $result->num_rows <= 0;
}

function checkTodayCall($number, $mysqli): bool
{
    $query = sprintf("SELECT *
        FROM `jos_vm_abandonment_calls`
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

function extractAbandonmentCalls($mysqli, $createAt)
{
    $queryNumbers = "SELECT *
        FROM `jos_vm_abandonment_calls` AS `a`
        WHERE 
            `created_at` < '$createAt'
            AND `status` = 'READY'
        ORDER BY `created_at` DESC";

    $result = $mysqli->query($queryNumbers);

    if (!$result) {
        die('SELECT jos_vm_abandonment_calls error: ' . $mysqli->error);
    }

    return $result;
}

function getAbandonmentCart(int $selectLimitAbandonment, $mysqli)
{
    $datetimeFrom = date('Y-m-d G:i:s', strtotime('-15 minutes', strtotime(date('Y-m-d G:i:s'))));
    $datetimeTo = date('Y-m-d G:i:s', strtotime('-120 minutes', strtotime(date('Y-m-d G:i:s'))));

    $queryNumbers = sprintf("SELECT
            `a`.`id`, 
            `a`.`number`, 
            `a`.`user_id`, 
            `a`.`first_name`, 
            `u`.`last_name`, 
            `u`.`middle_name`, 
            `u`.`user_email`, 
            `u`.`address_1`, 
            `u`.`state`, 
            `u`.`city`, 
            `a`.`status`,  
            `a`.`project`,
            `a`.`link`,
            `a`.`datetime_dt`
        FROM `tbl_cart_abandonment` AS `a`
        LEFT JOIN `jos_vm_user_info` AS `u` ON `u`.`user_id` = `a`.`user_id`
        LEFT JOIN `jos_vm_abandonment_calls` AS `ac` ON `ac`.`user_id` = `a`.`user_id` AND DATE_FORMAT(`ac`.`created_at`,'%%Y-%%m-%%d')='%s'
        WHERE  
            (`a`.`status` = 'sent' OR `a`.`status` = 'abandonment' OR `a`.`status` = 'sent_step1' OR `a`.`status` = 'sent_step2')
            AND (`a`.`send_ringcentral` IS NULL OR `a`.`send_ringcentral` = '')
            AND `a`.`datetime_dt` BETWEEN '%s' AND '%s'
            AND CHAR_LENGTH(a.number) > 8
            AND `a`.`project` IS NULL
            AND `ac`.`id` IS NULL 
        GROUP BY `a`.`user_id`
        ORDER BY `id`
        LIMIT %s",
        date('Y-m-d'),
        $datetimeTo,
        $datetimeFrom,
        $selectLimitAbandonment
    );

    $resultNumbers = $mysqli->query($queryNumbers);

    if (!$resultNumbers) {
        die('SELECT tbl_cart_abandonment error: ' . $mysqli->error);
    }

    return $resultNumbers;
}

function checkAbandonmentOrderPassed($mysqli, $id): bool
{
    $queryNumbers = "SELECT *
        FROM `tbl_cart_abandonment`
        WHERE `id` = '$id'";

    $result = $mysqli->query($queryNumbers);

    if (!$result) {
        die('SELECT jos_vm_abandonment_calls error: ' . $mysqli->error);
    }
    $cartAbandonment = $result->fetch_object();

    if ($cartAbandonment->order_id) {
        return true;
    }

    return false;
}

function getAbandonmentOrderPassed($mysqli, $id)
{
    $queryNumbers = "SELECT *
        FROM `tbl_cart_abandonment`
        WHERE `id` = '$id'";

    $result = $mysqli->query($queryNumbers);

    if (!$result) {
        die('SELECT jos_vm_abandonment_calls error: ' . $mysqli->error);
    }
    $cartAbandonment = $result->fetch_object();

    if ($cartAbandonment) {
        return $cartAbandonment->order_id;
    }

    return null;
}

$abandonmentOrderedCalls = extractAbandonmentCalls($mysqli, date('Y-m-d H:i:s', strtotime('-1 minute')));
$abandonmentCallsCancelled = extractAbandonmentCalls($mysqli, date('Y-m-d H:i:s', strtotime('-1 hour')));

if ($abandonmentCallsCancelled->num_rows > 0 || $abandonmentOrderedCalls->num_rows > 0) {
    $callsCancelled = $abandonmentCallsCancelled->fetch_all(MYSQLI_ASSOC);
    $callsOrdered = $abandonmentOrderedCalls->fetch_all(MYSQLI_ASSOC);

    $ordered = [];
    foreach ($callsOrdered as $order) {
        if (checkAbandonmentOrderPassed($mysqli, $order['abandonment_id'])) {
            $ordered[] = $order;
        }
    }

    $calls = array_merge($callsCancelled, $ordered);
    try {
        $ev->login(['jwt' => $mosConfig_ringcentral_client_jwt]);

        $params = [
            "campaignIds" => [$mosConfig_ringcentral_campaign_id_abandonment],
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
                    "leadPhone" => $abandonmentCallRingcentral['leadPhone'],
                ];
            }
        }

        $leadIds = [];
        $abandonmentCallIds = [];
        foreach ($calls as $call) {
            $externId = $call['extern_id'];
            if (array_key_exists($externId, $searchedLeads)) {
                $leadIds[] = $searchedLeads[$externId]['leadId'];
            }
            $abandonmentCallIds[] = (string)$call['id'];
        }

        if (count($abandonmentCallIds) > 0) {
            $queryUpdate = sprintf("UPDATE `jos_vm_abandonment_calls` 
                SET `status`='CANCELLED',
                `updated_at`='%s'
                WHERE `id` IN (%s)",
                date('Y-m-d H:i:s'),
                implode(',', $abandonmentCallIds)
            );
            $resultUpdate = $mysqli->query($queryUpdate);
            if (!$resultUpdate) {
                echo "Update jos_vm_abandonment_calls failed: " . $mysqli->error . PHP_EOL;
                $logger->error("Update jos_vm_abandonment_calls failed: " . $mysqli->error . PHP_EOL);
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
        if (count($ordered) > 0) {
            foreach ($ordered as $order) {
                $phone = $order['phone_number'];
                if (array_key_exists($phone, $searchedLeads)) {
                    $orderId = getAbandonmentOrderPassed($mysqli, $order['abandonment_id']);
                    $leadIdOrdered = $searchedLeads[$phone]['leadId'];
                    $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaignLeads/{$leadIdOrdered}?duplicateHandling=REMOVE_ALL_EXISTING&campaignId={$mosConfig_ringcentral_campaign_id_abandonment}";
                    $paramsOrderPassed = [
                        "auxData3" => "Order passed ID: " . $orderId
                    ];
                    $response = $ev->patch($url, $paramsOrderPassed);
                }
            }
        }
        if (count($callsCancelled) > 0) {
            foreach ($callsCancelled as $cancelItem) {
                $phone = $cancelItem['phone_number'];
                if (array_key_exists($phone, $searchedLeads)) {
                    $orderId = getAbandonmentOrderPassed($mysqli, $cancelItem['abandonment_id']);
                    $leadIdCancelled = $searchedLeads[$phone]['leadId'];
                    $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaignLeads/{$leadIdCancelled}?duplicateHandling=REMOVE_ALL_EXISTING&campaignId={$mosConfig_ringcentral_campaign_id_abandonment}";
                    $paramsCallsCancelled = [
                        "auxData3" => "Expired, one hour has passed."
                    ];
                    $response = $ev->patch($url, $paramsCallsCancelled);
                }
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

$resultNumbers = getAbandonmentCart($selectLimitAbandonment, $mysqli);

$leads = [];
if ($resultNumbers->num_rows > 0) {
    $leads = [
        'description' => 'Bloomex abandoment calls',
        'dialPriority' => 'IMMEDIATE',
        'duplicateHandling' => 'REMOVE_FROM_LIST',
        'listState' => 'ACTIVE',
        'timeZoneOption' => 'EXPLICIT',
        'phoneNumbersI18nEnabled' => true,
        "internationalNumberFormat" => true,
        "numberOriginCountry" => "e164",
        'uploadLeads' => [],
        'dncTags' => [],
        'type' => 'abandonment'
    ];
    $objects = $resultNumbers->fetch_all(MYSQLI_ASSOC);
    foreach ($objects as $index => $value) {
        $only_d = preg_replace("/\D/", '', $value['number']);

        if (
            checkTodayCall($only_d, $mysqli) &&
            checkNeverCall($value['number'], $mysqli) &&
            checkCallLater($only_d, $value['id'], $mysqli) &&
            (mb_strlen($only_d) > 8)
        ) {
            $queryUpdate = sprintf("UPDATE `tbl_cart_abandonment` 
                SET `send_ringcentral`='READY'  
                WHERE `id`='%s'",
                $value['id']
            );
            $resultUpdate = $mysqli->query($queryUpdate);
            if (!$resultUpdate) {
                echo "Update tbl_cart_abandonment failed: " . $mysqli->error . PHP_EOL;
                $logger->error("Update tbl_cart_abandonment failed: " . $mysqli->error . PHP_EOL);
            }

            $stateTimeZone = $timeZone[$value['state']]??'Australia/Sydney';

            $phoneNumber = '+61'.substr($only_d,-9);
            parse_str(ltrim($value['link'], "?"), $products);
            $products = str_replace('?cart_products=', '', $products);
            $pieces = explode(";", $products['cart_products']);

            $linkSetOrder = $mosConfig_live_site . '/administrator/index2.php?option=com_phoneorder&user_id=' . $value['user_id'] . '&order_call_type=abandonment&' . ltrim($value['link'], '?');

            $products_ids = [];
            if ($pieces) {
                foreach ($pieces as $p) {
                    $p_id = explode(",", $p);
                    if ($p_id[0]) {
                        $products_ids[] = $p_id[0];
                    }
                }
            }

            $productsNameSku = '';
            if (count($products_ids) > 0) {
                $queryProduct = sprintf("SELECT 
                        *
                    FROM `jos_vm_product` 
                    WHERE 
                        `product_id` IN (%s)",
                    implode(',', $products_ids)
                );
                $resultProduct = $mysqli->query($queryProduct);
                $productObjs = null;
                if ($resultProduct->num_rows > 0) {
                    $productObjs = $resultProduct->fetch_all(MYSQLI_ASSOC);
                    foreach ($productObjs as $prod) {
                        $productsNameSku .= $prod['product_name'] . ' (' . $prod['product_sku'] . ');';
                    }
                }
            }

            $leads['uploadLeads'][] = [
                'externId' => $value['id'],
                'leadPhone' => $phoneNumber,
                'countryId' => "AUS",
                'countryCode' => "61",
                'firstName' => $value['first_name'],
                'lastName' => $value['last_name'],
                'middleName' => $value['middle_name'],
                'email' => $value['user_email'],
                'address1' => $value['address_1'],
                'city' => $value['city'],
                'state' => $value['state'],
                'leadTimezone' => $stateTimeZone,
                'auxData1' => $linkSetOrder,
                'auxData2' => $productsNameSku,
                'auxData3' => $value['datetime_dt'],
                "extendedLeadData" => [
                    "auxExternalUrl" => $linkSetOrder,
                    "important" => "data",
                    "interested" => true
                ]
            ];

            $queryInsert = sprintf("INSERT INTO `jos_vm_abandonment_calls`(
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
                            `abandonment_id`,
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
                $mysqli->real_escape_string($value['user_id']),
                $mysqli->real_escape_string($value['first_name']),
                $mysqli->real_escape_string($value['last_name']),
                $mysqli->real_escape_string($value['middle_name']),
                $mysqli->real_escape_string($value['user_email']),
                $mysqli->real_escape_string($value['address_1']),
                $mysqli->real_escape_string($phoneNumber),
                $mysqli->real_escape_string($value['city']),
                $mysqli->real_escape_string($value['state']),
                $mysqli->real_escape_string($stateTimeZone),
                $mysqli->real_escape_string(1),
                $mysqli->real_escape_string($value['id']),
                'READY',
                $mysqli->real_escape_string($value['id']),
                $mysqli->real_escape_string(date('Y-m-d H:i:s')),
                $mysqli->real_escape_string(date('Y-m-d H:i:s'))
            );

            $resultOccasionCalls = $mysqli->query($queryInsert);

            if (!$resultOccasionCalls) {
                $logger->error("INSERT jos_vm_abandonment_calls failed: " . $mysqli->error . PHP_EOL);
                die("INSERT jos_vm_abandonment_calls failed: " . $mysqli->error . PHP_EOL);
            }
        }
    }
}

if ($leads && count($leads['uploadLeads']) > 0) {
    try {
        $ev->login(['jwt' => $mosConfig_ringcentral_client_jwt]);

        $url = "admin/accounts/{$mosConfig_ringcentral_account_id}/campaigns/{$mosConfig_ringcentral_campaign_id_abandonment}/leadLoader/direct";
        $response = $ev->post($url, $leads);
    } catch (Exception $e) {
        $logData = [
            'leads' => $leads,
            'code' => $e->getCode(),
            'line' => $e->getLine(),
            'message' => $e->getMessage(),
        ];
        $logger->error(json_encode($logData));
        echo "API Error: " . $e->getMessage();
    }
}