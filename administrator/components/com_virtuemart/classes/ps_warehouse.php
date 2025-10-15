<?php
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 *
 * @version $Id: ps_tax.php,v 1.9.2.3 2006/03/14 18:42:11 soeren_nb Exp $
 * @package VirtueMart
 * @subpackage classes
 * @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
class ps_warehouse
{
    var $classname = "ps_warehouse";

    /**
     * Validates the input values before adding an item
     *
     * @param arry $d The _REQUEST array
     * @return boolean True on success, false on failure
     */
    function validate_add(&$d)
    {
        if (substr($_SERVER['HTTP_HOST'], 0, 3) == "adm") {
            mosRedirect('/administrator/index2.php?pshop_mode=admin&page=warehouse.warehouse_list&option=com_virtuemart', 'You are not allowed to edit this content directly on live server');
        }
        global $vmLogger;
        $valid = true;
        $db = new ps_DB;
        if (!$d["warehouse_name"]) {
            $vmLogger->err('You must enter a warehouse name. ');
            $valid = False;
        }
        if (!$d["warehouse_email"]) {
            $vmLogger->err('You must enter a warehouse email. ');
            $valid = False;
        }

        if (!$d["warehouse_code"]) {
            $vmLogger->err('You must enter a proirity code.');
            $valid = False;
        }
        //$d["warehouse_rate"] = str_replace( ',', '.', $d['tax_rate']);

        return $valid;
    }

    /**
     * Validates the input values before updating an item
     *
     * @param arry $d The _REQUEST array
     * @return boolean True on success, false on failure
     */
    function validate_update(&$d)
    {
        if (substr($_SERVER['HTTP_HOST'], 0, 3) == "adm") {
            mosRedirect('/administrator/index2.php?pshop_mode=admin&page=warehouse.warehouse_list&option=com_virtuemart', 'You are not allowed to edit this content directly on live server');
        }
        global $vmLogger;

        $db = new ps_DB;


        if (!$d["warehouse_name"]) {
            $vmLogger->err('You must enter a warehouse name.');
            return False;
        }
        if (!$d["timezone"]) {
            $vmLogger->err('You must enter a timezone.');
            return False;
        }
        if (!$d["warehouse_code"]) {
            $vmLogger->err('You must enter a warehouse code.');
            return False;
        }
        if (!$d["warehouse_email"]) {
            $vmLogger->err('You must enter a warehouse email. ');
            $valid = False;
        }


        return True;
    }

    /**
     * Validates the input values before deleting an item
     *
     * @param arry $d The _REQUEST array
     * @return boolean True on success, false on failure
     */
    function validate_delete($d)
    {
        global $vmLogger;

        if (!$d["warehouse_id"]) {
            $vmLogger->err('Please select a warehouse to delete.');
            return False;
        }

        return True;

    }

    /**
     * Creates a new tax record
     * @param arry $d The _REQUEST array
     * @return boolean True on success, false on failure
     * @author pablo
     *
     */
    function add(&$d)
    {
        global $database, $vmLogger;;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        if (!$this->validate_add($d)) {
            $vmLogger->err("validation error");
            return false;
        }

        $q = "INSERT INTO jos_vm_warehouse (vendor_id, warehouse_name, warehouse_code, warehouse_email,person_name,company_name, list_warehouse,street_number,street_name,district,postal_code,state,published,city,phone,timezone) VALUES (";
        $q .= "'$ps_vendor_id','";
        $q .= $d["warehouse_name"] . "','";
        $q .= $d["warehouse_code"] . "','";
        $q .= $d["warehouse_email"] . "','";
        $q .= $d["person_name"] . "','";
        $q .= $d["company_name"] . "','";
        $q .= $d["list_warehouse"] . "','";
        $q .= $d["street_number"] . "','";
        $q .= $d["street_name"] . "','";
        $q .= $d["district"] . "','";
        $q .= $d["postal_code"] . "','";
        $q .= $d["state"] . "','";
        $q .= $d["published"] . "','";
        $q .= $d["city"] . "','";
        $q .= $d["phone"] . "','";
        $q .= $d["timezone"] . "')";
        $database->setQuery($q);

        if(!$database->query()){
            $vmLogger->err($database->getErrorMsg());
            return false;

        }
        $wh_id = $database->insertid();

        $query = "INSERT INTO `jos_vm_warehouse_info`
            (
                `warehouse_id`,
                `person_name`,
                `warehouse_type`,
                `company_name`,
                `street_number`,
                `street_name`,
                `city`,
                `state`,
                `country`,
                `zip`,
                `phone`,
                `lat`,
                `lng`
            )
            VALUES (
                " . $wh_id . ",
                '" . $database->getEscaped($d['person_name']) . "',
                '" . $database->getEscaped($d['warehouse_type']) . "',
                '" . $database->getEscaped($d['company_name']) . "',
                '" . $database->getEscaped($d['street_number']) . "',
                '" . $database->getEscaped($d['street_name']) . "',
                '" . $database->getEscaped($d['city']) . "',
                '" . $database->getEscaped($d['state']) . "',
                'AU',
                '" . $database->getEscaped($d['postal_code']) . "',
                '" . $database->getEscaped($d['phone']) . "',
                '" . $database->getEscaped($d['lat']) . "',
                '" . $database->getEscaped($d['lng']) . "'
            )";

        $database->setQuery($query);
        if ($database->query()) {
            return true;
        } else {
            $vmLogger->err($database->getErrorMsg());
            return false;
        }
    }

