
<?php

/*
  Bloomexorder.php :class for supporting bloomex orders in fedex
 */

class BloomexOrder {

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
    var $_CustomerComments = null;

    function BloomexOrder($id) {
        /*
        require_once 'configuration.php';
        $cfg = new FastWayCfg();
        
        $link = mysql_connect($cfg->host, $cfg->user, $cfg->pw);
        if (!$link) {
            die('fail[--1--]Could not connect: ' . mysql_error());
        }
        mysql_select_db($cfg->db, $link);
        */
        require '../configuration.php';
     require_once "mysql.php";   
        $link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
        mysql_select_db($mosConfig_db, $link);
        
        $q = "SELECT order_id,user_id,ddate,customer_comments FROM jos_vm_orders WHERE order_id='$id'";
        $result = mysql_query($q);
        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query: ' . mysql_error());
        }
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $order = $row;
        }
        $date_elements = explode("-", $order['ddate']);
        $q = "SELECT * from jos_vm_order_user_info WHERE order_id = $id ORDER BY address_type ASC LIMIT 2";
        $result = mysql_query($q);

        if (!$result) {
            echo $q . "<br/>";
            die('Invalid query : ' . mysql_error() . ' File bloomexorder.php');
        }
        while ($row = mysql_fetch_object($result)) {
            $oshipping[] = $row;
        }

        $shipping = $oshipping[1];
        
        $this->_PersonName = html_entity_decode($shipping->title . ' ' . $shipping->first_name . ' ' . $shipping->middle_name . ' ' . $shipping->last_name);
        $this->_CompanyName = html_entity_decode($shipping->company);
        $this->_PhoneNumber = ($shipping->phone_1) ? $shipping->phone_1 : $shipping->phone_2;

        $this->_City = html_entity_decode($shipping->city);
        $this->_StateOrProvinceCode = $shipping->state;
        $this->_PostalCode = $shipping->zip;
        
        //$this->_CountryCode = "CA";
        $this->_CountryCode = "11";
        $this->_Residential = "true";

        $this->_deliverydate = date("Y-m-d", mktime(0, 0, 0, $date_elements[1], $date_elements[0], $date_elements[2]));
        $this->_id = $order['order_id'];
        $this->_Suite = $shipping->suite;
        $this->_StreetNumber = $shipping->street_number;
        $this->_StreetName = $shipping->street_name;
        if ($shipping->suite) {
            $streetlines = $shipping->suite . '-';
        }
        if ($shipping->street_number) {
            $streetlines .=html_entity_decode($shipping->street_number) . ' ';
        }
        if ($shipping->street_name) {
            $streetlines .=html_entity_decode($shipping->street_name);
        }
        $this->_StreetLines1 = ($streetlines) ? $streetlines : html_entity_decode($shipping->address_1);
        $this->_StreetLines2 = ($streetlines) ? '' : html_entity_decode($shipping->address_2);
        $this->_CustomerComments = $order['customer_comments'];
    }

    function printorder() {
        echo "Person Nmae               " . $this->_PersonName . "<BR/>";
        echo "Company name              " . $this->_CompanyName . "<BR/>";
        echo "phone number              " . $this->_PhoneNumber . "<BR/>";
        echo "Street lines              " . $this->_StreetLines . "<BR/>";
        echo "City                      " . $this->_City . "<BR/>";
        echo "State or province code    " . $this->_StateOrProvinceCode . "<BR/>";
        echo "Postal code               " . $this->_PostalCode . "<BR/>";
        echo "Country code              " . $this->_CountryCode . "<BR/>";
        echo "Residental code           " . $this->_Residential . "<BR/>";
    }

}

?>