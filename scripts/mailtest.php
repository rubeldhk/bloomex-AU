<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';

define('_VALID_MOS', true);
define('_JEXEC', true);
global $mosConfig_absolute_path;
$mosConfig_absolute_path = $_SERVER['DOCUMENT_ROOT'];
            
require_once $_SERVER['DOCUMENT_ROOT'].'/language/english.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/joomla.php';

global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/components/com_virtuemart/classes/ps_comemails.php';

$ps_comemails = new ps_comemails;

$to = 'verdiev.ed@gmail.com';
mosMail($mosConfig_mailfrom, $mosConfig_fromname, $to, 'test', 'test', 1);

?>