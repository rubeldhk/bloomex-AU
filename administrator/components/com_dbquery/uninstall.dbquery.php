<?php

defined( '_VALID_MOS' ) or die( _LANG_NO_ACCESS );

function com_uninstall() {

	echo "performing uninstall";
?>
	<p>In order to completely uninstall DBQ, execute the following queries to remove the DBQ tables.  If your Joomla prefix is not "jos_", change the query to work with your prefix.</p>

		<ul>
			 <li>DELETE FROM `jos_dbquery`</li>
			 <li>DROP TABLE `jos_dbquery`</li>
			 <li>DELETE FROM `jos_dbquery_config`</li>
			 <li>DROP TABLE `jos_dbquery_config`</li>
			 <li>DELETE FROM `jos_dbquery_databases`</li>
			 <li>DROP TABLE `jos_dbquery_databases`</li>
			 <li>DELETE FROM `jos_dbquery_drivers`</li>
			 <li>DROP TABLE `jos_dbquery_drivers`</li>
			 <li>DELETE FROM `jos_dbquery_errors`</li>
			 <li>DROP TABLE `jos_dbquery_errors`</li>
			 <li>DELETE FROM `jos_dbquery_substitutions`</li>
			 <li>DROP TABLE `jos_dbquery_substitutions`</li>
			 <li>DELETE FROM `jos_dbquery_stats`</li>
			 <li>DROP TABLE `jos_dbquery_stats`</li>
			 <li>DELETE FROM `jos_dbquery_templates`</li>
			 <li>DROP TABLE `jos_dbquery_templates`</li>
			 <li>DELETE FROM `jos_dbquery_variables`</li>
			 <li>DROP TABLE `jos_dbquery_variables`</li>
			 <li>DELETE FROM `jos_components` WHERE `option` = 'com_dbquery'</li>
			 <li>DELETE FROM `jos_categories` WHERE `section` = 'com_dbquery'</li>
			 </ul>
 <?php
	 }

?>