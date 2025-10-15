<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$ComMetaTags = new ComMetaTags;
$ComMetaTags->database = $database;
$ComMetaTags->option = 'com_metatags';
$ComMetaTags->cid = josGetArrayInts('cid');
$ComMetaTags->id = (int)$id > 0 ? (int)$id : (int)$ComMetaTags->cid[0];

Switch ($task) {
    case 'remove':
        $ComMetaTags->remove();
    break;

    case 'save':
        $ComMetaTags->save();
    break;

    case 'new':
    case 'edit':
        $ComMetaTags->edit_new();
    break;

    default:
        $ComMetaTags->default_list();
    break;
}

class ComMetaTags {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_metatags` 
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
        $description_text = $this->database->getEscaped($_REQUEST["description_text"]);
        $description_text_footer = $this->database->getEscaped($_REQUEST["description_text_footer"]);
        $keywords = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "keywords", '')));
        $comment = $this->database->getEscaped(trim(mosGetParam($_REQUEST, "comment", '')));
        $page_type = (int)$_POST['page_type'];
        $landing_type = (int)$_POST['landing_type'];
        $city = (int)$_POST['city'];
        
        if (empty($url)) {
            mosRedirect('index2.php?option='.$this->option, 'Url is required');
        }

        $row = false;
        if ($this->id > 0) {
            $query = "SELECT 
                id
            FROM `jos_metatags` 
            WHERE id=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `jos_metatags`
                SET
                    `url`='".$url."',
                    `page_type`='".$page_type."',
                    `landing_type`='".$landing_type."',
                    `city`='".$city."',
                    `title`='".$title."',
                    `h1`='".$h1."',
                    `description`='".$description."',
                    `description_text`='".$description_text."',
                    `description_text_footer`='".$description_text_footer."',
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
            $query = "INSERT INTO `jos_metatags`
            (
                `url`,
                `page_type`,
                `landing_type`,
                `city`,
                `title`,
                `h1`,
                `description`,
                `description_text`,
                `description_text_footer`,
                `comment`,
                `keywords`
            )
            VALUES (
                '".$url."',
                '".$page_type."',
                '".$landing_type."',
                '".$city."',
                '".$title."',
                '".$h1."',
                '".$description."',
                '".$description_text."',
                '".$description_text_footer."',
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
            $query = "SELECT 
                `mt`.*
            FROM `jos_metatags` AS `mt`  
            WHERE `mt`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        HTML_ComMetaTags::edit_new($this->option, $row);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search'.$this->option, 'search', '')));
    
        $where = '';

        if (!empty($search)) {
            $where = "WHERE (`mt`.`url` LIKE '%".$search."%' OR `mt`.`comment` LIKE '%".$search."%')";
        }
    
        $query = "SELECT 
            COUNT(`mt`.`id`) 
        FROM `jos_metatags` AS `mt` 
        ".$where."";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `mt`.*
        FROM `jos_metatags` AS `mt`
        ".$where."
        ORDER BY `mt`.`id` ASC";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_ComMetaTags::default_list($this->option, $rows, $pageNav, $search);
    }
    
}

?>