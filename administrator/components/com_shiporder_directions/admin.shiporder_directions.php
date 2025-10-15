<?php

defined('_VALID_MOS') or die('Restricted access');

date_default_timezone_set('Australia/Sydney');

require_once( $mainframe->getPath('admin_html') );

switch ($task) {
    case 'get-scan':
        get_scan();
        break;

    case 'in_transit':
        in_transit();
        break;

    case 'get_order_address':
        $result = get_order_address();

        echo json_encode($result);
        exit(0);
        break;

    default:
        defaultview();
        break;
}

function getOptimize($wh_address, $orders) {
    global $mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass;

    $return = (object) array(
                'result' => false
    );

    $st_array = array('optimize:true');

    foreach ($orders as $order_obj) {
        $st_array[] = $order_obj->st;
    }

    $getfields = array(
        'origin' => $wh_address,
        'destination' => $wh_address,
        'waypoints' => implode('|', $st_array),
        'key' => 'AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY'
    );

    $curl = curl_init('https://maps.googleapis.com/maps/api/directions/json?' . http_build_query($getfields));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    $curl_response = curl_exec($curl);
    if (!curl_errno($curl)) {
        $curl_info = curl_getinfo($curl);

        if ($curl_info['http_code'] == '200') {
            $json = json_decode($curl_response);

            if (isset($json->status) AND $json->status == 'OK') {
                $new_orders = array();
                $waypoint_order = $json->routes[0]->waypoint_order;

                $i = 0;
                $range = range('B', 'Z');

                foreach ($waypoint_order as $k => $v) {
                    $orders[$v]->queue = $range[$k];
                    $orders[$v]->distance = $json->routes[0]->legs[$k]->distance->text;
                    $orders[$v]->duration = $json->routes[0]->legs[$k]->duration->text;
                    $orders[$v]->distance_raw = $json->routes[0]->legs[$k]->distance->value;
                    $orders[$v]->duration_raw = $json->routes[0]->legs[$k]->duration->value;
                    $orders[$v]->location = $json->routes[0]->legs[$k ]->end_location;
                    $new_orders[] = $orders[$v];
                }

                $markers = array(
                    'color:yellow|label:W|' . $json->routes[0]->legs[0]->start_location->lat . ',' . $json->routes[0]->legs[0]->start_location->lng
                );

                $i = 0;
                foreach ($new_orders as $new_order_obj) {
                    Switch ($new_order_obj->shipment) {
                        case 3:
                            $marker_color = 'blue';
                            break;
                        case 2:
                            $marker_color = 'pink';
                            break;
                        default:
                            $marker_color = 'red';
                    }

                    $markers[] = 'color:' . $marker_color . '|label:' . $new_order_obj->queue . '|' . $new_order_obj->location->lat . ',' . $new_order_obj->location->lng;
                    $i++;
                }

                $getfields = array(
                    //'path' => 'enc:' . $json->routes[0]->overview_polyline->points,
                    'key' => 'AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY',
                    'size' => '600x300',
                );

                $curl2 = curl_init('https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($getfields) . '&markers=' . implode('&markers=', array_map('urlencode', $markers)));
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl2, CURLOPT_CONNECTTIMEOUT, 15);
                $curl_response2 = curl_exec($curl2);

                if (!curl_errno($curl2)) {
                    $curl_info = curl_getinfo($curl2);

                    if ($curl_info['http_code'] == '200') {
                        $json2 = json_decode($curl_response);

                        if (isset($json2->status) AND $json2->status == 'OK') {

                            $tmp = tmpfile();
                            fwrite($tmp, $curl_response2);
                            rewind($tmp);

                            $file_folder = 'bloomex.ca/routemaps';
                            $image_name = time() . '_' . mt_rand(1000, 9999) . '.png';
                            $image_link = $file_folder . '/' . $image_name;
                            $metaDatas = stream_get_meta_data($tmp);
                            $tmpFilename = $metaDatas['uri'];
                            $move = ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $tmpFilename, $image_link, $file_folder);
                            fclose($tmp);

                            $image_link = 'http://media.bloomex.ca/' . $image_link;

                            $return->image_link = $image_link;
                            $return->new_orders = $new_orders;
                            $return->result = true;
                        }
                    } else {
                        $return->error = 'Curl2 error: not 200 '.http_build_query($getfields).' curl response: '.$curl_response;
                    }
                } else {
                    $return->error = 'Curl2 error: ' + curl_error($curl);
                }
                curl_close($curl2);
            } else {
                $return->error = 'Curl error: not route '.http_build_query($getfields).' curl response: '.$curl_response;
            }
        } else {
            $return->error = 'Curl error: not 200';
        }
    } else {
        $return->error = 'Curl error: ' + curl_error($curl);
    }
    curl_close($curl);

    //$return->gmap_response = $curl_response;

    return $return;
}

