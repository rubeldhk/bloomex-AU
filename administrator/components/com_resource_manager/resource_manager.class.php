<?php

/**
 * @version $Id: contact.class.php 10002 2008-02-08 10:56:57Z willebil $
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

class mosResourceManagerOptionSettings extends mosDBTable
{
    var $id = null;
    var $name = null;
    var $description = null;
    var $type = null;
    var $author_name = null;
    var $header_content = null;
    var $body_content = null;
    var $footer_content = null;
    var $status = null;
    var $queue = null;
    var $alias = null;
    var $created_at = null;
    var $updated_at = null;

    public function __construct()
    {
        global $database;
        parent::__construct('jos_vm_resource_managers', 'id', $database);
    }
}
