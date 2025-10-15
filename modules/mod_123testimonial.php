<?php
/**
 * @version $Id: mod_mainmenu.php 3592 2006-05-22 15:26:35Z stingrey $
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_VALID_MOS') or die('Restricted access');

//SETUP MENU
global $mosConfig_absolute_path, $mosConfig_shownoauth, $mosConfig_live_site;

$limit = $params->get('limit', '100');

$query = "SELECT * FROM tbl_testimonials WHERE published= 1 ORDER BY id DESC LIMIT $limit";
$database->setQuery($query);
$rows = $database->loadObjectList();

if (count($rows)) {
    ?>
<div id="marquue" height="25" style="display: block-inline; width: 568px; height: 25px; overflow: hidden;" SCROLLDELAY="120">
        <div style="margin:0px 0px 0px 0px; float: left;font-weight: bold;">
            <div id="hidden-testimonial" style="display:none">
                <?php
                $k = 0;
                foreach ($rows as $row) {
                    echo trim($row->msg . ", " . $row->client_name . ", " . $row->city_name);
                    if (count($rows) > $k + 1)
                        echo "&nbsp;---&nbsp;";
                    $k++;
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>