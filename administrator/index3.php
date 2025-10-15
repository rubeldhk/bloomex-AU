<?php
/**
 * @version $Id: index3.php 4750 2006-08-25 01:08:30Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// Set flag that this is a parent file
define('_VALID_MOS', 1);

$aHost = explode(".", $_SERVER["HTTP_HOST"]);
if (strtolower($aHost[0]) == "www") {
    header("Location: https://bloomex.com.au" . $_SERVER["REQUEST_URI"]);
}
require( '../globals.php' );
require_once( '../configuration.php' );
require_once( $mosConfig_absolute_path . '/includes/joomla.php' );
include_once( $mosConfig_absolute_path . '/language/' . $mosConfig_lang . '.php' );
require_once( $mosConfig_absolute_path . '/administrator/includes/admin.php' );
header('Content-Type: text/html; charset=utf-8');
// must start the session before we create the mainframe object
session_name(md5($mosConfig_live_site));
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 0,
    'cookie_secure' => true,
    'cookie_httponly' => true,
]);

$option = strval(strtolower(mosGetParam($_REQUEST, 'option', '')));
$task = strval(mosGetParam($_REQUEST, 'task', ''));

// mainframe is an API workhorse, lots of 'core' interaction routines
$mainframe = new mosMainFrame($database, $option, '..', true);

// admin session handling
$my = $mainframe->initSessionAdmin($option, $task);

$warehouse_only = false;
$query = "SELECT `warehouse_code` FROM `tbl_mix_user_group` AS `MUG`
INNER JOIN `tbl_new_user_group` AS `NUG` ON `NUG`.`id`=`MUG`.`user_group_id`
LEFT JOIN `jos_vm_warehouse` AS `w` ON `w`.`warehouse_code`=`NUG`.`departments_name`
WHERE `MUG`.`user_id`=".$my->id." AND `NUG`.`area_name` LIKE '%manage_warehouse_orders%' AND `w`.`warehouse_id` IS NOT NULL";
$database->setQuery($query);
$database->loadObject($warehouse_only);

if (!isset($my->prevs)) {
    $my->prevs = (object)[];
}

if ($warehouse_only) {
    $my->prevs->warehouse_only = $warehouse_only->warehouse_code;
} else {
    $my->prevs->warehouse_only = false;
}

// initialise some common request directives
$act = strtolower(mosGetParam($_REQUEST, 'act', ''));
$section = mosGetParam($_REQUEST, 'section', '');
$mosmsg = strval(strip_tags(mosGetParam($_REQUEST, 'mosmsg', '')));
$no_html = mosGetParam($_REQUEST, 'no_html', '');
$id = intval(mosGetParam($_REQUEST, 'id', 0));

// start the html output
if ($no_html) {
    if ($path = $mainframe->getPath('admin')) {
        require $path;
    }
    exit;
}

initGzip();
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Card Message&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
        <link rel="stylesheet" href="templates/<?php echo $mainframe->getTemplate(); ?>/css/template_css.css" type="text/css">
            <link rel="stylesheet" href="templates/<?php echo $mainframe->getTemplate(); ?>/css/theme.css" type="text/css">
         <!--       <script language="JavaScript" src="../includes/js/JSCookMenu_mini.js" type="text/javascript"></script>
                <script language="JavaScript" src="includes/js/ThemeOffice/theme.js" type="text/javascript"></script>
                <script language="JavaScript" src="../includes/js/joomla.javascript.js" type="text/javascript"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
$mainframe->set('loadEditor', true);
include_once( $mosConfig_absolute_path . '/editor/editor.php' );
initEditor();
?>-->
                </head>
                <body>
<?php
if ($mosmsg) {
    if (!get_magic_quotes_gpc()) {
        $mosmsg = addslashes($mosmsg);
    }
    echo "\n<script language=\"javascript\" type=\"text/javascript\">alert('$mosmsg');</script>";
}

// Show list of items to edit or delete or create new
if ($path = $mainframe->getPath('admin')) {
    require $path;
} else {
    ?>

                        <br />
    <?php
}
?>
                </body>
                </html>
<?php
doGzip();
?>