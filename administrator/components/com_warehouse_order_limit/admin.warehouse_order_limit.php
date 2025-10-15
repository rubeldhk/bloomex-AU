<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
defined('_VALID_MOS') or die('Restricted access');

require_once $mainframe->getPath('admin_html');

$WarehouseOrderLimit = new WarehouseOrderLimit;
$WarehouseOrderLimit->database = $database;
Switch ($task) {
    case 'update':
        $WarehouseOrderLimit->update();
        break;
    default:
        $WarehouseOrderLimit->default_list();
        break;
}

class WarehouseOrderLimit {

    var $database;
    public function update() {

        $warehouseStaticLimits = ($_POST['warehouseStaticLimits'])??'';

        if(!$warehouseStaticLimits){
            $msg = 'warehouse static limits can not be emtpy';
            exit($msg);
        }

        $this->database->setQuery( "TRUNCATE TABLE `jos_vm_warehouse_order_limit`" );
        $this->database->query();

        $queryInsert = "INSERT INTO `jos_vm_warehouse_order_limit` (warehouse_id,orders_count) VALUES ";
        foreach($warehouseStaticLimits as $k=>$w){
            $queryInsert.="({$w['warehouse_id']},{$this->database->getEscaped($w['warehouse_limit'])}),";
        }
        $this->database->setQuery(rtrim($queryInsert,','));
        $this->database->query();
        exit('success');
    }

    public function default_list() {
        $query = "SELECT 
            w.`warehouse_id`,
            w.`warehouse_code`,
            w.`warehouse_name`,
            l.`orders_count`
        FROM `jos_vm_warehouse` as w left join jos_vm_warehouse_order_limit as l on l.warehouse_id=w.warehouse_id WHERE w.published = 1";

        $this->database->setQuery($query);
        $warehouses = $this->database->loadObjectList();


        HTML_WarehouseOrderLimit::edit_new($warehouses);
    }



}
?>