    /**
     * Updates a tax record
     * @param arry $d The _REQUEST array
     * @return boolean True on success, false on failure
     * @author pablo
     *
     */
    function update(&$d)
    {
        global $database;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        if (!$this->validate_update($d)) {
            return False;
        }
        $q = "UPDATE `jos_vm_warehouse` SET ";
        $q .= "`warehouse_name`='" . $d["warehouse_name"];
        $q .= "',`warehouse_code`='" . $d["warehouse_code"];
        $q .= "',`warehouse_email`='" . $d["warehouse_email"];
        $q .= "',`company_name`='" . $d["company_name"];
        $q .= "',`person_name`='" . $d["person_name"];
        $q .= "',`list_warehouse`='" . $d["list_warehouse"];
        $q .= "',`street_number`='" . $d["street_number"];
        $q .= "',`street_name`='" . $d["street_name"];
        $q .= "',`district`='" . $d["district"];
        $q .= "',`postal_code`='" . $d["postal_code"];
        $q .= "',`city`='" . $d["city"];
        $q .= "',`timezone`='" . $d["timezone"];
        $q .= "',`phone`='" . $d["phone"];
        $q .= "',`state`='" . $d["state"];
        $q .= "',`published`='" . $d["published"];
        $q .= "' WHERE `warehouse_id`='" . $d["warehouse_id"] . "'";
        $q .= " AND `vendor_id`='$ps_vendor_id'";
        $database->setQuery($q);
        $result = $database->query();
        $query = "SELECT 
                `wh_i`.`id`
            FROM `jos_vm_warehouse_info` AS `wh_i`
            WHERE `wh_i`.`warehouse_id`=" . $d['warehouse_id'] . "";

        $database->setQuery($query);
        $wh_obj = false;
        $database->loadObject($wh_obj);
        if ($wh_obj) {
            $query = "UPDATE `jos_vm_warehouse_info`
                SET    
                    `person_name`='" . $database->getEscaped($d['person_name']) . "',
                    `warehouse_type`='" . $database->getEscaped($d['warehouse_type']) . "',
                    `company_name`='" . $database->getEscaped($d['company_name']) . "',
                    `street_number`='" . $database->getEscaped($d['street_number']) . "',
                    `street_name`='" . $database->getEscaped($d['street_name']) . "',
                    `city`='" . $database->getEscaped($d['city']) . "',
                    `state`='" . $database->getEscaped($d['state']) . "',
                    `country`='AU',
                    `zip`='" . $database->getEscaped($d['postal_code']) . "',
                    `phone`='" . $database->getEscaped($d['phone']) . "',
                    `lat`='" . $database->getEscaped($d['lat']) . "',
                    `lng`='" . $database->getEscaped($d['lng']) . "',
                    `warehouse_type` = 1
                WHERE `id`=" . $wh_obj->id . "";
        } else {
            $query = "INSERT INTO `jos_vm_warehouse_info`
                (
                    `warehouse_id`,
                    `person_name`,
                    `warehouse_type`,
                    `company_name`,
                    `street_number`,
                    `street_name`,
                    `city`,
                    `state`,
                    `country`,
                    `zip`,
                    `phone`,
                 	`warehouse_type`,
                 	`lat`,
                 	`lng`
                )
                VALUES (
                    " . $d['warehouse_id'] . ",
                    '" . $database->getEscaped($d['person_name']) . "',
                    '1',
                    '" . $database->getEscaped($d['company_name']) . "',
                    '" . $database->getEscaped($d['street_number']) . "',
                    '" . $database->getEscaped($d['street_name']) . "',
                    '" . $database->getEscaped($d['city']) . "',
                    '" . $database->getEscaped($d['state']) . "',
                    '" . $database->getEscaped($d['country']) . "',
                    '" . $database->getEscaped($d['zip']) . "',
                    '" . $database->getEscaped($d['phone']) . "',
                    '1',
                    '" . $database->getEscaped($d['lat']) . "',
                    '" . $database->getEscaped($d['lng']) . "'
                )";
        }
        $database->setQuery($query);
        $database->query();
        return True;
    }

    /**
     * Controller for Deleting Records.
     */
    function delete(&$d)
    {

        if (!$this->validate_delete($d)) {
            return False;
        }

        $record_id = $d["warehouse_id"];

        if (is_array($record_id)) {
            foreach ($record_id as $record) {
                if (!$this->delete_record($record, $d))
                    return false;
            }
            return true;
        } else {
            return $this->delete_record($record_id, $d);
        }
    }

    /**
     * Deletes one tax record.
     */
    function delete_record($record_id, &$d)
    {
        global $db;
        $ps_vendor_id = $_SESSION["ps_vendor_id"];

        $q = "DELETE from #__{vm}_warehouse where warehouse_id='$record_id'";
        $q .= " AND vendor_id='$ps_vendor_id'";
        $db->query($q);
        $db->next_record();
        return True;
    }

    function list_warehouse($warehouse_code, $extra = "")
    {
        echo $this->getWarehouse($warehouse_code, $extra);
    }


    function getWarehouse($warehouse_code, $extra = "")
    {
        $db = new ps_DB;

        $q = "SELECT * from #__{vm}_warehouse ORDER BY list_warehouse";
        $db->query($q);
        $html = "<select name=\"warehouse\" class=\"inputbox\" $extra>\n";
        while ($db->next_record()) {

            $html .= "<option value=\"" . $db->f("warehouse_code") . "\"";
            if ($warehouse_code == $db->f("warehouse_code"))
                $html .= " selected=\"selected\">";
            else
                $html .= ">";
            $html .= $db->f("warehouse_name") . "</option>\n";
        }
        $html .= "</select>\n";
        return $html;
    }


}

?>
