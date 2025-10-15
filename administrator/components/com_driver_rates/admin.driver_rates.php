<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$ComDriverRates = new ComDriverRates;
$ComDriverRates->database = $database;
$ComDriverRates->option = 'com_driver_rates';
$ComDriverRates->cid = josGetArrayInts('cid');
$ComDriverRates->id = (int) $id > 0 ? (int) $id : (int) $ComDriverRates->cid[0];

Switch ($task) {
    case 'postalcode_save':
        $ComDriverRates->postalcode_save();
        break;

    case 'postalcode_cancel':
        $ComDriverRates->postalcode_cancel();
        break;

    case 'postalcode_remove':
        $ComDriverRates->postalcode_remove();
        break;

    case 'remove':
        $ComDriverRates->remove();
        break;

    case 'save':
        $ComDriverRates->save();
        break;
    case 'reorder':
        $ComDriverRates->reorder();
        break;
    case 'postalcode_new':
    case 'postalcode_edit':
        $ComDriverRates->postalcode_edit_new();
        break;

    case 'new':
    case 'rate_edit':
        $ComDriverRates->edit_new();
        break;

    case 'postalcodes_list':
        $ComDriverRates->postalcodes_list();
        break;

    default:
        $ComDriverRates->default_list();
        break;
}

class ComDriverRates {

    var $database;
    var $option;