function ftp_is_dir($ftp, $dir) {
    $pushd = ftp_pwd($ftp);

    if ($pushd !== false && @ftp_chdir($ftp, $dir)) {
        ftp_chdir($ftp, $pushd);
        return true;
    }

    return false;
}

function ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $photo, $filename, $dir) {
    $ftp = ftp_connect($mosConfig_email_sender_ftp_host);
    //$photo = resize_image($photo,$filename, 1920, 1080);
    if (ftp_login($ftp, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass)) {
        ftp_pasv($ftp, true);


        if (!ftp_is_dir($ftp, $dir)) {
            if (!ftp_mkdir($ftp, $dir)) {
                exit("There was a problem while creating $dir\n");
            }
        }


        $trackErrors = ini_get('track_errors');
        ini_set('track_errors', 1);

        $res = ftp_size($ftp, $filename);
        if ($res == -1) {

            if (!@ftp_put($ftp, $filename, $photo, FTP_BINARY)) {
                die("error while uploading file");
            }
        } else {
            return false;
        }
    } else {
        die("Could not login to FTP account");
    }



    ftp_close($ftp);
}

function get_scan() {
    global $database;

    $scan_id = isset($_POST['scan_id']) ? (int) $_POST['scan_id'] : 0;

    $return = (object) array(
                'result' => false
    );

    $query = "SELECT
        `s`.`id`
    FROM `jos_vm_scanning_sessions` AS `s`
    WHERE
        `s`.`id`=" . $scan_id . "
    ";
    $session_obj = false;
    $database->setQuery($query);
    $database->loadObject($session_obj);

    if ($session_obj) {
        $query = "SELECT
            `so`.`order_id`
        FROM `jos_vm_scanning_orders` AS `so`
        WHERE
            `so`.`session_id`=" . $session_obj->id . "
        ";
        $orders_obj = false;
        $database->setQuery($query);
        $orders_obj = $database->loadObjectList();

        $return->result = true;

        $return->orders = array();

        $range = range('A', 'Z');

        foreach ($orders_obj as $order_obj) {
            //$return->orders[] = $order_obj->order_id;

            $_POST['order_id'] = $order_obj->order_id;

            $return->orders[] = get_order_address();

            $_POST['queue'] = array_search($_POST['queue'], $range) + 1;
        }
    }

    echo json_encode($return);

    exit;
}

