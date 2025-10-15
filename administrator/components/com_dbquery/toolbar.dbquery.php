<?php

  /***************************************
   * $Id: toolbar.dbquery.php,v 1.6 2005/05/30 17:43:38 tcp Exp $
   * 
   * @package Database Query
   * @Copyright (C) Toby Patterson
   * @ All rights reserved
   * @ DBQuery is Free Software
   * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
   * @version $Revision: 1.6 $
   **/

defined('_VALID_MOS') or die(_LANG_NO_ACCESS);

require_once ($mainframe->getPath('toolbar_html'));
require_once ($mainframe->getPath('toolbar_default'));

global $task;

switch ($task) {
	//	menuDBQuery::NEW_MENU();
	//	break;
 case 'savethenparse' :
 case 'parse' :
	 menuDBQuery::PARSE_MENU();
	 break;
 case 'new' :
	 //case 'save' :
 case 'edit' :
 case 'apply':
	 menuDBQuery::EDIT_MENU();
	 break;
 case 'preview':
	 menuDBQuery::EMPTY_MENU();
	 // nothing to display
	 break;
 default :
	 $act = mosGetParam($_REQUEST, 'act');
	 switch ($act) {
	 case 'template':
		 menuDBQuery::File_MENU();
		 break;
	 case 'query' :
		 menuDBQuery::QUERY_MENU();
		 break;
	 case 'errors':
	 case 'help' :
	 case 'consulting' :
	 case 'license' :
	 case 'stats' :
	 case 'web' :
	 case 'preview' :
	 case 'ExecuteQuery' :
	 case 'PrepareQuery' :
		 //case 'template':
	 case 'SelectQuery' :
		 menuDBQuery::EMPTY_MENU();
		 break;
	 default :
		 menuDBQuery::DEFAULT_MENU();
		 break;
	 }
 }
?>

