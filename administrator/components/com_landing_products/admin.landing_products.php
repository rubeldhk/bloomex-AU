<?php

/**
 * @version 1.0
 * @package Citymanger
 * @copyright Copyright (C) 2012 Bloomex
 */
/** ensure this file is being included by a parent file */
defined('_VALID_MOS') or
        die('Direct Access to this location is not allowed.');

require_once( $mainframe->getPath('admin_html') ); // include support libraries

$LandingProducts = new LandingProducts();
$LandingProducts->create(); // Формируем список
?>