function in_transit() {
    global $database, $my, $mosConfig_mailfrom, $mosConfig_fromname,
    $mosConfig_live_site, $mosConfig_absolute_path,
    $mosConfig_smtpauth, $mosConfig_mailer, $vmLogger,
    $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtphost, $mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass;
    $mysqlDatetime = date("Y-m-d G:i:s");
    $return = (object) array(
                'result' => false
    );

    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/virtuemart.cfg.php');
    require_once(CLASSPATH . 'ps_main.php');
    require_once(CLASSPATH . 'language.class.php');
    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/languages/english.php');

    $VM_LANG = new vmLanguage();
    $a_orders_info = json_decode($_POST['a_orders_info']);
    $departureTime = $_POST['departureTime'] ?? date("Y-m-d G:i:s");
    if (sizeof($a_orders_info) > 0) {

        $order_id_new = (int) key($a_orders_info[0]);
        $warehouse_id = (int) $a_orders_info[0]->$order_id_new->warehouse_id;
        $driver_id = (int) $a_orders_info[0]->$order_id_new->driver_id;

        $query = "SELECT
            `wh_i`.`warehouse_id`,
            CONCAT(`wh_i`.`city`, ' ', `wh_i`.`street_name`, ' ', `wh_i`.`street_number`, ' ', `wh_i`.`zip`, ' ', `wh_i`.`state`, ' ', `wh_i`.`country`) AS `wh_address`
        FROM `jos_vm_warehouse_info` AS `wh_i`
        WHERE
            `wh_i`.`warehouse_id`=" . $warehouse_id . "
        ";
        $wh_obj = false;
        $database->setQuery($query);
        $database->loadObject($wh_obj);

        //gmap
        $orders = array();
        foreach ($a_orders_info as $order_id => $order_info) {
            $order_id = (int) key($order_info);

            $query = "SELECT
                `ui`.*
            FROM `jos_vm_order_user_info` AS `ui`
            WHERE
                `ui`.`order_id`=" . $order_id . "
                AND
                `ui`.`address_type`='ST'
            ";
            $st_obj = false;
            $database->setQuery($query);
            $database->loadObject($st_obj);

            if ($st_obj) {
                $order = (object) array(
                            'order_id' => $order_id,
                            'st' => ((!empty($st_obj->suite)) ? $st_obj->suite . '#, ' : '') . $st_obj->street_number . ' ' . $st_obj->street_name . ', ' . $st_obj->city . ', ' . $st_obj->zip . '',
                            'tracking_number' => $order_info->$order_id->tracking_number,
                            'driver_service_name' => $order_info->$order_id->driver_service_name,
                            'queue' => $order_info->$order_id->queue,
                            'driver_description' => $order_info->$order_id->driver_description,
                            'driver_information' => $order_info->$order_id->driver_information,
                            'drivers_rate' => $order_info->$order_id->drivers_rate,
                );

                $orders[] = $order;
            }
        }

        $getOptimize = getOptimize($wh_obj->wh_address, $orders);

        if ($getOptimize->result === true) {
            $new_orders = $getOptimize->new_orders;


            $query = "INSERT INTO `jos_vm_routes` (
                `driver_id`, 
                `warehouse_id`, 
                `datetime`, 
                `username`,
                `destination`,
                `map_image`
            ) 
            VALUES (
                " . $driver_id . ",
                " . $wh_obj->warehouse_id . ",
                '" . $departureTime . "',
                '" . $database->getEscaped($my->username) . "',
                '" . $database->getEscaped($_POST['destination']) . "',
                '" . $database->getEscaped($getOptimize->image_link) . "'
            )";
            $database->setQuery($query);
            if ($database->query()) {
                $return->result = true;

                $route_id = $database->insertid();

                $query = "INSERT INTO `jos_vm_routes_history`
                (
                    `id_route`,
                    `text`,
                    `username`,
                    `datetime`
                )
                VALUES (
                    " . $route_id . ",
                    'Created',
                    '" . $database->getEscaped($my->username) . "',
                    '" . $mysqlDatetime . "'
                )";
                $database->setQuery($query);
                if(!$database->query()){
                    $return->error= $database->getErrorMsg();
                }

                require_once CLASSPATH . 'ps_comemails.php';

                foreach ($new_orders as $order_obj) {
                    $order_id = $order_obj->order_id;

                    $ps_comemails = new ps_comemails;
                    $confirmation_obj = $ps_comemails->get_email_text($order_id, 'Z');

                    $query = "UPDATE `jos_vm_orders`
                        SET 
                        `order_status`='Z' 
                    WHERE 
                        `order_id`=" . $order_id . "
                    ";
                    $database->setQuery($query);
                    if(!$database->query()){
                        $return->error = $database->getErrorMsg();
                    }

                    $email_sub = $confirmation_obj->email_subject;
                    $email_html = str_replace('{UpdateStatusComment}', '', $confirmation_obj->email_html);

                    $query = "SELECT 
                        `first_name`, 
                        `last_name`, 
                        `user_email` 
                    FROM `jos_vm_order_user_info` 
                    WHERE 
                        `order_id`=" . $order_id . "
                        AND 
                        `address_type`='BT'
                    ";
                    $user_info = false;
                    $database->setQuery($query);
                    $database->loadObject($user_info);

                    $order_ids[] = $order_id;
                    $comment = "Driver: " . $order_obj->driver_description . "<br/>Driver information: " . $order_obj->driver_information . "<br/>";

                    if (!empty($order_obj->tracking_number)) {
                        $comment .= 'Tracking number ';

                        if (preg_match('/fedex/siu', $order_obj->driver_service_name)) {
                            if (!empty($order_obj->tracking_number)) {
                                $comment .= 'FedEx: <a class="delivery_service" target="_blank" href="https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=' . $order_obj->tracking_number . '&cntry_code=ca">' . $order_obj->tracking_number . '</a>';
                            }
                        } elseif (preg_match('/purolator/siu', $order_obj->driver_service_name)) {
                            if (!empty($order_obj->tracking_number)) {
                                $order_obj->tracking_number = substr($order_obj->tracking_number, 11, 12);
                                $comment .= 'Purolator: <a class="delivery_service" target="_blank" href="https://www.purolator.com/purolator/ship-track/tracking-details.page?pin=' . $order_obj->tracking_number . '">' . $order_obj->tracking_number . '</a>';
                            }
                        } elseif (preg_match('/canpar/siu', $order_info->$order_id->driver_service_name)) {
                            if (!empty($order_obj->tracking_number)) {
                                $comment .= 'Canpar: <a class="delivery_service" target="_blank" href="http://www.canpar.com">' . $order_obj->tracking_number . '</a>';
                            }
                        } elseif (preg_match('/canadapost/siu', $order_obj->driver_service_name)) {
                            if (!empty($order_obj->tracking_number)) {
                                $comment .= 'Canadapost: <a class="delivery_service" target="_blank" href="https://www.canadapost.ca/trackweb/rs/track/json/package?pins=' . $order_obj->tracking_number . '">' . $order_obj->tracking_number . '</a>';
                            }
                        } elseif (preg_match('/tyltgo/siu', $order_obj->driver_service_name)) {
                            if (!empty($order_obj->tracking_number)) {
                                $comment .= 'TYLTGO: <a class="delivery_service" target="_blank" href="https://www.tyltgo.com/tracking/' . $order_obj->tracking_number . '">' . $order_obj->tracking_number . '</a>';
                            }
                        }
                    }

                    $id_rate = (int) $order_obj->drivers_rate;
                    $rate = 0;
                    $driver_rate = 0;

                    $query = "SELECT
                        `dr`.`rate`,
                        `rx`.`rate` AS 'driver_rate'
                    FROM `jos_driver_rates` AS `dr`
                    LEFT JOIN `jos_driver_rate_xref` AS `rx` 
                    ON 
                    `rx`.`id_rate`=`dr`.`id_rate`
                    WHERE
                        `dr`.`id_rate`=" . $id_rate . "
                    ";
                    $database->setQuery($query);
                    $rate_obj = false;
                    $database->loadObject($rate_obj);
                    if ($rate_obj) {
                        $rate = $rate_obj->rate;
                        $driver_rate = $rate_obj->driver_rate;
                    }



                    $routes_orders[] = "(
                        " . $route_id . ", 
                        " . $order_id . ", 
                        " . $id_rate . ",
                        '" . $database->getEscaped($rate) . "', 
                        '" . $database->getEscaped($driver_rate) . "', 
                        '" . $database->getEscaped($order_obj->queue) . "',
                        '" . $database->getEscaped($order_obj->location->lat) . "',
                        '" . $database->getEscaped($order_obj->location->lng) . "',
                        '" . $database->getEscaped($order_obj->distance) . "',
                        '" . $database->getEscaped($order_obj->duration) . "',
                        '" . $database->getEscaped($mysqlDatetime) . "',
                        '" . $database->getEscaped($order_obj->distance_raw) . "',
                        '" . $database->getEscaped($order_obj->duration_raw) . "'
                    )";

                    $comment .= '';

                    $query = "INSERT INTO `jos_vm_order_history` 
                    (
                        `order_id`, 
                        `order_status_code`, 
                        `customer_notified`, 
                        `date_added`, 
                        `comments`, 
                        `user_name`
                    )
                    VALUES (
                        " . $order_id . ",
                        'Z', 
                        '1', 
                        '" . $mysqlDatetime . "', 
                        '" . $database->getEscaped($comment) . "', 
                        '" . $database->getEscaped($my->username) . "'
                    )";
                    $database->setQuery($query);
                    if(!$database->query()){
                        $return->error = $database->getErrorMsg();
                    }
                    $ps_comemails->send($ps_comemails->setVariables($order_id, $email_sub), $ps_comemails->setVariables($order_id, $email_html), $order_id, $user_info->user_email);

                    unset($ps_comemails);
                }

                if (isset($routes_orders) && count($routes_orders) > 0) {
                    $query = "INSERT INTO `jos_vm_routes_orders` 
                    (
                        `route_id`, 
                        `order_id`, 
                        `id_rate`, 
                        `rate`, 
                        `driver_rate`,
                        `queue`,
                        `lat`,
                        `lng`,
                        `distance`,
                        `duration`,
                        `last_update_datetime`,
                        `distance_raw`,
                        `duration_raw`
                    )
                    VALUES 
                        " . implode(', ', $routes_orders) . "
                    ";
                    $database->setQuery($query);
                    if(!$database->query()){
                        $return->error = $database->getErrorMsg();
                    }
                }
            } else {
                $return->error = $database->getErrorMsg();
            }
        }else {
            $return->error = $getOptimize->error;
        }
    }

    echo json_encode($return);
    die;
}

