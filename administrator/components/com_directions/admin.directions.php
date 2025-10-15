<?php

defined('_VALID_MOS') or die('Restricted access');

date_default_timezone_set('Australia/Sydney');

require_once $mainframe->getPath('admin_html');


$directions = new directions;
$directions->database = $database;
$directions->option = 'com_directions';
$directions->cid = josGetArrayInts('cid');
$directions->id = (int)$id > 0 ? (int)$id : (int)$directions->cid[0];
$directions->my = $my;

switch ($task) {
    case 'get-pdf':
        $directions->getPdf();
        break;
    case 'remake':
        $directions->remake();
        break;
    case 'set-unbillable':
        $directions->unbillable();
        break;

    case 'unpublish':
        $ids = array();
        if (count($directions->cid) > 0) {
            $ids = $directions->cid;
        } else {
            $ids[] = $directions->id;
        }
        $directions->unpublish($ids);
        break;
    case 'publish':
        $ids = array();
        if (count($directions->cid) > 0) {
            $ids = $directions->cid;
        } else {
            $ids[] = $directions->id;
        }
        $directions->publish($ids);
        break;
    case 'remove':
        $route_id = (isset($_POST['route_id']) and !empty($_POST['route_id'])) ? (int)$_POST['route_id'] : false;
        $directions->deleteRoute($route_id);
        break;

    case 'getOrders':
        $route_id = (isset($_POST['route_id']) and !empty($_POST['route_id'])) ? (int)$_POST['route_id'] : false;
        $directions->getOrders($route_id);
        break;

    case 'new':
    case 'edit':
        $directions->edit_new();
        break;

    case 'save':
        $directions->save();
        break;

    default:
        $warehouse = (isset($_POST['warehouse']) and !empty($_POST['warehouse'])) ? (int)$_POST['warehouse'] : false;
        $driver = (isset($_POST['driver']) and !empty($_POST['driver'])) ? (int)$_POST['driver'] : false;
        $show_completed = (isset($_POST['show_completed']) and !empty($_POST['show_completed'])) ? (int)$_POST['show_completed'] : false;
        $order_route_id = (isset($_POST['order_route_id']) and !empty($_POST['order_route_id'])) ? (int)$_POST['order_route_id'] : false;
        $date_from = (isset($_POST['date_from']) and !empty($_POST['date_from'])) ? $_POST['date_from'] : false;
        $date_to = (isset($_POST['date_to']) and !empty($_POST['date_to'])) ? $_POST['date_to'] : false;
        $zerovalue = (isset($_POST['zerovalue']) and !empty($_POST['zerovalue'])) ? $_POST['zerovalue'] : false;
        $only_get_unpublished = ($task == 'get_unpublished') ? true : false;
        $directions->default_list($warehouse, $driver, $show_completed, $order_route_id, $date_from, $date_to, $only_get_unpublished, $zerovalue);
        break;
}

class directions
{

    var $database;
    var $option;
    var $my;
    var $route_id;

