<?php
defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$footer_links = new footer_links;
$footer_links->database = $database;
$footer_links->option = 'com_footer_links';
$footer_links->cid = josGetArrayInts('cid');
$footer_links->id = (int)$id > 0 ? (int)$id : (int)$footer_links->cid[0];
$footer_links->my = $my;

Switch ($task) {
    case 'remove':
        $footer_links->remove();
    break;

    case 'save':
        $footer_links->save();
    break;

    case 'new':
    case 'edit':
        $footer_links->edit_new();
    break;

    default: 
        $footer_links->defaultList();
        break;
}

Class footer_links {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `tbl_footer_links_default` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }
    
    public function save() {
        $html = $this->database->getEscaped(trim($_POST['html']));
        $name = $this->database->getEscaped(trim($_POST['name']));
        $type = $this->database->getEscaped(trim($_POST['type']));
        $ref = $this->database->getEscaped(trim(( $type=='category' && isset($_POST['categories']))?serialize($_POST['categories']):(($type=='category')?'':$_POST['ref'])));

        if ($this->id > 0) {
            $query = "SELECT 
                `l`.`id`
            FROM `tbl_footer_links_default` AS `l`  
            WHERE `l`.`id`=".$this->id."";
            $row = false;
            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `tbl_footer_links_default`
                SET
                    `html`='".$html."',
                    `type`='".$type."',
                    `ref`='".$ref."',
                    `name`='".$name."'
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
            $query = "INSERT INTO `tbl_footer_links_default`
            (
                `html`,
                `type`,
                `name`,
                `ref`
            )
            VALUES (
                '".$html."',
                '".$type."',
                '".$name."',
                '".$ref."'
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
                `l`.*
            FROM `tbl_footer_links_default` AS `l`  
            WHERE 
                `l`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);
        }

        $selected_categories = (unserialize($row->ref))?array_flip(unserialize($row->ref)):'';

        $categories_list = $this->list_categories('categories[]', '', $selected_categories);
        HTML_footerLinks::edit_new($this->option, $row,$categories_list);
    }
    function list_categories($name, $category_id, $selected_categories=Array()) {
        $row_category = false;
        $q  = "SELECT category_parent_id FROM jos_vm_category_xref ";
        if( $category_id )
            $q .= "WHERE category_child_id='$category_id'";
        $this->database->setQuery($q);
        $row_category = $this->database->loadObjectList();

        $category_id=isset($row_category->category_parent_id)?$row_category->category_parent_id:0;
        $res= "<select class=\"inputbox\" size=\"10\" multiple=\"multiple\" name=\"$name\">\n";
        $res.= $this->list_categories_tree($category_id,'0', '0', $selected_categories);
        $res.= "</select>\n";

        return $res;
    }
    function list_categories_tree($category_id="", $cid='0', $level='0', $selected_categories=Array() ) {

        $common_categories=array(
            "Occasions",
            "Flowers",
            "Gift Baskets",
            "Roses",
            "Birthday Flowers & Gifts",
            "Sympathy & Funeral Flowers",
            "Valentines Day Flowers",
            "Mother's Day Flowers & Gifts",
        );
        $ps_vendor_id=1;

        $level++;

        $q = "SELECT category_id, category_child_id,category_name FROM jos_vm_category,jos_vm_category_xref ";
        $q .= "WHERE jos_vm_category_xref.category_parent_id='$cid' ";
        $q .= "AND jos_vm_category.category_id=jos_vm_category_xref.category_child_id ";
        $q .= "AND jos_vm_category.vendor_id ='$ps_vendor_id' ";
        $q .= "ORDER BY jos_vm_category.list_order, jos_vm_category.category_name ASC";
        $this->database->setQuery($q);
        $row_categories = $this->database->loadObjectList();

        $res='';
       foreach ($row_categories as $c){
            $child_id = $c->category_child_id;
            if ($child_id != $cid) {
                $selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";

                if( $selected == "" && isset($selected_categories[$child_id])) {
                    $selected = "selected=\"selected\"";
                }
                if(in_array($c->category_name,$common_categories)){
                    $res.= "<option style='font-weight:bold;' $selected value=\"$child_id\">\n";
                }
                else{
                    $res.= "<option  $selected value=\"$child_id\">\n";
                }
            }

            for ($i=0;$i<$level;$i++) {
                $res.= "&#151;";
            }

            $res.= "|$level|";
            $res.= "&nbsp;" . $c->category_name."</option>";

            $res.= $this->list_categories_tree($category_id, $child_id, $level, $selected_categories);
        }
        return $res;
    }
    public function defaultList() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search'.$this->option, 'search', '')));
    
        $query = "SELECT 
            COUNT(`l`.`id`) 
        FROM `tbl_footer_links_default` AS `l` 
        ";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `l`.`id`,
            `l`.`html`,
            `l`.`type`,
            `l`.`name`
        FROM `tbl_footer_links_default` AS `l`
        ORDER BY `l`.`id` ASC
        ";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();
        
        HTML_footerLinks::default_list($this->option, $rows, $pageNav);
    }
}
