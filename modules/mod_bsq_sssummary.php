<?php
/*
BSQ Sitestats is written by Brent Stolle (c) 2006
Brent can be contacted at dev@bs-squared.com or at http://www.bs-squared.com/

This software is FREE. Please distribute it under the terms of the GNU/GPL License
See http://www.gnu.org/copyleft/gpl.html GNU/GPL for details.

If you fork this to create your own project, please make a reference to BSQ_Sitestats
someplace in your code and provide a link to http://www.bs-squared.com

BSQ Sitestats is based on and made to operate along side of Shaun Inman's ShortStat
http://www.shauninman.com/
*/

/**
* 
* @package mod_bsq_sssummary
* @copyright 2006 Brent Stolle
* @license http://www.gnu.org/copyleft/gpl.html. GNU Public License
* @author Brent Stolle
*
*/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

include($mosConfig_absolute_path . '/administrator/components/com_bsq_sitestats/bsqglobals.inc.php');

//Load library classes
require_once($bsqClassPath . '/bsqsitestatsrender.php');

if(version_compare($bsqVersion, '2.0.0') < 0) {
	echo sprintf(_BSQ_MODULEREQUIRESCOMPONENTUPGRADE, '2.0.0', $bsqAppTitle);
}
else {
	//Read in the params
	$showTotal = intval($params->get('showtotal'));
	$showToday = intval($params->get('showtoday'));
	$showThisWeek = intval($params->get('showthisweek'));
	$showThisMonth = intval($params->get('showthismonth'));
	$showThisYear = intval($params->get('showthisyear'));
	$cacheTime = intval($params->get('cachetime'));
	$cssPrepend = $params->get('cssprepend');
	
	if (!$showTotal && !$showToday && !$showThisWeek && !$showThisMonth && !$showThisYear) {
		$str = "<i>No reports requested.</i>\n";
	}
	else {
		$bsqJllog = new JLLog($bsqAppHandle, $BSQ->jllogLevel);
		$renderer = new BSQSitestatsRender($cssPrepend, true, $bsqJllog, $cacheTime);
		echo $renderer->sssRender($showTotal, $showToday, $showThisWeek, $showThisMonth, $showThisYear);
	}
	
	echo "\n<!-- Site statistics by BSQ Sitestats, (c) 2006 Brent Stolle, http://www.bs-squared.com/mambo/index.php -->\n";
}

?>