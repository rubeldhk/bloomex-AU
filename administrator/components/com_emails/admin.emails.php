<?php

defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');
require_once $GLOBALS['mosConfig_absolute_path'].'/includes/lib/Diff.php';
require_once $GLOBALS['mosConfig_absolute_path'].'/includes/lib/Diff/Renderer/Html/SideBySide.php';

$ComEmails = new ComEmails;
$ComEmails->database = $database;
$ComEmails->option = 'com_emails';
$ComEmails->cid = josGetArrayInts('cid');
$ComEmails->id = (int) $id > 0 ? (int) $id : (int) $ComEmails->cid[0];

$ComEmails->email_types = array(
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
    '13' => 'Security Notification',
    '14' => 'Partner Notification',
    '15' => 'POD Notification',
);

$ComEmails->recipient_types = array(
    '1' => 'Customer',
    '2' => 'Production',
    '3' => 'Supervisor',
    '4' => 'Partner'
);

Switch ($task) {
    case 'remove':
        $ComEmails->remove();
        break;

    case 'save':
        $ComEmails->save();
        break;

    case 'new':
    case 'edit':
        $ComEmails->edit_new();
        break;

    default:
        $ComEmails->default_list();
        break;
}

class ComEmails {

    var $database;
    var $option;

    public function remove() {
        global $my;
        if (sizeof($this->cid) > 0) {

            $query = "SELECT 
                `e`.`id`,`e`.`email_html`
            FROM `jos_vm_emails` AS `e`  
            WHERE `e`.`id`=" . $this->id . "";
            $row=false;
            $this->database->setQuery($query);
            $this->database->loadObject($row);

            $query = "INSERT INTO `jos_vm_emails_html_change_history`
                                (
                                    `email_id`,
                                    `action`,
                                    `username`,
                                    `datetime`,
                                    `email_html`
                                )
                                VALUES (
                                    '" . $this->id . "',
                                    'delete',
                                    '" . $my->username . "',
                                    NOW(),
                                    '" . $this->database->getEscaped($row->email_html) . "'
                                )";
            $this->database->setQuery($query);
            $this->database->query();


            $query = "DELETE
            FROM `jos_vm_emails` 
            WHERE `id` IN (" . implode(',', $this->cid) . ")";

            $this->database->setQuery($query);
            $this->database->query();
        }

        mosRedirect('index2.php?option=' . $this->option, 'Success.');
    }

    public function save() {

        global $my;

        if (substr($_SERVER['HTTP_HOST'], 0, 3) == "adm") {
            mosRedirect('index2.php?option=' . $this->option, 'You are not allowed to edit this content directly on live server');
        }
        $email_type = (int) trim($_POST['email_type']);
        $recipient_type = (int) trim($_POST['recipient_type']);

        $order_status_code = $this->database->getEscaped(trim($_POST['order_status_code']));
        $email_subject = $this->database->getEscaped(trim($_POST['email_subject']));
        $email_html = $this->database->getEscaped(trim($_POST['email_html']));
        $for_foreign_orders = $this->database->getEscaped(trim($_POST['for_foreign_orders']??''));
        if ($this->id > 0) {
            $query = "SELECT 
               `e`.`id`,`e`.`email_html`
            FROM `jos_vm_emails` AS `e`  
            WHERE `e`.`id`=".$this->id."";
            $row=false;
            $this->database->setQuery($query);
            $this->database->loadObject($row);


            // Initialize the diff class
            $diff_en = new Diff(explode("\n",$row->email_html),explode("\n",trim($_POST['email_html'])), []);
            $renderer_en = new Diff_Renderer_Html_SideBySide;
            $diff_html_en = $diff_en->Render($renderer_en);

            if ($row) {
                $query = "UPDATE `jos_vm_emails`
                SET
                    `email_type`='" . $email_type . "',
                    `for_foreign_orders`='" . $for_foreign_orders . "',
                    `recipient_type`='" . $recipient_type . "',
                    `order_status_code`='" . $order_status_code . "',
                    `email_subject`='" . $email_subject . "',
                    `email_html`='" . $email_html . "'
                WHERE `id`=" . $this->id . "";

                $this->database->setQuery($query);
                if ($this->database->query()) {
                    $query = "INSERT INTO `jos_vm_emails_html_change_history`
                                (
                                    `email_id`,
                                    `action`,
                                    `username`,
                                    `datetime`,
                                    `email_html`
                                )
                                VALUES (
                                    '" . $this->id . "',
                                    'update',
                                    '" . $my->username . "',
                                    NOW(),
                                    '" . $this->database->getEscaped($diff_html_en) . "'
                                )";
                    $this->database->setQuery($query);
                    $this->database->query();
                    mosRedirect('index2.php?option='.$this->option, 'Success.');
                }
                else {
                    mosRedirect('index2.php?option='.$this->option, 'ERROR: Database error (update).');
                }
            } else {
                mosRedirect('index2.php?option=' . $this->option, 'ERROR: Database error (select).');
            }
        } else {
            $query = "INSERT INTO `jos_vm_emails`
            (
                `email_type`,
                `for_foreign_orders`,
                `recipient_type`,
                `order_status_code`,
                `email_subject`,
                `email_html`
            )
            VALUES (
                '" . $email_type . "',
                '" . $for_foreign_orders . "',
                '" . $recipient_type . "',
                '" . $order_status_code . "',
                '" . $email_subject . "',
                '" . $email_html . "'
            )";

            $this->database->setQuery($query);
            if ($this->database->query()) {
                $query = "INSERT INTO `jos_vm_emails_html_change_history`
                                (
                                    `email_id`,
                                    `action`,
                                    `username`,
                                    `datetime`,
                                    `email_html`
                                )
                                VALUES (
                                    '" . $this->id . "',
                                    'insert',
                                    '" . $my->username . "',
                                    NOW(),
                                    '" . $this->database->getEscaped($email_html) . "'
                                )";
                $this->database->setQuery($query);
                $this->database->query();
                mosRedirect('index2.php?option='.$this->option, 'Success.');
            }
            else {
                mosRedirect('index2.php?option='.$this->option, 'ERROR: Database error (insert).');
            }
        }
    }

