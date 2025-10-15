<?php

/***************************************
 *
 **/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_live_site;

$itemID = @$_GET['Itemid'];

define('_DBQ_DEBUG',false);
define('_DBQ_COMPONENT_NAME','com_dbquery');
define('_DBQ_COMPONENT_TITLE','DBQuery');
// You may want to toggle _DBQ_URL if you are having trouble with a CGI Wrapper
// However, this may break the admin preview
define('_DBQ_URL', $_SERVER['SCRIPT_NAME'].'?option=com_dbquery&amp;Itemid=' . $itemID);
define('_DBQ_URL_SEF', $mosConfig_live_site.'/component/option,com_dbquery/Itemid,' . $itemID);
define('_DBQ_URL2', $mosConfig_live_site.'/index2.php?option=com_dbquery&amp;Itemid=' . $itemID);
define('_DBQ_URL_ADMIN', '?option=com_dbquery&amp;Itemid=' . $itemID);
define('_DBQ_QUERIES_TABLE', '#__dbquery');
define('_DBQ_DATABASES_TABLE', '#__dbquery_databases');
define('_DBQ_CONFIG_TABLE', '#__dbquery_config');
define('_DBQ_SUBSTITUTIONS_TABLE', '#__dbquery_substitutions');
define('_DBQ_VARIABLES_TABLE', '#__dbquery_variables');
define('_DBQ_STATS_TABLE', '#__dbquery_stats');
define('_DBQ_ERRORS_TABLE', '#__dbquery_errors');
define('_DBQ_TEMPLATE_TABLE', '#__dbquery_templates');
define('_DBQ_DRIVERS_TABLE', '#__dbquery_drivers');
define('_DBQ_USERS_TABLE','#__users');
define('_DBQ_GROUPS_TABLE','#__groups');
define('_DBQ_CATEGORIES_TABLE','#__categories');
define('_DBQ_LIST_IDENTIFIER','_list');
define('_DBQ_LIST_SEPARATOR','|');

define('_DBQ_VERSION', '1.4.1' );
define('_DBQ_SOURCE_ADMIN','ADMIN');
define('_DBQ_SOURCE_COMPONENT','COMPONENT');
define('_DBQ_SOURCE_MODULE','MODULE');
define('_DBQ_USER_PATH','/components/com_dbquery/');
define('_DBQ_ADMIN_PATH','/administrator/components/com_dbquery/');
define('_DBQ_CLASS_PATH', _DBQ_USER_PATH . 'classes/DBQ/');
define('_DBQ_HTML_PATH', _DBQ_ADMIN_PATH . 'html/');
define('_DBQ_XHTML_PATH', _DBQ_ADMIN_PATH . 'xhtml/');
define('_DBQ_SUMMARY_PATH', _DBQ_ADMIN_PATH . 'admin/');
define('_DBQ_LIB_PATH', _DBQ_USER_PATH . 'lib/');
define('_DBQ_XML_PATH', _DBQ_USER_PATH . 'xml/');
//define('_DBQ_HELP_PATH', _DBQ_ADMIN_PATH . 'help/');
define('_DBQ_HELP_URL', 'http://www.gmitc.biz/');
define('_DBQ_ADMIN_PAGENAV_PATH', $mosConfig_absolute_path . '/administrator/includes/pageNavigation.php');

$dbq_user_path = $mosConfig_absolute_path . _DBQ_USER_PATH;
$dbq_admin_path = $mosConfig_absolute_path . _DBQ_ADMIN_PATH;
$dbq_class_path = $mosConfig_absolute_path . _DBQ_CLASS_PATH;
$dbq_html_path = $mosConfig_absolute_path . _DBQ_HTML_PATH;
$dbq_xhtml_path = $mosConfig_absolute_path . _DBQ_XHTML_PATH;
$dbq_summary_path = $mosConfig_absolute_path . _DBQ_SUMMARY_PATH;
$dbq_lib_path = $mosConfig_absolute_path . _DBQ_LIB_PATH;
$dbq_xml_path = $mosConfig_absolute_path . _DBQ_XML_PATH;
$dbq_domit_path = $mosConfig_absolute_path.'/includes/domit/';
//$dbq_help_path = $mosConfig_absolute_path . _DBQ_HELP_PATH;

?>
