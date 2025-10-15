<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$CorporateApp = new CorporateApp;
$CorporateApp->database = $database;
$CorporateApp->option = 'com_corporateapp';
$CorporateApp->cid = josGetArrayInts('cid');
$CorporateApp->id = (int)$id > 0 ? (int)$id : (int)$CorporateApp->cid[0];

Switch ($task) {
    case 'remove':
        $CorporateApp->remove();
    break;

    case 'save':
        $CorporateApp->save();
    break;

    case 'new':
    case 'edit':
        $CorporateApp->edit_new();
    break;

    default:
        $CorporateApp->default_list();
    break;
}

Class CorporateApp {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_corporateapp` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }
    
    public function save() {
        $row = false;
        
        $name = $this->database->getEscaped(trim($_POST['name']));
        $url = $this->database->getEscaped(trim($_POST['url']));
        $shopper_group_id = (int)trim($_POST['shopper_group_id']);
        $assign = $this->database->getEscaped(trim($_POST['assign']));

        if (!empty($name) AND !empty($url)) {
            if ($this->id > 0) {
                $query = "SELECT 
                    `c`.`id`
                FROM `jos_corporateapp` AS `c`  
                WHERE `c`.`id`=".$this->id."";

                $this->database->setQuery($query);
                $this->database->loadObject($row);

                if ($row) {
                    $query = "UPDATE `jos_corporateapp`
                    SET
                        `name`='".$name."',
                        `url`='".$url."',
                        `shopper_group_id`='".$shopper_group_id."',
                        `assign`='".$assign."'
                    WHERE `id`=".$this->id."";

                    $this->database->setQuery($query);
                    if ($this->database->query()) {
                        mosRedirect('index2.php?option='.$this->option, 'Success.');
                    }
                    else {
                        mosRedirect('index2.php?option='.$this->option, 'ERROR: Database error (update).');
                    }
                }
                else {
                    mosRedirect('index2.php?option='.$this->option, 'ERROR: This corporate isn\'t exist.');
                }
            }
            else {
                $query = "INSERT INTO `jos_corporateapp`
                (
                    `name`,
                    `url`,
                    `shopper_group_id`,
                    `assign`
                )
                VALUES (
                    '".$name."',
                    '".$url."',
                    '".$shopper_group_id."',
                    '".$assign."'
                )";
                
                $this->database->setQuery($query);
                if ($this->database->query()) {
                    mosRedirect('index2.php?option='.$this->option, 'Success.');
                }
                else {
                    mosRedirect('index2.php?option='.$this->option, 'ERROR: Database error (insert).');
                }
            }
        }
        else {
            mosRedirect('index2.php?option='.$this->option, 'ERROR: Name and url can\'t be empty.');
        }
    }
    
    public function edit_new() {
        $row = $users = $orders = false;
        
        $query = "SELECT 
            `shopper_group_id`, 
            `shopper_group_name`, 
            `shopper_group_discount` 
        FROM `jos_vm_shopper_group`
        ORDER BY `shopper_group_name`";
        
        $this->database->setQuery($query);
        $rows = $this->database->loadObjectList();
        
        if ($this->id > 0) {
            $query = "SELECT 
                `c`.`id`,
                `c`.`name`,
                `c`.`url`,
                `c`.`shopper_group_id`,
                `c`.`assign`
            FROM `jos_corporateapp` AS `c`  
            WHERE `c`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
            
            $query = "SELECT 
                `u`.`id`,
                `u`.`name`,
                `u`.`email`,
                `u`.`registerDate`
            FROM `jos_corporateapp_users_xref` AS `x`  
            INNER JOIN `jos_users` AS `u` ON `u`.`id`=`x`.`user_id`
            WHERE `x`.`corporate_id`=".$this->id."
            ORDER BY `u`.`registerDate` DESC";
            
            $this->database->setQuery($query);
            $users = $this->database->loadObjectList();
            
            $query = "SELECT 
                `o`.`user_id`,
                `o`.`order_id`,
                `o`.`order_subtotal`,
                `o`.`order_total`,
                `o`.`order_shipping`,
                FROM_UNIXTIME(`o`.`cdate`-3600*5) AS 'creation_date',
                `o`.`ddate`,
                `u`.`name`,
                `u`.`email`,
                `s`.`order_status_name`
            FROM `jos_corporateapp_users_xref` AS `x`
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`user_id`=`x`.`user_id`
            INNER JOIN `jos_users` AS `u` ON `u`.`id`=`x`.`user_id`
            INNER JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`o`.`order_status`
            WHERE `x`.`corporate_id`=".$this->id."
            GROUP BY `o`.`order_id`
            ORDER BY `o`.`order_id` DESC";

            $this->database->setQuery($query);
            $orders = $this->database->loadObjectList();

        }
        
        HTML_CorporateApp::edit_new($this->option, $row, $rows, $users, $orders);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search'.$this->option, 'search', '')));
    
        $where = '';

        if (!empty($search)) {
            $where = "WHERE `c`.`name` LIKE '%".$search."%'";
        }
    
        $query = "SELECT 
            COUNT(`c`.`id`) 
        FROM `jos_corporateapp` AS `c` 
        ".$where."";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `c`.`id`,
            `c`.`name`,
            `c`.`url`,
            `c`.`assign`,
            `g`.`shopper_group_name`,
            `g`.`shopper_group_discount`
        FROM `jos_corporateapp` AS `c`
        LEFT JOIN `jos_vm_shopper_group` AS `g` ON `g`.`shopper_group_id`=`c`.`shopper_group_id`
        ".$where."";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        foreach ($rows as $key => $row) {
            $query = "SELECT COUNT(`user_id`) FROM `jos_corporateapp_users_xref` WHERE `corporate_id`=".$row->id."";
            
            $this->database->setQuery($query);
            $result = $this->database->loadResult();
            
            $rows[$key]->count_users = $result > 0 ? $result : 0;
            
            $query = "SELECT COUNT(`o`.`order_id`) FROM `jos_corporateapp_users_xref` AS `x`
                LEFT JOIN `jos_vm_orders` AS `o` ON `o`.`user_id`=`x`.`user_id`
            WHERE `x`.`corporate_id`=".$row->id."";
            
            $this->database->setQuery($query);
            $result = $this->database->loadResult();
            
            $rows[$key]->count_orders = $result > 0 ? $result : 0;
        }

        HTML_CorporateApp::default_list($this->option, $rows, $pageNav, $search);
    }
    
}

?>

