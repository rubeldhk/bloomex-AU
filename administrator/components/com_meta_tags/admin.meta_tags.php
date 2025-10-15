<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$MetaTags = new MetaTags;
$MetaTags->database = $database;
$MetaTags->option = 'com_meta_tags';
$MetaTags->cid = josGetArrayInts('cid');
$MetaTags->id = (int)$id > 0 ? (int)$id : (int)$MetaTags->cid[0];


Switch ($task) {
    case 'remove':
        $MetaTags->remove();
    break;

    case 'save':
        $MetaTags->save();
    break;

    case 'new':
    case 'edit':
        $MetaTags->edit_new();
    break;

    default:
        $MetaTags->default_list();
    break;
}

class MetaTags {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `tbl_meta_tags` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }
    
    public function save() {
        $url =  $this->database->getEscaped(trim(mosGetParam($_REQUEST, "url", '')));
        $title =  $this->database->getEscaped(trim(mosGetParam($_REQUEST, "title", '')));
        $h1 = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "h1", '')));
        $description = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "description", '')));
        $keywords = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "keywords", '')));
        $comment = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "comment", '')));
        if(!$url){
            mosRedirect('index2.php?option='.$this->option, 'Url is required');
        }

        $row=false;
        if ($this->id > 0) {
            $query = "SELECT 
                id
            FROM `tbl_meta_tags` 
            WHERE id=".$this->id."";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `tbl_meta_tags`
                SET
                    `url`='".$url."',
                    `title`='".$title."',
                    `h1`='".$h1."',
                    `description`='".$description."',
                    `comment`='".$comment."',
                    `keywords`='".$keywords."'
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
            $query = "INSERT INTO `tbl_meta_tags`
            (
                `url`,
                `title`,
                `h1`,
                `description`,
                `comment`,
                `keywords`
            )
            VALUES (
                '".$url."',
                '".$title."',
                '".$h1."',
                '".$description."',
                '".$comment."',
                '".$keywords."'
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
            $query = "SELECT *
            FROM `tbl_meta_tags` AS `e`  
            WHERE `e`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        HTML_MetaTags::edit_new($this->option, $row);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search'.$this->option, 'search', '')));
    
        $where = '';

        if (!empty($search)) {
            $where = "WHERE `url` LIKE '%".$search."%' or title LIKE '%".$search."%'";
        }
    
        $query = "SELECT 
            COUNT(id) 
        FROM `tbl_meta_tags` 
        ".$where."";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT *
        FROM `tbl_meta_tags` 
        ".$where."";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_MetaTags::default_list($this->option, $rows, $pageNav, $search);
    }

}


?>



