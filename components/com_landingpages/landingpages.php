<?php

/**
 * @version $Id: contact.php 4730 2006-08-24 21:25:37Z stingrey $
 * @package Joomla
 * @subpackage Contact
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

// load the html drawing class
require_once( $mainframe->getPath('front_html') ); //landingpages.html.php
require_once( 'landingpages.class.php' ); //landingpages.class.php


switch ($task) {
    default:
        viewPage();
        break;
}

function prepare_symbols($str) {

    $normalizeChars = array(
        '?' => 'S', '?' => 's', '?' => 'Dj', '?' => 'Z', '?' => 'z', '?' => 'A', '?' => 'A', '?' => 'A', '?' => 'A', '?' => 'A',
        '?' => 'A', '?' => 'A', '?' => 'C', '?' => 'E', '?' => 'E', '?' => 'E', '?' => 'E', '?' => 'I', '?' => 'I', '?' => 'I',
        '?' => 'I', '?' => 'N', '?' => 'N', '?' => 'O', '?' => 'O', '?' => 'O', '?' => 'O', '?' => 'O', '?' => 'O', '?' => 'U', '?' => 'U',
        '?' => 'U', '?' => 'U', '?' => 'Y', '?' => 'B', '?' => 'Ss', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'a', '?' => 'a',
        '?' => 'a', '?' => 'a', '?' => 'c', '?' => 'e', '?' => 'e', '?' => 'e', '?' => 'e', '?' => 'i', '?' => 'i', '?' => 'i',
        '?' => 'i', '?' => 'o', '?' => 'n', '?' => 'n', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'o', '?' => 'u',
        '?' => 'u', '?' => 'u', '?' => 'u', '?' => 'y', '?' => 'y', '?' => 'b', '?' => 'y', '?' => 'f',
        '?' => 'a', '?' => 'i', '?' => 'a', '?' => 's', '?' => 't', '?' => 'A', '?' => 'I', '?' => 'A', '?' => 'S', '?' => 'T',
    );
    $str = strtr($str, $normalizeChars);
    return $str;
}

function viewPage() {
    global $mainframe, $sef;
    $values = new LandingPage();
    if (!$values->check_url()) {
        $sef->landing_type = 0;
        $sef->run404();
        return false;
    }
    $info_def = $values->getdefaultvalues();
    $info_landing = $values->getinfo();
    // f($info_def, $info_landing);
    foreach ($info_landing as $k => $v) {
        //  if ($v == '' && $info_def[$k]) {
        if (isset($info_def[$k]) && ($info_def[$k] != '')) {
            $info_landing[$k] = $info_def[$k];
        }
        unset($info_def[$k]);
    }
    $Page = array_merge($info_landing, $info_def);
    if ($Page) {
        $Page['city'] = prepare_symbols($Page['city']);
        $values->settimezone($Page['province']);
        $products = $values->getproducts($Page['category']);
        $healthy = array("{city_name}","{city}", "{province}");
        $yummy = array($Page['city'],$Page['city'], $Page['province']);
        $Page['right_pop'] = str_replace($healthy, $yummy, $Page['right_pop']);
        $Page['left_pop'] = str_replace($healthy, $yummy, $Page['left_pop']);
        $Page['content'] = str_replace($healthy, $yummy, (($sef->description_text)?$sef->description_text:$Page['content']));
        $Page['center_pop'] = str_replace($healthy, $yummy, $Page['center_pop']);
        HTML_LandingPages::viewPage($Page, $products);
    } else {
        $sef->landing_type = 0;
        $sef->run404();
    }
}
