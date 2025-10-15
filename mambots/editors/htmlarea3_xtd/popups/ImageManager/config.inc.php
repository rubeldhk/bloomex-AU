<?php
defined( '_VALID_MOS' ) or die( 'Direct access to this location is not allowed!' );
// $Id: config.inc.php, v 1.0 2004/04/14 17:35:27 bpfeifer Exp $
/**
* HTMLArea3 addon - ImageManager
* Based on Wei Zhuo's ImageManager
* @package Mambo Open Source
* @Copyright � 2004 Bernhard Pfeifer aka novocaine
* @ All rights reserved
* @ Mambo Open Source is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0 $
**/

global $mosConfig_absolute_path,$mosConfig_live_site;

$BASE_DIR = $mosConfig_absolute_path;
$BASE_URL = $mosConfig_live_site."/";
$BASE_ROOT = "images/stories"; 
$SAFE_MODE = false;
$IMG_ROOT = $BASE_ROOT;

if(strrpos($BASE_DIR, '/')!= strlen($BASE_DIR)-1) 
	$BASE_DIR .= '/';

if(strrpos($BASE_URL, '/')!= strlen($BASE_URL)-1) 
	$BASE_URL .= '/';

function dir_name($dir) 
{
	$lastSlash = intval(strrpos($dir, '/'));
	if($lastSlash == strlen($dir)-1){
		return substr($dir, 0, $lastSlash);
	}
	else
		return dirname($dir);
}

?>
