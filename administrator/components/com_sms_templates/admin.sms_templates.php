<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$ComSmsTemplates = new ComSmsTemplates;
$ComSmsTemplates->database = $database;
$ComSmsTemplates->option = 'com_sms_templates';
$ComSmsTemplates->cid = josGetArrayInts('cid');
$ComSmsTemplates->id = (int)$id > 0 ? (int)$id : (int)$ComSmsTemplates->cid[0];

$ComSmsTemplates->template_types = array(
    '1' => 'Confirmation',
    '2' => 'Status Change',
    '4' => 'Driver Investigation',
    '5' => 'Corporate App',
    '6' => 'Company Group Email',
    '7' => 'BULK Corporate email',
    '8' => 'Coupon',
    '9' => 'Call Christmas 2019',
    '10' => 'Gift Coupon',
    '11' => 'Abandonment',
    '12' => 'Send Shipping Form',
);

$ComSmsTemplates->recipient_types = array(
    '1' => 'Customer',
    '2' => 'Production',
    '3' => 'Supervisor'
);

Switch ($task) {
    case 'remove':
        $ComSmsTemplates->remove();
    break;

    case 'save':
        $ComSmsTemplates->save();
    break;

    case 'new':
    case 'edit':
        $ComSmsTemplates->edit_new();
    break;

    default:
        $ComSmsTemplates->default_list();
    break;
}

class ComSmsTemplates {
    var $database;
    var $option;
    
    public function remove() {
        
        if (sizeof($this->cid) > 0) {
            $query = "DELETE
            FROM `jos_sms_templates` 
            WHERE `id` IN (".implode(',', $this->cid).")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option='.$this->option, 'Success.');
    }
    
    public function save() {
        $template_type = (int)trim($_POST['template_type']);
        $recipient_type = (int)trim($_POST['recipient_type']);
        $order_status_code = $this->database->getEscaped(trim($_POST['order_status_code']));
        $title = $this->database->getEscaped(trim($_POST['title']));
        $template = $this->database->getEscaped(trim($_POST['template']));

        if ($this->id > 0) {
            $query = "SELECT 
                `e`.`id`
            FROM `jos_sms_templates` AS `e`  
            WHERE `e`.`id`=".$this->id."";
            $row=false;
            $this->database->setQuery($query);
            $this->database->loadObject($row);

            if ($row) {
                $query = "UPDATE `jos_sms_templates`
                SET
                    `template_type`='".$template_type."',
                    `recipient_type`='".$recipient_type."',
                    `order_status_code`='".$order_status_code."',
                    `title`='".$title."',
                    `template`='".$template."'
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
            $query = "INSERT INTO `jos_sms_templates`
            (
                `template_type`,
                `recipient_type`,
                `order_status_code`,
                `title`,
                `template`
            )
            VALUES (
                '".$template_type."',
                '".$recipient_type."',
                '".$order_status_code."',
                '".$title."',
                '".$template."'
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
        $row = $statuses = $variables_all = false;

        $query = "SELECT 
            `order_status_code`, 
            `order_status_name`
        FROM `jos_vm_order_status`
        ORDER BY `order_status_name`";

        $this->database->setQuery($query);
        $statuses = $this->database->loadObjectList();
        
        $object = new stdClass();
        $object->order_status_code = '';
        $object->order_status_name = 'Default';

        $statuses[] = $object;

        $query = "SELECT 
            `ev`.`id`,
            `ev`.`variable`,
            `ev`.`value`
        FROM `jos_vm_emails_variables` AS `ev`
        WHERE `ev`.`email_id`='0'";

        $this->database->setQuery($query);
        $variables_all = $this->database->loadObjectList();
        
        if ($this->id > 0) {
            $query = "SELECT 
                `e`.`id`,
                `e`.`template_type`,
                `e`.`recipient_type`,
                `e`.`title`,
                `e`.`template`,
                `e`.`order_status_code`
            FROM `jos_sms_templates` AS `e`  
            LEFT JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`e`.`order_status_code`
            WHERE `e`.`id`=".$this->id."";
            
            $this->database->setQuery($query);
            $this->database->loadObject($row);

        }

        HTML_ComSmsTemplates::edit_new($this->option, $row, $statuses,$variables_all,  $this->template_types, $this->recipient_types);
    }
    
    public function default_list() {
        global $mainframe, $mosConfig_list_limit;
                
        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval( $mainframe->getUserStateFromRequest('viewpl'.$this->option.'limitstart', 'limitstart', 0));

        $where = '';

        $query = "SELECT 
            COUNT(`e`.`id`) 
        FROM `jos_sms_templates` AS `e` 
        ".$where."";
        
        $this->database->setQuery($query);
        $total = $this->database->loadResult();
        
        require_once $GLOBALS['mosConfig_absolute_path'].'/administrator/includes/pageNavigation.php';
    
        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `e`.`id`,
            `e`.`template_type`,
            `e`.`recipient_type`,
            `e`.`title`,
            IF (ISNULL(`s`.`order_status_id`), 'Default', `s`.`order_status_name`) AS 'status_name'
        FROM `jos_sms_templates` AS `e`
        LEFT JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`e`.`order_status_code`
        ".$where."";
        
        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();

        HTML_ComSmsTemplates::default_list($this->option, $this->template_types, $this->recipient_types, $rows, $pageNav);
    }

}


?>



