<?php

defined('_VALID_MOS') or die('Restricted access');

require_once( $mainframe->getPath('admin_html') );

switch ($task) {
    case 'in_transit':
        in_transit();
        break;

    case 'get_order_address':
        get_order_address();
        break;

    default:
        defaultview();
        break;
}

function in_transit() {
    global $database, $my, $mosConfig_mailfrom, $mosConfig_fromname,
    $mosConfig_live_site, $mosConfig_absolute_path,
    $mosConfig_smtpauth, $mosConfig_mailer, $vmLogger,
    $mosConfig_smtpuser, $mosConfig_smtppass, $mosConfig_smtphost;

    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/virtuemart.cfg.php');
    require_once( CLASSPATH . 'ps_main.php');
    require_once( CLASSPATH . 'language.class.php');
    require_once($mosConfig_absolute_path . '/administrator/components/com_virtuemart/languages/english.php');

    $VM_LANG = new vmLanguage();

    $a_orders_info = json_decode($_POST['a_orders_info']);

    if (sizeof($a_orders_info) > 0) {
        $order_ids = $inserts = array();
        require_once CLASSPATH . 'ps_comemails.php';

        foreach ($a_orders_info as $order_info) {
            $ps_comemails = new ps_comemails;

            $order_id = (int) key($order_info);

            $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1' AND `order_status_code`='Z'";
            $confirmation_obj = false;
            $database->setQuery($query);
            $database->loadObject($confirmation_obj);

            if (!$confirmation_obj) {
                $query = "SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1'";
                $confirmation_obj = false;
                $database->setQuery($query);
                $database->loadObject($confirmation_obj);
            }

            $confirmation_obj->email_html = str_replace('{UpdateStatusComment}', '', $confirmation_obj->email_html);

            $query = "SELECT `first_name`, `last_name`, `user_email` FROM `jos_vm_order_user_info` WHERE `order_id`=" . $order_id . " AND `address_type`='BT'";
            $user_info = false;
            $database->setQuery($query);
            $database->loadObject($user_info);

            $order_ids[] = $order_id;
            $comment = "Driver: " . $order_info->$order_id->driver_description . "<br/>Driver information: " . $order_info->$order_id->driver_information . "<br/>";

            if (!empty($order_info->$order_id->tracking_number)) {
                $comment .= 'Tracking number ';

                if (preg_match('/fastway/siu', $order_info->$order_id->driver_service_name)) {
                    if (!empty($order_info->$order_id->tracking_number)) {
                        $comment .= 'Fastway: <a class="delivery_service" target="_blank" href="https://www.fastway.com.au/tools/track/">' . $order_info->$order_id->tracking_number . '</a>';
                    }
                } elseif (preg_match('/gopeople/siu', $order_info->$order_id->driver_service_name)) {
                    if (!empty($order_info->$order_id->tracking_number)) {
                        $comment .= 'GoPeople: <a class="delivery_service" target="_blank" href="https://www.gopeople.com.au/tracking/?code=' . $order_info->$order_id->tracking_number . '">' . $order_info->$order_id->tracking_number . '</a>';
                    }
                }
            }
            date_default_timezone_set('Australia/Sydney');
            $mysqlDatetime = date("Y-m-d G:i:s");

            $inserts = "(" . $order_id . ", 'Z', '1', '" . $mysqlDatetime . "', '" . $comment . "', '" . $my->username . "')";

            $query = "INSERT INTO `jos_vm_order_history` (`order_id`, `order_status_code`, `customer_notified`, `date_added`, `comments`, `user_name`) VALUES {$inserts}";
            $database->setQuery($query);
            $database->query();

            $query = "UPDATE `jos_vm_orders` SET `order_status`='Z' WHERE `order_id` = '{$order_id}'";
            $database->setQuery($query);
            $database->query();

            $subject = $ps_comemails->setVariables($order_id, $confirmation_obj->email_subject);
            $message = $ps_comemails->setVariables($order_id, $confirmation_obj->email_html);

            vmMail($mosConfig_mailfrom, $mosConfig_fromname, $user_info->user_email, $subject, $message, '', true);
            unset($ps_comemails);
        }
    }
    exit(0);
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

    $query = "SELECT `warehouse_name` FROM `jos_vm_warehouse` WHERE `warehouse_id`=" . $warehouse_id . "";
    $warehouse = false;
    $database->setQuery($query);
    $database->loadObject($warehouse);

    $return['warehouse_name'] = $warehouse->warehouse_name;

    $query = "SELECT `service_name`, `driver_option_type`, `description` FROM `tbl_driver_option` WHERE `id`=" . $driver_id . "";
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

    $return['address'] = $address->city . ', ' . $address->address_1 . ', ' . $address->zip . ', ' . 'AU';

    $query = "SELECT `s`.`order_status_name`
        FROM `jos_vm_orders` AS `o`
        INNER JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`o`.`order_status`
        WHERE `order_id`=" . $order_id . "";
    $status = false;
    $database->setQuery($query);
    $database->loadObject($status);

    $return['order_status'] = $status->order_status_name;
    $return['tracking_number'] = $tracking_number;
    if($status->order_status_name =='in transit'){
        $return = array();
    }
    echo json_encode($return);

    die(0);
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
    $query = "SELECT * FROM jos_vm_warehouse ORDER BY warehouse_name ";

    $database->setQuery($query);
    $rows = $database->loadObjectList();

    if (count($rows)) {
//        require_once 'bloomexorder.php';

        foreach ($rows as $warehouse) {
            $name = strtolower($warehouse->warehouse_name);

//            $sender_options = new SenderOptions($name);

            $return_a['warehouse_address'][$warehouse->warehouse_id] = $warehouse->city . ', ' . $warehouse->street_name . ' ' . $warehouse->street_number . ', ' . $warehouse->postal_code . ', ' . 'AU';

            $types[] = mosHTML::makeOption($warehouse->warehouse_id, $warehouse->warehouse_name);
        }
    }
    $warehouse_id = '';
    $return_a['warehouses_select'] = mosHTML::selectList($types, 'warehouse_id', 'class="form-control" size="1"', 'value', 'text', $warehouse_id);

    HTML_ShipOrder::defaultview($return_a);
}
