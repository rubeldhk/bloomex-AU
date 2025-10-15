<?php

/*
  Bloomexorder.php :class for supporting bloomex orders in fedex
 */
require_once('../warehouses.php');

class BloomexOrder {

    var $_deliverydate = null;
    var $_PersonName = null;
    var $_CompanyName = null;
    var $_PhoneNumber = null;
    var $_CustomerComments = null;
    var $_StreetLines1 = null;
    var $_StreetLines2 = null;
    var $_City = null;
    var $_StateOrProvinceCode = null;
    var $_PostalCode = null;
    var $_CountryCode = null;
    var $_Residential = null;
    var $_id = null;
    var $_Suite = null;
    var $_StreetNumber = null;
    var $_StreetName = null;
    var $_WH = null;

    function __construct() {
        require $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
        $this->link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
        if (!$this->link) {
            die('fail[--1--]Could not connect: ' . mysqli_error($this->link));
        }
        mysqli_select_db($this->link, $mosConfig_db);

        $this->status_sent = 27;
        $this->status_cancel = 28;
    }

    function changeStateCode($state) {
        $stateArr = [
            'VI' => 'VIC',
            'NT' => 'NT',
            'WA' => 'WA',
            'AT' => 'ACT',
            'QL' => 'QLD',
            'TA' => 'TAS',
            'SA' => 'SA',
            'NW' => 'NSW'
        ];
        return $stateArr[$state];
    }



    function getsenderonstartrack() {

        $warehouse_obj = new warehouses($this->_WH);
        $Warehouse = $warehouse_obj->warehouse;
        $Warehouse['username'] = '9f54f89b-42f4-4748-9c13-e82f62172f92';
        $Warehouse['Password'] = 'qI4Jfye9sSMafMvrRWYp';

        switch ($this->_WH) {
            case "WH12":
                $Warehouse['customerNumber'] = '10175979';
                break;
            case "WH16":
                $Warehouse['customerNumber'] = '10176169';
                break;
            case "WH14":
                $Warehouse['customerNumber'] = '10176170';
                break;
            case "p01":
                $Warehouse['customerNumber'] = '10176171';
                break;
            case "WH15":
                $Warehouse['customerNumber'] = '10176172';
                break;
            default :
                $Warehouse['customerNumber'] = '10175979';
        }


        return $Warehouse;
    }

    function get_warehouse_list() {
        $q = "SELECT warehouse_code,warehouse_name FROM jos_vm_warehouse where published=1";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        if (mysqli_num_rows($result)) {
            $select = "<select class='choose_warehouse' name='choose_warehouse'>";
            while ($row = mysqli_fetch_assoc($result)) {
                $select .= '<option value="' . $row['warehouse_code'] . '">' . $row['warehouse_name'] . '</option>';
            }
            $select .= "</select>";
            return $select;
        } else {
            return false;
        }
    }

    function checkwarehousesbyorder($orders, $warehouse) {
        $in_orders = rtrim($orders, ',');
        $q = "SELECT order_id FROM jos_vm_orders WHERE order_id in ($in_orders) and warehouse!='$warehouse'";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        if (mysqli_num_rows($result)) {
            $orders = '';
            while ($row = mysqli_fetch_assoc($result)) {
                $orders .= $row['order_id'] . "<br>";
            }
            return $orders;
        } else {
            return false;
        }
    }