function get_order_address() {
    global $database;

    $order_id = mosGetParam($_POST, 'order_id', '');
    $tracking_number = mosGetParam($_POST, 'tracking_number', '');
    $order_id = (int) str_replace('bloom-', '', $order_id);

    $warehouse_id = mosGetParam($_POST, 'warehouse_id', '');
    $driver_id = mosGetParam($_POST, 'driver_id', '');

    $return = array();

    $return['order_id'] = $order_id;

    $query = "SELECT `warehouse_id`, `warehouse_name` FROM `jos_vm_warehouse` WHERE `warehouse_id`=" . (int) $warehouse_id . "";
    $warehouse = false;
    $database->setQuery($query);
    $database->loadObject($warehouse);

    $return['warehouse_name'] = $warehouse->warehouse_name;

    $query = "SELECT `id`, `service_name`, `driver_option_type`, `description` FROM `tbl_driver_option` WHERE `id`=" . (int) $driver_id . "";
    $driver = false;
    $database->setQuery($query);
    $database->loadObject($driver);
    $driver->description = mosGetParam($_POST, 'description', '');
    $return['driver_description'] = $driver->description;
    $return['driver_service_name'] = $driver->driver_option_type . ' - ' . $driver->service_name;

    $query = "SELECT * FROM `jos_vm_order_user_info` WHERE `order_id`=" . $order_id . " AND `address_type`='ST'";
    $address = false;
    $database->setQuery($query);
    $database->loadObject($address);

    //$return['address'] = $address->city . ', ' . $address->address_1 . ', ' . $address->zip . ', ' . 'CA';
    $return['address'] = (!empty($address->suite) ? $address->suite . '#, ' : '') . $address->street_number . ' ' . $address->street_name . ', ' . $address->city . ', ' . $address->zip . ', Australia';
    $return['driver_id'] = $driver->id;
    $return['warehouse_id'] = $warehouse->warehouse_id;
    $return['company'] = $address->company;
    $return['suite'] = $address->suite;
    $return['street_number'] = $address->street_number;
    $return['street_name'] = $address->street_name;
    $return['zip'] = $address->zip;
    $return['phone'] = $address->phone_1;
    $return['full_name'] = $address->first_name . ' ' . $address->middle_name . ' ' . $address->last_name;
    $return['queue'] = $_POST['queue'];

    $query = "SELECT 
        `s`.`order_status_name`,
        `o`.`ship_method_id`
    FROM `jos_vm_orders` AS `o`
    INNER JOIN `jos_vm_order_status` AS `s` 
        ON 
        `s`.`order_status_code`=`o`.`order_status`    
    WHERE `order_id`=" . $order_id . "";
    $status = false;
    $database->setQuery($query);
    $database->loadObject($status);

    $return['order_status'] = $status->order_status_name;
    $return['tracking_number'] = $tracking_number;

    $ship_method_id_a = explode('|', $status->ship_method_id);

    $return['shipment'] = 1; //standart

    if (isset($ship_method_id_a[4]) OR isset($ship_method_id_a[5])) {
        Switch ($ship_method_id_a[4]) {
            case 25:
                $return['shipment'] = 2; //morning
                break;
            case 26:
                $return['shipment'] = 3; //evening
                break;
        }
        Switch ($ship_method_id_a[5]) {
            case 25:
                $return['shipment'] = 2; //morning
                break;
            case 26:
                $return['shipment'] = 3; //evening
                break;
        }
    }

    $query = "SELECT 
        `score`
    FROM `tbl_address_validation` 
    WHERE `order_id`=" . $order_id . "";
    $score = false;
    $database->setQuery($query);
    $database->loadObject($score);

    if ($score) {
        $return['score'] = $score->score;
    } else {
        $return['score'] = 0;
    }

    $query = "SELECT 
        *
    FROM `jos_vm_orders` 
    WHERE `order_id`=" . $order_id . "";
    $order_obj = false;
    $database->setQuery($query);
    $database->loadObject($order_obj);

    $return['ddate'] = $order_obj->ddate;
    $return['special_instructions'] = $order_obj->customer_comments;
    $ship_method_id_a = explode('|', $order_obj->ship_method_id);
    $return['dtime'] = 'Regular';
    $shipping_method_names = array(
        24 => 'Regular',
        25 => 'Morning',
        26 => 'Evening'
    );

    $shipping_id = (int) $ship_method_id_a[4];

    if (!array_key_exists($shipping_id, $shipping_method_names)) {
        $shipping_id = (int) $ship_method_id_a[5];
    }
    if (array_key_exists($shipping_id, $shipping_method_names)) {
        $return['dtime'] = $shipping_method_names[$shipping_id];
    }

    $return['drivers_rates'] = array();
    $return['drivers_rate'] = '';

    $query = "SELECT 
        `r`.`id_rate`,
        `r`.`rate`,
        `r`.`name`
    FROM `jos_driver_rates` AS `r`
    WHERE
        `r`.`warehouse_id`=" . (int) $warehouse_id . " 
    ORDER BY 
            `r`.`orderby` asc";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if ($rows) {
        $return['drivers_rates'] = $rows;

        $query = "SELECT 
            `r`.`id_rate`
        FROM `jos_driver_rates_postalcodes` AS `rp`
        INNER JOIN `jos_driver_rates` AS `r`
            ON 
            `r`.`id_rate`=`rp`.`id_rate`
        WHERE
            `rp`.`postalcode`='" . $database->getEscaped(strtoupper(mb_substr(trim($address->zip), 0, 3))) . "'
        ";
        $rate_obj = false;
        $database->setQuery($query);
        $database->loadObject($rate_obj);

        if ($rate_obj) {
            $return['drivers_rate'] = $rate_obj->id_rate;
        }
    }
    return $return;
}

