<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$ComAliases = new ComAliases;
$ComAliases->database = $database;
$ComAliases->option = 'com_aliases';
$ComAliases->cid = josGetArrayInts('cid');
$ComAliases->id = (int)$id > 0 ? (int)$id : (int)$ComAliases->cid[0];

$ComAliases->redirect_types = array(
    '1' => '301',
    '2' => 'Replace'
);

$ComAliases->page_types = array(
    '0' => 'Default',
    '1' => 'Category',
    '2' => 'Product',
    '3' => 'Landing'
);

Switch ($task) {
    case 'remove':
        $ComAliases->remove();
    break;

    case 'save':
        $ComAliases->save();
    break;
    case 'update_pages_codes':
        $ComAliases->update_pages_codes();
    break;

    case 'new':
    case 'edit':
        $ComAliases->edit_new();
    break;

    default:
        $ComAliases->default_list();
    break;
}

class ComAliases {
    var $database;
    var $option;
    
    public function update_pages_codes() {
        $start_page = $this->database->getEscaped($_REQUEST['start_page']);
        $process_page_limit = $this->database->getEscaped($_REQUEST['process_page_limit']);

        $query = "SELECT 
            `id`,`to`
        FROM `jos_aliases` where `to`!=''  ORDER BY `from` ASC limit $start_page,$process_page_limit";
        $this->database->setQuery($query);
        $rows = $this->database->loadObjectList();
        if($rows){
            foreach ($rows as $row){
                set_time_limit(600);
                $first_letter = substr($row->to, 0,1);
                if($first_letter=='/'){
                    $url = 'https://bloomex.com.au' . $row->to;
                }else{
                    $url = 'https://bloomex.com.au/' . $row->to;
                }

                $http_code='';
                $ch='';

                $ch = curl_init($url);
                curl_exec($ch);
                if (!curl_errno($ch)) {
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if($http_code){
                        $query = "UPDATE `jos_aliases` SET status_code='".$this->database->getEscaped($http_code)."' where id=".$row->id;
                        $this->database->setQuery($query);
                        $this->database->query();
                    }
                }
                curl_close($ch);
            }
            ob_end_clean();
            exit('Processing...');
        }else{
            ob_end_clean();
            exit('Success');
        }
    }
    public function remove() {

        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_aliases` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }

    public function save() {
        $from = $this->database->getEscaped(trim($_POST['from']));
        $to = $this->database->getEscaped(trim($_POST['to']));
        $status = (int)trim($_POST['status']);
        $type = (int)trim($_POST['type']);
        
        if ($this->id > 0) {
            $query = "SELECT 
                `a`.`id`
            FROM `jos_aliases` AS `a`  
            WHERE `a`.`id`=".$this->id."";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `jos_aliases`
                SET
                    `from`='".$from."',
                    `to`='".$to."',
                    `status`='".$status."',
                    `type`='".$type."'
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
            $query = "INSERT INTO `jos_aliases`
            (
                `from`,
                `to`,
                `status`,
                `type`
            )
            VALUES (
                '".$from."',
                '".$to."',
                '".$status."',
                '".$type."'
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
                `a`.`id`,
                `a`.`from`,
                `a`.`to`,
                `a`.`status`,
                `a`.`type`
            FROM `jos_aliases` AS `a`  
            WHERE `a`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        HTML_ComAliases::edit_new($this->option, $row, $this->redirect_types, $this->page_types);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = isset($_POST['search']) ? $this->database->getEscaped(trim($_POST['search'])) : '';
        $search_type = isset($_POST['search_type']) ? (int)$_POST['search_type'] : -1;

        $where = [];

        if (!empty($search)) {
            $where[] = "(`a`.`from` LIKE '%".$search."%' OR `a`.`to` LIKE '%".$search."%')";
        }
        if ($search_type >= 0) {
            $where[] = "(`a`.`type`='".$search_type."')";
        }
    
        $query = "SELECT 
            COUNT(`a`.`id`) 
        FROM `jos_aliases` AS `a`";
        
        if (sizeof($where) > 0) {
            $query .= " WHERE ".implode(' AND ', $where);
        }

        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `a`.`id`,
            `a`.`from`,
            `a`.`to`,
            `a`.`status`,
            `a`.`type`,
            `a`.`status_code`
        FROM `jos_aliases` AS `a`";
        if (sizeof($where) > 0) {
            $query .= " WHERE ".implode(' AND ', $where);
        }
        $query .= " ORDER BY `a`.`status_code` desc";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_ComAliases::default_list($this->option, $this->redirect_types, $this->page_types, $rows, $pageNav, $search, $search_type);
    }

}


?>



