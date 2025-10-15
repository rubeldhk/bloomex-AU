<?php

/*
  Bloomexorder.php :class for supporting bloomex orders in fedex
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/scripts/deliveries/warehouses.php');

class BloomexOrder {

    var $_deliverydate = null;
    var $_PersonName = null;
    var $_PersonEmail = null;
    var $_CompanyName = null;
    var $_PhoneNumber = null;
    var $_CustomerComments = null;
    var $_StreetLines1 = null;
    var $_StreetLines2 = null;
    var $_District = null;
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
    var $_hasPerishableProduct = false;
    function __construct() {
        require $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
        $this->link = mysqli_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
        if (!$this->link) {
            die('fail[--1--]Could not connect: ' . mysqli_error($this->link));
        }
        mysqli_select_db($this->link, $mosConfig_db);

        $this->status_sent = $mosConfig_status_sent_nzpost;
        $this->status_cancel = $mosConfig_status_cancel_nzpost;
    }


    function getsender() {

        $warehouse_obj = new warehouses($this->_WH);
        $Warehouse = $warehouse_obj->warehouse;
        switch ($this->_WH) {
            default :

                $Warehouse['clientId'] = '8b62d279609741118ff4caeb304ffdb1';
                $Warehouse['clientSecret'] = '449F244ff0b345A68E5c575E31A6F6A2';

        }
        return $Warehouse;
    }

    function get_warehouse_list() {
        $q = "SELECT warehouse_code,warehouse_name FROM jos_vm_warehouse where published=1 and warehouse_code = 'bcz'";
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
        $q = "SELECT shipment_id,order_id FROM jos_order_nzpost_json WHERE order_id in ($in_orders)";
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
        $q = "SELECT shipment_id,json_manifest,manifest_id,warehouse FROM jos_order_nzpost_json WHERE order_id='$order_id'";
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

    function addshipment($order_id, $shipment_id, $sender, $response_json) {
        $query = "UPDATE `jos_vm_orders` SET `order_status`='" . $this->status_sent . "'
                WHERE `order_id`=" . $order_id . "";
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                            VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'The request for tracking number was sent ','" . $sender . "')";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }


        $query = "INSERT INTO `jos_order_nzpost_json`
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


    function update_order_information($new_value, $type_db, $order_id) {
        $query = "UPDATE `jos_vm_order_user_info` SET ".$type_db."='" . $new_value . "'
                WHERE `order_id`=" . $order_id . " and address_type='ST' ";
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        die('success');
    }

    function addshipmentTrackingNumber($order_id, $trackingNumber,$shipment_id, $sender) {
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                            VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'Tracking Number: " . $trackingNumber . " ','" . $sender . "')";

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
                        '10',
                        '" . $mysqlDatetime . "',
                        '" . $trackingNumber . "',
                        '" . $shipment_id . "',
                        '1'
                    )";
        $result = mysqli_query($this->link, $query);

        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
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

        $this->_PersonName = htmlspecialchars($shipping->first_name . ' ' . $shipping->last_name);
        $this->_CompanyName = $shipping->company ? htmlspecialchars($shipping->company) : '';
        $this->_PhoneNumber = ($shipping->phone_1) ? $shipping->phone_1 : $shipping->phone_2;
        $this->_PersonEmail = $shipping->user_email;

        $this->_Suite = $shipping->suite;
        $this->_StreetNumber = $shipping->street_number;
        $this->_StreetName = $shipping->street_name;
        $streetlines = '';
        if ($shipping->suite) {
            $streetlines = $shipping->suite . '/';
        }
        if ($shipping->street_number) {
            $streetlines .= htmlspecialchars(str_replace(' ','',$shipping->street_number)) . ' ';
        }
        if ($shipping->street_name) {
            $streetlines .= htmlspecialchars($shipping->street_name);
        }
        $this->_StreetLines1 = ($streetlines) ? $streetlines : htmlspecialchars($shipping->address_1);
        $this->_StreetLines2 = ($streetlines) ? '' : htmlspecialchars($shipping->address_2);

        $this->_City = htmlspecialchars($shipping->city);
        $this->_District = htmlspecialchars($shipping->district);
        $this->_StateOrProvinceCode = $shipping->state;
        $this->_PostalCode = $shipping->zip;
        $this->_CountryCode = "NZ";
        $this->_Residential = "true";
        $this->_deliverydate = date("d/m/y",strtotime($order['ddate']));
        $this->_id = $order['order_id'];
        $this->_CustomerComments = $order['customer_comments'];
        $this->_WH = $order['warehouse'];


        $q = "SELECT * FROM `jos_vm_order_item` as i 
        join jos_vm_product as p on i.order_item_sku = p.product_sku 
        join jos_vm_product_options as o on o.product_id = p.product_id and o.product_type != 2 
         WHERE i.order_id = $id";
        $result = mysqli_query($this->link, $q);

        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        if(mysqli_num_rows($result) > 0) {
            $this->_hasPerishableProduct = true;
        }

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
                                            VALUES ('$order_id', '" . $this->status_cancel . "',  '$mysqlDatetime', 'Cancel NzPost Consignment ID (" . $shipment_id . ")','" . $sender . "')";

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

        $query = "DELETE  FROM `jos_order_nzpost_json`
        WHERE order_id='$order_id'";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
    }

    function addmanifest($order_id, $sender, $job_id,$job_number, $warehouse, $curl_response) {
        $mysqlDatetime = date("Y-m-d G:i:s");
        $query = "INSERT INTO jos_vm_order_history(order_id,order_status_code, date_added,comments,user_name) 
                                                        VALUES ('$order_id', '" . $this->status_sent . "', '$mysqlDatetime', 'Manifest Id : " . htmlspecialchars($job_id) . " Manifest Number : " . htmlspecialchars($job_number) . "','" . $sender . "')";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            echo $query . "<br/>";
            die('Invalid query: ' . mysqli_error($this->link));
        }
        $query = "UPDATE `jos_order_nzpost_json` SET json_manifest='" . $curl_response . "',manifest_id='" . htmlspecialchars($job_id) . "',warehouse='" . $warehouse . "'  WHERE `order_id`=" . $order_id;
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

?>
