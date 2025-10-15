<?php 
/** Create a Joomla/Mambo environment for our example programs 
 * @package examples 
 */
$baseDir = dirname(__FILE__) . '/';	
/** */	
define('_VALID_MOS', 1); //Pretend we're Joomla
require_once($baseDir.'../../../globals.php');
require_once($baseDir.'../../../configuration.php');
require_once($baseDir .'../../../includes/mambo.php');
$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );

$GLOBALS['database'] = $database;
?>