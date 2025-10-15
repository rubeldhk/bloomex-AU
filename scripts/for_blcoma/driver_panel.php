<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);

$return = array();
$return['result'] = false;

if ($_POST['key'] == md5($_POST['driver_id'].$_POST['leg_id'].'blca')) {
    Switch($task = isset($_POST['task']) ? $_POST['task'] : '') {
        case 'send_email_delivered':
            $leg_sql = $mysqli->query("SELECT 
                `ro`.`id`,
                `r`.`id` AS 'route_id',
                `ro`.`order_id`,
                `u`.`user_email`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id`=".(int)$_POST['driver_id']."
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
            INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
            WHERE `ro`.`id`=".(int)$_POST['leg_id']."");

            if ($leg_sql->num_rows > 0) {
                $leg_obj = $leg_sql->fetch_object();
                define('_VALID_MOS', true);
                define('_JEXEC', true);

                global $mosConfig_absolute_path;
                $mosConfig_absolute_path = $_SERVER['DOCUMENT_ROOT'];
            
                require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';

                global $database;
                $database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
                require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_comemails.php';

                $ps_comemails = new ps_comemails;

                $email_sql = $mysqli->query("SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='4'");
                $email_obj = $email_sql->fetch_object();
                
                $email_sql = $mysqli->query("SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1' AND `order_status_code`='D'");
                $email_obj = $email_sql->fetch_object();
                
                if (!$email_obj) {
                    $email_sql = $mysqli->query("SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='2' AND `recipient_type`='1'");
                    $email_obj = $email_sql->fetch_object();
                }

                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $leg_obj->user_email, $ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_subject), $ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_html), 1);

                $return['result'] = true;
            }
            else {
                $return['error'] = 'No leg.';
            }
        break;
        
        case 'send_email':
            $leg_sql = $mysqli->query("SELECT 
                `ro`.`id`,
                `r`.`id` AS 'route_id',
                `ro`.`order_id`,
                `u`.`user_email`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id`=".(int)$_POST['driver_id']."
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
            INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
            WHERE `ro`.`id`=".(int)$_POST['leg_id']."");

            if ($leg_sql->num_rows > 0) {
                $leg_obj = $leg_sql->fetch_object();
                define('_VALID_MOS', true);
                define('_JEXEC', true);

                global $mosConfig_absolute_path;
                $mosConfig_absolute_path = $_SERVER['DOCUMENT_ROOT'];
            
                require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';

                global $database;
                $database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
                require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_comemails.php';

                $ps_comemails = new ps_comemails;

                $email_sql = $mysqli->query("SELECT `email_subject`, `email_html` FROM `jos_vm_emails` WHERE `email_type`='4'");
                $email_obj = $email_sql->fetch_object();

                mosMail($mosConfig_mailfrom, $mosConfig_fromname, $leg_obj->user_email, $ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_subject), $ps_comemails->setVariables($leg_obj->order_id, $email_obj->email_html), 1);

                $return['result'] = true;
            }
            else {
                $return['error'] = 'No leg.';
            }
        break;
        
        case 'update_status':
            $leg_sql = $mysqli->query("SELECT 
                `ro`.`id`,
                `r`.`id` AS 'route_id',
                `ro`.`order_id`,
                `u`.`user_email`
            FROM `jos_vm_routes_orders` AS `ro`
            INNER JOIN `jos_vm_routes` AS `r` ON `r`.`id`=`ro`.`route_id` AND `r`.`driver_id`=".(int)$_POST['driver_id']."
            INNER JOIN `jos_vm_orders` AS `o` ON `o`.`order_id`=`ro`.`order_id`
            INNER JOIN `jos_vm_order_user_info` AS `u` ON `u`.`order_id`=`ro`.`order_id` AND `u`.`address_type`='BT'
            WHERE `ro`.`id`=".(int)$_POST['leg_id']."");

            if ($leg_sql->num_rows > 0) {
                $leg_obj = $leg_sql->fetch_object();
                
                $query = "UPDATE `jos_vm_orders` SET
                `order_status`='".$mysqli->real_escape_string($_POST['order_status'])."' 
                WHERE `order_id`=".$leg_obj->order_id."
                ";
                
                $mysqli->query($query);
                
                $return['result'] = true;
            }
            else {
                $return['error'] = 'No leg.';
            }
        break;
        
        default:
        break;
    }
}

echo json_encode($return);

$mysqli->close();

?>

