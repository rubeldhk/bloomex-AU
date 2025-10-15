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

if (!(function_exists('mosGetMenuLink2'))) {
    ?>
    <link rel="stylesheet" href="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/css/style.css" type="text/css" media="screen, projection"/>
    <!--[if lte IE 7]>
            <link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_absolute_path; ?>/modules/mod_123clickmenu/css/ie.css" media="screen" />
        <![endif]-->
    <script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/js/hoverIntent.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_123clickmenu/js/jquery.dropdown.js"></script>
    <?php

    function mosGetMenuLink2($mitem, &$params, $open = null, $menuclass = "") {
        global $Itemid, $mosConfig_live_site, $mainframe;

        $txt = '';

        switch ($mitem->type) {
            case 'separator':
            case 'component_item_link':
                break;

            case 'url':
                if (preg_match('/index.php\?/i', $mitem->link)) {
                    if (!preg_match('/Itemid=/i', $mitem->link)) {
                        $mitem->link .= '&Itemid=' . $mitem->id;
                    }
                }
                break;

            case 'content_item_link':
            case 'content_typed':
                // load menu params
                $menuparams = new mosParameters($mitem->params, $mainframe->getPath('menu_xml', $mitem->type), 'menu');

                $unique_itemid = $menuparams->get('unique_itemid', 1);

                if ($unique_itemid) {
                    $mitem->link .= '&Itemid=' . $mitem->id;
                } else {
                    $temp = explode('&task=view&id=', $mitem->link);

                    if ($mitem->type == 'content_typed') {
                        $mitem->link .= '&Itemid=' . $mainframe->getItemid($temp[1], 1, 0);
                    } else {
                        $mitem->link .= '&Itemid=' . $mainframe->getItemid($temp[1], 0, 1);
                    }
                }
                break;

            default:
                $mitem->link .= '&Itemid=' . $mitem->id;
                break;
        }

        // Active Menu highlighting
        $current_itemid = $Itemid;
        if (!$current_itemid) {
            $id = '';
        } else if ($current_itemid == $mitem->id) {
            $id = 'id="active_custom_menu' . $params->get('class_sfx') . '"';
        } else if ($params->get('activate_parent') && isset($open) && in_array($mitem->id, $open)) {
            $id = 'id="active_custom_menu' . $params->get('class_sfx') . '"';
        } else {
            $id = '';
        }

        if ($params->get('full_active_id')) {
            // support for `active_custom_menu` of 'Link - Component Item'	
            if ($id == '' && $mitem->type == 'component_item_link') {
                parse_str($mitem->link, $url);
                if ($url['Itemid'] == $current_itemid) {
                    $id = 'id="active_custom_menu' . $params->get('class_sfx') . '"';
                }
            }

            // support for `active_custom_menu` of 'Link - Url' if link is relative
            if ($id == '' && $mitem->type == 'url' && strpos('http', $mitem->link) === false) {
                parse_str($mitem->link, $url);
                if (isset($url['Itemid'])) {
                    if ($url['Itemid'] == $current_itemid) {
                        $id = 'id="active_custom_menu' . $params->get('class_sfx') . '"';
                    }
                }
            }
        }

        // replace & with amp; for xhtml compliance
        $mitem->link = ampReplace($mitem->link);

        // run through SEF convertor
        $mitem->link = sefRelToAbs($mitem->link);

        // replace & with amp; for xhtml compliance
        // remove slashes from excaped characters
        $mitem->name = stripslashes(ampReplace($mitem->name));

        switch ($mitem->browserNav) {
            // cases are slightly different
            case 1:
                // open in a new window
                $txt = '<a href="' . $mitem->link . '" target="_blank" class="' . $menuclass . '" ' . $id . '>' . $mitem->name . '</a>';
                break;

            case 2:
                // open in a popup window
                $txt = "<a href=\"#\" onclick=\"javascript: window.open('" . $mitem->link . "', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false\" class=\"$menuclass\" " . $id . ">" . $mitem->name . "</a>\n";
                break;

            case 3:
                // don't link it
                $txt = '<a href="#" class="' . $menuclass . '" ' . $id . ' onclick="return false;">' . $mitem->name . '</a>';
                break;

            default:
                // open in parent window
                $txt = '<a href="' . $mitem->link . '" class="' . $menuclass . '" ' . $id . '>' . $mitem->name . '</a>';
                break;
        }

        return $txt;
    }

}

