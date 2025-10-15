<?php
/**
* YOOaccordion Joomla! Module
*
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $mainframe, $mosConfig_live_site;

// count instances
if (!isset($GLOBALS['yoo_accordions'])) {
	$GLOBALS['yoo_accordions'] = 1;
} else {
	$GLOBALS['yoo_accordions']++;
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mod_yoo_accordion' . DIRECTORY_SEPARATOR . 'helper.php');

// Disable edit ability icon
$access = new stdClass();
$access->canEdit 	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;

$list = modYOOaccordionHelper::getList($params, $access);

// check if any results returned
$items = count($list);
if (!$items) {
	return;
}

// init vars
$style           = $params->get('style', 'default');
$layout          = $params->get('layout', 'vertical');
$open_all        = $params->get('open_all', 0) ? 'true' : 'false';
$multiple_open   = $params->get('multiple_open', 0) ? 'true' : 'false';
$module_base     = $mosConfig_live_site . '/modules/mod_yoo_accordion/';

// css parameters
$accordion_id    = 'yoo-accordion-' . $GLOBALS['yoo_accordions'];

// js parameters
$javascript      = "new YOOaccordion($$('#$accordion_id .toggler'), $$('#$accordion_id .content'), { openAll: $open_all, allowMultipleOpen: $multiple_open });";

switch ($style) {
	case "watermark":
   		require(modYOOaccordionHelper::getLayoutPath('mod_yoo_accordion', 'watermark'));
   		break;
	case "whitespace":
   		require(modYOOaccordionHelper::getLayoutPath('mod_yoo_accordion', 'whitespace'));
   		break;
	default:
    	require(modYOOaccordionHelper::getLayoutPath('mod_yoo_accordion', 'default'));
}

if ($GLOBALS['yoo_accordions'] == 1) {  
	echo "<script src=\"" . $module_base . "mod_yoo_accordion.js\" type=\"text/javascript\"></script>\n";
}

echo "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";
?>