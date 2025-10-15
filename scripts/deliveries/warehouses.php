<?php

Class warehouses {

    public $warehouse = array();

    public function __construct($warehouse_code) {
        include $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

        $mysqli_wh = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
        $mysqli_wh->set_charset('utf8');

        $query = "SELECT *
        FROM `jos_vm_warehouse` 
        WHERE 
           `warehouse_code`='" . $mysqli_wh->real_escape_string($warehouse_code) . "'
        ";

        $result = $mysqli_wh->query($query);

        if ($result->num_rows > 0) {
            $wh_obj = $result->fetch_object();
            if ($wh_obj) {
                $this->warehouse['PersonName'] = $wh_obj->person_name;
                $this->warehouse['CompanyName'] = $wh_obj->company_name;
                $this->warehouse['StreetLines1'] = $wh_obj->street_number;
                $this->warehouse['StreetLines2'] = $wh_obj->street_name;
                $this->warehouse['City'] = $wh_obj->city;
                $this->warehouse['StateOrProvinceCode'] = $wh_obj->state;
                $this->warehouse['CountryCode'] = 'AUS';
                $this->warehouse['PostalCode'] = $wh_obj->postal_code;
                $this->warehouse['District'] = $wh_obj->district;
                $this->warehouse['PhoneNumber'] = $wh_obj->phone;
                $this->warehouse['StreetNumber'] = $wh_obj->street_number;
                $this->warehouse['StreetName'] = $wh_obj->street_name;
                $this->warehouse['WarehouseName'] = $wh_obj->warehouse_name;
                $this->warehouse['WarehouseEmail'] = $wh_obj->warehouse_email;
            }
        }

        $mysqli_wh->close();

        return $this->warehouse;
    }

}

?>
