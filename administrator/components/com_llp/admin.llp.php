<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_absolute_path;

require_once( $mainframe->getPath('admin_html') );
require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/virtuemart.cfg.php" );
//require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/classes/language.class.php" );
//require_once( $mosConfig_absolute_path . "/administrator/components/com_virtuemart/languages/english.php" );
require_once(CLASSPATH . 'vmAbstractObject.class.php' );
require_once(CLASSPATH . 'ps_database.php');
require_once(CLASSPATH . 'ps_product_category.php');

$ps_product_category = new ps_product_category;


Switch ($task)
{
    case 'save':
        save();
    break;

    default:
        default_view();
}

function default_view()
{
    global $ps_product_category, $database;

    $query = "SELECT * FROM `jos_vm_llp_settings` ORDER BY `id_llp` ASC";
    $database->setQuery($query);
    $rows = $database->loadObjectList();
    
    HTML_LLP::default_view($ps_product_category, $rows);
}

function save()
{
    global $database;
    
    $category_ids = $_POST['category_id'];
    
    for ($i = 0; $i <=1; $i++)
    {
        $query = "UPDATE `jos_vm_llp_settings` SET `category_id_llp` = ".$category_ids[$i]." WHERE `id_llp`=".($i+1)."";
        $database->setQuery($query);
        $database->query();
    }
    
    mosRedirect("index2.php?option=com_llp", "Save Successfully" );
}

?>