//SETUP MENU


$params->def('menutype', 'mainmenu');
$params->def('class_sfx', '');
$params->def('menu_images', 0);
$params->def('menu_images_align', 0);
$params->def('expand_menu', 0);
$params->def('activate_parent', 0);
$params->def('indent_image', 0);
$params->def('indent_image1', 'indent1.png');
$params->def('indent_image2', 'indent2.png');
$params->def('indent_image3', 'indent3.png');
$params->def('indent_image4', 'indent4.png');
$params->def('indent_image5', 'indent5.png');
$params->def('indent_image6', 'indent.png');
$params->def('spacer', '');
$params->def('end_spacer', '');
$params->def('full_active_id', 0);


global $database, $my, $cur_template, $Itemid;
global $mosConfig_absolute_path, $mosConfig_shownoauth, $mosConfig_live_site;
?>
<?php
$and = '';
if (!$mosConfig_shownoauth) {
    $and = "\n AND access <= $my->gid";
}
$sql = "SELECT m.*"
        . "\n FROM #__menu AS m"
        . "\n WHERE menutype = '" . $params->get('menutype') . "'"
        . "\n AND published = 1"
        . $and
        . "\n AND parent = 0"
        . "\n ORDER BY ordering"
;
$database->setQuery($sql);
$rows = $database->loadObjectList('id');

//echo $sql."<br/><br/><br/>";
////print_r($rows);
//
//echo"<br/><br/><br/>";
//
//$links = array();
//foreach ($rows as $row) {
//	$links[] = mosGetMenuLink2( $row, $params, null, $menuclass );
//}
//print_r($links);
$menuclass = '';
if (count($rows)) {
    ?>
    <ul class="dropdown">
        <?php
        foreach ($rows as $row) {
            $links = mosGetMenuLink2($row, $params, null, $menuclass);

            $sql = "SELECT m.* FROM #__menu AS m WHERE menutype = '" . $params->get('menutype') . "' AND published = 1" . $and . "\n AND parent = " . $row->id . " ORDER BY ordering";
            $database->setQuery($sql);
            $sub_rows2 = $database->loadObjectList('id');
            ?>	
            <li>
                <?php echo $links; ?>

                <?php
                if (count($sub_rows2)) {
                    ?>
                    <ul class="sub_menu">
                        <?php
                        foreach ($sub_rows2 as $row2) {
                            $links2 = mosGetMenuLink2($row2, $params, null, $menuclass);

                            $sql = "SELECT m.* FROM #__menu AS m WHERE menutype = '" . $params->get('menutype') . "' AND published = 1" . $and . "\n AND parent = " . $row2->id . " ORDER BY ordering";
                            $database->setQuery($sql);
                            $sub_rows3 = $database->loadObjectList('id');
                            ?>
                            <li>
                                <?php echo $links2; ?>

                                <?php
                                if (count($sub_rows3)) {
                                    ?>
                                    <ul>
                                        <?php
                                        foreach ($sub_rows3 as $row3) {
                                            $links3 = mosGetMenuLink2($row3, $params, null, $menuclass);
                                            ?>
                                            <li><?php echo $links3; ?></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                        <?php
                                    }
                                    ?>
                            </li>
                                <?php
                            }
                            ?>
                    </ul>
                        <?php
                    }
                    ?>
            </li>
                <?php
            }
            ?>	
    </ul>
        <?php
    }
    ?>