<?php
define( '_VALID_MOS', 1 );
require( '../../../globals.php' );
require_once( '../../../configuration.php' );
require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
include_once( $mosConfig_absolute_path . '/language/'. $mosConfig_lang .'.php' );
session_name( md5( $mosConfig_live_site ) );
session_start();
$mainframe	= new mosMainFrame( $database, '', '', true );
$my 		= $mainframe->initSessionAdmin( '', 'apply' );

$joomlacachephp_file   =$mosConfig_absolute_path.'/includes/joomla.cache.php';
$joomlacachepatch_file =$mosConfig_absolute_path.'/mambots/system/pda/joomla.cache.php';
$joomlacachebackup_file=$mosConfig_absolute_path.'/mambots/system/pda/joomla.cache.backup';

function message($text)
{?>
<html><head><title>Install patch</title></head><body onload="window.moveTo((screen.availWidth-document.body.offsetWidth)/2,(screen.availHeight-document.body.clientHeight)/2);"><div style="text-align:center">
<?php echo $text; ?><br />
<a href="#" onclick="window.close();">[X] Close</a>
<?php
	die();
}

if(($joomlacache=@file($joomlacachephp_file))==FALSE)
	message('Cannot read <b>/includes/joomla.cache.php</b>');
if(($joomlacachepatch=@file($joomlacachepatch_file))==FALSE)
	message('Cannot read <b>/mambots/system/pda/joomla.cache.php</b>');
if(implode('',$joomlacache)==implode('',$joomlacachepatch))
	message('Patch already installed');
if(!copy($joomlacachephp_file,$joomlacachebackup_file))
	message('Cannot make a backup');
if(!copy($joomlacachepatch_file,$joomlacachephp_file))
	message('Cannot copy patch');

message('Patch installed successfully');
?>