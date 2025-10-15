<?php

include "../configuration.php";
$db = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);
if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}



if (isset($_REQUEST['orders'])) {
    $orders_str = $_REQUEST['orders'];
    $orders_array = explode(",", $orders_str);
    $rows = array();
    foreach ($orders_array as $order) {
        $sql = "SELECT CONCAT(`first_name`,' ',`last_name`) as name,
            `company`,`suite`,CONCAT(`street_number`,' ',`street_name`) as address_line,`city`,
            `state`,`zip`,
            `phone_1`
FROM `jos_vm_order_user_info`
WHERE `order_id` =$order
AND `address_type` LIKE 'ST'";
        if (!$result = $db->query($sql)) {
            die('There was an error running the select query [' . $db->error . ']');
        }
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                $row['items']='1';
                $row['order_id']=$order;
                $rows[] = $row;
            }
        }
    }

   showExcell($rows, 'PeoplePost Orders');
}

function showExcell($rows, $title) {

    $data = "";
    $sep = "\t"; //tabbed character
    if (count($rows) > 0) {
        //firstline is title + date



        $fields = array("Contact", "Company Name", "Unit Number", "Address Line", "Suburb", "State", "Post code", "Phone", "Items", "Internal Reference");

        $columns = count($fields);
        // Put the name of all fields to $out.
        for ($i = 0; $i < $columns; $i++) {
            $data .= $fields[$i] . $sep;
        }
        $data .= "\n";

        for ($k = 0; $k < count($rows); $k++) {
            $row = $rows[$k];
            $line = '';

            foreach ($row as $value) {
                $value = str_replace('"', '""', $value);
                $line .= '"' . $value . '"' . "\t";
            }
            $data .= trim($line) . "\n";
        }

        $data = str_replace("\r", "", $data);
    } else {
        $data = "\n(0) Records Found!\n";
    }

    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=jeports.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Lacation: excel.htm?id=yes");
    print $data;
    die();
}
