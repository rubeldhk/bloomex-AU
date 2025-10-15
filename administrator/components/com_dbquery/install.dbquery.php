<?php

/***************************************
 * 
 * @package Database Query
 * @Copyright (C) Toby Patterson
 * @ All rights reserved
 * @ DBQuery is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 **/

defined( '_VALID_MOS' ) or die( _LANG_NO_ACCESS );

function com_install() {
  echo "If you wish to run the sample queries, you will need to adjust permissions of the DBQ categories.<br/>";

  global $database, $mainframe;
  
  // Create Two Categories
  $check = "SELECT COUNT(*) AS already FROM #__categories WHERE section = 'com_dbquery' AND `name` like 'DBQ%'";
  $database->SetQuery($check);
  $database->query();
  $categories = $database->loadResult();

  // If the categories do not exist, create them
  if ( ! $categories ) {
	  echo "Inserting Categories";
	  $category_query1 = "insert into #__categories values ( NULL, 0, 'Database Query', 'DBQ', '', 'com_dbquery', 'left', 'DBQ Category', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '' )";
	  $category_query2 = "insert into #__categories values ( NULL, 0, 'Database Query Demo', 'DBQ Demo', '', 'com_dbquery', 'left', 'DBQ Demo Category', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '' )";
	  
	  // Exec the queries, get the id from the second query
	  $database->setQuery($category_query1);
	  $database->query();
	  $database->setQuery($category_query2);
	  if ( $database->query() ) {
		  $catid = $database->insertid();
	  }
  }

  // Insert a database entry
  $check = 'SELECT COUNT(*) AS already FROM #__dbquery_databases';
  $database->SetQuery($check);
  $database->query();
  $databases = $database->loadResult();

  // If a database entry for the Joomla does not exist, create it
  if ( ! $databases ) {
	  echo "Creating an connection to the Joomla database";
	  $joomla_db = "INSERT INTO #__dbquery_databases VALUES (NULL,'Joomla','mysql','JOOMLA',1,0,'" . $mainframe->getCfg('host') . "','" . $mainframe->getCfg('db') . "','" . $mainframe->getCfg('user') . "','xxx',1,1,0,'GENERAL_DESCRIPTION=Your Joomla database.','',0,'0000-00-00 00:00:00', NULL)";
	  $database->setQuery($joomla_db);
	  $database->query();
  }

  // Queries to update the installation
  $queries = array(
  			/* "UPDATE #__dbquery SET catid = $catid", */
			"UPDATE #__components SET admin_menu_img='../administrator/components/com_dbquery/images/dbq_mini.jpg' WHERE `option` = 'com_dbquery' AND admin_menu_link = 'option=com_dbquery'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/db.png'         WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=database%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/query.png'      WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=query%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/content.png'    WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=variable%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/content.png'    WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=substitution%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/template.png'   WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=template%'",		   
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/statistics.png' WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=stats%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/categories.png' WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%option=categories%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/config.png'     WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=config%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/preview.png'    WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=preview%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/globe1.png'     WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=consulting%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/help.png'       WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=help%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/license.png'    WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=license%'",
			"UPDATE #__components SET admin_menu_img='js/ThemeOffice/globe2.png'     WHERE `option` = 'com_dbquery' AND admin_menu_link LIKE '%act=web%'"
			);

	// Fire off the quries
	foreach ( $queries as $query ) {
		$database->setQuery($query);
		if ( ! $database->query() ) {
			echo "Error: could not execute query '$query'<br/>" . $database->getErrorMsg() . '<br/>';
		}
	}
}

?>