    function get_shipments($orders) {
        $in_orders = rtrim($orders, ',');
        $q = "SELECT shipment_id,order_id FROM jos_order_startrack_json WHERE order_id in ($in_orders)";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        if (mysqli_num_rows($result)) {
            $shipments = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $shipments[$row['shipment_id']] = $row['order_id'];
            }
            return $shipments;
        } else {
            return false;
        }
    }

    function get_shipment_id($order_id) {
        $q = "SELECT shipment_id,json_label,json_manifest,manifest_id,warehouse FROM jos_order_startrack_json WHERE order_id='$order_id'";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        if (mysqli_num_rows($result)) {
            $shipping = mysqli_fetch_assoc($result);
            return $shipping;
        } else {
            return false;
        }
    }

    function attachlabel($order_id, $label_url, $sender) {

        $query = "SELECT id FROM `jos_order_startrack_json` WHERE label_created='1' and `order_id`=" . $order_id;
        $result = mysqli_query($this->link, $query);
        if ($result->num_rows == 0) {
            $mysqlDatetime = date("Y-m-d G:i:s");
            $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                      VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'Print Label: " . htmlspecialchars($label_url) . "','" . $sender . "')";
            $result = mysqli_query($this->link, $query);
            if (!$result) {
                echo $query . "<br/>";
                die('Invalid query: ' . mysqli_error($this->link));
            }
        }
        $query = "UPDATE `jos_order_startrack_json` SET label_created='1'  WHERE `order_id`=" . $order_id;
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function addlabeljson($order_id, $curl_response_label) {
        $query = "UPDATE `jos_order_startrack_json` SET json_label='" . $curl_response_label . "'  WHERE `order_id`=" . $order_id;
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function addshipment($order_id, $shipment_id, $tracking_pin, $sender, $response_json) {
        $query = "UPDATE `jos_vm_orders` SET `order_status`='" . $this->status_sent . "'
                WHERE `order_id`=" . $order_id . "";
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                            VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'Tracking ID: " . $tracking_pin . "','" . $sender . "')";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        $query = "INSERT INTO `jos_vm_orders_deliveries`
                    (
                        `order_id`,
                        `delivery_type`,
                        `dateadd`,
                        `pin`,
                        `shipment_id`,
                        `active`
                    ) 
                    VALUES (
                        " . $order_id . ",
                        '13',
                        '" . $mysqlDatetime . "',
                        '" . $tracking_pin . "',
                        '" . $shipment_id . "',
                        '1'
                    )";
        $result = mysqli_query($this->link, $query);

        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        $query = "INSERT INTO `jos_order_startrack_json`
                (
                    `order_id`, 
                    `json_shipment`,
                    `shipment_id`,
                    `date_added`
                )
                VALUES
                (
                    " . $order_id . ",
                    '" . mysqli_real_escape_string($this->link, $response_json) . "', 
                   '" . $shipment_id . "',
                    NOW()
                )
               ";


        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function GetStateCode($state) {
        $q = "SELECT state_3_code FROM jos_vm_state WHERE country_id=13 and state_2_code = '$state'";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        if (mysqli_num_rows($result)) {
            $row = mysqli_fetch_assoc($result);
            $state_code = $row['state_3_code'];
            return $state_code;
        } else {
            return false;
        }
    }

    function GetOrderDetails($id) {

        $q = "SELECT order_id,user_id,ddate, warehouse,customer_comments FROM jos_vm_orders WHERE order_id='$id'";
        $result = mysqli_query($this->link, $q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        $order = mysqli_fetch_assoc($result);

        $q = "SELECT * from jos_vm_order_user_info WHERE order_id = $id ORDER BY address_type ASC LIMIT 2";
        $result = mysqli_query($this->link, $q);

        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        while ($row = mysqli_fetch_object($result)) {
            $oshipping[] = $row;
        }
        $shipping = $oshipping[1];

        $this->_PersonName = utf8_encode(htmlspecialchars($shipping->first_name . ' ' . $shipping->last_name));
        $this->_CompanyName = $shipping->company ? utf8_encode(htmlspecialchars($shipping->company)) : 'Company Name';
        $this->_PhoneNumber = ($shipping->phone_1) ? $shipping->phone_1 : $shipping->phone_2;

        $this->_Suite = $shipping->suite;
        $this->_StreetNumber = $shipping->street_number;
        $this->_StreetName = $shipping->street_name;
        $streetlines = '';
        if ($shipping->suite) {
            $streetlines = $shipping->suite . '-';
        }
        if ($shipping->street_number) {
            $streetlines .= utf8_encode(htmlspecialchars($shipping->street_number)) . ' ';
        }
        if ($shipping->street_name) {
            $streetlines .= utf8_encode(htmlspecialchars($shipping->street_name));
        }
        $this->_StreetLines1 = ($streetlines) ? $streetlines : utf8_encode(htmlspecialchars($shipping->address_1));
        $this->_StreetLines2 = ($streetlines) ? '' : utf8_encode(htmlspecialchars($shipping->address_2));

        $this->_City = utf8_encode(htmlspecialchars($shipping->city));
        $this->_StateOrProvinceCode = $this->changeStateCode($shipping->state);
        $this->_PostalCode = $shipping->zip;
        $this->_CountryCode = "AUS";
        $this->_Residential = "true";
        $date_elements = explode("-", $order['ddate']);

        $this->_deliverydate = date("Y-m-d", mktime(0, 0, 0, $date_elements[1], $date_elements[0], $date_elements[2]));
        $this->_id = $order['order_id'];
        $this->_CustomerComments = $order['customer_comments'];
        $this->_WH = $order['warehouse'];
    }

    function deleteshipment($order_id, $sender, $shipment_id) {


        $query = "UPDATE `jos_vm_orders` SET `order_status`='" . $this->status_cancel . "'
                WHERE `order_id`=" . $order_id . "";
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                            VALUES ('$order_id', '" . $this->status_cancel . "',  '$mysqlDatetime', 'Cancel Australia Post Shipment (" . $shipment_id . ")','" . $sender . "')";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        $query = "DELETE  FROM `jos_vm_orders_deliveries`
                  WHERE order_id='$order_id'";
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }

        $query = "DELETE  FROM `jos_order_startrack_json`
        WHERE order_id='$order_id'";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function addmanifest($order_id, $sender, $manifest, $warehouse, $curl_response) {
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                                        VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'Manifest : " . htmlspecialchars($manifest) . "','" . $sender . "')";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        $query = "UPDATE `jos_order_startrack_json` SET json_manifest='" . $curl_response . "',manifest_id='" . htmlspecialchars($manifest) . "',warehouse='" . $warehouse . "'  WHERE `order_id`=" . $order_id;
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function __destruct() {
        $this->link->close();
    }

}