    public function edit_new() {
        $row = $statuses = $variables_all = $variables_one = false;

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
                `e`.`email_type`,
                `e`.`for_foreign_orders`,
                `e`.`recipient_type`,
                `e`.`email_subject`,
                `e`.`email_html`,
                `e`.`order_status_code`
            FROM `jos_vm_emails` AS `e`  
            LEFT JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`e`.`order_status_code`
            WHERE `e`.`id`=" . $this->id . "";

            $this->database->setQuery($query);
            $this->database->loadObject($row);

            $query = "SELECT *
            FROM `jos_vm_emails_html_change_history` 
            WHERE `email_id`=" . $this->id . " order by datetime desc";

            $this->database->setQuery($query);
            $change_history = $this->database->loadObjectList();
        }

        HTML_ComEmails::edit_new($this->option, $row, $statuses, $variables_all, $change_history, $this->email_types, $this->recipient_types);
    }

    public function default_list() {
        global $mainframe, $mosConfig_list_limit;

        $limit = intval($mainframe->getUserStateFromRequest('viewlistlimit', 'limit', $mosConfig_list_limit));
        $limitstart = intval($mainframe->getUserStateFromRequest('viewpl' . $this->option . 'limitstart', 'limitstart', 0));
        $search = $this->database->getEscaped(trim($mainframe->getUserStateFromRequest('search' . $this->option, 'search', '')));

        $where = '';

        if (!empty($search)) {
            $where = "WHERE `e`.`email_html` LIKE '%" . $search . "%'";
        }

        $query = "SELECT 
            COUNT(`e`.`id`) 
        FROM `jos_vm_emails` AS `e` 
        " . $where . "";

        $this->database->setQuery($query);
        $total = $this->database->loadResult();

        require_once $GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php';

        $pageNav = new mosPageNav($total, $limitstart, $limit);

        $query = "SELECT 
            `e`.`id`,
            `e`.`email_type`,
            `e`.`for_foreign_orders`,
            `e`.`recipient_type`,
            `e`.`email_subject`,
            IF (ISNULL(`s`.`order_status_id`), 'Default', `s`.`order_status_name`) AS 'status_name'
        FROM `jos_vm_emails` AS `e`
        LEFT JOIN `jos_vm_order_status` AS `s` ON `s`.`order_status_code`=`e`.`order_status_code`
        " . $where . "";

        $this->database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
        $rows = $this->database->loadObjectList();

        HTML_ComEmails::default_list($this->option, $this->email_types, $this->recipient_types, $rows, $pageNav, $search);
    }

}
?>



