<?php
/**
 * sh404SEF prototype support for Banners component.
 * Copyright Yannick Gaultier (shumisha) - 2007
 * shumisha@gmail.com
 * @version     $Id: com_banners.php 197 2007-12-08 19:01:20Z silianacom-svn $
 * {shSourceVersionTag: Version x - 2007-09-20}
 */

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $sefConfig; 

$shName = shGetComponentPrefix($option);
$title[] = empty($shName) ? 'banners':$shName;

$title[] = '/';

$title[] = $task . $bid . $sefConfig->suffix;


if (count($title) > 0) $string = sef_404::sefGetLocation($string, $title,null);

?>
