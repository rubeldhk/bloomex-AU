<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

include($mosConfig_absolute_path.'/components/com_joomlalib/jlcoreapi.inc');


//Read in the params
$cronProbability = intval($params->get('cronprobability'));
$zendDebug = intval($params->get('debugzend'));

$random = rand(1, 100);
if($random <= $cronProbability) {
	
	$zendStr = $zendDebug ? '?start_debug=1' : '';
	$str = '<iframe name="joomlalib_cron" width="1" height="1" frameborder="0" src="'
	        .JLCoreApi::getJLUrl().'/standalone/jlcron.php'.$zendStr.'" style="display:none;">IFRAME Not supported</iframe>';
	echo $str;
}


?>