function defaultview() {
    global $database, $mainframe, $mosConfig_list_limit;

    $return_a = array();

    $query = "SELECT `DO`.*, `VMW`.`warehouse_name`
        FROM `tbl_driver_option` AS `DO`, `jos_vm_warehouse` AS `VMW`
        WHERE `DO`.`warehouse_id` = `VMW`.`warehouse_id` ORDER BY `VMW`.`warehouse_name`";

    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {
        foreach ($rows as $value) {
            if (!isset($return_a['driver_options'][$value->warehouse_id])) {
                $return_a['driver_options'][$value->warehouse_id] = array();
            }

            $return_a['driver_options'][$value->warehouse_id][] = array(
                'driver_id' => $value->id,
                'title' => trim($value->driver_option_type) . ' - ' . trim($value->service_name),
                'description' => trim($value->description)
            );
        }
    }

    $types = array();
    $types[] = mosHTML::makeOption("", "------ Select Warehouse ------");
    $query = "SELECT 
        `wh`.*,
        `wh_i`.`city`,
        `wh_i`.`street_number`,
        `wh_i`.`street_name`,
        `wh_i`.`state`,
        `wh_i`.`zip`,
        `wh_i`.`country`
    FROM `jos_vm_warehouse` AS `wh`
    INNER JOIN `jos_vm_warehouse_info` AS `wh_i`
        ON
        `wh_i`.`warehouse_id`=`wh`.`warehouse_id`
    ORDER BY `wh`.`warehouse_name`";

    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {

        foreach ($rows as $warehouse) {
            $name = strtolower($warehouse->warehouse_name);

            $return_a['warehouse_address'][$warehouse->warehouse_id] = $warehouse->city . ', ' . $warehouse->street_name . ' ' . $warehouse->street_number . ', ' . $warehouse->zip . ', ' . $warehouse->country . '';

            $types[] = mosHTML::makeOption($warehouse->warehouse_id, $warehouse->warehouse_name);
        }
    }

    $return_a['warehouses_select'] = mosHTML::selectList($types, 'warehouse_id', 'class="form-control" size="1"', 'value', 'text', '');


    $types = array();
    $types[] = mosHTML::makeOption("", "------ Select List ------");

    $query = "SELECT
        `s`.`id`
    FROM 
        `jos_vm_scanning_sessions` AS `s`
    WHERE
        `s`.`datetime_modified`>'" . date('Y-m-d H:i:s', strtotime('-30 minutes')) . "'
    ";
    $database->setQuery($query);
    $rows = $database->loadObjectList();

    foreach ($rows as $scan) {
        $types[] = mosHTML::makeOption($scan->id, '#' . $scan->id);
    }

    $return_a['scan_select'] = mosHTML::selectList($types, 'scan_id', 'class="form-control" size="1"', 'value', 'text');

    HTML_ShipOrder::defaultview($return_a);
}
