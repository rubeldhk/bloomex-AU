<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/deliveries/fedex/config.php");

class GetOrderInformation {

    var $_deliverydate = null;
    var $_PersonName = null;
    var $_CompanyName = null;
    var $_PhoneNumber = null;
    var $_StreetLines1 = null;
    var $_StreetLines2 = null;
    var $_City = null;
    var $_StateOrProvinceCode = null;
    var $_PostalCode = null;
    var $_CountryCode = null;
    var $_Residential = null;
    var $_id = null;
    var $_WH = null;

    function __construct($id, $bt = false) {
        $cfg = new JConfig();
        $dsn = "mysql:host=$cfg->host;dbname=$cfg->db;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, $cfg->user, $cfg->password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }

        $stmt = $pdo->prepare("SELECT * "
                . "FROM jos_vm_orders o "
                . "INNER JOIN jos_vm_order_user_info ui on  ui.order_id = o.order_id AND address_type = 'ST' "
                . "WHERE o.order_id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        $this->_PersonName = $order->title . " " . $order->first_name . " " . $order->middle_name . " " . $order->last_name;
        $this->_CompanyName = $order->company;
        $this->_PhoneNumber = ($order->phone_1) ? $order->phone_1 : $order->phone_2;
        $streetlines = "";
        $this->_Suite = $order->suite;
        $this->_StreetNumber = $order->street_number;
        //$order->street_name =utf8_encode(urldecode(str_replace('&#039;','%27',$order->street_name)));
        $this->_StreetName = $order->street_name;
        $this->_Suite = ($this->_Suite) ? ' ' . $this->_Suite : '';

        $this->explode_address($this->_StreetNumber . ' ' . $order->street_name . $this->_Suite);

        $this->_City = $order->city;

        $this->_StateOrProvinceCode = $order->state;
        $this->_PostalCode = $order->zip;
        $this->_CountryCode = "AU";
        $this->_Residential = "true";
        $this->_deliverydate = $order->ddate;
        $this->_id = $order->order_id;
        $this->_WH = $order->warehouse;

        $pdo = null;
    }

    function explode_address($str) {

        if (strlen($str) < 35) {
            $this->_StreetLines1 = $str;
        } else {
            $b = explode(' ', $str);
            $this->_StreetLines1 = '';
            $this->_StreetLines2 = '';
            foreach ($b as $m) {
                $new_str = $this->_StreetLines1 . $m;
                if (strlen($new_str) < 35) {
                    $this->_StreetLines1 .= ' ' . $m;
                } else {
                    $this->_StreetLines2 .= ' ' . $m;
                }
            }
        }
    }

    function update_score($score, $order_id) {
        $cfg = new JConfig();
        $link = mysqli_connect($cfg->host, $cfg->user, $cfg->password);
        if (!$link) {
            die('success[--1--]Could not connect: ' . mysql_error());
        }

        mysqli_select_db($link,$cfg->db);
        $query_select = "SELECT order_id FROM tbl_address_validation WHERE order_id=" . $order_id;
        $result_select = mysqli_query($link,$query_select);
        $row_select = mysqli_num_rows($result_select);

        if ($row_select > 0) {
            $query = "UPDATE  tbl_address_validation SET "
                    . "`score` = '" . $score . "'"
                    . " WHERE  order_id='" . $order_id . "'";
            $result = mysqli_query($link,$query);
        } else {
            $query = "INSERT INTO tbl_address_validation (score,order_id)  VALUES ('" . $score . "','" . $order_id . "')";
            $result = mysqli_query($link,$query);
        }

        if (!$result) {
            echo $q . "<br/>";
            die('success[--1--]' . __FILE__ . ":" . __LINE__ . 'Invalid query: ' . mysql_error());
        }
        mysqli_close($link);
    }

    function update_order_information($new_value, $type, $order_id) {
        $cfg = new JConfig();
        $dsn = "mysql:host=$cfg->host;dbname=$cfg->db;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, $cfg->user, $cfg->password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
        $allowed_fields = array("title", "first_name", "middle_name", "last_name", "company", "phone_1", "phone_2", "suite", "street_number", "street_name", "city", "state", "zip");
        if (!in_array($type, $allowed_fields)) {
            return 'success[--1--] not allowed field to update: ' . $type;
        }
        $stmt = $pdo->prepare("UPDATE  jos_vm_order_user_info SET "
                . "$type = ?"
                . " WHERE address_type='ST' AND order_id= ?");
        $stmt->execute([$new_value, $order_id]);


        $history = "Update " . $type . " from AV system";

        $stmt = $pdo->prepare("Select order_status from jos_vm_orders where order_id = ? ");
        $stmt->execute([$order_id]);
        $code = $stmt->fetchColumn();

        $stmt = $pdo->prepare("INSERT INTO jos_vm_order_history(order_id,date_added,order_status_code,comments, user_name)
                    VALUES (?, NOW(),?,?, 'AV Tool')");
        $stmt->execute([$order_id, $code, $history]);

        $pdo = null;
        return 'success';
    }


}
