<?php
defined('_VALID_MOS') or die('Restricted access');

date_default_timezone_set('Australia/Sydney');

require_once $mainframe->getPath('admin_html');

$directionsms = new directionsms;
$directionsms->database = $database;
$directionsms->option = 'com_directionsms';
$directionsms->cid = josGetArrayInts('cid');
$directionsms->id = (int)$id > 0 ? (int)$id : (int)$directionsms->cid[0];
$directionsms->my = $my;

Switch ($task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '') {
    case 'remove':
        $directionsms->remove($directionsms->cid);
    break;

    case 'save':
        $directionsms->save((int)$_POST['id']);
    break;

    case 'publish':
        $directionsms->publish($directionsms->cid, '1');
    break;

    case 'unpublish':
        $directionsms->publish($directionsms->cid, '0');
    break;
    
    case 'cancel':
        mosRedirect('index2.php?option='.$directionsms->option);
    break;

    case 'new':
    case 'edit':
        $directionsms->edit($directionsms->id);
    break;
    
    default:
        $directionsms->default_list();
    break;
}

Class directionsms {
    
    public function remove($ids) {
        $query = "DELETE FROM `jos_vm_routes_sms` WHERE `id` IN (".implode(',', $ids).")";
        
        $this->database->setQuery($query);
        $this->database->query();
        
        mosRedirect('index2.php?option='.$this->option, 'Success!');
    }
    
    public function save($id) {
        $template = $this->database->getEscaped($_POST['template']);
        
        if ($id > 0) {
            $query = "UPDATE `jos_vm_routes_sms` SET
                `template`='".$template."',
                `datetime`='".date('Y-m-d H:i:s')."',
                `username`='".$this->database->getEscaped($this->my->username)."'
            WHERE `id`=".$id."";
        }
        else {
            $query = "INSERT INTO `jos_vm_routes_sms`
            (
                `template`,
                `datetime`,
                `username`
            )
            VALUES (
                '".$template."',
                '".date('Y-m-d H:i:s')."',
                '".$this->database->getEscaped($this->my->username)."'
            )";
        }
        
        $this->database->setQuery($query);
        $this->database->query();
        
        mosRedirect('index2.php?option='.$this->option, 'Success!');
    }
    
    public function publish($ids, $publish) {
        $query = "UPDATE `jos_vm_routes_sms` SET `publish`='".$publish."' WHERE `id` IN (".implode(',', $ids).")";

        $this->database->setQuery($query);
        $this->database->query();
        
        mosRedirect('index2.php?option='.$this->option, 'Success!');
    }
    
    public function edit($id) {
        if ($id > 0) {
            $query = "SELECT 
            `s`.*
            FROM `jos_vm_routes_sms` AS `s` 
            WHERE `s`.`id`=".$id."
            ";
            $row = false;
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }
        else {
            $row = false;
        }
        
        HTML_directionsms::edit($this->option, $this->my, $row);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
        
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));

        $query = "SELECT 
            COUNT(`s`.`id`)
        FROM `jos_vm_routes_sms` AS `s`
        ";

        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_virtuemart/classes/htmlTools.class.php';
        
        $pageNav = new mosPageNav($total, $limitstart, $limit);
    
        $query = "SELECT 
            `s`.*
        FROM `jos_vm_routes_sms` AS `s` ORDER BY `s`.`id` DESC
        ";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_directionsms::default_list($this->option, $this->my, $rows, $pageNav);
    }
    
}