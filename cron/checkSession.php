<?php
require_once( 'configuration.php' );
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Which database?
if (!mysql_select_db($mosConfig_db, $link)) {
    echo 'Could not select database';
    exit;
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
