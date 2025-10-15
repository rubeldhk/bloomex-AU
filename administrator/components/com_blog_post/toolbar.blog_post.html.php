<?php
/**
 * @version $Id: toolbar.contact.html.php 10002 2008-02-08 10:56:57Z willebil $
 * @package Joomla
 * @subpackage Contact
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_VALID_MOS') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Contact
 */
class TOOLBAR_Settings
{
    /**
     * Draws the menu for a New Contact
     */
    static function _EDIT()
    {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::back();
        mosMenuBar::spacer();
        mosMenuBar::save();
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }

    static function _DEFAULT()
    {
        mosMenuBar::startTable();
        mosMenuBar::spacer();
        mosMenuBar::back();
        mosMenuBar::spacer();
        mosMenuBar::addNewX();
        mosMenuBar::spacer();
        mosMenuBar::deleteList('');
        mosMenuBar::spacer();
        mosMenuBar::endTable();
    }
}