    public function postalcode_cancel() {
        $default_id_rate = isset($_POST['default_id_rate']) ? (int) $_POST['default_id_rate'] : 0;

        mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate);
    }

    public function postalcode_remove() {
        $id_rate = isset($_REQUEST['id_rate']) ? (int) $_REQUEST['id_rate'] : 0;

        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_driver_rates_postalcodes` 
            WHERE 
                `id` IN (" . implode(',', $this->cid) . ")
            ";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $id_rate, 'Success.');
    }

    public function remove() {

        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_driver_rates` 
            WHERE 
                `id_rate` IN (" . implode(',', $this->cid) . ")
            ";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option=' . $this->option, 'Success.');
    }

    public function postalcode_save() {
        $default_id_rate = isset($_POST['default_id_rate']) ? (int) $_POST['default_id_rate'] : 0;
        $postalcode = $this->database->getEscaped(trim($_POST['postalcode']));
        $id_rate = isset($_POST['id_rate']) ? (int) $_POST['id_rate'] : 0;

        $postalcode = strtoupper(str_replace(array(' ', '-'), '', trim($postalcode)));

        if ($this->id > 0) {
            $query = "SELECT 
                `rp`.`id`
            FROM `jos_driver_rates_postalcodes` AS `rp`  
            WHERE 
                `rp`.`id`=" . $this->id . "
            ";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `jos_driver_rates_postalcodes`
                SET
                    `postalcode`='" . $postalcode . "',
                    `id_rate`=" . $id_rate . "
                WHERE 
                    `id`=" . $this->id . "
                ";

                $this->database->setQuery($query);
                if ($this->database->query()) {
                    mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate, 'Success.');
                } else {
                    mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate, 'ERROR: Database error (update).');
                }
            } else {
                mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate, 'ERROR: Database error (select).');
            }
        } else {
            $query = "INSERT INTO `jos_driver_rates_postalcodes`
            (
                `postalcode`,
                `id_rate`
            )
            VALUES (
                '" . $postalcode . "',
                " . $id_rate . "
            )";

            $this->database->setQuery($query);
            if ($this->database->query()) {
                mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate, 'Success.');
            } else {
                mosRedirect('index2.php?option=' . $this->option . '&task=postalcodes_list&id_rate=' . $default_id_rate, 'ERROR: Database error (insert).');
            }
        }
    }

    public function reorder() {
        $warehouse_id = ((isset($_POST['warehouse_id'])) ? (int) $_POST['warehouse_id'] : 0);
        $orderArrJson = ((isset($_POST['orderArrJson'])) ? $_POST['orderArrJson'] : '');
        if ($orderArrJson) {
            $orderArr = json_decode($orderArrJson);
            if ($orderArr) {
                foreach ($orderArr as $o) {
                    $query = "UPDATE `jos_driver_rates`
                SET
                    `orderby`='" . $o->order . "'
                WHERE 
                    `id_rate`=" . $o->id . " AND `warehouse_id`=" . $warehouse_id . "
                ";
                    $this->database->setQuery($query);
                    $this->database->query();
                }
            }
        }
    }

    public function save() {
        $name = $this->database->getEscaped(trim($_POST['name']));
        $rate = $this->database->getEscaped(trim($_POST['rate']));
        $rate_driver = $this->database->getEscaped(trim($_POST['rate_driver']));
        $comment = $this->database->getEscaped(trim($_POST['comment']));
        $warehouse_id = ((isset($_POST['warehouse_id'])) ? (int) $_POST['warehouse_id'] : 0);
        $warehouse = ((isset($_REQUEST['warehouse'])) ? (int) $_REQUEST['warehouse'] : 0);
        $is_gofor = ((isset($_POST['is_gofor'])) ? 1 : 0);

        if ($this->id > 0) {
            $query = "SELECT 
                `r`.`id_rate`
            FROM `jos_driver_rates` AS `r`  
            WHERE 
                `r`.`id_rate`=" . $this->id . "
            ";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `jos_driver_rates`
                SET
                    `name`='" . $name . "',
                    `warehouse_id`=" . $warehouse_id . ",
                    `is_gofor`=" . $is_gofor . ",
                    `rate`='" . $rate . "',
                    `rate_driver`='" . $rate_driver . "',
                    `comment`='" . $comment . "'
                WHERE 
                    `id_rate`=" . $this->id . "
                ";

                $this->database->setQuery($query);
                if ($this->database->query()) {
                    mosRedirect('index2.php?option=' . $this->option . "&warehouse=" . $warehouse, 'Success.');
                } else {
                    mosRedirect('index2.php?option=' . $this->option . "&warehouse=" . $warehouse, 'ERROR: Database error (update).');
                }
            } else {
                mosRedirect('index2.php?option=' . $this->option . "&warehouse=" . $warehouse, 'ERROR: Database error (select).');
            }
        } else {
            $query = "INSERT INTO `jos_driver_rates`
            (
                `name`,
                `warehouse_id`,
                `is_gofor`,
                `rate`,
                `comment`
            )
            VALUES (
                '" . $name . "',
                '" . $warehouse_id . "',
                '" . $is_gofor . "',
                '" . $rate . "',
                '" . $comment . "'
            )";

            $this->database->setQuery($query);
            if ($this->database->query()) {
                mosRedirect('index2.php?option=' . $this->option, 'Success.');
            } else {
                mosRedirect('index2.php?option=' . $this->option, 'ERROR: Database error (insert).');
            }
        }
    }

    public function postalcode_edit_new() {
        $row = false;

        $id_rate = isset($_REQUEST['id_rate']) ? (int) $_REQUEST['id_rate'] : 0;

        if ($this->id > 0) {
            $query = "SELECT 
                `rp`.`id`,
                `rp`.`id_rate`,
                `rp`.`postalcode`
            FROM `jos_driver_rates_postalcodes` AS `rp`  
            WHERE 
                `rp`.`id`=" . $this->id . "
            ";

            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        $query = "SELECT 
            `r`.`id_rate` AS `id`,
            `r`.`name`
        FROM `jos_driver_rates` AS `r`
        ORDER BY 
            `r`.`rate` 
            ASC
        ";

        $this->database->setQuery($query);
        $r_rows = $this->database->loadObjectList();

        HTML_ComDriverRates::postalcode_edit_new($this->option, $row, $r_rows, $id_rate);
    }

    public function edit_new() {
        global $my;
        $row = false;

        if ($this->id > 0) {
            $query = "SELECT 
                `r`.`id_rate` AS `id`,
                `r`.`name`,
                `r`.`is_gofor`,
                `r`.`rate`,
                `r`.`rate_driver`,
                `r`.`warehouse_id`,
                `r`.`comment`
            FROM `jos_driver_rates` AS `r`  
            WHERE 
                `r`.`id_rate`=" . $this->id . "
            ";

            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        $query = "SELECT 
            `w`.`warehouse_id`,
            `w`.`warehouse_name`
        FROM `jos_vm_warehouse` AS `w`
        ";

        if ($my->gid != 25) {
            if (isset($my->routes_warehouses)) {
                $query .= " WHERE `w`.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ") ";
            }
        }

        $query .= " 
        ORDER BY 
            `w`.`warehouse_name` 
            ASC
        ";

        $this->database->setQuery($query);
        $wh_rows = $this->database->loadObjectList();

        HTML_ComDriverRates::edit_new($this->option, $row, $wh_rows);
    }

    public function postalcodes_list() {
        global $mainframe, $mosConfig_list_limit;

        $limit = 1000; //intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = 0; //intval($mainframe->getUserStateFromRequest('viewpl' . $this->option . 'limitstart', 'limitstart', 0));
        $id_rate = isset($_REQUEST['id_rate']) ? (int) $_REQUEST['id_rate'] : 0;


        $query = "SELECT 
            `r`.`id_rate` AS `id`,
            `r`.`name`,
            `r`.`rate`,
            `r`.`warehouse_id`
        FROM `jos_driver_rates` AS `r`  
        WHERE 
            `r`.`id_rate`=" . $id_rate . "
        ";

        $this->database->setQuery($query);
        $this->database->loadObject($row);

        $rows = array();

        if ($row) {
            $query = "SELECT 
                COUNT(`rp`.`id_rate`) 
            FROM `jos_driver_rates_postalcodes` AS `rp`
            WHERE
                `rp`.`id_rate`=" . $row->id . "
            ";
            $this->database->setQuery($query);
            $total = $this->database->loadResult();

            require_once $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php';

            $pageNav = new mosPageNav($total, $limitstart, $limit);

            $query = "SELECT 
                `rp`.`id`,
                `rp`.`postalcode`
            FROM `jos_driver_rates_postalcodes` AS `rp`
            WHERE
                `rp`.`id_rate`=" . $row->id . "
            ";

            $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
            $rows = $this->database->loadObjectList();
        }

        HTML_ComDriverRates::postalcodes_list($this->option, $row, $rows, $pageNav, $id_rate);
    }

    public function default_list() {
        global $mainframe, $mosConfig_list_limit, $my;
        //we need all on same page to reorder

        $warehouse = (isset($_REQUEST['warehouse']) AND!empty($_REQUEST['warehouse'])) ? (int) $_REQUEST['warehouse'] : false;
        $limit = 1000; //intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = 0;
        intval($mainframe->getUserStateFromRequest('viewpl' . $this->option . 'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search' . $this->option, 'search', '')));

        $where_a = array();
        if ($search) {
            $where_a[] = "(`rp`.`postalcode` ='" . $search . "' OR `r`.`name` LIKE '%" . $search . "%')";
        }
        if ($warehouse != false) {
            $where_a[] = "`r`.`warehouse_id`=" . $warehouse . "";
        }

        if ($my->gid != 25) {
            if (isset($my->routes_warehouses)) {
                $where_a[] = "`r`.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
                $where_a[] = "`r`.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ")";
            }
        }

        $query = "SELECT 
            COUNT(`r`.`id_rate`) 
        FROM `jos_driver_rates` AS `r`
        ";

        if (count($where_a) > 0) {
            $query .= " WHERE " . implode(' AND ', $where_a) . " ";
        }
        $this->database->setQuery($query);
        $total = $this->database->loadResult();

        require_once $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php';

        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `r`.`id_rate` AS `id`,
            `r`.`name`,
            `r`.`rate`,
            `r`.`rate_driver`,
            `r`.`is_gofor`,
            `r`.`comment`,
            `wh`.`warehouse_name`,
            GROUP_CONCAT(`rp`.postalcode) as 'pc',
            GROUP_CONCAT(`rp`.id) as 'pc_ids'
        FROM `jos_driver_rates` AS `r`
        LEFT JOIN `jos_driver_rates_postalcodes` AS `rp` ON rp.id_rate=r.id_rate
        INNER JOIN `jos_vm_warehouse` AS `wh`
            ON
            `wh`.`warehouse_id`=`r`.`warehouse_id`
        ";

        if (count($where_a) > 0) {
            $query .= " WHERE " . implode(' AND ', $where_a) . " ";
        }

        $query .= "GROUP BY
            `r`.`id_rate` order by r.orderby asc
        ";
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();

        $query = "SELECT 
            `w`.`warehouse_id`,
            `w`.`warehouse_name`
        FROM `jos_vm_warehouse` AS `w`
        ";

        if ($my->gid != 25) {
            if (isset($my->routes_warehouses)) {
                $query .= " WHERE `w`.`warehouse_id` IN (" . implode(', ', $my->routes_warehouses) . ") ";
            }
        }

        $query .= " ORDER BY `w`.`warehouse_name`
        ";

        $this->database->setQuery($query);
        $warehouses = $this->database->loadObjectList();

        HTML_ComDriverRates::default_list($this->option, $warehouse, $warehouses, $rows, $pageNav, $search);
    }

}
?>