    public function getPdf()
    {
        global $mosConfig_adm_link, $mosConfig_adm_auth;
        $d['route_id'] = (isset($_POST['id']) ? (int)$_POST['id'] : 0);
        ob_end_clean();
        $service_url = $mosConfig_adm_link . '/scripts/for_blca/routerPdfGenerator.php';
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $mosConfig_adm_auth);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        $result = curl_exec($curl);
        curl_close($curl);
        header('Content-Type: application/pdf');
        header("Content-Disposition:attachment;filename=route_" . $d['route_id'] . ".pdf");
        echo $result;
        die;
    }

    public function remake()
    {
        global $mosConfig_adm_link, $mosConfig_adm_auth;
        $route_id = (isset($_POST['id']) ? (int)$_POST['id'] : 0);

        $query = "UPDATE `jos_vm_scanning_sessions`
                SET `driver_id` = '" . $this->database->getEscaped($_POST['driver_id']) . "'
                WHERE token = '" . $this->database->getEscaped($_POST['scan_session_token']) . "'
            ";
        $this->database->setQuery($query);
        $this->database->query();

        $query = "UPDATE `jos_vm_routes`
        SET
            `publish`='0', scan_session_token =''
        WHERE 
            `id`=" . $route_id . "
        ";
        $this->database->setQuery($query);
        $this->database->query();

        $this->add_to_history("Route $route_id unpublished and returned to driver app scan");
        mosRedirect('index2.php?option=' . $this->option, "Route $route_id returned to driver phone");
    }

    public function unbillable()
    {
        $return = (object)array(
            'result' => false
        );
        $order_id = (isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0);
        $route_id = (isset($_POST['route_id']) ? (int)$_POST['route_id'] : 0);
        $reason = (isset($_POST['reason']) ? $_POST['reason'] : '');

        $query = "UPDATE `jos_vm_routes_orders`
        SET
            `status`='3'
        WHERE 
            `order_id`=" . $order_id . "
            AND
            `route_id`=" . $route_id . "
        ";

        $this->database->setQuery($query);
        if ($this->database->query()) {
            $return->result = true;

            $query = "SELECT 
                `o`.`order_id`,
                `o`.`order_status`
            FROM `jos_vm_orders` AS `o` 
            WHERE 
                `o`.`order_id`=" . $order_id . "
            ";
            $this->database->setQuery($query);
            $this->database->loadObject($order_obj);
            /*
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
              " . $order_obj->order_id . ",
              '" . $order_obj->order_status . "',
              '0',
              '" . date('Y-m-d H:i:s') . "',
              '" . $this->database->getEscaped('Order was unbillable for driver.') . "',
              '" . $this->database->getEscaped($this->my->username) . "'
              )";
             */
            $query = "INSERT INTO `jos_vm_routes_history`
            (
                `id_route`,
                `text`,
                `username`,
                `datetime`
            )
            VALUES (
                " . $route_id . ",
                '" . $this->database->getEscaped('Order #' . $order_id . ' question to unbillable. ' . $reason . '') . "',
                '" . $this->database->getEscaped($this->my->username) . "',
                '" . date('Y-m-d G:i:s') . "'
            )";
            $this->database->setQuery($query);
            $this->database->query();
        }

        echo json_encode($return);

        exit();
    }

    public function save()
    {
        global $my;

        $id_driver = (int)($_POST['driver_id']);

        if ($this->id > 0) {
            $query = "SELECT 
                `r`.`id`,
                `d`.`service_name`
            FROM `jos_vm_routes` AS `r` 
            INNER JOIN `tbl_driver_option` AS `d` 
                ON 
                    `d`.`id`=`r`.`driver_id`
            WHERE `r`.`id`=" . $this->id . "";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "SELECT 
                    `d`.`service_name`
                FROM `tbl_driver_option` AS `d` 
                WHERE
                    `d`.`id`=" . $id_driver . "";

                $this->database->setQuery($query);
                $this->database->loadObject($driver_row);

                $query = "INSERT INTO `jos_vm_routes_history`
                (
                    `id_route`,
                    `text`,
                    `username`,
                    `datetime`
                )
                VALUES (
                    " . $this->id . ",
                    '" . $this->database->getEscaped('Change driver from ' . $row->service_name . ' to ' . $driver_row->service_name . '.') . "',
                    '" . $this->database->getEscaped($my->username) . "',
                    '" . date('Y-m-d G:i:s') . "'
                )";

                $this->database->setQuery($query);
                $this->database->query();

                $query = "SELECT 
                    `ro`.`id`,
                    `ro`.`id_rate`,
                    `ro`.`rate`,
                    `ro`.`driver_rate`,
                    `ro`.`order_id`,
                    `ro`.`billable`,
                    `dr`.`name` as 'zone'
                FROM `jos_vm_routes_orders` AS `ro` 
                LEFT JOIN `jos_driver_rates` AS `dr` ON `dr`.`id_rate`=`ro`.`id_rate`
                WHERE 
                    `ro`.`route_id`=" . $this->id . "
                ORDER BY 
                    `ro`.`queue`";
                $this->database->setQuery($query);
                $orders = $this->database->loadObjectList();
                $i = 0;
                foreach ($orders as $order) {
                    if ($order->id_rate != $_POST['rates'][$i]) {
                        $query = "UPDATE `jos_vm_routes_orders` 
                        SET
                            `id_rate`=" . (int)$_POST['rates'][$i] . "
                        WHERE
                            `id`=" . $order->id . "
                            AND
                            `route_id`=" . $this->id . "
                        ";
                        $this->database->setQuery($query);
                        $this->database->query();
                        $this->add_to_history($order->order_id . ' Changed zone from ' . $order->zone . '(' . $order->id_rate . ') to ' . $this->database->getEscaped($_POST['rates'][$i]));
                    }
                    if ($order->rate != $_POST['custom_rates'][$i]) {
                        $query = "UPDATE `jos_vm_routes_orders` 
                        SET
                            `rate`='" . $this->database->getEscaped($_POST['custom_rates'][$i]) . "'
                        WHERE
                            `id`=" . $order->id . "
                            AND
                            `route_id`=" . $this->id . "
                        ";
                        $this->database->setQuery($query);
                        $this->database->query();
                        $this->add_to_history($order->order_id . ' Changed rate from ' . $order->rate . ' to ' . $this->database->getEscaped($_POST['custom_rates'][$i]));
                    }
                    if ($order->driver_rate != $_POST['custom_rates_driver'][$i]) {
                        $query = "UPDATE `jos_vm_routes_orders` 
                        SET
                            `driver_rate`='" . $this->database->getEscaped($_POST['custom_rates_driver'][$i]) . "'
                        WHERE
                            `id`=" . $order->id . "
                            AND
                            `route_id`=" . $this->id . "
                        ";
                        $this->database->setQuery($query);
                        $this->database->query();
                        $this->add_to_history($order->id . ' Changed driver rate from ' . $order->driver_rate . ' to ' . $this->database->getEscaped($_POST['custom_rates_driver'][$i]));
                    }
                    if ($order->billable != $_POST['billable'][$i]) {
                        $query = "UPDATE `jos_vm_routes_orders` 
                        SET
                            `billable`='" . (int)$_POST['billable'][$i] . "'
                        WHERE
                            `id`=" . $order->id . "
                            AND
                            `route_id`=" . $this->id . "
                        ";
                        $this->database->setQuery($query);
                        $this->database->query();
                        $this->add_to_history($order->order_id . ' Changed billable  from ' . $order->billable . ' to ' . $_POST['billable'][$i]);
                    }
                    $i++;
                }

                $query = "UPDATE `jos_vm_routes`
                SET
                    `driver_id`=" . $id_driver . "
                WHERE `id`=" . $this->id . "";

                $this->database->setQuery($query);
                if ($this->database->query()) {
                    mosRedirect('index2.php?option=' . $this->option, 'Success.');
                } else {
                    mosRedirect('index2.php?option=' . $this->option, 'ERROR: Database error (update).');
                }
            } else {
                mosRedirect('index2.php?option=' . $this->option, 'ERROR: Database error (select).');
            }
        }
    }

    public function unpublish($ids)
    {
        $query = "UPDATE `jos_vm_routes` SET `publish`='0' WHERE `id` IN (" . implode(',', $ids) . ")";

        $this->database->setQuery($query);
        $this->database->query();

        mosRedirect('index2.php?option=' . $this->option, 'Unpublished!');
    }

    public function publish($ids)
    {
        $query = "UPDATE `jos_vm_routes` SET `publish`='1' WHERE `id` IN (" . implode(',', $ids) . ")";

        $this->database->setQuery($query);
        $this->database->query();

        mosRedirect('index2.php?option=' . $this->option, 'published!');
    }

    public function deleteRoute($route_id)
    {
        $return = array();
        $return['result'] = false;

        $route = false;

        $query = "SELECT 
            `r`.`id`
        FROM `jos_vm_routes` AS `r` 
        WHERE `r`.`id`=" . $route_id . "";

        $this->database->setQuery($query);
        $this->database->loadObject($route);

        if ($route) {
            $query = "DELETE `jos_vm_routes_orders` WHERE `route_id`=" . $route->id . "";
            $database->setQuery($sql);
            $database->query();

            $query = "DELETE `jos_vm_routes` WHERE `id`=" . $route->id . "";
            $database->setQuery($sql);
            $database->query();
        }

        mosRedirect('index2.php?option=' . $this->option, 'Deleted!');
    }

    public function getOrders($route_id)
    {
        $return = array();
        $return['result'] = false;

        $route = false;

        $query = "SELECT 
            `r`.`id`
        FROM `jos_vm_routes` AS `r` 
        WHERE `r`.`id`=" . $route_id . "";

        $this->database->setQuery($query);
        $this->database->loadObject($route);

        if ($route) {
            $query = "SELECT 
                `ro`.*
            FROM `jos_vm_routes_orders` AS `ro` 
            WHERE `ro`.`route_id`=" . $route->id . "
            ORDER BY `ro`.`id`";

            $this->database->setQuery($query);
            $rows = $this->database->loadObjectList();

            if ($rows) {
                $return['result'] = true;
                $return['route_id'] = $route->id;
                $return['rows'] = $rows;
            }
        }

        echo json_encode($return);
        die;
    }

    public function edit_new()
    {
        global $mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass;
        $row = false;

        if ($this->id > 0) {
            $query = "SELECT 
                `r`.*,
                `d`.`service_name`,
                CONCAT(`wh_i`.`city`, ' ', `wh_i`.`street_name`, ' ', `wh_i`.`street_number`, ' ', `wh_i`.`zip`, ' ', `wh_i`.`state`, ' ', `wh_i`.`country`) AS `wh_address`,
                `wh`.`warehouse_name`,
                `r`.`warehouse_id`
            FROM `jos_vm_routes` AS `r` 
            INNER JOIN `tbl_driver_option` AS `d` 
                ON 
                `d`.`id`=`r`.`driver_id`
            INNER JOIN `jos_vm_warehouse` AS `wh`
                ON
                `wh`.`warehouse_id`=`r`.`warehouse_id`
            INNER JOIN `jos_vm_warehouse_info` AS `wh_i`
                ON
                `wh_i`.`warehouse_id`=`wh`.`warehouse_id`
            WHERE 
                `r`.`id`=" . $this->id . "
            ";

            $this->database->setQuery($query);
            $this->database->loadObject($route);

            if ($route) {
                $query = "SELECT 
                    `ro`.*,
                    `ui`.`company`,
                    `ui`.`suite`,
                    `ui`.`street_number`,
                    `ui`.`street_name`,
                    `ui`.`city`,
                    `ui`.`zip`,
                    `dr`.`id_rate`
                FROM `jos_vm_routes_orders` AS `ro` 
                INNER JOIN `jos_vm_order_user_info` AS `ui`
                    ON
                    `ui`.`order_id`=`ro`.`order_id`
                    AND
                    `ui`.`address_type`='ST'
                INNER JOIN `jos_vm_warehouse` AS `wh`
                    ON
                    `wh`.`warehouse_id`=" . $route->warehouse_id . "
                LEFT JOIN `jos_driver_rates` AS `dr`
                    ON
                    `dr`.`id_rate`=`ro`.`id_rate`
                    AND
                    `dr`.`warehouse_id`=`wh`.`warehouse_id`
                WHERE 
                    `ro`.`route_id`=" . $route->id . "
                ORDER BY 
                    `ro`.`queue`";

                $this->database->setQuery($query);
                $orders = $this->database->loadObjectList();

                $route->orders = array();

                foreach ($orders as $order) {
                    $order->address = ((!empty($order->suite)) ? $order->suite . '#, ' : '') . $order->street_number . ' ' . $order->street_name . ", " . $order->city . ', ' . $order->zip . '';

                    $route->orders[] = $order;
                }

                $query = "SELECT 
                    `d`.`id`,
                    CONCAT(`wh`.`warehouse_name`, ' - ', `d`.`service_name`) AS `name`
                FROM `tbl_driver_option` AS `d` 
                INNER JOIN `jos_vm_warehouse` AS `wh`
                    ON
                        `wh`.`warehouse_id`=`d`.`warehouse_id`
                WHERE `d`.warehouse_id = " . $route->warehouse_id . "
                ORDER BY `wh`.`warehouse_name`,`d`.`service_name`";

                $this->database->setQuery($query);
                $drivers = $this->database->loadObjectList();

                $route->drivers = array();

                foreach ($drivers as $driver) {
                    $route->drivers[] = $driver;
                }

                $query = "SELECT 
                    `rh`.*
                FROM `jos_vm_routes_history` AS `rh` 
                WHERE `rh`.`id_route`=" . $route->id . "
                ORDER BY `rh`.`id_history`";

                $this->database->setQuery($query);
                $histories = $this->database->loadObjectList();

                $route->histories = array();

                foreach ($histories as $history) {
                    $route->histories[] = $history;
                }

                $query = "SELECT 
                    `dr`.*
                FROM `jos_driver_rates` AS `dr` 
                WHERE 
                    `dr`.`warehouse_id`=" . $route->warehouse_id . " ORDER BY 
                `dr`.`orderby` asc";

                $this->database->setQuery($query);
                $rates = $this->database->loadObjectList();

                $route->rates = array();

                foreach ($rates as $rate) {
                    $route->rates[] = $rate;
                }

                if (empty($route->map_image) || !getimagesize($route->map_image)) {
                    $map_orders = array();

                    foreach ($orders as $order_obj) {
                        $map_orders[] = array(
                            $order_obj->queue,
                            $order_obj->order_id,
                            $order_obj->address,
                            $order_obj->company
                        );
                    }
                    $wh_address = $route->wh_address;

                    $addresses = array();
                    foreach ($map_orders as $order_obj) {
                        $addresses[] = $order_obj[2];
                    }

                    $getfields = array(
                        'origin' => $wh_address,
                        'destination' => $wh_address, //end($st_array),
                        'waypoints' => implode('|', $addresses),
                        'key' => 'AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY'
                    );

                    $curl = curl_init('https://maps.googleapis.com/maps/api/directions/json?' . http_build_query($getfields));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
                    $curl_response = curl_exec($curl);
                    $json = json_decode($curl_response);
                    curl_close($curl);

                    if (isset($json->status) and $json->status == 'OK') {
                        $markers = array(
                            urlencode('color:red|label:WH|' . $wh_address)
                        );

                        $range = range('B', 'Z');
                        $i = 0;
                        foreach ($addresses as $address) {
                            $markers[] = urlencode('color:red|label:' . $range[$i] . '|' . $address);
                            $i++;
                        }
                        $getfields = array(
                            'size' => '600x300',
                            'path' => 'enc:' . $json->routes[0]->overview_polyline->points,
                            'key' => 'AIzaSyBTEd41u9X6a_9Mh7RIRSQD2vmGL40BcSY'
                        );

                        $curl = curl_init('https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($getfields) . '&markers=' . implode('&markers=', $markers));
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
                        $curl_response = curl_exec($curl);
                        curl_close($curl);

                        $tmp = tmpfile();
                        fwrite($tmp, $curl_response);
                        rewind($tmp);

                        $file_folder = 'bloomex.ca/routemaps/' . $route->id;
                        $image_name = time() . '_' . mt_rand(1000, 9999) . '.png';
                        $image_link = $file_folder . '/' . $image_name;
                        $metaDatas = stream_get_meta_data($tmp);
                        $tmpFilename = $metaDatas['uri'];

                        $this->ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $tmpFilename, $image_link, $file_folder);

                        fclose($tmp);

                        $route->map_image = 'https://media.bloomex.ca/' . $image_link;

                        $query = "UPDATE `jos_vm_routes` 
                        SET
                            `map_image`='" . $this->database->getEscaped($route->map_image) . "'
                        WHERE
                            `id`=" . $route->id . "
                        ";

                        $this->database->setQuery($query);
                        $this->database->query();
                    }
                }
            }
        }
        $route->map_image = preg_replace("/^http:/i", "https:", $route->map_image);
        HTML_directions::edit_new($this->option, $route);
    }

    function ftp_is_dir($ftp, $dir)
    {
        $pushd = ftp_pwd($ftp);

        if ($pushd !== false && @ftp_chdir($ftp, $dir)) {
            ftp_chdir($ftp, $pushd);
            return true;
        }

        return false;
    }

    function ftp_move_file($mosConfig_email_sender_ftp_host, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass, $photo, $filename, $dir)
    {
        $ftp = ftp_connect($mosConfig_email_sender_ftp_host);

        if (ftp_login($ftp, $mosConfig_email_sender_ftp_login, $mosConfig_email_sender_ftp_pass)) {
            ftp_pasv($ftp, true);

            if (!$this->ftp_is_dir($ftp, $dir)) {
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

    public function default_list($warehouse, $driver, $show_completed, $order_route_id, $date_from, $date_to, $only_get_unpublished, $zerovalue = false)
    {
        global $mainframe, $mosConfig_list_limit;

        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval($mainframe->getUserStateFromRequest('viewpl' . $this->option . 'limitstart', 'limitstart', 0));

        $where_a = array();
        $where_a[] = ($only_get_unpublished) ? "`r`.`publish`='0'" : "`r`.`publish`='1'";

        if ($warehouse != false) {
            $where_a[] = "`r`.`warehouse_id`=" . $warehouse . "";
        }
        if (($warehouse != false) and ($driver != false)) {
            $where_a[] = "`r`.`driver_id`=" . $driver . "";
        }

        if ($show_completed != 1) {
            $where_a[] = "`ro`.`id` IS NOT NULL";
        }
        if ($date_from != false) {
            $where_a[] = "DATE_FORMAT(`r`.`datetime`, '%Y-%m-%d') >= '" . $date_from . "'";
        }
        if ($date_to != false) {
            $where_a[] = "DATE_FORMAT(`r`.`datetime`, '%Y-%m-%d') <= '" . $date_to . "'";
        }
        if ($order_route_id != false) {
            $where_a[] = "(`ro`.`order_id`=" . $order_route_id . " OR `ro`.`route_id`=" . $order_route_id . ")";
        }
        if ($zerovalue != false) {
            $where_a[] = '(`ro`.`rate`="0.00" ")';
        }
        if ($this->my->gid != 25) {
            if (isset($this->my->routes_warehouses)) {
                $where_a[] = "`r`.`warehouse_id` IN (" . implode(', ', $this->my->routes_warehouses) . ")";
            }
        }

        $where = '';

        if (sizeof($where_a) > 0) {
            $where = ' WHERE ' . implode(' AND ', $where_a) . ' ';
        }

        $query = "SELECT 
            COUNT(`r`.`id`)
        FROM `jos_vm_routes` AS `r`
        INNER JOIN `tbl_driver_option` AS `d` ON `d`.`id`=`r`.`driver_id`";
        if ($warehouse != false) {
            $query .= " INNER JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_id`=`r`.`warehouse_id`";
        }
        $query .= " LEFT JOIN `jos_vm_routes_orders` AS `ro` ON `ro`.`route_id`=`r`.`id` 
        " . $where . "
        GROUP BY `r`.`id`
        ORDER BY `r`.`id`
        ";

        $this->database->setQuery($query);

        $res = $this->database->query();
        $total = $this->database->getNumRows($res);
        f($query, $res);
        require_once $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php';

        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `r`.*,
            `d`.`service_name`,
            `w`.`warehouse_name`
        FROM `jos_vm_routes` AS `r`
        INNER JOIN `tbl_driver_option` AS `d` ON `d`.`id`=`r`.`driver_id`";
        if ($warehouse != false) {
            $query .= " INNER";
        } else {
            $query .= " LEFT";
        }
        $query .= " JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_id`=`r`.`warehouse_id`
        LEFT JOIN `jos_vm_routes_orders` AS `ro` ON `ro`.`route_id`=`r`.`id` 
        " . $where . "
        GROUP BY `r`.`id`
        ORDER BY `r`.`id` DESC
        ";
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        f($query, $rows);
        if ($rows) {
            foreach ($rows as $key => $row) {
                $done = false;

                $query = "SELECT 
                    COUNT(`ro`.`id`) AS 'count'
                FROM `jos_vm_routes_orders` AS `ro` 
                WHERE `ro`.`route_id`=" . $row->id . " AND `ro`.`status`='1'";

                $this->database->setQuery($query);
                $this->database->loadObject($done);

                $remaining = false;

                $query = "SELECT 
                    COUNT(`ro`.`id`) AS 'count'
                FROM `jos_vm_routes_orders` AS `ro` 
                WHERE `ro`.`route_id`=" . $row->id . " AND `ro`.`status`='0'";

                $this->database->setQuery($query);
                $this->database->loadObject($remaining);

                $investigation = false;

                $query = "SELECT 
                    COUNT(`ro`.`id`) AS 'count'
                FROM `jos_vm_routes_orders` AS `ro` 
                WHERE `ro`.`route_id`=" . $row->id . " AND `ro`.`status`='2'";

                $this->database->setQuery($query);
                $this->database->loadObject($investigation);

                $query = "SELECT 
                    COUNT(`ro`.`id`) AS 'count'
                FROM `jos_vm_routes_orders` AS `ro` 
                WHERE `ro`.`route_id`=" . $row->id . " AND `ro`.`status`='3'";

                $this->database->setQuery($query);
                $this->database->loadObject($questionable);

                $rows[$key]->done_count = $done->count;
                $rows[$key]->remaining_count = $remaining->count;
                $rows[$key]->investigation_count = $investigation->count;
                $rows[$key]->questionable_count = $questionable->count;
            }
        } else {
            $rows = array();
        }

        $query = "SELECT 
            `w`.`warehouse_id`,
            `w`.`warehouse_name`
        FROM `jos_vm_warehouse` AS `w`
        ";

        if ($this->my->gid != 25) {
            if (isset($this->my->routes_warehouses)) {
                $query .= " WHERE `w`.`warehouse_id` IN (" . implode(', ', $this->my->routes_warehouses) . ") ";
            }
        }

        $query .= " ORDER BY `w`.`warehouse_name`
        ";

        $this->database->setQuery($query);
        $warehouses = $this->database->loadObjectList();

        $query = "SELECT 
            `d`.`id`,
            CONCAT(`d`.`driver_option_type`, ' ', `d`.`service_name`) AS `name`
        FROM `tbl_driver_option` AS `d`
        WHERE
            `d`.`warehouse_id`=" . $warehouse . "
        ORDER BY 
            `d`.`service_name`
        ";

        $this->database->setQuery($query);
        $drivers = $this->database->loadObjectList();

//        echo '<pre>';
//            print_r($rows);
//        echo '</pre>';

        HTML_directions::default_list($this->option, $this->my, $warehouse, $driver, $show_completed, $order_route_id, $rows, $warehouses, $drivers, $pageNav, $date_from, $date_to, $only_get_unpublished);
    }

    function add_to_history($msg)
    {
        $query = "INSERT INTO `jos_vm_routes_history`
            (
                `id_route`,
                `text`,
                `username`,
                `datetime`
            )
            VALUES (
                " . $this->id . ",
                '" . $this->database->getEscaped($msg) . "',
                '" . $this->database->getEscaped($this->my->username) . "',
                '" . date('Y-m-d G:i:s') . "'
            )";
        $this->database->setQuery($query);
        $this->database->query();
    }

}
