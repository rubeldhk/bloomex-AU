<?php

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../');
define('_VALID_MOS', 'true');
define('_JEXEC', 'true');
require_once $_SERVER['DOCUMENT_ROOT'].'/configuration.php';
require_once($mosConfig_absolute_path . "/includes/kint.phar");
require_once($mosConfig_absolute_path . "/includes/kint_fleo.php");
require_once $mosConfig_absolute_path . '/includes/joomla.php';
global $database;
$database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$task = 'generate_html';

$mosConfig_lang = 'english';
$html_sitemap_en = include '../components/com_sitemap_html/sitemap_html.php';

$sitemap = $_SERVER['DOCUMENT_ROOT'] . '/sitemap.html';
$handle = fopen($sitemap, 'w') or die('Cannot open file: ' . $sitemap);
fwrite($handle, $html_sitemap_en);
fclose($handle);
