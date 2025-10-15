<?php
/**
* YOOcarousel Joomla! Module
*
* @version   1.0.3
* @author    yootheme.com
* @copyright Copyright (C) 2007 YOOtheme Ltd. & Co. KG. All rights reserved.
* @license	 GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

global $mainframe, $mosConfig_live_site;

// count instances
if (!isset($GLOBALS['yoo_carousels'])) {
	$GLOBALS['yoo_carousels'] = 1;
} else {
	$GLOBALS['yoo_carousels']++;
}

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mod_yoo_carousel' . DIRECTORY_SEPARATOR . 'helper.php');

// disable edit ability icon
$access = new stdClass();
$access->canEdit	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;

$list = modYOOcarouselHelper::getList($params, $access);

// check if any results returned
$items = count($list);
if (!$items) {
	return;
}

// init vars
$style                 = $params->get('style', 'default');
$module_width          = $params->get('module_width', '400');
$module_height         = $params->get('module_height', '200');
$slide_interval        = $params->get('slide_interval', '3000');
$transition_effect     = $params->get('transition_effect', 'scroll');
$transition_duration   = $params->get('transition_duration', '700');
$control_panel         = $params->get('control_panel', 'top');
$rotate_action         = $params->get('rotate_action', 'click');
$rotate_duration       = $params->get('rotate_duration', '100');
$rotate_effect         = $params->get('rotate_effect', 'scroll');
$buttons               = $params->get('buttons', '1');
$autoplay              = $params->get('autoplay', '1') ? 'true' : 'false';

$carousel_id           = 'yoo-carousel-' . $GLOBALS['yoo_carousels'];
$container_class       = $style;
$button_width          = 50;
$tab_height            = 40;
$panel_width           = ($buttons) ? $module_width - (2 * $button_width) : $module_width;
$panel_height          = ($control_panel != "none") ? $module_height - $tab_height : $module_height;
$css_module_width      = 'width: ' . $module_width . 'px;';
$css_module_height     = 'height: ' . $module_height . 'px;';
$css_total_module_width = 'width: ' . ($module_width * $items + 3) . 'px;';
$css_panel_width       = 'width: ' . $panel_width . 'px;';
$css_panel_height      = 'height: ' . $panel_height . 'px;';
$css_total_panel_width = 'width: ' . ($panel_width * $items + 3) . 'px;';
$javascript            = "new YOOcarousel('" . $carousel_id . "', { transitionEffect: '" . $transition_effect . "', transitionDuration: " . $transition_duration . ", rotateAction: '" . $rotate_action . "', rotateActionDuration: " . $rotate_duration . ", rotateActionEffect: '" . $rotate_effect . "', slideInterval: " . $slide_interval . ", autoplay: " . $autoplay . " });";
$module_base           = $mosConfig_live_site . '/modules/mod_yoo_carousel/';

if ($style == 'slideshow') {
	include(modYOOcarouselHelper::getLayoutPath('mod_yoo_carousel', 'slideshow'));
} else {
	include(modYOOcarouselHelper::getLayoutPath('mod_yoo_carousel', 'default'));
}

if ($GLOBALS['yoo_carousels'] == 1) {  
	echo "<script src=\"" . $module_base . "mod_yoo_carousel.js\" type=\"text/javascript\"></script>\n";
	echo "<script type=\"text/javascript\">\n// <!--\nnew Asset.css('" . $module_base . "mod_yoo_carousel.css');\n// -->\n</script>\n";
}

echo "<script type=\"text/javascript\">\n// <!--\n$javascript\n// -->\n</script>\n";