<?php

/***************************************
 * $Id: support.html.php,v 1.3 2005/06/05 10:12:44 tcp Exp $
 *
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.3 $
 **/

// Get the PHP include path
// Create a DBQ Query object so that we can see if the user has added
//  any paths to be included
DBQ_Settings::includeClassFileForAdminType('DBQ_admin_query');
$obj = new DBQ_admin_query();
$include_path = ini_get('include_path');

// ADODB Driver
require_once("$dbq_class_path/drivers/adodb.class.php");
$support['adodb'] = DBQ_driver_adodb::isSupportedLocally();

// Joomla Driver
require_once("$dbq_class_path/drivers/joomla.class.php");
$support['joomla'] = DBQ_driver_joomla::isSupportedLocally();

// MySQL Driver
require_once("$dbq_class_path/drivers/mysql.class.php");
$support['mysql'] = DBQ_driver_mysql::isSupportedLocally();

// PEAR Driver
require_once("$dbq_class_path/drivers/pear.class.php");
$support['pear'] = DBQ_driver_pear::isSupportedLocally();

$PEAR = $support['pear'] ? _LANG_YES : _LANG_NO;
$ADODB = $support['adodb'] ? _LANG_YES : _LANG_NO;
$MYSQL = $support['mysql'] ? _LANG_YES : _LANG_NO;
$MYSQLI = _LANG_UNDER_DEVELOPMENT;

$proFile = $obj->_settings->getClassFileForType('dbq_professional');
$pro = ( is_file($proFile) ) ? true: false;
?>

<table>
  <tr>
    <td><?php echo  _LANG_HOSTNAME ?></td>
    <td><?php echo  $_SERVER['SERVER_NAME'] ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_DBQ_VERSION ?></td>
    <td><?php echo  _DBQ_VERSION ?> </td>
  </tr>
  <tr>
    <td><?php echo  _LANG_DBQ_PROFESSIONAL_INSTALLED ?></td>
    <td><?php echo  $pro ? _LANG_YES : _LANG_NO ?> </td>
  </tr>
  <tr>
    <td><?php echo  _LANG_PHP_VERSION ?></td>
    <td><?php echo  PHP_VERSION ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_JOOMLA_VERSION ?></td>
    <td><?php echo  $version ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_WEBSERVER_VERSION ?></td>
    <td><?php echo  $_SERVER['SERVER_SOFTWARE'] ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_INCLUDE_PATH ?></td>
    <td><?php echo  $include_path ?></td>
  </tr> 
  <tr>
    <td><?php echo  _LANG_JOOMLA_SUPPORT ?></td>
    <td><?php echo  _LANG_YES ?></td>
  </tr>  
  <tr>
    <td><?php echo  _LANG_PEAR_SUPPORT ?></td>
    <td><?php echo  $PEAR ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_ADODB_SUPPORT ?></td>
    <td><?php echo  $ADODB ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_MYSQL_SUPPORT ?></td>
    <td><?php echo  $MYSQL ?></td>
  </tr>
  <tr>
    <td><?php echo  _LANG_MYSQLI_SUPPORT ?></td>
    <td><?php echo  $MYSQLI ?></td>
  </tr>  
</table>
