<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$ComBadEmails = new ComBadEmails;
$ComBadEmails->database = $database;
$ComBadEmails->option = 'com_bad_emails';
$ComBadEmails->cid = josGetArrayInts('cid');
$ComBadEmails->id = (isset($id) AND (int)$id > 0) ? (int)$id : ((isset($ComEmails->cid) AND is_array($ComEmails->cid)) ? (int)$ComEmails->cid[0] : '');

Switch ($task) {
    case 'remove':
        $ComBadEmails->remove();
    break;

    case 'save':
        $ComBadEmails->save();
    break;

    case 'new':
    case 'edit':
        $ComBadEmails->edit_new();
    break;

    default:
        $ComBadEmails->default_list();
    break;
}

class ComBadEmails {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `tbl_bad_emails` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }
    
    public function save() {
        $email = $this->database->getEscaped(trim($_POST['email']));
        $reason = $this->database->getEscaped(trim($_POST['reason']));

        if ($this->id > 0) {
            $query = "SELECT 
                `e`.`id`
            FROM `tbl_bad_emails` AS `e`  
            WHERE `e`.`id`=".$this->id."";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `tbl_bad_emails`
                SET
                    `email`='".$email."',
                    `reason`='".$reason."'
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
                mosRedirect('index2.php?option='.$this->option, 'ERROR: Database error (select).');
            }
        }
        else {
            $query = "INSERT INTO `tbl_bad_emails`
            (
                `email`,
                `reason`
            )
            VALUES (
                '".$email."',
                '".$reason."'
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
    
    public function edit_new() {
        $row = false;
        
        if ($this->id > 0) {
            $query = "SELECT 
                `e`.`id`,
                `e`.`email`,
                `e`.`reason`
            FROM `tbl_bad_emails` AS `e`  
            WHERE `e`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        HTML_ComBadEmails::edit_new($this->option, $row);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search'.$this->option, 'search', '')));
    
        $where = '';

        if (!empty($search)) {
            $where = "WHERE `e`.`email` LIKE '%".$search."%' OR `e`.`reason` LIKE '%".$search."%'";
        }
    
        $query = "SELECT 
            COUNT(`e`.`id`) 
        FROM `tbl_bad_emails` AS `e` 
        ".$where."";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `e`.`id`,
            `e`.`email`,
            `e`.`reason`
        FROM `tbl_bad_emails` AS `e`
        ".$where."";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_ComBadEmails::default_list($this->option, $rows, $pageNav, $search);
    }

}


?>



